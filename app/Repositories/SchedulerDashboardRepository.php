<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Schedule;
use App\Models\EnrollmentPayment;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SchedulerDashboardRepository
{
    public function getTodaySchedules(): Collection
    {
        return Schedule::with(['student', 'teacher', 'course'])
            ->whereDate('starts_at', Carbon::today())
            ->orderBy('starts_at')
            ->get();
    }

    public function getUpcomingSchedules(int $limit = 10): Collection
    {
        return Schedule::with(['student', 'teacher', 'course'])
            ->where('starts_at', '>', Carbon::now())
            ->whereDate('starts_at', '!=', Carbon::today())
            ->orderBy('starts_at')
            ->take($limit)
            ->get();
    }

    public function getStudentsCount(): int
    {
        return User::role('Student')->count();
    }

    public function getTeachersCount(): int
    {
        return User::role('Teacher')->count();
    }

    public function getPendingAttendanceCount(): int
    {
        return Schedule::where('starts_at', '<', Carbon::now())
            ->where('status', 'scheduled')
            ->doesntHave('attendance')
            ->count();
    }

    public function getMonthlyRevenue(): float
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return EnrollmentPayment::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount') ?? 0.0;
    }

    public function getReportsCount(): int
    {
        return Report::count();
    }

    public function searchUsers(string $search, array $roles = ['Student', 'Teacher'], int $limit = 10): Collection
    {
        return User::where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->whereIn('role', $roles)
            ->limit($limit)
            ->get();
    }

    public function getStudentsQuery(Request $request): Builder
    {
        $query = User::role('Student');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function getTeachersQuery(Request $request): Builder
    {
        $query = User::role('Teacher');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
