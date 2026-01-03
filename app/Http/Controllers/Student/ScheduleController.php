<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function weekly(Request $request)
    {
        $student = auth()->user();
        
        // Get the week to display (default to current week)
        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : Carbon::now()->startOfWeek();
        
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // Calculate previous and next week dates
        $prevWeek = $weekStart->copy()->subWeek();
        $nextWeek = $weekStart->copy()->addWeek();
        
        // Get all schedules for this week
        $schedules = Schedule::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->orderBy('starts_at')
            ->get();
        
        // Group schedules by day
        $schedulesByDay = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $schedulesByDay[] = [
                'date' => $date,
                'schedules' => $schedules->filter(function($schedule) use ($date) {
                    return $schedule->starts_at->isSameDay($date);
                })
            ];
        }
        
        return view('student.schedule-weekly', compact(
            'student',
            'schedulesByDay',
            'weekStart',
            'weekEnd',
            'prevWeek',
            'nextWeek'
        ));
    }
    
    public function daily(Request $request)
    {
        $student = auth()->user();
        
        // Get the date to display (default to today)
        $date = $request->has('date')
            ? Carbon::parse($request->date)
            : Carbon::today();
        
        // Calculate previous and next day dates
        $prevDay = $date->copy()->subDay();
        $nextDay = $date->copy()->addDay();
        
        // Get all schedules for this day
        $schedules = Schedule::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->whereDate('starts_at', $date)
            ->orderBy('starts_at')
            ->get();
        
        return view('student.schedule-daily', compact(
            'student',
            'schedules',
            'date',
            'prevDay',
            'nextDay'
        ));
    }
}
