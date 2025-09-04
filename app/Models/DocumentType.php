<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'submission_type',
        'is_required',
        'due_days',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the compliance submissions for the document type.
     */
    public function complianceSubmissions(): HasMany
    {
        return $this->hasMany(ComplianceSubmission::class);
    }
}
