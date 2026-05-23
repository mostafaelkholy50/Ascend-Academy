<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'course_id',
        'evaluation_date',
        'evaluation_month',
        'evaluation_year',
        'q1_score',
        'q2_score',
        'q3_score',
        'q4_score',
        'q5_score',
        'q6_score',
        'q7_score',
        'q8_score',
        'q9_score',
        'q10_score',
        'total_score',
        'notes',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'evaluation_month' => 'integer',
        'evaluation_year' => 'integer',
        'q1_score' => 'integer',
        'q2_score' => 'integer',
        'q3_score' => 'integer',
        'q4_score' => 'integer',
        'q5_score' => 'integer',
        'q6_score' => 'integer',
        'q7_score' => 'integer',
        'q8_score' => 'integer',
        'q9_score' => 'integer',
        'q10_score' => 'integer',
        'total_score' => 'integer',
    ];

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

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('evaluation_month', $month)
                     ->where('evaluation_year', $year);
    }
}
