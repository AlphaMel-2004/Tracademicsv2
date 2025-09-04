<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplianceSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type_id',
        'semester_id',
        'subject_id',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'review_comments',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document type for the submission.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the semester for the submission.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the subject for the submission (if applicable).
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the reviewer of the submission.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the compliance documents for the submission.
     */
    public function complianceDocuments(): HasMany
    {
        return $this->hasMany(ComplianceDocument::class, 'submission_id');
    }

    /**
     * Get the compliance links for the submission.
     */
    public function complianceLinks(): HasMany
    {
        return $this->hasMany(ComplianceLink::class, 'submission_id');
    }
}
