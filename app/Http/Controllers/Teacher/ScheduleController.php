<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display weekly schedule view
     */
    public function index(Request $request)
    {
        $teacher = auth()->user();
        
        // Get the week start date (default to current week)
        $weekStart = $request->has('week') 
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();
        
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // Get all schedules for this week
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->where('status', '!=', 'cancelled')
            ->with(['student', 'course', 'attendance'])
            ->orderBy('starts_at')
            ->get();
        
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
        
        return view('teacher.schedule-weekly', compact(
            'schedulesByDay',
            'weekStart',
            'weekEnd',
            'prevWeek',
            'nextWeek'
        ));
    }
    
    /**
     * Display daily schedule view
     */
    public function daily(Request $request)
    {
        $teacher = auth()->user();
        
        // Get the date (default to today)
        $date = $request->has('date') 
            ? Carbon::parse($request->date)
            : now();
        
        // Get all schedules for this day
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->whereDate('starts_at', $date)
            ->where('status', '!=', 'cancelled')
            ->with(['student', 'course', 'attendance'])
            ->orderBy('starts_at')
            ->get();
        
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
        
        return view('teacher.schedule-daily', compact(
            'schedules',
            'date',
            'prevDay',
            'nextDay',
            'totalSessions',
            'completedSessions',
            'totalHours'
        ));
    }
}
