<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'url',
        'title',
        'description',
    ];

    /**
     * Get the submission that owns the link.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(ComplianceSubmission::class, 'submission_id');
    }
}
