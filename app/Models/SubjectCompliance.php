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
     * Get the subject that this compliance belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the document type for this compliance.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
