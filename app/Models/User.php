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

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'gender',
        'phone',
        'birth_date',
        'active',
        'hourly_rate',
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
            'birth_date' => 'date',
            'hourly_rate' => 'decimal:2',
        ];
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopeParents($query)
    {
        return $query->where('role', 'Parent');
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'Student');
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', 'Teacher');
    }

    public function scopeAdmins($query)
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

    public function teacherHours()
    {
        return $this->hasMany(TeacherHour::class, 'teacher_id');
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
}
