<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function weekly(Request $request)
    {
        $parent = auth()->user();
        $children = $parent->children;
        $userTimezone = $parent->getUserTimezone();

        // Get selected child or all children
        $selectedChildId = $request->get('child_id', 'all');

        // Get current week or requested week
        $weekStart = $request->get('week_start')
            ? Carbon::parse($request->get('week_start'))
            : Carbon::now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        // Build query
        $query = Schedule::with(['student', 'teacher', 'course']);

        if ($selectedChildId === 'all') {
            $query->whereIn('student_id', $children->pluck('id'));
        } else {
            // Verify child belongs to parent
            $parent->children()->findOrFail($selectedChildId);
            $query->where('student_id', $selectedChildId);
        }

        $schedules = $query->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->orderBy('starts_at')
            ->get();

        // Group schedules by day
        $schedulesByDay = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $schedulesByDay[$day->format('Y-m-d')] = [
                'date' => $day,
                'schedules' => $schedules->filter(function ($schedule) use ($day) {
                    return Carbon::parse($schedule->starts_at)->isSameDay($day);
                })->values()
            ];
        }

        return view('parent.schedule.weekly', compact(
            'parent',
            'children',
            'schedulesByDay',
            'weekStart',
            'weekEnd',
            'selectedChildId',
            'userTimezone'
        ));
    }

    public function daily(Request $request)
    {
        $parent = auth()->user();
        $children = $parent->children;
        $userTimezone = $parent->getUserTimezone();

        // Get selected child or all children
        $selectedChildId = $request->get('child_id', 'all');

        // Get selected date or today
        $selectedDate = $request->get('date')
            ? Carbon::parse($request->get('date'))
            : Carbon::today();

        // Build query
        $query = Schedule::with(['student', 'teacher', 'course', 'attendance']);

        if ($selectedChildId === 'all') {
            $query->whereIn('student_id', $children->pluck('id'));
        } else {
            // Verify child belongs to parent
            $parent->children()->findOrFail($selectedChildId);
            $query->where('student_id', $selectedChildId);
        }

        $schedules = $query->whereDate('starts_at', $selectedDate)
            ->orderBy('starts_at')
            ->get();

        return view('parent.schedule.daily', compact(
            'parent',
            'children',
            'schedules',
            'selectedDate',
            'selectedChildId',
            'userTimezone'
        ));
    }
}
