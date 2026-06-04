<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Report;
use App\Models\Resource;
use App\Models\TeacherHour;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $attributes = [
        'class_reminders_enabled' => true,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'gender',
        'phone',
        'timezone',
        'country',
        'allowed_countries',
        'can_access_payroll',
        'birth_date',
        'active',
        'class_reminders_enabled',
        'hourly_rate',
        'teacher_application_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'class_reminders_enabled' => 'boolean',
            'birth_date' => 'date',
            'hourly_rate' => 'decimal:2',
            'allowed_countries' => 'array',
            'can_access_payroll' => 'boolean',
        ];
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopeRoleParent($query)
    {
        return $query->where('role', 'Parent');
    }

    public function scopeRoleStudent($query)
    {
        return $query->where('role', 'Student');
    }

    public function scopeRoleTeacher($query)
    {
        return $query->where('role', 'Teacher');
    }

    public function scopeRoleAdmin($query)
    {
        return $query->where('role', 'Admin');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // ============================================
    // Relationships - Parent
    // ============================================
    public function children()
    {
        return $this->belongsToMany(User::class, 'children', 'parent_id', 'child_id')
            ->withTimestamps();
    }

    public function parents()
    {
        return $this->belongsToMany(User::class, 'children', 'child_id', 'parent_id')
            ->withTimestamps();
    }

    // ============================================
    // Relationships - Student
    // ============================================
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'student_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'student_id');
    }

    public function studentEvaluations()
    {
        return $this->hasMany(StudentEvaluation::class, 'student_id');
    }

    public function resources()
    {
        return $this->hasMany(Resource::class, 'student_id');
    }

    // ============================================
    // Relationships - Teacher
    // ============================================
    public function teacherSchedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function teacherAttendances()
    {
        return $this->hasMany(Attendance::class, 'teacher_id');
    }

    public function teacherReports()
    {
        return $this->hasMany(Report::class, 'teacher_id');
    }

    public function teacherResources()
    {
        return $this->hasMany(Resource::class, 'teacher_id');
    }

    public function evaluations()
    {
        return $this->hasMany(TeacherEvaluation::class, 'teacher_id');
    }

    public function evaluationsGiven()
    {
        return $this->hasMany(TeacherEvaluation::class, 'evaluator_id');
    }

    public function teacherHours()
    {
        return $this->hasMany(TeacherHour::class, 'teacher_id');
    }

    public function teacherEvaluations()
    {
        return $this->hasMany(StudentEvaluation::class, 'teacher_id');
    }

    public function availabilities()
    {
        return $this->hasMany(UserAvailability::class);
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isParent(): bool
    {
        return $this->role === 'Parent';
    }

    public function isStudent(): bool
    {
        return $this->role === 'Student';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'Teacher';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Get the timezone for the user.
     * All users use their configured timezone or default to Egypt.
     */
    public function getUserTimezone(): string
    {
        return $this->timezone ?? 'Africa/Cairo';
    }

    /**
     * Check if the user has access to teacher payroll.
     */
    public function canAccessPayroll(): bool
    {
        // SuperAdmins always have access
        if ($this->hasRole('SuperAdmin')) {
            return true;
        }

        // Direct access
        if ($this->can_access_payroll) {
            return true;
        }

        // Role-based access
        foreach ($this->roles as $role) {
            if ($role->can_access_payroll) {
                return true;
            }
        }

        return false;
    }
}
