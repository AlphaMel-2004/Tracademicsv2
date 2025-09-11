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
        'review_comments'
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
