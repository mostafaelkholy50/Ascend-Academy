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
     * Get attendance statistics.
     *
     * @return array
     */
    public function getStats()
    {
        $totalSessions = Attendance::count();
        $bothPresent = Attendance::where('student_present', true)
            ->where('teacher_present', true)
            ->count();
        $partialAttendance = Attendance::where(function($q) {
            $q->where('student_present', true)->where('teacher_present', false)
              ->orWhere('student_present', false)->where('teacher_present', true);
        })->count();
        $bothAbsent = Attendance::where('student_present', false)
            ->where('teacher_present', false)
            ->count();

        return [
            'totalSessions' => $totalSessions,
            'bothPresent' => $bothPresent,
            'partialAttendance' => $partialAttendance,
            'bothAbsent' => $bothAbsent,
        ];
    }
}
