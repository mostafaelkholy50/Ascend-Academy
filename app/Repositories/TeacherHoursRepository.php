<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TeacherHoursRepository
{
    public function getAttendancesQueryForMonth(User $teacher, Carbon $startOfMonth, Carbon $endOfMonth): Builder
    {
        return Attendance::with(['schedule.student', 'schedule.course'])
            ->where('teacher_id', $teacher->id)
            ->where('teacher_present', true)
            ->where('student_present', true)
            ->whereHas('schedule', function($q) use ($startOfMonth, $endOfMonth) {
                // Optimized date querying
                $q->whereBetween('starts_at', [$startOfMonth, $endOfMonth]);
            })
            ->orderBy('created_at', 'desc');
    }
}
