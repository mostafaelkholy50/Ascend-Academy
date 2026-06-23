<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use App\Filters\ScheduleFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleService
{
    protected $repository;
    protected $filter;

    public function __construct(ScheduleRepository $repository, ScheduleFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getCalendarData(Request $request)
    {
        $weekStart = $request->filled('week') 
            ? Carbon::parse($request->week)->startOfWeek() 
            : Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $schedules = $this->repository->getSchedulesQuery()
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->orderBy('starts_at')
            ->get();

        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $weekDays[] = [
                'date' => $day,
                'schedules' => $schedules->filter(function($schedule) use ($day) {
                    return $schedule->starts_at->isSameDay($day);
                })->sortBy('starts_at')->values()
            ];
        }

        $stats = $this->getStats();

        return compact('stats', 'weekDays', 'weekStart', 'weekEnd');
    }

    public function getEnrollmentGroupedData(Request $request, int $perPage = 15)
    {
        $query = Enrollment::with(['student', 'course', 'schedules.teacher'])
            ->whereHas('schedules');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhereHas('course', function($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('teacher_id')) {
            $query->whereHas('schedules', function($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            });
        }

        $enrollments = $query->latest()->paginate($perPage);
        $stats = $this->getStats();

        return compact('enrollments', 'stats');
    }

    protected function getStats()
    {
        return [
            'total' => Schedule::count(),
            'upcoming' => Schedule::where('starts_at', '>', now())->where('status', 'scheduled')->count(),
            'completed' => Schedule::where('status', 'completed')->count(),
            'cancelled' => Schedule::where('status', 'cancelled')->count(),
        ];
    }

    public function storeSchedule(array $data)
    {
        $createdCount = 0;
        $firstSchedule = null;

        \Illuminate\Support\Facades\DB::transaction(function () use (&$createdCount, &$firstSchedule, $data) {
            $enrollment = Enrollment::where('student_id', $data['student_id'])
                ->where('course_id', $data['course_id'])
                ->where('status', 'active')
                ->first();

            $daysPerWeek = count($data['days']);
            $sessionDuration = (int) $data['duration_minutes'];
            $currency = $data['currency'] ?? 'CAD';
            $adminPrice = $data['admin_price'] ?? null;

            if ($enrollment) {
                $targetMonth = Carbon::parse($data['start_date'])->startOfMonth();
                $existingSchedulesInMonth = Schedule::where('enrollment_id', $enrollment->id)
                    ->whereYear('starts_at', $targetMonth->year)
                    ->whereMonth('starts_at', $targetMonth->month)
                    ->exists();

                if ($existingSchedulesInMonth) {
                    throw new \Exception('This student already has an enrollment and schedules for this month.');
                }

                $enrollment->update([
                    'days_per_week' => $daysPerWeek,
                    'session_duration' => $sessionDuration,
                    'admin_price' => $adminPrice ?? $enrollment->admin_price,
                    'currency' => $currency ?? $enrollment->currency,
                ]);
            }

            if (!$enrollment) {
                // Use the submitted price if provided, otherwise suggest one for the derived schedule shape.
                $adminPrice = $adminPrice
                    ?? \App\Models\PricingTier::getSuggestedPrice($daysPerWeek, $sessionDuration, $currency)
                    ?? 0.00;

                $enrollment = Enrollment::create([
                    'student_id' => $data['student_id'],
                    'course_id' => $data['course_id'],
                    'start_date' => Carbon::parse($data['start_date']),
                    'status' => 'active',
                    'days_per_week' => $daysPerWeek,
                    'session_duration' => $sessionDuration,
                    'admin_price' => $adminPrice,
                    'currency' => $currency,
                ]);

                // Automatically create the first month's payment record
                \App\Models\EnrollmentPayment::firstOrCreate([
                    'enrollment_id' => $enrollment->id,
                    'month' => Carbon::parse($data['start_date'])->startOfMonth(),
                ], [
                    'amount' => $enrollment->admin_price,
                    'currency' => $enrollment->currency,
                    'payment_status' => 'unpaid',
                ]);

                $this->generatePaymentsForEnrollment(
                    $enrollment,
                    Carbon::parse($data['start_date'])->startOfMonth()->addMonths(3)
                );
            }

            $days = $data['days'];
            $scheduleTimes = $data['schedule_times'];
            
            $schedulePattern = [];
            foreach ($days as $day) {
                $schedulePattern[$day] = $scheduleTimes[$day];
            }
            $enrollment->setSchedulePattern($schedulePattern);

            $monthStart = Carbon::parse($data['start_date']);
            $monthEnd = $monthStart->copy()->addMonth();
            
            if (!$enrollment->start_date || $monthStart->lt($enrollment->start_date)) {
                $enrollment->update(['start_date' => $monthStart]);
            }

            $durationMinutes = $sessionDuration;

            $sessionDates = [];
            $currentDate = $monthStart->copy();

            while ($currentDate->lte($monthEnd)) {
                $dayName = $currentDate->format('l');
                if (in_array($dayName, $days)) {
                    $sessionDates[] = [
                        'date' => $currentDate->copy(),
                        'time' => $scheduleTimes[$dayName],
                    ];
                }
                $currentDate->addDay();
            }

            if (empty($sessionDates)) {
                throw new \Exception('No sessions would be created with the selected days for this month.');
            }

            $conflicts = [];
            foreach ($sessionDates as $session) {
                $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $session['time']);
                $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                if ($conflict = Schedule::getTeacherConflict($data['teacher_id'], $startsAt, $endsAt)) {
                    $studentName = $conflict->student ? $conflict->student->name : 'Unknown Student';
                    $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Unknown Teacher';
                    $courseName = $conflict->course ? $conflict->course->title : 'Unknown Course';
                    $conflicts[] = "Teacher conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Teacher {$teacherName} is booked with Student {$studentName} for {$courseName})";
                }

                if ($conflict = Schedule::getStudentConflict($enrollment->student_id, $startsAt, $endsAt)) {
                    $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Unknown Teacher';
                    $studentName = $conflict->student ? $conflict->student->name : 'Unknown Student';
                    $courseName = $conflict->course ? $conflict->course->title : 'Unknown Course';
                    $conflicts[] = "Student conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Student {$studentName} is booked with Teacher {$teacherName} for {$courseName})";
                }
            }

            if (!empty($conflicts)) {
                throw new \Exception("Cannot create schedule due to conflicts:\n" . implode("\n", array_slice($conflicts, 0, 5)));
            }

            foreach ($sessionDates as $session) {
                $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $session['time']);
                $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                $schedule = $this->repository->create([
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'teacher_id' => $data['teacher_id'],
                    'course_id' => $enrollment->course_id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'zoom_link' => $data['zoom_link'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'status' => 'scheduled',
                ]);

                if (!$firstSchedule) {
                    $firstSchedule = $schedule;
                }

                $createdCount++;
            }
        });

        if ($firstSchedule) {
            try {
                $teacher = User::find($data['teacher_id']);
                $firstSchedule->load(['student.parents', 'teacher', 'course']);
                
                // Notify Teacher
                $teacher->notify(new \App\Notifications\ScheduleAssignedNotification(
                    $firstSchedule,
                    $createdCount > 1,
                    $createdCount
                ));

                // Notify Student
                $student = $firstSchedule->student;
                if ($student) {
                    $student->notify(new \App\Notifications\StudentScheduleAssignedNotification(
                        $firstSchedule,
                        $createdCount > 1,
                        $createdCount
                    ));
                    
                    // Notify Parents
                    foreach ($student->parents as $parent) {
                        $parent->notify(new \App\Notifications\StudentScheduleAssignedNotification(
                            $firstSchedule,
                            $createdCount > 1,
                            $createdCount
                        ));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send schedule notification: ' . $e->getMessage());
            }
        }

        return $createdCount;
    }

    public function updateSchedule(Schedule $schedule, array $data)
    {
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = $startsAt->copy()->addMinutes((int) $data['duration_minutes']);

        if (Schedule::hasTeacherConflict($data['teacher_id'], $startsAt, $endsAt, $schedule->id)) {
            throw new \Exception('Teacher has a conflict at this time.');
        }

        if (Schedule::hasStudentConflict($schedule->student_id, $startsAt, $endsAt, $schedule->id)) {
            throw new \Exception('Student has a conflict at this time.');
        }

        $oldTeacherId = $schedule->teacher_id;
        $oldStartsAt = $schedule->starts_at;

        $updated = $this->repository->update($schedule, [
            'teacher_id' => $data['teacher_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'zoom_link' => $data['zoom_link'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'],
        ]);

        if ($updated) {
            try {
                if ($oldTeacherId != $data['teacher_id'] || !$oldStartsAt->equalTo($startsAt)) {
                    $teacher = User::find($data['teacher_id']);
                    $schedule->load(['student.parents', 'teacher', 'course']);
                    
                    // Notify Teacher
                    $teacher->notify(new \App\Notifications\ScheduleAssignedNotification($schedule));

                    // Notify Student
                    $student = $schedule->student;
                    if ($student) {
                        $student->notify(new \App\Notifications\StudentScheduleAssignedNotification($schedule));
                        
                        // Notify Parents
                        foreach ($student->parents as $parent) {
                            $parent->notify(new \App\Notifications\StudentScheduleAssignedNotification($schedule));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send schedule update notification: ' . $e->getMessage());
            }
        }

        return $updated;
    }

    public function deleteSchedule(Schedule $schedule)
    {
        return $this->repository->delete($schedule);
    }

    public function bulkCancel(Enrollment $enrollment)
    {
        return $this->repository->bulkCancel($enrollment);
    }

    public function bulkDelete(Enrollment $enrollment)
    {
        return $this->repository->bulkDelete($enrollment);
    }

    protected function generatePaymentsForEnrollment(Enrollment $enrollment, $endMonth = null)
    {
        $startDate = $enrollment->start_date ?? $enrollment->created_at;
        $currentMonth = $endMonth
            ? Carbon::parse($endMonth)->startOfMonth()
            : now()->startOfMonth();
        $enrollmentMonth = Carbon::parse($startDate)->startOfMonth();

        while ($enrollmentMonth->lte($currentMonth)) {
            EnrollmentPayment::firstOrCreate([
                'enrollment_id' => $enrollment->id,
                'month' => $enrollmentMonth->copy(),
            ], [
                'amount' => $enrollment->admin_price,
                'currency' => $enrollment->currency,
                'payment_status' => 'unpaid',
            ]);

            $enrollmentMonth->addMonth();
        }
    }

    public function generateMonthlySchedules(Enrollment $enrollment, $month, $teacherId = null)
    {
        try {
            $targetMonth = Carbon::parse($month)->startOfMonth();
            $monthStart = $targetMonth->copy();
            $monthEnd = $targetMonth->copy()->endOfMonth();
            
            $enrollmentStart = Carbon::parse($enrollment->start_date);
            if ($monthStart->lt($enrollmentStart)) {
                $monthStart = $enrollmentStart->copy();
            }

            $existingCount = Schedule::where('enrollment_id', $enrollment->id)
                ->whereYear('starts_at', $targetMonth->year)
                ->whereMonth('starts_at', $targetMonth->month)
                ->count();
            
            if ($existingCount > 0) {
                return ['success' => true, 'count' => 0, 'message' => 'Schedules already exist for this month'];
            }

            $schedulePattern = null;
            $daysOfWeek = [];
            
            if ($enrollment->hasSchedulePattern()) {
                $schedulePattern = $enrollment->getSchedulePattern();
                $daysOfWeek = array_keys($schedulePattern);
            } else {
                $lastSchedule = Schedule::where('enrollment_id', $enrollment->id)
                    ->latest('starts_at')
                    ->first();
                
                if ($lastSchedule) {
                    $referenceMonth = $lastSchedule->starts_at->copy()->startOfMonth();
                    $previousSchedules = Schedule::where('enrollment_id', $enrollment->id)
                        ->whereYear('starts_at', $referenceMonth->year)
                        ->whereMonth('starts_at', $referenceMonth->month)
                        ->get();
                    
                    if ($previousSchedules->count() > 0) {
                        $schedulePattern = [];
                        foreach ($previousSchedules as $prevSchedule) {
                            $dayName = $prevSchedule->starts_at->format('l');
                            $time = $prevSchedule->starts_at->format('H:i');
                            
                            if (!isset($schedulePattern[$dayName])) {
                                $schedulePattern[$dayName] = $time;
                            }
                        }
                        $daysOfWeek = array_keys($schedulePattern);
                    }
                }
                
                if (empty($schedulePattern)) {
                    $daysPerWeek = $enrollment->days_per_week ?? 3;
                    $daysOfWeek = $this->getDefaultDaysForWeek($daysPerWeek);
                    $defaultTime = '16:00';
                    
                    $schedulePattern = [];
                    foreach ($daysOfWeek as $day) {
                        $schedulePattern[$day] = $defaultTime;
                    }
                }
            }
            
            if (!$teacherId) {
                $lastSchedule = Schedule::where('enrollment_id', $enrollment->id)
                    ->latest('starts_at')
                    ->first();
                
                if ($lastSchedule) {
                    $teacherId = $lastSchedule->teacher_id;
                } else {
                    $teacher = User::where('role', 'Teacher')->where('active', true)->first();
                    if (!$teacher) {
                        return ['success' => false, 'message' => 'No active teachers available'];
                    }
                    $teacherId = $teacher->id;
                }
            }
            
            $durationMinutes = (int) ($enrollment->session_duration ?? 60);

            $sessionDates = [];
            $currentDate = $monthStart->copy();

            while ($currentDate->lte($monthEnd)) {
                $dayName = $currentDate->format('l');
                if (in_array($dayName, $daysOfWeek)) {
                    $sessionDates[] = [
                        'date' => $currentDate->copy(),
                        'time' => $schedulePattern[$dayName],
                    ];
                }
                $currentDate->addDay();
            }

            if (empty($sessionDates)) {
                return ['success' => false, 'message' => 'No sessions to create for this month'];
            }

            $createdCount = 0;
            $conflictsCount = 0;
            $firstSchedule = null;
            $conflictedDates = [];

            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                foreach ($sessionDates as $session) {
                    $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $session['time']);
                    $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                    $exists = Schedule::where('enrollment_id', $enrollment->id)
                        ->whereDate('starts_at', $session['date'])
                        ->exists();
                    
                    if ($exists) {
                        continue;
                    }

                    if ($conflict = Schedule::getTeacherConflict($teacherId, $startsAt, $endsAt)) {
                        $studentName = $conflict->student ? $conflict->student->name : 'Unknown Student';
                        $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Unknown Teacher';
                        $courseName = $conflict->course ? $conflict->course->title : 'Unknown Course';
                        throw new \Exception("Teacher conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Teacher {$teacherName} is booked with Student {$studentName} for {$courseName})");
                    }

                    if ($conflict = Schedule::getStudentConflict($enrollment->student_id, $startsAt, $endsAt)) {
                        $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Unknown Teacher';
                        $studentName = $conflict->student ? $conflict->student->name : 'Unknown Student';
                        $courseName = $conflict->course ? $conflict->course->title : 'Unknown Course';
                        throw new \Exception("Student conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Student {$studentName} is booked with Teacher {$teacherName} for {$courseName})");
                    }

                    $schedule = Schedule::create([
                        'enrollment_id' => $enrollment->id,
                        'student_id' => $enrollment->student_id,
                        'teacher_id' => $teacherId,
                        'course_id' => $enrollment->course_id,
                        'starts_at' => $startsAt,
                        'ends_at' => $endsAt,
                        'status' => 'scheduled',
                    ]);

                    if (!$firstSchedule) {
                        $firstSchedule = $schedule;
                    }

                    $createdCount++;
                }
                \Illuminate\Support\Facades\DB::commit();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return ['success' => false, 'message' => 'Cannot create schedule due to conflicts: ' . $e->getMessage()];
            }

            if ($firstSchedule && $createdCount > 0) {
                try {
                    $teacher = User::find($teacherId);
                    $firstSchedule->load(['student.parents', 'teacher', 'course']);
                    
                    // Notify Teacher
                    $teacher->notify(new \App\Notifications\ScheduleAssignedNotification(
                        $firstSchedule,
                        $createdCount > 1,
                        $createdCount
                    ));

                    // Notify Student
                    $student = $firstSchedule->student;
                    if ($student) {
                        $student->notify(new \App\Notifications\StudentScheduleAssignedNotification(
                            $firstSchedule,
                            $createdCount > 1,
                            $createdCount
                        ));
                        
                        // Notify Parents
                        foreach ($student->parents as $parent) {
                            $parent->notify(new \App\Notifications\StudentScheduleAssignedNotification(
                                $firstSchedule,
                                $createdCount > 1,
                                $createdCount
                            ));
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send schedule notification: ' . $e->getMessage());
                }
            }

            $message = "Created {$createdCount} schedule(s) for " . $targetMonth->format('F Y');
            if ($conflictsCount > 0) {
                $message .= ". Skipped {$conflictsCount} sessions due to conflicts.";
            }

            return [
                'success' => true, 
                'count' => $createdCount, 
                'conflicts' => $conflictsCount,
                'conflicted_dates' => $conflictedDates,
                'message' => $message
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate schedules: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to generate schedules: ' . $e->getMessage()];
        }
    }

    public function updateSchedulePattern(Enrollment $enrollment, array $data)
    {
        $days = $data['days'];
        $scheduleTimes = $data['schedule_times'];
        $durationMinutes = (int) $data['duration_minutes'];
        $teacherId = $data['teacher_id'];

        $schedulePattern = [];
        foreach ($days as $day) {
            $schedulePattern[$day] = $scheduleTimes[$day];
        }

        $createdCount = 0;
        $deletedCount = 0;
        $firstSchedule = null;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Update enrollment pattern
            $enrollment->setSchedulePattern($schedulePattern);
            $enrollment->update([
                'days_per_week' => count($days),
                'session_duration' => $durationMinutes,
            ]);

            // Find all schedules
            $allSchedules = Schedule::where('enrollment_id', $enrollment->id)->get();

            if ($allSchedules->isEmpty()) {
                \Illuminate\Support\Facades\DB::commit();
                return ['success' => true, 'message' => 'Pattern updated. No schedules found to modify.'];
            }

            // Find the maximum and minimum date across all schedules
            $maxDate = $allSchedules->max('starts_at');
            $minDate = $allSchedules->min('starts_at')->copy()->startOfDay();

            // We will delete all schedules (past and future)
            foreach ($allSchedules as $schedule) {
                $schedule->delete();
                $deletedCount++;
            }

            // Generate new schedules from minDate to maxDate matching the new pattern
            $sessionDates = [];
            $currentDate = $minDate->copy();

            while ($currentDate->lte($maxDate)) {
                $dayName = $currentDate->format('l');
                if (in_array($dayName, $days)) {
                    $sessionDates[] = [
                        'date' => $currentDate->copy(),
                        'time' => $scheduleTimes[$dayName],
                    ];
                }
                $currentDate->addDay();
            }

            foreach ($sessionDates as $session) {
                $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $session['time']);
                $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                if ($conflict = Schedule::getTeacherConflict($teacherId, $startsAt, $endsAt)) {
                    $studentName = $conflict->student ? $conflict->student->name : 'Unknown Student';
                    $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Unknown Teacher';
                    $courseName = $conflict->course ? $conflict->course->title : 'Unknown Course';
                    throw new \Exception("Teacher conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Teacher {$teacherName} is booked with Student {$studentName} for {$courseName})");
                }

                if ($conflict = Schedule::getStudentConflict($enrollment->student_id, $startsAt, $endsAt)) {
                    $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Unknown Teacher';
                    $studentName = $conflict->student ? $conflict->student->name : 'Unknown Student';
                    $courseName = $conflict->course ? $conflict->course->title : 'Unknown Course';
                    throw new \Exception("Student conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Student {$studentName} is booked with Teacher {$teacherName} for {$courseName})");
                }

                $schedule = Schedule::create([
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'teacher_id' => $teacherId,
                    'course_id' => $enrollment->course_id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'status' => 'scheduled',
                ]);

                if (!$firstSchedule) {
                    $firstSchedule = $schedule;
                }

                $createdCount++;
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        if ($firstSchedule && $createdCount > 0) {
            try {
                $teacher = User::find($teacherId);
                $firstSchedule->load(['student.parents', 'teacher', 'course']);
                
                // Notify Teacher
                $teacher->notify(new \App\Notifications\ScheduleAssignedNotification(
                    $firstSchedule,
                    $createdCount > 1,
                    $createdCount
                ));

                // Notify Student
                $student = $firstSchedule->student;
                if ($student) {
                    $student->notify(new \App\Notifications\StudentScheduleAssignedNotification(
                        $firstSchedule,
                        $createdCount > 1,
                        $createdCount
                    ));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send schedule notification: ' . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'message' => "Pattern updated successfully. Deleted {$deletedCount} old sessions and created {$createdCount} new sessions."
        ];
    }

    private function getDefaultDaysForWeek($daysPerWeek)
    {
        $dayMappings = [
            1 => ['Monday'],
            2 => ['Monday', 'Wednesday'],
            3 => ['Monday', 'Wednesday', 'Friday'],
            4 => ['Monday', 'Tuesday', 'Wednesday', 'Thursday'],
            5 => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            6 => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            7 => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        ];

        return $dayMappings[$daysPerWeek] ?? ['Monday', 'Wednesday', 'Friday'];
    }
}
