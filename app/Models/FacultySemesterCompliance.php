<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultySemesterCompliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type_id',
        'semester_id',
        'evidence_link',
        'self_evaluation_status',
        'approval_status',
        'approved_by',
        'approved_at',
        'review_comments',
        'program_head_approval_status',
        'program_head_approved_by',
        'program_head_approved_at',
        'program_head_comments',
        'dean_approval_status',
        'dean_approved_by',
        'dean_approved_at',
        'dean_comments'
    ];

    /**
     * Get the user that owns the compliance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
     * Get the program head who approved this compliance.
     */
    public function programHeadApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'program_head_approved_by');
    }

    /**
     * Get the dean who approved this compliance.
     */
    public function deanApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_approved_by');
    }

    /**
     * Check if the compliance has evidence link.
     */
    public function hasEvidenceLink(): bool
    {
        return !empty($this->evidence_link);
    }

    /**
     * Get the display status for evidence.
     */
    public function getEvidenceStatusAttribute(): string
    {
        return $this->hasEvidenceLink() ? 'link' : 'required';
    }
}
