<?php

namespace App\Services;

use App\Repositories\TeacherHoursRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherHoursService
{
    protected $repository;

    public function __construct(TeacherHoursRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getHoursData(User $teacher, Request $request): array
    {
        // Get selected month and year, default to current month
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        
        // Create date for the selected month
        $date = Carbon::create($selectedYear, $selectedMonth, 1);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        $query = $this->repository->getAttendancesQueryForMonth($teacher, $startOfMonth, $endOfMonth);
        
        // Paginate the results
        $attendances = clone $query; // clone so we don't modify the original query
        $attendances = $attendances->paginate(15)->appends([
            'month' => $selectedMonth,
            'year' => $selectedYear
        ]);
        
        // Calculate total hours efficiently
        // We only need the schedule duration for calculation
        $allAttendances = clone $query;
        $allSchedules = $allAttendances->with('schedule')->get()->pluck('schedule');
        
        $totalHours = $allSchedules->sum(function($schedule) {
            return $schedule ? $schedule->getDurationInHours() : 0;
        });
        
        // Add evaluation bonus if earned
        $teacherHour = \App\Models\TeacherHour::where('teacher_id', $teacher->id)
            ->where('year', $selectedYear)
            ->where('month', $selectedMonth)
            ->first();
            
        $bonusHours = 0;
        if ($teacherHour && str_contains($teacherHour->notes ?? '', 'Evaluation Bonus')) {
            $totalHours += 0.5;
            $bonusHours = 0.5;
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

        return compact(
            'attendances',
            'totalHours',
            'bonusHours',
            'hourlyRate',
            'totalEarnings',
            'selectedMonth',
            'selectedYear',
            'months',
            'years',
            'date'
        );
    }
}
