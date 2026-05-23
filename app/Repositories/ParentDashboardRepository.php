<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Attendance;
use App\Models\EnrollmentPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Filters\ScheduleFilter;

class ParentDashboardRepository
{
    protected $scheduleFilter;

    public function __construct(ScheduleFilter $scheduleFilter)
    {
        $this->scheduleFilter = $scheduleFilter;
    }

    public function getChildren(User $parent): Collection
    {
        return $parent->children()
            ->with(['enrollments.course'])
            ->get();
    }

    public function getSchedulesForChildren(Collection $childrenIds, Carbon $start, Carbon $end, Request $request): Collection
    {
        $query = Schedule::with(['student', 'teacher', 'course'])
            ->whereIn('student_id', $childrenIds)
            ->whereBetween('starts_at', [$start, $end]);

        $this->scheduleFilter->apply($query, $request);

        return $query->get();
    }

    public function getTodaySchedules(Collection $childrenIds, Request $request): Collection
    {
        $query = Schedule::with(['student', 'teacher', 'course'])
            ->scheduled()
            ->today()
            ->whereIn('student_id', $childrenIds);

        $this->scheduleFilter->apply($query, $request);

        return $query->orderBy('starts_at')->get();
    }

    public function getUpcomingSchedules(Collection $childrenIds, Request $request, int $days = 7, int $limit = 10): Collection
    {
        $query = Schedule::with(['student', 'teacher', 'course'])
            ->scheduled()
            ->whereIn('student_id', $childrenIds)
            ->whereBetween('starts_at', [
                Carbon::tomorrow(),
                Carbon::now()->addDays($days)
            ]);

        $this->scheduleFilter->apply($query, $request);

        return $query->orderBy('starts_at')->take($limit)->get();
    }

    public function getAttendancesForChildren(Collection $childrenIds, Carbon $start, Carbon $end): Collection
    {
        return Attendance::whereIn('student_id', $childrenIds)
            ->whereBetween('created_at', [$start, $end])
            ->get();
    }

    public function getLatestReports(Collection $childrenIds): Collection
    {
        // تحسين الأداء: جلب أحدث تقرير لكل طالب مباشرة من قاعدة البيانات
        // بدلاً من جلب جميع التقارير وفلترتها في الذاكرة
        $subQuery = Report::select('student_id')
            ->selectRaw('MAX(report_date) as max_date')
            ->whereIn('student_id', $childrenIds)
            ->groupBy('student_id');

        return Report::with(['teacher', 'course'])
            ->joinSub($subQuery, 'latest_reports', function ($join) {
                $join->on('reports.student_id', '=', 'latest_reports.student_id')
                     ->on('reports.report_date', '=', 'latest_reports.max_date');
            })
            ->get();
    }

    public function getUnpaidPayments(Collection $childrenIds, int $month, int $year): Collection
    {
        return EnrollmentPayment::whereHas('enrollment', function($q) use ($childrenIds) {
                $q->whereIn('student_id', $childrenIds)
                  ->where('status', 'active');
            })
            ->whereMonth('month', $month)
            ->whereYear('month', $year)
            ->where('payment_status', '!=', 'paid')
            ->with(['enrollment.student', 'enrollment.course'])
            ->get();
    }

    public function getPendingReportsCount(Collection $childrenIds, int $days = 7): int
    {
        return Report::whereIn('student_id', $childrenIds)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->count();
    }
}
