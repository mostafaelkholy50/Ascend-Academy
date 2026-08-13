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
        $attendancesList = $allAttendances->with('schedule')->get();
        
        $totalHours = $attendancesList->sum(function($attendance) {
            if (!$attendance->schedule) return 0;
            $duration = $attendance->schedule->getDurationInHours();
            
            if (!$attendance->student_present && $attendance->remark === 'Waited Half Time') {
                return $duration / 2;
            }
            return $duration;
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

    public function getPdfData(User $teacher, int $month, int $year): array
    {
        $date = Carbon::create($year, $month, 1);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get all attendances for the teacher in the given month, regardless of present status
        $allAttendances = \App\Models\Attendance::with(['schedule.student', 'schedule.course'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('schedule', function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('starts_at', [$startOfMonth, $endOfMonth]);
            })
            ->get();

        $stats = [
            'total_attendances' => 0,
            'teacher_absences' => 0,
            'student_absences' => 0,
            'waited_half_time' => 0,
        ];

        $teacherAbsencesList = [];
        $studentAbsencesList = [];
        $studentStats = [];
        $totalHours = 0;

        foreach ($allAttendances as $attendance) {
            $schedule = $attendance->schedule;
            if (!$schedule) continue;

            $student = $schedule->student;
            $studentId = $student ? $student->id : 'unknown';
            $studentName = $student ? $student->name : 'Unknown Student';

            if (!isset($studentStats[$studentId])) {
                $studentStats[$studentId] = [
                    'name' => $studentName,
                    'total_classes' => 0,
                    'attended' => 0,
                    'missed' => 0,
                    'waited_half_time' => 0,
                ];
            }

            $studentStats[$studentId]['total_classes']++;

            if (!$attendance->teacher_present) {
                $stats['teacher_absences']++;
                $teacherAbsencesList[] = [
                    'date' => $schedule->starts_at->format('Y-m-d'),
                    'time' => $schedule->starts_at->format('H:i'),
                    'remark' => $attendance->remark ?: 'No remark',
                ];
            } else {
                $stats['total_attendances']++;
                $duration = $schedule->getDurationInHours();

                if (!$attendance->student_present) {
                    $stats['student_absences']++;
                    $studentStats[$studentId]['missed']++;
                    
                    if ($attendance->remark === 'Waited Half Time') {
                        $stats['waited_half_time']++;
                        $studentStats[$studentId]['waited_half_time']++;
                        $totalHours += ($duration / 2);
                    }
                    
                    $studentAbsencesList[] = [
                        'student' => $studentName,
                        'date' => $schedule->starts_at->format('Y-m-d'),
                        'time' => $schedule->starts_at->format('H:i'),
                        'remark' => $attendance->remark ?: 'No remark',
                    ];
                } else {
                    $studentStats[$studentId]['attended']++;
                    $totalHours += $duration;
                }
            }
        }

        // Add evaluation bonus if earned
        $teacherHour = \App\Models\TeacherHour::where('teacher_id', $teacher->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
            
        $bonusHours = 0;
        if ($teacherHour && str_contains($teacherHour->notes ?? '', 'Evaluation Bonus')) {
            $totalHours += 0.5;
            $bonusHours = 0.5;
        }

        $hourlyRate = $teacher->hourly_rate ?? 0;
        $totalEarnings = $totalHours * $hourlyRate;

        return compact(
            'teacher',
            'date',
            'stats',
            'studentStats',
            'teacherAbsencesList',
            'studentAbsencesList',
            'totalHours',
            'bonusHours',
            'hourlyRate',
            'totalEarnings'
        );
    }
}
