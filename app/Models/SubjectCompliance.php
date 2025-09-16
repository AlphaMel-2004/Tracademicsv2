<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectCompliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'document_type_id',
        'semester_id',
        'evidence_link',
        'evidence_description',
        'self_evaluation_status',
        'approval_status',
        'program_head_approval_status',
        'dean_approval_status',
        'program_head_comments',
        'dean_comments',
        'program_head_approved_by',
        'dean_approved_by',
        'program_head_approved_at',
        'dean_approved_at',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'program_head_approved_at' => 'datetime',
        'dean_approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the compliance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject for the compliance.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the document type for the compliance.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the semester for the compliance.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the program head approver.
     */
    public function programHeadApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'program_head_approved_by');
    }

    /**
     * Get the dean approver.
     */
    public function deanApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_approved_by');
    }
}
