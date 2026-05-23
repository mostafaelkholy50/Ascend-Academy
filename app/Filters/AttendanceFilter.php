<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AttendanceFilter
{
    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function apply(Builder $query, Request $request)
    {
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('starts_at', '>=', $request->date_from);
            });
        }
        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('starts_at', '<=', $request->date_to);
            });
        }

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // Filter by attendance status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'both_present':
                    $query->where('student_present', true)
                          ->where('teacher_present', true);
                    break;
                case 'student_absent':
                    $query->where('student_present', false);
                    break;
                case 'teacher_absent':
                    $query->where('teacher_present', false);
                    break;
                case 'both_absent':
                    $query->where('student_present', false)
                          ->where('teacher_present', false);
                    break;
            }
        }

        return $query;
    }
}
