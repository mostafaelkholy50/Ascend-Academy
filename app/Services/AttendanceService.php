<?php

namespace App\Services;

use App\Repositories\AttendanceRepository;
use App\Filters\AttendanceFilter;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceService
{
    protected $repository;
    protected $filter;
    protected $scheduleService;

    public function __construct(AttendanceRepository $repository, AttendanceFilter $filter, \App\Services\ScheduleService $scheduleService)
    {
        $this->repository = $repository;
        $this->filter = $filter;
        $this->scheduleService = $scheduleService;
    }

    public function getAttendances(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getAttendancesQuery();
        $query = $this->filter->apply($query, $request);

        $attendances = $query->paginate($perPage);
        $stats = $this->repository->getStats();

        // Get filter options
        $students = User::role('Student')->orderBy('name')->get();
        $teachers = User::role('Teacher')->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        return array_merge([
            'attendances' => $attendances,
            'students' => $students,
            'teachers' => $teachers,
            'courses' => $courses,
        ], $stats);
    }

    public function getAttendanceDetails(Attendance $attendance)
    {
        return $this->repository->getAttendanceWithRelations($attendance);
    }

    public function getCreateData(Request $request)
    {
        $view = $request->get('view', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        $currentDate = Carbon::parse($date);
        
        if ($view === 'weekly') {
            $weekStart = $currentDate->copy()->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            
            $schedules = Schedule::whereBetween('starts_at', [$weekStart, $weekEnd])
                ->with(['student', 'teacher', 'course', 'attendance'])
                ->orderBy('starts_at')
                ->get()
                ->groupBy(function($item) {
                    return $item->starts_at->format('Y-m-d');
                });
                
            $stats = [
                'total' => Schedule::whereBetween('starts_at', [$weekStart, $weekEnd])->count(),
                'completed' => Schedule::whereBetween('starts_at', [$weekStart, $weekEnd])->where('status', 'completed')->count(),
            ];

            return compact('schedules', 'weekStart', 'weekEnd', 'stats', 'date', 'view');
        }

        // Daily view
        $schedules = Schedule::whereDate('starts_at', $date)
            ->with(['student', 'teacher', 'course', 'attendance'])
            ->orderBy('starts_at')
            ->get();
            
        $stats = [
            'total' => $schedules->count(),
            'completed' => $schedules->where('status', 'completed')->count(),
            'pending' => $schedules->where('status', 'scheduled')->count(),
        ];

        return compact('schedules', 'date', 'stats', 'view');
    }

    public function storeAttendance(array $data)
    {
        $schedule = Schedule::findOrFail($data['schedule_id']);

        $attendance = $this->repository->updateOrCreate(
            ['schedule_id' => $schedule->id],
            [
                'student_id' => $schedule->student_id,
                'teacher_id' => $schedule->teacher_id,
                'student_present' => $data['student_present'],
                'teacher_present' => $data['teacher_present'],
                'student_report' => $data['student_report'] ?? null,
                'teacher_report' => $data['teacher_report'] ?? null,
                'remark' => $data['remark'] ?? null,
            ]
        );

        if ($attendance->student_present && $attendance->teacher_present) {
            $schedule->update(['status' => 'completed']);
            
            // Auto-renewal logic
            $this->handleAutoRenewal($schedule);
        }

        return $attendance;
    }

    protected function handleAutoRenewal(Schedule $schedule)
    {
        $enrollment = $schedule->enrollment;
        if (!$enrollment) {
            return;
        }

        $futureSchedules = Schedule::where('enrollment_id', $enrollment->id)
            ->where('starts_at', '>', $schedule->starts_at)
            ->where('status', 'scheduled')
            ->count();
        
        // If 0 or 1 future sessions left, try to generate the next month
        if ($futureSchedules <= 1) {
            $lastSchedule = Schedule::where('enrollment_id', $enrollment->id)
                ->latest('starts_at')
                ->first();
            
            $nextMonthStart = $lastSchedule ? $lastSchedule->starts_at->copy()->addDay() : now();
            
            // Check if there's a paid payment for the next month that isn't scheduled
            $nextPayment = \App\Models\EnrollmentPayment::where('enrollment_id', $enrollment->id)
                ->where('payment_status', 'paid')
                ->where('month', '>=', $nextMonthStart->format('Y-m-01'))
                ->whereDoesntHave('enrollment.schedules', function($query) use ($nextMonthStart) {
                     $query->whereMonth('starts_at', $nextMonthStart->month)
                           ->whereYear('starts_at', $nextMonthStart->year);
                })
                ->first();
                
            if ($nextPayment) {
                $result = $this->scheduleService->generateMonthlySchedules(
                    $enrollment, 
                    $nextPayment->month->format('Y-m-d'), 
                    $schedule->teacher_id
                );

                if (!$result['success']) {
                    \Log::error("Auto-renewal failed for Enrollment #{$enrollment->id}: {$result['message']}");
                }
            }
        }
    }
}
