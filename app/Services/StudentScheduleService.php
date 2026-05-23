<?php

namespace App\Services;

use App\Repositories\StudentScheduleRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentScheduleService
{
    protected $repository;

    public function __construct(StudentScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getWeeklyData(User $student, Request $request): array
    {
        $userTimezone = $student->getUserTimezone();

        // Get the week to display (default to current week)
        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : Carbon::now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        // Calculate previous and next week dates
        $prevWeek = $weekStart->copy()->subWeek();
        $nextWeek = $weekStart->copy()->addWeek();

        // Get all schedules for this week
        $schedules = $this->repository->getSchedulesForRange($student, $weekStart, $weekEnd);

        // Group schedules by day
        $schedulesByDay = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $schedulesByDay[] = [
                'date' => $date,
                'schedules' => $schedules->filter(function ($schedule) use ($date) {
                    return $schedule->starts_at->isSameDay($date);
                })
            ];
        }

        return compact(
            'student',
            'schedulesByDay',
            'weekStart',
            'weekEnd',
            'prevWeek',
            'nextWeek',
            'userTimezone'
        );
    }

    public function getDailyData(User $student, Request $request): array
    {
        $userTimezone = $student->getUserTimezone();

        // Get the date to display (default to today)
        $date = $request->has('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        // Calculate previous and next day dates
        $prevDay = $date->copy()->subDay();
        $nextDay = $date->copy()->addDay();

        // Get all schedules for this day
        $schedules = $this->repository->getSchedulesForDate($student, $date);

        return compact(
            'student',
            'schedules',
            'date',
            'prevDay',
            'nextDay',
            'userTimezone'
        );
    }
}
