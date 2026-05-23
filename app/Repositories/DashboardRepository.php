<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Course;
use App\Models\Inquiry;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\Attendance;
use App\Models\StudentEvaluation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getUserCountsByRole(): Collection
    {
        return User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');
    }

    public function getPendingInquiriesCount(): int
    {
        return Inquiry::where('status', 'pending')->count();
    }

    public function getRecentEnrollments(int $limit = 5): Collection
    {
        return Enrollment::with(['student', 'course'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getRecentInquiries(int $limit = 4): Collection
    {
        return Inquiry::latest()
            ->take($limit)
            ->get();
    }

    public function getRevenueForMonth(int $month, int $year): float
    {
        return EnrollmentPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $month)
            ->whereYear('paid_at', $year)
            ->sum('amount') ?? 0.0;
    }

    public function getMonthlyEnrollmentTrends(int $months = 6): Collection
    {
        $driver = DB::connection()->getDriverName();
        $monthRaw = $driver === 'sqlite' ? "strftime('%m', created_at)" : "MONTH(created_at)";
        $yearRaw = $driver === 'sqlite' ? "strftime('%Y', created_at)" : "YEAR(created_at)";

        return Enrollment::selectRaw("COUNT(*) as count, $monthRaw as month, $yearRaw as year")
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy(DB::raw($yearRaw), DB::raw($monthRaw))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getMonthlyRevenueTrends(int $months = 6): Collection
    {
        $driver = DB::connection()->getDriverName();
        $monthRaw = $driver === 'sqlite' ? "strftime('%m', paid_at)" : "MONTH(paid_at)";
        $yearRaw = $driver === 'sqlite' ? "strftime('%Y', paid_at)" : "YEAR(paid_at)";

        return EnrollmentPayment::where('payment_status', 'paid')
            ->selectRaw("SUM(amount) as total, $monthRaw as month, $yearRaw as year")
            ->where('paid_at', '>=', now()->subMonths($months))
            ->groupBy(DB::raw($yearRaw), DB::raw($monthRaw))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getAttendanceSummary(): array
    {
        $stats = Attendance::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN student_present = 1 THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN student_present = 0 THEN 1 ELSE 0 END) as absent
        ')->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'present' => (int) ($stats->present ?? 0),
            'absent' => (int) ($stats->absent ?? 0),
            'late' => 0, // Not tracked in current schema
        ];
    }

    public function getEvaluationsSummary(): array
    {
        $stats = StudentEvaluation::selectRaw('COUNT(*) as total, AVG(total_score) as average_score')->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'average_score' => (float) ($stats->average_score ?? 0),
            'recent' => StudentEvaluation::with(['student', 'teacher'])->latest()->take(5)->get()
        ];
    }

    public function getInquiryConversionRate(): float
    {
        $stats = Inquiry::selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "converted" THEN 1 ELSE 0 END) as converted')->first();
        
        $totalInquiries = (int) ($stats->total ?? 0);
        if ($totalInquiries === 0) return 0.0;
        
        $converted = (int) ($stats->converted ?? 0);
        return ($converted / $totalInquiries) * 100;
    }

    public function getTopCoursesPerformance(int $limit = 5): Collection
    {
        return Course::select('courses.id', 'courses.title')
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take($limit)
            ->get();
    }

    public function getTeacherPerformanceRanking(int $limit = 5): Collection
    {
        return User::role('Teacher')
            ->select('users.id', 'users.name')
            ->withAvg('teacherEvaluations as avg_score', 'total_score')
            ->orderBy('avg_score', 'desc')
            ->take($limit)
            ->get();
    }

    public function getMonthlyComparisonData(): array
    {
        $now = now();
        $lastMonth = now()->subMonth();

        return [
            'revenue' => [
                'current' => EnrollmentPayment::paid()->whereMonth('paid_at', $now->month)->whereYear('paid_at', $now->year)->sum('amount'),
                'previous' => EnrollmentPayment::paid()->whereMonth('paid_at', $lastMonth->month)->whereYear('paid_at', $lastMonth->year)->sum('amount'),
            ],
            'enrollments' => [
                'current' => Enrollment::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count(),
                'previous' => Enrollment::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count(),
            ]
        ];
    }
}
