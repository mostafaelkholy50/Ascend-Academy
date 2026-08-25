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
            ->where('status', '!=', 'cancelled')
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
            $sessionDuration = 60; // Fallback or could take max duration
            if (!empty($data['durations'])) {
                foreach ($data['durations'] as $dayDurs) {
                    if (!empty($dayDurs)) {
                        $sessionDuration = (int)$dayDurs[0];
                        break;
                    }
                }
            }
            $currency = $data['currency'] ?? 'CAD';
            $adminPrice = $data['admin_price'] ?? null;
            $teacherId = $data['teacher_id'];
            $newPattern = $this->normalizeSchedulePattern($data['days'], $data['schedule_times'], $data['durations'] ?? []);
            $dayActive = $data['day_active'] ?? array_fill_keys($data['days'], 1);
            foreach ($newPattern as $day => $slots) {
                $newPattern[$day] = [
                    'active' => !empty($dayActive[$day]),
                    'slots' => $slots,
                ];
            }
            $mergedPattern = $newPattern;

            if ($enrollment) {
                $targetMonth = Carbon::parse($data['start_date'])->startOfMonth();
                $existingSchedulesInMonth = Schedule::where('enrollment_id', $enrollment->id)
                    ->whereYear('starts_at', $targetMonth->year)
                    ->whereMonth('starts_at', $targetMonth->month)
                    ->get();

                $existingPattern = $enrollment->getSchedulePattern() ?? [];
                $mergedPattern = $this->mergeSchedulePatterns($existingPattern, $newPattern);

                $enrollment->update([
                    'days_per_week' => count($mergedPattern),
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

            $enrollment->setSchedulePattern($mergedPattern);

            $monthStart = Carbon::parse($data['start_date']);
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            if (!$enrollment->start_date || $monthStart->lt($enrollment->start_date)) {
                $enrollment->update(['start_date' => $monthStart]);
            }

            $schedulePattern = $enrollment->getSchedulePattern() ?? $this->normalizeSchedulePattern($data['days'], $data['schedule_times'], $data['durations'] ?? []);

            $sessionDates = [];
            $currentDate = $monthStart->copy();

            while ($currentDate->lte($monthEnd)) {
                $dayName = $currentDate->format('l');
                $dayData = $schedulePattern[$dayName] ?? null;
                $isActive = is_array($dayData) ? (($dayData['active'] ?? true) !== false) : false;
                $hasSlots = is_array($dayData) && !empty($dayData['slots'] ?? $dayData);

                if ($isActive && $hasSlots) {
                    $sessionDates[] = [
                        'date' => $currentDate->copy(),
                        'times' => $dayData['slots'] ?? $dayData ?? [],
                    ];
                }
                $currentDate->addDay();
            }

            if (empty($sessionDates)) {
                throw new \Exception('No sessions would be created with the selected days for this month.');
            }

            $existingStarts = Schedule::where('enrollment_id', $enrollment->id)
                ->whereYear('starts_at', $monthStart->year)
                ->whereMonth('starts_at', $monthStart->month)
                ->get(['starts_at'])
                ->map(fn ($schedule) => $schedule->starts_at->format('Y-m-d H:i:s'))
                ->flip();

            $conflicts = [];
            
            // OPTIMIZATION: Fetch existing schedules for the teacher and student in the month
            $existingTeacherSchedules = Schedule::with(['student', 'course'])
                ->where('teacher_id', $data['teacher_id'])
                ->where('status', '!=', 'cancelled')
                ->whereBetween('starts_at', [$monthStart, $monthEnd->copy()->endOfDay()])
                ->get();
                
            $existingStudentSchedules = Schedule::with(['teacher', 'course'])
                ->where('student_id', $enrollment->student_id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('starts_at', [$monthStart, $monthEnd->copy()->endOfDay()])
                ->get();

            $newSchedulesData = [];

            foreach ($sessionDates as $session) {
                foreach ($session['times'] as $timeSlot) {
                    $time = is_array($timeSlot) ? $timeSlot['time'] : $timeSlot;
                    $durationMinutes = is_array($timeSlot) ? ($timeSlot['duration'] ?? $sessionDuration) : $sessionDuration;

                    $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $time);
                    $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                    if (isset($existingStarts[$startsAt->format('Y-m-d H:i:s')])) {
                        continue;
                    }

                    // Check Teacher Conflict in-memory
                    $teacherConflict = $existingTeacherSchedules->first(function ($schedule) use ($startsAt, $endsAt, $data) {
                        if ((int)$schedule->student_id === (int)$data['student_id'] && (int)$schedule->course_id === (int)$data['course_id']) return false;
                        return $schedule->starts_at < $endsAt
                            && $schedule->ends_at > $startsAt
                            && $schedule->isConflictRelevantFor($startsAt);
                    });
                    
                    if ($teacherConflict) {
                        $studentName = $teacherConflict->student ? $teacherConflict->student->name : 'Unknown Student';
                        $teacherName = $teacherConflict->teacher ? $teacherConflict->teacher->name : 'Unknown Teacher';
                        $courseName = $teacherConflict->course ? $teacherConflict->course->title : 'Unknown Course';
                        $conflicts[] = "Teacher conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Teacher {$teacherName} is booked with Student {$studentName} for {$courseName}. Check if you're booking a new course on the same days.)";
                    }

                    // Check Student Conflict in-memory
                    $studentConflict = $existingStudentSchedules->first(function ($schedule) use ($startsAt, $endsAt, $data) {
                        if ((int)$schedule->student_id === (int)$data['student_id'] && (int)$schedule->course_id === (int)$data['course_id']) return false;
                        return $schedule->starts_at < $endsAt
                            && $schedule->ends_at > $startsAt
                            && $schedule->isConflictRelevantFor($startsAt);
                    });

                    if ($studentConflict) {
                        $teacherName = $studentConflict->teacher ? $studentConflict->teacher->name : 'Unknown Teacher';
                        $studentName = $studentConflict->student ? $studentConflict->student->name : 'Unknown Student';
                        $courseName = $studentConflict->course ? $studentConflict->course->title : 'Unknown Course';
                        $conflicts[] = "Student conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Student {$studentName} is booked with Teacher {$teacherName} for {$courseName}. Are you booking overlapping classes?)";
                    }

                    if (!$teacherConflict && !$studentConflict) {
                        $schedule = $this->repository->create([
                            'enrollment_id' => $enrollment->id,
                            'student_id' => $enrollment->student_id,
                            'teacher_id' => $teacherId,
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
                }
            }

            if (!empty($conflicts)) {
                throw new \Exception("Cannot create schedule due to conflicts:\n" . implode("\n", array_slice($conflicts, 0, 5)));
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

            $sessionDuration = (int) ($enrollment->session_duration ?? 60);
            $schedulePattern = null;
            $daysOfWeek = [];

            if ($enrollment->hasSchedulePattern()) {
                $schedulePattern = $enrollment->getSchedulePattern();
                $daysOfWeek = array_keys(array_filter($schedulePattern, fn ($dayData) => !empty($dayData['active'])));
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
                                $schedulePattern[$dayName] = ['active' => true, 'slots' => []];
                            }

                            if (!in_array($time, array_column($schedulePattern[$dayName]['slots'], 'time'), true)) {
                                $schedulePattern[$dayName]['slots'][] = ['time' => $time, 'duration' => $sessionDuration];
                            }
                        }
                        $daysOfWeek = array_keys(array_filter($schedulePattern, fn ($dayData) => !empty($dayData['active'])));
                    }
                }
                
                if (empty($schedulePattern)) {
                    $daysPerWeek = $enrollment->days_per_week ?? 3;
                    $daysOfWeek = $this->getDefaultDaysForWeek($daysPerWeek);
                    $defaultTime = '16:00';
                    
                    $schedulePattern = [];
                    foreach ($daysOfWeek as $day) {
                        $schedulePattern[$day] = ['active' => true, 'slots' => [['time' => $defaultTime, 'duration' => $sessionDuration]]];
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
            
            $sessionDates = [];
            $currentDate = $monthStart->copy();

            while ($currentDate->lte($monthEnd)) {
                $dayName = $currentDate->format('l');
                $dayData = $schedulePattern[$dayName] ?? null;
                $isActive = is_array($dayData) ? (($dayData['active'] ?? true) !== false) : false;
                $hasSlots = is_array($dayData) && !empty($dayData['slots'] ?? $dayData);

                if ($isActive && $hasSlots && in_array($dayName, $daysOfWeek)) {
                    $sessionDates[] = [
                        'date' => $currentDate->copy(),
                        'times' => $dayData['slots'] ?? $dayData ?? [],
                    ];
                }
                $currentDate->addDay();
            }

            if (empty($sessionDates)) {
                return ['success' => false, 'message' => 'No sessions to create for this month'];
            }

            $existingStarts = Schedule::where('enrollment_id', $enrollment->id)
                ->whereYear('starts_at', $targetMonth->year)
                ->whereMonth('starts_at', $targetMonth->month)
                ->pluck('starts_at')
                ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d H:i:s'))
                ->flip();

            $createdCount = 0;
            $conflictsCount = 0;
            $firstSchedule = null;
            $conflictedDates = [];

            // OPTIMIZATION: Fetch existing schedules for the teacher and student in the month
            $existingTeacherSchedules = Schedule::with(['student', 'course'])
                ->where('teacher_id', $teacherId)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('starts_at', [$monthStart, $monthEnd->copy()->endOfDay()])
                ->get();
                
            $existingStudentSchedules = Schedule::with(['teacher', 'course'])
                ->where('student_id', $enrollment->student_id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('starts_at', [$monthStart, $monthEnd->copy()->endOfDay()])
                ->get();

            $newSchedulesData = [];

            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                foreach ($sessionDates as $session) {
                    foreach ($session['times'] as $timeSlot) {
                        if (is_array($timeSlot) && !isset($timeSlot['time'])) {
                            continue; // Invalid structure
                        }
                        
                        $time = is_array($timeSlot) ? $timeSlot['time'] : $timeSlot;
                        $durationMinutes = is_array($timeSlot) ? ($timeSlot['duration'] ?? $sessionDuration) : $sessionDuration;

                        $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $time);
                        $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                        if (isset($existingStarts[$startsAt->format('Y-m-d H:i:s')])) {
                            continue;
                        }

                        // Check Teacher Conflict in-memory
                        $teacherConflict = $existingTeacherSchedules->first(function ($schedule) use ($startsAt, $endsAt, $enrollment) {
                            if ($schedule->student_id == $enrollment->student_id && $schedule->course_id == $enrollment->course_id) return false;
                            return $schedule->starts_at < $endsAt
                                && $schedule->ends_at > $startsAt
                                && $schedule->isConflictRelevantFor($startsAt);
                        });

                        if ($teacherConflict) {
                            $studentName = $teacherConflict->student ? $teacherConflict->student->name : 'Unknown Student';
                            $teacherName = $teacherConflict->teacher ? $teacherConflict->teacher->name : 'Unknown Teacher';
                            $courseName = $teacherConflict->course ? $teacherConflict->course->title : 'Unknown Course';
                            throw new \Exception("Teacher conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Teacher {$teacherName} is booked with Student {$studentName} for {$courseName})");
                        }

                        // Check Student Conflict in-memory
                        $studentConflict = $existingStudentSchedules->first(function ($schedule) use ($startsAt, $endsAt, $enrollment) {
                            if ($schedule->student_id == $enrollment->student_id && $schedule->course_id == $enrollment->course_id) return false;
                            return $schedule->starts_at < $endsAt
                                && $schedule->ends_at > $startsAt
                                && $schedule->isConflictRelevantFor($startsAt);
                        });

                        if ($studentConflict) {
                            $teacherName = $studentConflict->teacher ? $studentConflict->teacher->name : 'Unknown Teacher';
                            $studentName = $studentConflict->student ? $studentConflict->student->name : 'Unknown Student';
                            $courseName = $studentConflict->course ? $studentConflict->course->title : 'Unknown Course';
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

    public function updateSchedulePattern(Enrollment $enrollment, array $data, ?Carbon $targetMonth = null)
    {
        $useScopedMonth = $targetMonth !== null;
        $targetMonth = $targetMonth ? $targetMonth->copy()->startOfMonth() : now()->startOfMonth();
        $monthStart = $targetMonth->copy()->startOfMonth();
        $monthEnd = $targetMonth->copy()->endOfMonth();

        $days = array_keys($data['day_active'] ?? $data['schedule_times'] ?? []);
        if (empty($days)) {
            $days = $data['days'] ?? [];
        }
        $scheduleTimes = $data['schedule_times'];
        $durations = $data['durations'] ?? [];
        $teacherId = $data['teacher_id'];
        $dayActive = $data['day_active'] ?? array_fill_keys($days, 1);

        $schedulePattern = $this->normalizeSchedulePattern($days, $scheduleTimes, $durations);

        $finalPattern = [];
        foreach ($schedulePattern as $day => $slots) {
            if (empty($dayActive[$day])) {
                continue;
            }
            $finalPattern[$day] = [
                'active' => true,
                'slots' => $slots,
            ];
        }
        $schedulePattern = $finalPattern;

        $sessionDuration = 60;
        if (!empty($durations)) {
            foreach ($durations as $dayDurs) {
                if (!empty($dayDurs)) {
                    $sessionDuration = (int) $dayDurs[0];
                    break;
                }
            }
        }

        $createdCount = 0;
        $deletedCount = 0;
        $firstSchedule = null;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $enrollment->update([
                'days_per_week' => count($days),
                'session_duration' => $sessionDuration,
            ]);

            $scheduleQuery = Schedule::where('enrollment_id', $enrollment->id);
            if ($useScopedMonth) {
                $scheduleQuery->whereBetween('starts_at', [$monthStart, $monthEnd->copy()->endOfDay()]);
            }

            $schedulesToRefresh = $scheduleQuery->get();

            if ($schedulesToRefresh->isEmpty()) {
                if ($useScopedMonth) {
                    \Illuminate\Support\Facades\DB::commit();
                    return ['success' => true, 'message' => 'Pattern updated for ' . $targetMonth->format('F Y') . '. No schedules found to modify.'];
                }

                $enrollment->setSchedulePattern($schedulePattern);
                \Illuminate\Support\Facades\DB::commit();
                return ['success' => true, 'message' => 'Pattern updated. No schedules found to modify.'];
            }

            if ($useScopedMonth) {
                foreach ($schedulesToRefresh as $schedule) {
                    $schedule->delete();
                    $deletedCount++;
                }
            } else {
                $maxDate = $schedulesToRefresh->max('starts_at');
                $minDate = $schedulesToRefresh->min('starts_at')->copy()->startOfDay();

                foreach ($schedulesToRefresh as $schedule) {
                    $schedule->delete();
                    $deletedCount++;
                }

                $monthStart = $minDate;
                $monthEnd = $maxDate;
            }

            $sessionDates = [];
            $currentDate = $monthStart->copy();

            while ($currentDate->lte($monthEnd)) {
                $dayName = $currentDate->format('l');
                if (!empty($schedulePattern[$dayName]['active']) && in_array($dayName, $days)) {
                    $sessionDates[] = [
                        'date' => $currentDate->copy(),
                        'times' => $schedulePattern[$dayName]['slots'] ?? [],
                    ];
                }
                $currentDate->addDay();
            }

            foreach ($sessionDates as $session) {
                foreach ($session['times'] as $timeSlot) {
                    if (is_array($timeSlot) && !isset($timeSlot['time'])) {
                        continue;
                    }

                    $time = is_array($timeSlot) ? $timeSlot['time'] : $timeSlot;
                    $durationMinutes = is_array($timeSlot) ? ($timeSlot['duration'] ?? $sessionDuration) : $sessionDuration;

                    $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $time);
                    $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                    if ($conflict = Schedule::getTeacherConflict($teacherId, $startsAt, $endsAt, null, $enrollment->student_id, $enrollment->course_id)) {
                        $studentName = $conflict->student ? $conflict->student->name : 'Unknown Student';
                        $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Unknown Teacher';
                        $courseName = $conflict->course ? $conflict->course->title : 'Unknown Course';
                        throw new \Exception("Teacher conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')} (Teacher {$teacherName} is booked with Student {$studentName} for {$courseName})");
                    }

                    if ($conflict = Schedule::getStudentConflict($enrollment->student_id, $startsAt, $endsAt, null, $enrollment->student_id, $enrollment->course_id)) {
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
            }

            $enrollment->setSchedulePattern($schedulePattern);
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        if ($firstSchedule && $createdCount > 0) {
            try {
                $teacher = User::find($teacherId);
                $firstSchedule->load(['student.parents', 'teacher', 'course']);

                $teacher->notify(new \App\Notifications\ScheduleAssignedNotification(
                    $firstSchedule,
                    $createdCount > 1,
                    $createdCount
                ));

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
            'message' => $useScopedMonth
                ? "Pattern updated successfully for {$targetMonth->format('F Y')}. Deleted {$deletedCount} old sessions and created {$createdCount} new sessions."
                : "Pattern updated successfully. Deleted {$deletedCount} old sessions and created {$createdCount} new sessions."
        ];
    }

    protected function mergeSchedulePatterns(array $existingPattern, array $newPattern): array
    {
        $merged = [];

        foreach ($existingPattern as $day => $dayData) {
            if (is_array($dayData) && array_key_exists('slots', $dayData)) {
                $merged[$day] = [
                    'active' => $dayData['active'] ?? true,
                    'slots' => $dayData['slots'] ?? [],
                ];
                continue;
            }

            $merged[$day] = [
                'active' => true,
                'slots' => is_array($dayData) ? $dayData : (empty($dayData) ? [] : [['time' => $dayData, 'duration' => 60]]),
            ];
        }

        foreach ($newPattern as $day => $dayData) {
            $newSlots = $dayData['slots'] ?? [];
            $existingSlots = $merged[$day]['slots'] ?? [];
            $merged[$day] = [
                'active' => $dayData['active'] ?? ($merged[$day]['active'] ?? true),
                'slots' => array_values(array_unique(array_merge($existingSlots, $newSlots), SORT_REGULAR)),
            ];
        }

        return $merged;
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

    protected function normalizeSchedulePattern(array $days, array $scheduleTimes, array $durations = []): array
    {
        $schedulePattern = [];

        foreach ($days as $day) {
            $times = $scheduleTimes[$day] ?? [];
            $dayDurations = $durations[$day] ?? [];

            if (is_string($times)) {
                $times = [$times];
            }
            if (is_string($dayDurations)) {
                $dayDurations = [$dayDurations];
            }

            if (!is_array($times)) {
                continue;
            }

            $cleanSlots = [];
            foreach ($times as $index => $time) {
                if (is_string($time)) {
                    $time = trim($time);
                    if ($time) {
                        $duration = isset($dayDurations[$index]) ? (int)$dayDurations[$index] : 60;
                        $cleanSlots[] = ['time' => $time, 'duration' => $duration];
                    }
                }
            }

            if (!empty($cleanSlots)) {
                // Keep unique times
                $uniqueTimes = [];
                $finalSlots = [];
                foreach ($cleanSlots as $slot) {
                    if (!in_array($slot['time'], $uniqueTimes)) {
                        $uniqueTimes[] = $slot['time'];
                        $finalSlots[] = $slot;
                    }
                }
                $schedulePattern[$day] = $finalSlots;
            }
        }

        return $schedulePattern;
    }
}
