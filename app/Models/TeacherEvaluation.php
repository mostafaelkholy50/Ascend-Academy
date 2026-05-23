<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherEvaluation extends Model
{
    protected $fillable = [
        'teacher_id',
        'evaluator_id',
        'evaluation_date',
        'week_start_date',
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
        'week_start_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
