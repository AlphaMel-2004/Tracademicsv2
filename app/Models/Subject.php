<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'units',
        'program_id',
        'year_level',
    ];

    /**
     * Get all faculty users assigned to this subject.
     */
    public function assignedFaculty()
    {
        return $this->hasManyThrough(
            User::class,
            FacultyAssignment::class,
            'subject_id', // Foreign key on FacultyAssignment table...
            'id',         // Foreign key on User table...
            'id',         // Local key on Subject table...
            'user_id'     // Local key on FacultyAssignment table...
        );
    }


    /**
     * Get the program that owns the subject.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the faculty assignments for the subject.
     */
    public function facultyAssignments(): HasMany
    {
        return $this->hasMany(FacultyAssignment::class);
    }

    /**
     * Get the compliance submissions for the subject.
     */
    public function complianceSubmissions(): HasMany
    {
        return $this->hasMany(ComplianceSubmission::class);
    }

    /**
     * Get the curriculum subjects for the subject.
     */
    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }
}
