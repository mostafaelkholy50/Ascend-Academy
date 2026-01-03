<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Inquiry;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get user counts by role in a single query
        $userCounts = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');
        
        $totalUsers = $userCounts->sum();
        $totalStudents = $userCounts->get('Student', 0);
        $totalTeachers = $userCounts->get('Teacher', 0);
        $totalParents = $userCounts->get('Parent', 0);

        // New inquiries count
        $newInquiries = Inquiry::where('status', 'pending')->count();

        // Recent enrollments with relationships
        $recentEnrollments = Enrollment::with(['student', 'course'])
            ->latest()
            ->take(5)
            ->get();

        // Recent inquiries
        $recentInquiries = Inquiry::latest()
            ->take(4)
            ->get();

        // Calculate revenue efficiently with single query for both months
        $currentMonth = now();
        $lastMonth = now()->subMonth();
        
        $revenues = EnrollmentPayment::selectRaw('
                SUM(CASE WHEN MONTH(paid_at) = ? AND YEAR(paid_at) = ? THEN amount ELSE 0 END) as current_month,
                SUM(CASE WHEN MONTH(paid_at) = ? AND YEAR(paid_at) = ? THEN amount ELSE 0 END) as last_month
            ', [
                $currentMonth->month, $currentMonth->year,
                $lastMonth->month, $lastMonth->year
            ])
            ->where('payment_status', 'paid')
            ->first();

        $monthlyRevenue = $revenues->current_month ?? 0;
        $lastMonthRevenue = $revenues->last_month ?? 0;

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : 0;

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalTeachers',
            'totalParents',
            'newInquiries',
            'recentEnrollments',
            'recentInquiries',
            'monthlyRevenue',
            'revenueGrowth'
        ));
    }

}
