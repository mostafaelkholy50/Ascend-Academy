<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;

use App\Traits\HasRegionalAccess;

class DashboardController extends Controller
{
    use HasRegionalAccess;

    public function index()
    {
        $user = auth()->user();
        
        $allowedCountries = $this->getAllowedCountries($user);


        // Base queries
        $studentsQuery = User::roleStudent();
        $teachersQuery = User::roleTeacher();
        $schedulesQuery = Schedule::query();

        // Apply country filtering if restricted
        $studentsQuery = $this->applyRegionalFilter($studentsQuery, 'country');
        $schedulesQuery = $this->applyRegionalFilter($schedulesQuery, 'student.country');
        $paymentsQuery = $this->applyRegionalFilter(\App\Models\EnrollmentPayment::query(), 'enrollment.student.country');


        $stats = [
            'total_students' => $studentsQuery->count(),
            'total_teachers' => $teachersQuery->count(),
            'today_sessions' => $schedulesQuery->whereDate('starts_at', today())->count(),
            'pending_payments' => $paymentsQuery->whereMonth('month', now()->month)
                ->whereYear('month', now()->year)
                ->where('payment_status', 'unpaid')
                ->count(),
        ];

        // Monthly revenue for the last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $monthlyRevenue[] = [
                'month' => $month->format('M'),
                'revenue' => (clone $paymentsQuery)
                    ->where('payment_status', 'paid')
                    ->whereMonth('month', $month->month)
                    ->whereYear('month', $month->year)
                    ->sum('amount'),
            ];
        }

        $recentSchedules = $schedulesQuery->with(['student', 'teacher', 'course'])
            ->orderBy('starts_at', 'desc')
            ->limit(10)
            ->get();

        return view('accountant.dashboard', compact('stats', 'recentSchedules', 'allowedCountries', 'monthlyRevenue'));
    }
}
