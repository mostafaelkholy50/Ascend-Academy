<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class StudentScheduleRepository
{
    public function getSchedulesForRange(User $student, Carbon $start, Carbon $end): Collection
    {
        return Schedule::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get();
    }

    public function getSchedulesForDate(User $student, Carbon $date): Collection
    {
        return Schedule::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->whereDate('starts_at', $date)
            ->orderBy('starts_at')
            ->get();
    }
}
