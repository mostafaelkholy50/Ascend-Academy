<?php

namespace App\Http\Controllers\Admin;

use App\Models\Schedule;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Determine view type (default to calendar)
        $viewType = $request->get('view', 'calendar');
        
        // Get current week or requested week
        $weekStart = $request->filled('week') 
            ? Carbon::parse($request->week)->startOfWeek() 
            : Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        // Statistics
        $stats = [
            'total' => Schedule::count(),
            'upcoming' => Schedule::where('starts_at', '>', now())->where('status', 'scheduled')->count(),
            'completed' => Schedule::where('status', 'completed')->count(),
            'cancelled' => Schedule::where('status', 'cancelled')->count(),
        ];

        if ($viewType === 'calendar') {
            // Get all schedules for the current week
            $schedules = Schedule::with(['student', 'teacher', 'course', 'enrollment'])
                ->whereBetween('starts_at', [$weekStart, $weekEnd])
                ->orderBy('starts_at')
                ->get();

            // Organize schedules by day and time
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

            return view('admin.schedules.index', compact('stats', 'viewType', 'weekDays', 'weekStart', 'weekEnd'));
        } else {
            // Enrollment-grouped view
            $query = Enrollment::with(['student', 'course', 'schedules.teacher'])
                ->whereHas('schedules');

            // Search filter
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

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $enrollments = $query->latest()->paginate(15);

            return view('admin.schedules.index', compact('enrollments', 'stats', 'viewType'));
        }
    }

    public function create()
    {
        $enrollments = Enrollment::with(['student', 'course'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $teachers = User::where('role', 'Teacher')->where('active', true)->orderBy('name')->get();

        return view('admin.schedules.create', compact('enrollments', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'teacher_id' => 'required|exists:users,id',
            'days' => 'required|array|min:1',
            'days.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'schedule_times' => 'required|array|min:1',
            'schedule_times.*' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'zoom_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
            'month' => 'nullable|date_format:Y-m',
        ]);

        try {
            $enrollment = Enrollment::with(['student', 'course'])->findOrFail($request->enrollment_id);

            // Validate enrollment is active
            if ($enrollment->status !== 'active') {
                return back()->with('error', 'Cannot create schedule for inactive enrollment.');
            }

            // Validate that each selected day has a corresponding time
            $days = $request->days;
            $scheduleTimes = $request->schedule_times;
            
            foreach ($days as $day) {
                if (!isset($scheduleTimes[$day])) {
                    return back()->with('error', "Missing time for {$day}.")->withInput();
                }
            }

            // Build schedule pattern from days and times
            $schedulePattern = [];
            foreach ($days as $day) {
                $schedulePattern[$day] = $scheduleTimes[$day];
            }

            // Store the schedule pattern on the enrollment for future use
            $enrollment->setSchedulePattern($schedulePattern);

            // Determine which month to create schedules for
            $targetMonth = $request->filled('month') 
                ? Carbon::parse($request->month . '-01')->startOfMonth()
                : now()->startOfMonth();
            
            $monthStart = $targetMonth->copy();
            $monthEnd = $targetMonth->copy()->endOfMonth();
            
            // Ensure we don't create schedules before enrollment start date
            $enrollmentStart = Carbon::parse($enrollment->start_date);
            if ($monthStart->lt($enrollmentStart)) {
                $monthStart = $enrollmentStart->copy();
            }

            $durationMinutes = (int) $request->duration_minutes;

            // Generate session dates for this month only
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
                return back()->with('error', 'No sessions would be created with the selected days for this month.');
            }

            // Check for conflicts before creating any schedules
            $conflicts = [];
            foreach ($sessionDates as $session) {
                $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $session['time']);
                $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                // Check teacher conflict
                if (Schedule::hasTeacherConflict($request->teacher_id, $startsAt, $endsAt)) {
                    $conflicts[] = "Teacher conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')}";
                }

                // Check student conflict
                if (Schedule::hasStudentConflict($enrollment->student_id, $startsAt, $endsAt)) {
                    $conflicts[] = "Student conflict on {$startsAt->format('l, M d, Y')} at {$startsAt->format('g:i A')}";
                }
            }

            // If any conflicts, return error
            if (!empty($conflicts)) {
                $conflictMessage = "Cannot create schedule due to conflicts:\n" . implode("\n", array_slice($conflicts, 0, 5));
                if (count($conflicts) > 5) {
                    $conflictMessage .= "\n... and " . (count($conflicts) - 5) . " more conflicts.";
                }
                return back()->with('error', $conflictMessage)->withInput();
            }

            // No conflicts, create all schedules
            $createdCount = 0;
            $firstSchedule = null;
            foreach ($sessionDates as $session) {
                $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $session['time']);
                $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                $schedule = Schedule::create([
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'teacher_id' => $request->teacher_id,
                    'course_id' => $enrollment->course_id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'zoom_link' => $request->zoom_link,
                    'notes' => $request->notes,
                    'status' => 'scheduled',
                ]);

                if (!$firstSchedule) {
                    $firstSchedule = $schedule;
                }

                $createdCount++;
            }

            // Send email notification to teacher
            if ($firstSchedule) {
                try {
                    $teacher = User::find($request->teacher_id);
                    $firstSchedule->load(['student', 'teacher', 'course']);
                    $teacher->notify(new \App\Notifications\ScheduleAssignedNotification(
                        $firstSchedule,
                        $createdCount > 1,
                        $createdCount
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send schedule notification: ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.schedules.index')
                ->with('success', "Successfully created {$createdCount} recurring schedule sessions!");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create schedules: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['student', 'teacher', 'course', 'enrollment', 'attendance']);
        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $teachers = User::where('role', 'Teacher')->where('active', true)->orderBy('name')->get();
        return view('admin.schedules.edit', compact('schedule', 'teachers'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'starts_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'zoom_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        try {
            $startsAt = Carbon::parse($request->starts_at);
            $endsAt = $startsAt->copy()->addMinutes((int) $request->duration_minutes);

            // Check for conflicts (excluding current schedule)
            if (Schedule::hasTeacherConflict($request->teacher_id, $startsAt, $endsAt, $schedule->id)) {
                return back()->with('error', 'Teacher has a conflict at this time.');
            }

            if (Schedule::hasStudentConflict($schedule->student_id, $startsAt, $endsAt, $schedule->id)) {
                return back()->with('error', 'Student has a conflict at this time.');
            }

            $schedule->update([
                'teacher_id' => $request->teacher_id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'zoom_link' => $request->zoom_link,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            return back()->with('success', 'Schedule updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }

    public function bulkCancel(Request $request, Enrollment $enrollment)
    {
        $count = Schedule::where('enrollment_id', $enrollment->id)
            ->where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->update(['status' => 'cancelled']);

        return back()->with('success', "Cancelled {$count} upcoming schedule(s) for this enrollment.");
    }

    public function bulkDelete(Enrollment $enrollment)
    {
        $count = Schedule::where('enrollment_id', $enrollment->id)->count();
        
        Schedule::where('enrollment_id', $enrollment->id)->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', "Successfully deleted {$count} schedule(s) for this enrollment.");
    }

    /**
     * Generate monthly schedules for an enrollment
     * Called when payment for a month is marked as paid
     */
    public static function generateMonthlySchedules(Enrollment $enrollment, $month, $teacherId = null)
    {
        try {
            // Parse the month
            $targetMonth = Carbon::parse($month)->startOfMonth();
            $monthStart = $targetMonth->copy();
            $monthEnd = $targetMonth->copy()->endOfMonth();
            
            // Ensure we don't create schedules before enrollment start date
            $enrollmentStart = Carbon::parse($enrollment->start_date);
            if ($monthStart->lt($enrollmentStart)) {
                $monthStart = $enrollmentStart->copy();
            }

            // Check if any schedules already exist for this month
            // If so, skip generation to prevent duplicates
            $existingCount = Schedule::where('enrollment_id', $enrollment->id)
                ->whereYear('starts_at', $targetMonth->year)
                ->whereMonth('starts_at', $targetMonth->month)
                ->count();
            
            if ($existingCount > 0) {
                return ['success' => true, 'count' => 0, 'message' => 'Schedules already exist for this month'];
            }

            // Priority 1: Use stored schedule pattern if available
            $schedulePattern = null;
            $daysOfWeek = [];
            
            if ($enrollment->hasSchedulePattern()) {
                $schedulePattern = $enrollment->getSchedulePattern();
                $daysOfWeek = array_keys($schedulePattern);
            } else {
                // Priority 2: Try to detect pattern from previous schedules
                $lastSchedule = Schedule::where('enrollment_id', $enrollment->id)
                    ->latest('starts_at')
                    ->first();
                
                if ($lastSchedule) {
                    // Look at the most recent full month of data to determine days and times
                    $referenceMonth = $lastSchedule->starts_at->copy()->startOfMonth();
                    $previousSchedules = Schedule::where('enrollment_id', $enrollment->id)
                        ->whereYear('starts_at', $referenceMonth->year)
                        ->whereMonth('starts_at', $referenceMonth->month)
                        ->get();
                    
                    if ($previousSchedules->count() > 0) {
                        // Build pattern from previous schedules
                        $schedulePattern = [];
                        foreach ($previousSchedules as $prevSchedule) {
                            $dayName = $prevSchedule->starts_at->format('l');
                            $time = $prevSchedule->starts_at->format('H:i');
                            
                            // Use the first occurrence of each day
                            if (!isset($schedulePattern[$dayName])) {
                                $schedulePattern[$dayName] = $time;
                            }
                        }
                        $daysOfWeek = array_keys($schedulePattern);
                    }
                }
                
                // Priority 3: Fall back to defaults
                if (empty($schedulePattern)) {
                    $daysPerWeek = $enrollment->days_per_week ?? 3;
                    $daysOfWeek = self::getDefaultDaysForWeek($daysPerWeek);
                    $defaultTime = '16:00'; // 4 PM default
                    
                    $schedulePattern = [];
                    foreach ($daysOfWeek as $day) {
                        $schedulePattern[$day] = $defaultTime;
                    }
                }
            }
            
            // Fallback for teacher if still null
            if (!$teacherId) {
                // Try to get teacher from last schedule
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
            
            // Get duration from enrollment
            $durationMinutes = (int) ($enrollment->session_duration ?? 60);

            // Generate session dates for this month using the pattern
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

            // Create schedules
            $createdCount = 0;
            $firstSchedule = null;
            foreach ($sessionDates as $session) {
                $startsAt = Carbon::parse($session['date']->format('Y-m-d') . ' ' . $session['time']);
                $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                // Skip if schedule already exists
                $exists = Schedule::where('enrollment_id', $enrollment->id)
                    ->whereDate('starts_at', $session['date'])
                    ->exists();
                
                if ($exists) {
                    continue;
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

            // Send email notification to teacher
            if ($firstSchedule && $createdCount > 0) {
                try {
                    $teacher = User::find($teacherId);
                    $firstSchedule->load(['student', 'teacher', 'course']);
                    $teacher->notify(new \App\Notifications\ScheduleAssignedNotification(
                        $firstSchedule,
                        $createdCount > 1,
                        $createdCount
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send schedule notification: ' . $e->getMessage());
                }
            }

            return ['success' => true, 'count' => $createdCount, 'message' => "Created {$createdCount} schedule(s) for " . $targetMonth->format('F Y')];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to generate schedules: ' . $e->getMessage()];
        }
    }

    /**
     * Get default days of week based on days_per_week setting
     */
    private static function getDefaultDaysForWeek($daysPerWeek)
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
