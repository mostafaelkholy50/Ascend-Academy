<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'course_id',
        'level',
        'mastery_score',
        'strengths',
        'weaknesses',
        'behavior',
        'notes',
        'report_date',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'mastery_score' => 'integer',
        ];
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('report_date', '>=', now()->subDays($days));
    }

    // ============================================
    // Relationships
    // ============================================
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function hasMasteryScore(): bool
    {
        return !is_null($this->mastery_score);
    }

    public function getMasteryLevel(): string
    {
        if (!$this->hasMasteryScore()) {
            return 'Not Available';
        }

        return match (true) {
            $this->mastery_score >= 90 => 'Excellent',
            $this->mastery_score >= 80 => 'Very Good',
            $this->mastery_score >= 70 => 'Good',
            $this->mastery_score >= 60 => 'Average',
            default =>  'Needs Improvement',
        };
    }
}
