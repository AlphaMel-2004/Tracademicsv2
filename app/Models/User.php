<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'current_semester_id',
        'faculty_type',
        'department_id',
        'program_id',
        'last_login_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the current semester that owns the user.
     */
    public function currentSemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'current_semester_id');
    }

    /**
     * Get the department that owns the user.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the program that owns the user.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the faculty assignments for the user.
     */
    public function facultyAssignments(): HasMany
    {
        return $this->hasMany(FacultyAssignment::class);
    }

    /**
     * Get the faculty semester compliances for the user.
     */
    public function facultySemesterCompliances(): HasMany
    {
        return $this->hasMany(FacultySemesterCompliance::class);
    }

    /**
     * Get the subject compliances for the user.
     */
    public function subjectCompliances(): HasMany
    {
        return $this->hasMany(SubjectCompliance::class);
    }

    /**
     * Get the semester sessions for the user.
     */
    public function semesterSessions(): HasMany
    {
        return $this->hasMany(SemesterSession::class);
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role->name === $roleName;
    }

    /**
     * Check if user is MIS
     */
    public function isMIS(): bool
    {
        return $this->hasRole('MIS');
    }

    /**
     * Check if user is VPAA
     */
    public function isVPAA(): bool
    {
        return $this->hasRole('VPAA');
    }

    /**
     * Check if user is Dean
     */
    public function isDean(): bool
    {
        return $this->hasRole('Dean');
    }

    /**
     * Check if user is Program Head
     */
    public function isProgramHead(): bool
    {
        return $this->hasRole('Program Head');
    }

    /**
     * Check if user is Faculty
     */
    public function isFaculty(): bool
    {
        return $this->hasRole('Faculty');
    }
}
