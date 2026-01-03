<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\EnrollmentPayment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $parent = auth()->user();
        
        // Get all children with their enrollments (eager load to avoid N+1)
        $children = $parent->children()
            ->with(['enrollments.course'])
            ->get();
            
        $childrenIds = $children->pluck('id');
        
        // Get all schedules for all children at once
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $allSchedules = Schedule::with(['student', 'teacher', 'course'])
            ->whereIn('student_id', $childrenIds)
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->get();
            
        // Get today's schedules from the collection or DB if needed (for wider range)
        // Since we need today specifically and it might be outside the week view logic depending on implementation,
        // but usually today is within the week. Let's fetch specifically for today to be safe if week logic changes,
        // or better, filter from a larger set if we fetched enough.
        // Actually, let's fetch a broader range if needed, or just specific queries optimized.
        
        // Let's fetch today's schedules specifically for the "Today's Schedule" section
        $todaySchedules = Schedule::with(['student', 'teacher', 'course'])
            ->whereIn('student_id', $childrenIds)
            ->whereDate('starts_at', today())
            ->where('status', 'scheduled')
            ->orderBy('starts_at')
            ->get();
            
        // Get upcoming schedules (next 7 days)
        $upcomingSchedules = Schedule::with(['student', 'teacher', 'course'])
            ->whereIn('student_id', $childrenIds)
            ->whereBetween('starts_at', [
                Carbon::tomorrow(),
                Carbon::now()->addDays(7)
            ])
            ->orderBy('starts_at')
            ->take(10)
            ->get();

        // Get attendance records for the week for all children
        $weekAttendances = Attendance::whereIn('student_id', $childrenIds)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->get();
            
        // Get latest reports for all children
        // We can use a subquery or just fetch recent reports and filter
        $latestReports = Report::with(['teacher', 'course'])
            ->whereIn('student_id', $childrenIds)
            ->latest('report_date')
            ->get()
            ->unique('student_id');

        // Calculate statistics for each child in memory
        foreach ($children as $child) {
            // Active courses
            $child->active_courses = $child->enrollments
                ->where('status', 'active')
                ->count();
            
            // Today's classes
            $child->today_classes = $todaySchedules->where('student_id', $child->id)->count();
            
            // This week's attendance rate
            $childWeekSchedules = $allSchedules->where('student_id', $child->id)->count();
            
            $childPresentCount = $weekAttendances->where('student_id', $child->id)
                ->where('student_present', true)
                ->count();
            
            $child->attendance_rate = $childWeekSchedules > 0 
                ? round(($childPresentCount / $childWeekSchedules) * 100) 
                : 0;
            
            // Recent report
            $child->latest_report = $latestReports->where('student_id', $child->id)->first();
        }
        
        // Overall statistics
        $stats = [
            'total_children' => $children->count(),
            'total_active_courses' => $children->sum('active_courses'),
            'today_total_classes' => $todaySchedules->count(),
            'pending_reports' => Report::whereIn('student_id', $childrenIds)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count(),
        ];

        // Check for unpaid payments for the current month for all children
        $unpaidPayments = EnrollmentPayment::whereHas('enrollment', function($q) use ($childrenIds) {
                $q->whereIn('student_id', $childrenIds)
                  ->where('status', 'active');
            })
            ->whereMonth('month', now()->month)
            ->whereYear('month', now()->year)
            ->where('payment_status', '!=', 'paid')
            ->with(['enrollment.student', 'enrollment.course'])
            ->get();
        
        return view('parent.dashboard', compact(
            'parent',
            'children',
            'todaySchedules',
            'upcomingSchedules',
            'stats',
            'unpaidPayments'
        ));
    }
}
