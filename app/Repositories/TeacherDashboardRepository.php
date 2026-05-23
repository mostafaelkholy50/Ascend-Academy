<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherDashboardRepository
{
    public function getWeekSchedules(User $teacher, Carbon $weekStart, Carbon $weekEnd): Collection
    {
        return Schedule::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->orderBy('starts_at')
            ->get();
    }

    public function getMyStudents(User $teacher, int $limit = 6): Collection
    {
        return User::whereHas('schedules', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->with(['enrollments.course'])
            ->withCount([
                'schedules as total_sessions' => function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                },
                'schedules as completed_sessions' => function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id)
                      ->where('status', 'completed');
                }
            ])
            ->take($limit)
            ->get();
    }

    public function getStudentsNeedingReports(User $teacher, int $limit = 5): Collection
    {
        return Schedule::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->where('status', 'completed')
            ->where('starts_at', '>=', now()->subDays(7))
            ->whereDoesntHave('attendance') // Can be used as proxy for report creation
            ->orderBy('starts_at', 'desc')
            ->take($limit)
            ->get()
            ->unique('student_id')
            ->values(); // Reset keys after unique
    }

    public function getRecentReports(User $teacher, int $limit = 5): Collection
    {
        return Report::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest('report_date')
            ->take($limit)
            ->get();
    }

    public function getRecentResources(User $teacher, int $limit = 5): Collection
    {
        return Resource::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getThisMonthHours(User $teacher): float
    {
        return Schedule::where('teacher_id', $teacher->id)
            ->where('status', 'completed')
            ->whereBetween('starts_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('starts_at', 'ends_at')
            ->get()
            ->sum(fn($schedule) => $schedule->getDurationInHours());
    }
}
