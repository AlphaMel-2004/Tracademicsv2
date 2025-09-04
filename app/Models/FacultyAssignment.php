<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'semester_id',
        'program_id',
    ];

    /**
     * Get the user (faculty) for the assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject for the assignment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the semester for the assignment.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the program for the assignment.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
