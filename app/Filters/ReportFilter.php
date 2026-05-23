<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportFilter
{
    public function apply(Builder $query, Request $request): Builder
    {
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        if ($request->filled('mastery_min')) {
            $query->where('mastery_score', '>=', $request->mastery_min);
        }
        if ($request->filled('mastery_max')) {
            $query->where('mastery_score', '<=', $request->mastery_max);
        }

        return $query;
    }
}
