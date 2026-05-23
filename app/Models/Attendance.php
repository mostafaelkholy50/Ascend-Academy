<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'schedule_id',
        'student_id',
        'teacher_id',
        'student_present',
        'teacher_present',
        'remark',
        'student_report',
        'teacher_report',
    ];

    protected function casts(): array
    {
        return [
            'student_present' => 'boolean',
            'teacher_present' => 'boolean',
        ];
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopeStudentPresent($query)
    {
        return $query->where('student_present', true);
    }

    public function scopeStudentAbsent($query)
    {
        return $query->where('student_present', false);
    }

    public function scopeTeacherPresent($query)
    {
        return $query->where('teacher_present', true);
    }

    public function scopeTeacherAbsent($query)
    {
        return $query->where('teacher_present', false);
    }

    // ============================================
    // Relationships
    // ============================================
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isStudentPresent(): bool
    {
        return $this->student_present;
    }

    public function isTeacherPresent(): bool
    {
        return $this->teacher_present;
    }

    public function isBothPresent(): bool
    {
        return $this->student_present && $this->teacher_present;
    }
}
