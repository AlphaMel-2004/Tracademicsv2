<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'filename',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the submission that owns the document.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(ComplianceSubmission::class, 'submission_id');
    }
}
