<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ComplianceSubmission;

class SubmissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(ComplianceSubmission $submission, string $type)
    {
        $this->submission = $submission;
        $this->type = $type; // 'submitted', 'approved', 'rejected'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'submitted' => 'Document Submitted Successfully - TracAdemics',
            'approved' => 'Document Approved - TracAdemics',
            'rejected' => 'Document Requires Attention - TracAdemics',
            default => 'TracAdemics Notification'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-notification',
            with: [
                'submission' => $this->submission,
                'type' => $this->type,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
