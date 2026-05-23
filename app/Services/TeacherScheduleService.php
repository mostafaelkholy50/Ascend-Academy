<?php

namespace App\Services;

use App\Repositories\TeacherScheduleRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherScheduleService
{
    protected $repository;

    public function __construct(TeacherScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getWeeklyData(User $teacher, Request $request): array
    {
        // Get the week start date (default to current week)
        $weekStart = $request->has('week') 
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();
        
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // Get all schedules for this week
        $schedules = $this->repository->getSchedulesForRange($teacher, $weekStart, $weekEnd);
        
        // Group schedules by day
        $schedulesByDay = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $schedulesByDay[$date->format('Y-m-d')] = [
                'date' => $date,
                'schedules' => $schedules->filter(function($schedule) use ($date) {
                    return $schedule->starts_at->isSameDay($date);
                })->values()
            ];
        }
        
        // Calculate navigation dates
        $prevWeek = $weekStart->copy()->subWeek();
        $nextWeek = $weekStart->copy()->addWeek();

        return compact(
            'schedulesByDay',
            'weekStart',
            'weekEnd',
            'prevWeek',
            'nextWeek'
        );
    }

    public function getDailyData(User $teacher, Request $request): array
    {
        // Get the date (default to today)
        $date = $request->has('date') 
            ? Carbon::parse($request->date)
            : now();
        
        // Get all schedules for this day using performance optimization (whereBetween instead of whereDate)
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        
        $schedules = $this->repository->getSchedulesForRange($teacher, $startOfDay, $endOfDay);
        
        // Calculate navigation dates
        $prevDay = $date->copy()->subDay();
        $nextDay = $date->copy()->addDay();
        
        // Get statistics for the day
        $totalSessions = $schedules->count();
        $completedSessions = $schedules->filter(function($schedule) {
            return $schedule->status === 'completed';
        })->count();
        
        $totalHours = $schedules->sum(function($schedule) {
            return $schedule->getDurationInHours();
        });

        return compact(
            'schedules',
            'date',
            'prevDay',
            'nextDay',
            'totalSessions',
            'completedSessions',
            'totalHours'
        );
    }
}
