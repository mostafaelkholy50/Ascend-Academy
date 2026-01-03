<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HoursController extends Controller
{
    /**
     * Display teacher's hours and earnings for selected month
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        
        // Get selected month and year, default to current month
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        
        // Create date for the selected month
        $date = Carbon::create($selectedYear, $selectedMonth, 1);
        
        // Get all attendances where both teacher and student were present
        $query = Attendance::with(['schedule.student', 'schedule.course'])
            ->where('teacher_id', $teacher->id)
            ->where('teacher_present', true)
            ->where('student_present', true)
            ->whereHas('schedule', function($q) use ($selectedYear, $selectedMonth) {
                $q->whereYear('starts_at', $selectedYear)
                  ->whereMonth('starts_at', $selectedMonth);
            })
            ->orderBy('created_at', 'desc');
        
        // Paginate the results
        $attendances = $query->paginate(15)->appends([
            'month' => $selectedMonth,
            'year' => $selectedYear
        ]);
        
        // Calculate total hours and earnings
        $totalHours = 0;
        $scheduleDetails = [];
        
        foreach ($query->get() as $attendance) {
            $schedule = $attendance->schedule;
            $hours = $schedule->getDurationInHours();
            $totalHours += $hours;
            
            $scheduleDetails[] = [
                'date' => $schedule->starts_at->format('Y-m-d'),
                'start_time' => $schedule->starts_at->format('g:i A'),
                'end_time' => $schedule->ends_at->format('g:i A'),
                'hours' => $hours,
                'student' => $schedule->student->name,
                'course' => $schedule->course->title ?? 'N/A',
            ];
        }
        
        // Get hourly rate
        $hourlyRate = $teacher->hourly_rate ?? 0;
        
        // Calculate total earnings
        $totalEarnings = $totalHours * $hourlyRate;
        
        // Generate month/year options for selector
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create(null, $i, 1)->format('F');
        }
        
        $years = range(now()->year - 2, now()->year + 1);
        
        return view('teacher.hours.index', compact(
            'attendances',
            'totalHours',
            'hourlyRate',
            'totalEarnings',
            'selectedMonth',
            'selectedYear',
            'months',
            'years',
            'date'
        ));
    }
}
