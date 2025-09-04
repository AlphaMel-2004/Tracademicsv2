<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'academic_year',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users with this as current semester.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'current_semester_id');
    }

    /**
     * Get the faculty assignments for the semester.
     */
    public function facultyAssignments(): HasMany
    {
        return $this->hasMany(FacultyAssignment::class);
    }

    /**
     * Get the compliance submissions for the semester.
     */
    public function complianceSubmissions(): HasMany
    {
        return $this->hasMany(ComplianceSubmission::class);
    }

    /**
     * Get the semester sessions for the semester.
     */
    public function semesterSessions(): HasMany
    {
        return $this->hasMany(SemesterSession::class);
    }
}
