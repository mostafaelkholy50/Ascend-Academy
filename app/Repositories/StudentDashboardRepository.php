<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Resource;
use App\Models\EnrollmentPayment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class StudentDashboardRepository
{
    public function getEnrollments(User $student): Collection
    {
        return $student->enrollments()
            ->with('course')
            ->where('status', 'active')
            ->get();
    }

    public function getCourseProgressStats(User $student): Collection
    {
        return Schedule::where('student_id', $student->id)
            ->selectRaw('course_id, COUNT(*) as total, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');
    }

    public function getTodaySchedules(User $student): Collection
    {
        return Schedule::where('student_id', $student->id)
            ->whereDate('starts_at', Carbon::today())
            ->where('status', 'scheduled')
            ->with(['teacher', 'course'])
            ->orderBy('starts_at')
            ->get();
    }

    public function getWeekSchedules(User $student): Collection
    {
        return Schedule::where('student_id', $student->id)
            ->whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->with(['teacher', 'course'])
            ->orderBy('starts_at')
            ->get();
    }

    public function getRecentReports(User $student, int $limit = 5): Collection
    {
        return Report::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest('report_date')
            ->take($limit)
            ->get();
    }

    public function getRecentResources(User $student, int $limit = 5): Collection
    {
        return Resource::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getUnpaidPayments(User $student, int $month, int $year): Collection
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        return EnrollmentPayment::whereHas('enrollment', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', 'active');
            })
            ->whereBetween('month', [$startOfMonth, $endOfMonth])
            ->where('payment_status', '!=', 'paid')
            ->with(['enrollment.course'])
            ->get();
    }

    public function getCompletedSchedulesCount(User $student): int
    {
        return Schedule::where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();
    }

    public function getTotalSchedulesCount(User $student): int
    {
        return Schedule::where('student_id', $student->id)
            ->count();
    }

    public function getCompletedThisWeekCount(User $student): int
    {
        return Schedule::where('student_id', $student->id)
            ->where('status', 'completed')
            ->whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
    }
}
