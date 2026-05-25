<?php

namespace App\Services;

use App\Repositories\TeacherHourRepository;
use Illuminate\Http\Request;

class TeacherHourService
{
    protected $repository;

    public function __construct(TeacherHourRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPayrollData(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        // Calculate hours for all active teachers for this month/year
        $this->calculateAndSaveHours($month, $year);

        $teachers = $this->repository->getTeachers($request);
        $teacherIds = $teachers->pluck('id');
        
        $payrollRecords = $this->repository->getPayrollRecords($teacherIds, $month, $year);

        return [
            'teachers' => $teachers,
            'payrollRecords' => $payrollRecords,
            'month' => $month,
            'year' => $year,
        ];
    }

    public function calculateAndSaveHours(int $month, int $year)
    {
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();

        // Get aggregated hours for all teachers using PHP to support multiple DBs (SQLite/MySQL)
        $attendances = \App\Models\Attendance::with('schedule')
            ->where('teacher_present', true)
            ->where('student_present', true)
            ->whereHas('schedule', function($query) use ($startDate, $endDate) {
                $query->whereBetween('starts_at', [$startDate, $endDate]);
            })
            ->get();

        $hoursData = $attendances->groupBy('schedule.teacher_id')->map(function ($teacherAttendances) {
            return $teacherAttendances->sum(function ($attendance) {
                return $attendance->schedule ? $attendance->schedule->getDurationInHours() : 0;
            });
        });

        // Get all active teachers
        $teachers = \App\Models\User::roleTeacher()->where('active', true)->get();

        foreach ($teachers as $teacher) {
            $workedHours = isset($hoursData[$teacher->id]) ? round($hoursData[$teacher->id], 2) : 0;

            $this->repository->updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'total_hours' => $workedHours,
                    'total_salary' => $workedHours * ($teacher->hourly_rate ?? 0),
                ]
            );
        }
    }

    public function markAsPaid(int $teacherId, int $month, int $year)
    {
        return $this->repository->updateOrCreate(
            ['teacher_id' => $teacherId, 'month' => $month, 'year' => $year],
            ['is_paid' => true, 'paid_at' => now()]
        );
    }

    public function markAsUnpaid(int $teacherId, int $month, int $year)
    {
        return $this->repository->updateOrCreate(
            ['teacher_id' => $teacherId, 'month' => $month, 'year' => $year],
            ['is_paid' => false, 'paid_at' => null]
        );
    }
}
