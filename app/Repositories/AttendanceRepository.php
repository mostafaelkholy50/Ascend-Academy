<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttendanceRepository
{
    /**
     * Get query for attendances.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAttendancesQuery()
    {
        return Attendance::with(['schedule.course', 'student', 'teacher'])
            ->latest('created_at');
    }

    /**
     * Get attendance with relations.
     *
     * @param Attendance $attendance
     * @return Attendance
     */
    public function getAttendanceWithRelations(Attendance $attendance)
    {
        return $attendance->load(['schedule.course', 'student', 'teacher']);
    }

    /**
     * Update or create attendance record.
     *
     * @param array $attributes
     * @param array $values
     * @return Attendance
     */
    public function updateOrCreate(array $attributes, array $values)
    {
        return Attendance::updateOrCreate($attributes, $values);
    }

    /**
     * Get attendance statistics for the current month or filtered period.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getStats(\Illuminate\Database\Eloquent\Builder $query, \Illuminate\Http\Request $request)
    {
        // Clone the query so we don't modify the original query used for pagination
        $statsQuery = clone $query;

        // If no date filters are provided, restrict stats to the current month
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $statsQuery->whereHas('schedule', function($q) {
                $q->whereMonth('starts_at', now()->month)
                  ->whereYear('starts_at', now()->year);
            });
        }

        // Student Stats
        $studentAttended = (clone $statsQuery)->where('student_present', true)->count();
        $studentAbsent = (clone $statsQuery)->where('student_present', false)->count();
        $teacherAbsentForStudent = (clone $statsQuery)->where('teacher_present', false)->count();
        $totalStudentSessions = (clone $statsQuery)->count(); // Actually total sessions

        // Teacher Stats
        $teacherAttended = (clone $statsQuery)->where('teacher_present', true)->count();
        $teacherAbsent = (clone $statsQuery)->where('teacher_present', false)->count();
        $studentAbsentForTeacher = (clone $statsQuery)->where('student_present', false)->count();
        $totalTeacherSessions = (clone $statsQuery)->count();

        return [
            'studentStats' => [
                'total' => $totalStudentSessions,
                'attended' => $studentAttended,
                'absent' => $studentAbsent,
                'teacher_absent' => $teacherAbsentForStudent,
            ],
            'teacherStats' => [
                'total' => $totalTeacherSessions,
                'attended' => $teacherAttended,
                'absent' => $teacherAbsent,
                'student_absent' => $studentAbsentForTeacher,
            ]
        ];
    }
}
