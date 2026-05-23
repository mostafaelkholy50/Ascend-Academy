<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherScheduleRepository
{
    public function getSchedulesForRange(User $teacher, Carbon $start, Carbon $end): Collection
    {
        return Schedule::where('teacher_id', $teacher->id)
            ->whereBetween('starts_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->with(['student', 'course', 'attendance'])
            ->orderBy('starts_at')
            ->get();
    }
}
