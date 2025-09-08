<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'department_id',
        'description',
    ];

    /**
     * Get the department that owns the program.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the subjects for the program.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the curricula for the program.
     */
    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    /**
     * Get the faculty assignments for the program.
     */
    public function facultyAssignments(): HasMany
    {
        return $this->hasMany(FacultyAssignment::class);
    }

    /**
     * Get the users that belong to the program.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
