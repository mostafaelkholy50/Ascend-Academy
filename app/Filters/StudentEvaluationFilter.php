<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentEvaluationFilter
{
    public function apply(Builder $query, Request $request): Builder
    {
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('month')) {
            $query->where('evaluation_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('evaluation_year', $request->year);
        }

        if ($request->filled('score_min')) {
            $query->where('total_score', '>=', $request->score_min);
        }

        if ($request->filled('score_max')) {
            $query->where('total_score', '<=', $request->score_max);
        }

        return $query;
    }
}
