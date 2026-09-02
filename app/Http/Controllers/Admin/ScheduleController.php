<?php

namespace App\Http\Controllers\Admin;

use App\Models\Schedule;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ScheduleService;
use App\Http\Requests\Admin\StoreScheduleRequest;
use App\Http\Requests\Admin\UpdateScheduleRequest;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $viewType = $request->get('view', 'calendar');
        $teachers = User::where('role', 'Teacher')->where('active', true)->orderBy('name')->get();
        $students = User::where('role', 'Student')->where('active', true)->orderBy('name')->get();
        
        if ($viewType === 'calendar') {
            $data = $this->scheduleService->getCalendarData($request);
            return view('admin.schedules.index', array_merge($data, compact('viewType', 'teachers', 'students')));
        } else {
            $data = $this->scheduleService->getEnrollmentGroupedData($request);
            return view('admin.schedules.index', array_merge($data, compact('viewType', 'teachers', 'students')));
        }
    }

    public function create(Request $request)
    {
        $students = User::where('role', 'Student')->where('active', true)->orderBy('name')->get();
        $courses = \App\Models\Course::orderBy('title')->get();
        $teachers = User::where('role', 'Teacher')->where('active', true)->orderBy('name')->get();

        $selectedStudent = $request->query('student_id');
        $selectedCourse = $request->query('course_id');
        $selectedTeacher = $request->query('teacher_id');
        $selectedEnrollment = null;

        if ($selectedStudent && $selectedCourse) {
            $selectedEnrollment = Enrollment::with(['student', 'course'])
                ->where('student_id', $selectedStudent)
                ->where('course_id', $selectedCourse)
                ->where('status', 'active')
                ->first();
        }

        return view('admin.schedules.create', compact(
            'students',
            'courses',
            'teachers',
            'selectedStudent',
            'selectedCourse',
            'selectedTeacher',
            'selectedEnrollment'
        ));
    }

    public function store(StoreScheduleRequest $request)
    {
        try {
            $createdCount = $this->scheduleService->storeSchedule($request->validated());

            return redirect()->route('admin.schedules.index')
                ->with('success', "Successfully created {$createdCount} recurring schedule sessions!");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['student', 'teacher', 'course', 'enrollment', 'attendance']);
        $statusData = $this->getScheduleStatusData($schedule);

        return view('admin.schedules.show', compact('schedule', 'statusData'));
    }

    public function edit(Schedule $schedule)
    {
        $teachers = User::where('role', 'Teacher')->where('active', true)->orderBy('name')->get();
        return view('admin.schedules.edit', compact('schedule', 'teachers'));
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        try {
            $this->scheduleService->updateSchedule($schedule, $request->validated());

            return back()->with('success', 'Schedule updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    public function editPattern(Enrollment $enrollment)
    {
        $teachers = User::where('role', 'Teacher')->where('active', true)->orderBy('name')->get();
        $enrollment->load(['student', 'course']);
        $pattern = $enrollment->getSchedulePattern() ?? [];
        if (empty($pattern)) {
            $pattern = $this->buildPatternFromSchedules($enrollment);
        }
        
        $currentTeacherId = null;
        $lastSchedule = $enrollment->schedules()->latest('starts_at')->first();
        if ($lastSchedule) {
            $currentTeacherId = $lastSchedule->teacher_id;
        }

        return view('admin.schedules.edit-pattern', compact('enrollment', 'teachers', 'currentTeacherId', 'pattern'));
    }

    public function updatePattern(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'apply_from_date' => 'nullable|date',
            'teacher_id' => 'required|exists:users,id',
            'day_active' => 'required|array|min:1',
            'day_active.*' => 'nullable|boolean',
            'schedule_times' => 'required|array',
            'schedule_times.*' => 'required',
            'schedule_times.*.*' => 'nullable|date_format:H:i',
            'durations' => 'required|array',
            'durations.*' => 'required',
            'durations.*.*' => 'required|integer|min:15|max:240',
        ]);

        $applyFromDate = $request->input('apply_from_date') 
            ? Carbon::parse($request->input('apply_from_date'))->startOfDay() 
            : Carbon::now()->startOfDay();

        try {
            $result = $this->scheduleService->updateSchedulePattern($enrollment, $request->all(), $applyFromDate);

            return redirect()->route('admin.schedules.index', ['view' => 'list'])
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Schedule $schedule)
    {
        $this->scheduleService->deleteSchedule($schedule);
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule cancelled successfully.');
    }

    public function bulkCancel(Request $request, Enrollment $enrollment)
    {
        $count = $this->scheduleService->bulkCancel($enrollment);

        return back()->with('success', "Cancelled {$count} upcoming schedule(s) for this enrollment.");
    }

    public function bulkDelete(Enrollment $enrollment)
    {
        $count = $this->scheduleService->bulkDelete($enrollment);

        return redirect()->route('admin.schedules.index')
            ->with('success', "Successfully deleted {$count} schedule(s) for this enrollment.");
    }

    public function print(Request $request)
    {
        $teacherId = $request->get('teacher_id');
        $month = $request->get('month');

        if (!$teacherId || !$month) {
            return back()->with('error', 'Please select a teacher and a month to print.');
        }

        $teacher = User::findOrFail($teacherId);
        $timezone = $teacher->getUserTimezone();
        $targetMonth = Carbon::parse($month, $timezone);

        // Build the month boundaries in the teacher's timezone, then convert to app timezone
        // for the database query so we keep the exact local month the teacher expects.
        $startOfMonthApp = $targetMonth->copy()->startOfMonth()->startOfDay()->setTimezone(config('app.timezone'));
        $endOfMonthApp = $targetMonth->copy()->endOfMonth()->endOfDay()->setTimezone(config('app.timezone'));

        $schedules = Schedule::with(['student', 'course', 'enrollment', 'attendance'])
            ->where('teacher_id', $teacherId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('starts_at', [$startOfMonthApp, $endOfMonthApp])
            ->orderBy('starts_at')
            ->get();

        // Group by week and then by day
        $monthDays = [];
        $currentDate = $targetMonth->copy()->startOfMonth();
        $endDate = $targetMonth->copy()->endOfMonth();

        while ($currentDate->lte($endDate)) {
            $monthDays[$currentDate->format('Y-m-d')] = [
                'date' => $currentDate->copy(),
                'schedules' => collect()
            ];
            $currentDate->addDay();
        }

        foreach ($schedules as $schedule) {
            $dateString = $schedule->getStartsAtInTimezone($timezone)->format('Y-m-d');
            if (isset($monthDays[$dateString])) {
                $monthDays[$dateString]['schedules']->push($schedule);
            }
        }

        return view('admin.schedules.print', compact('teacher', 'targetMonth', 'monthDays', 'timezone'));
    }

    /**
     * Generate monthly schedules for an enrollment
     * Called when payment for a month is marked as paid
     */
    public static function generateMonthlySchedules(Enrollment $enrollment, $month, $teacherId = null)
    {
        return app(ScheduleService::class)->generateMonthlySchedules($enrollment, $month, $teacherId);
    }

    public function toggleDayStatus(Enrollment $enrollment, $day)
    {
        $pattern = $enrollment->getSchedulePattern() ?? [];
        if (isset($pattern[$day])) {
            $newStatus = !$pattern[$day]['active'];
            $pattern[$day]['active'] = $newStatus;
            $enrollment->setSchedulePattern($pattern);

            if (!$newStatus) {
                // If deactivated, cancel all upcoming scheduled sessions for this day
                $upcomingSchedules = Schedule::where('enrollment_id', $enrollment->id)
                    ->where('status', 'scheduled')
                    ->where('starts_at', '>', now())
                    ->get();

                $count = 0;
                foreach ($upcomingSchedules as $schedule) {
                    if ($schedule->starts_at->format('l') === $day) {
                        $schedule->update(['status' => 'cancelled']);
                        $count++;
                    }
                }
                $message = "Schedule for {$day} has been paused, and {$count} upcoming session(s) have been cancelled.";
            } else {
                // If activated, restore all upcoming cancelled sessions for this day
                $upcomingSchedules = Schedule::where('enrollment_id', $enrollment->id)
                    ->where('status', 'cancelled')
                    ->where('starts_at', '>', now())
                    ->get();

                $count = 0;
                foreach ($upcomingSchedules as $schedule) {
                    if ($schedule->starts_at->format('l') === $day) {
                        $schedule->update(['status' => 'scheduled']);
                        $count++;
                    }
                }
                $message = "Schedule for {$day} has been resumed, and {$count} upcoming session(s) have been restored.";
            }

            return redirect()->route('admin.schedules.index', ['view' => 'list'])
                ->with('success', $message);
        }
        return redirect()->route('admin.schedules.index', ['view' => 'list'])
            ->with('error', "Day {$day} not found in schedule pattern.");
    }

    public function toggleAllDays(Enrollment $enrollment)
    {
        $pattern = $enrollment->getSchedulePattern() ?? [];
        if (empty($pattern)) {
            $pattern = $this->buildPatternFromSchedules($enrollment);
        }

        if (empty($pattern)) {
            return redirect()->route('admin.schedules.index', ['view' => 'list'])
                ->with('error', 'No schedule pattern found.');
        }

        $allActive = collect($pattern)->every(function ($dayData) {
            return !empty($dayData['active']);
        });

        $newStatus = !$allActive;
        
        foreach ($pattern as $day => $dayData) {
            $pattern[$day]['active'] = $newStatus;
        }
        
        $enrollment->setSchedulePattern($pattern);

        if (!$newStatus) {
            $upcomingSchedules = Schedule::where('enrollment_id', $enrollment->id)
                ->where('status', 'scheduled')
                ->where('starts_at', '>', now())
                ->get();
                
            $count = $upcomingSchedules->count();
            foreach ($upcomingSchedules as $schedule) {
                $schedule->update(['status' => 'cancelled']);
            }
            $message = "All schedules have been paused, and {$count} upcoming session(s) have been cancelled.";
        } else {
            $upcomingSchedules = Schedule::where('enrollment_id', $enrollment->id)
                ->where('status', 'cancelled')
                ->where('starts_at', '>', now())
                ->get();
                
            $count = $upcomingSchedules->count();
            foreach ($upcomingSchedules as $schedule) {
                $schedule->update(['status' => 'scheduled']);
            }
            $message = "All schedules have been resumed, and {$count} upcoming session(s) have been restored.";
        }

        return redirect()->route('admin.schedules.index', ['view' => 'list'])
            ->with('success', $message);
    }

    protected function buildPatternFromSchedules(Enrollment $enrollment): array
    {
        $pattern = [];

        $enrollment->loadMissing('schedules');

        foreach ($enrollment->schedules as $schedule) {
            $day = $schedule->starts_at->format('l');
            $time = $schedule->starts_at->format('H:i');
            $duration = $schedule->getDurationInMinutes() ?: 60;

            if (!isset($pattern[$day])) {
                $pattern[$day] = [
                    'active' => true,
                    'slots' => [],
                ];
            }

            $pattern[$day]['slots'][] = [
                'time' => $time,
                'duration' => $duration,
            ];
        }

        foreach ($pattern as $day => $dayData) {
            $uniqueSlots = [];
            $seenTimes = [];

            foreach ($dayData['slots'] as $slot) {
                if (in_array($slot['time'], $seenTimes, true)) {
                    continue;
                }

                $seenTimes[] = $slot['time'];
                $uniqueSlots[] = $slot;
            }

            $pattern[$day]['slots'] = $uniqueSlots;
        }

        return $pattern;
    }

    protected function getScheduleStatusData(Schedule $schedule): array
    {
        $now = now();
        $isPast = $now->greaterThan($schedule->ends_at);
        $isInProgress = $now->between($schedule->starts_at, $schedule->ends_at);

        $status = 'not_yet';
        $label = 'Not Yet';
        $badgeClass = 'bg-blue-100 text-blue-700';
        $description = 'This class has not started yet.';
        $absenceInfo = null;

        if ($schedule->status === 'completed') {
            $status = 'attended';
            $label = 'Attended';
            $badgeClass = 'bg-green-100 text-green-700';
            $description = 'This class was completed successfully.';
        } elseif ($schedule->attendance) {
            $studentPresent = $schedule->attendance->student_present;
            $teacherPresent = $schedule->attendance->teacher_present;

            if ($studentPresent && $teacherPresent) {
                $status = 'attended';
                $label = 'Attended';
                $badgeClass = 'bg-emerald-100 text-emerald-700';
                $description = 'Both teacher and student attended this class.';
            } else {
                $status = 'absent';
                $label = 'Absent';
                $badgeClass = 'bg-red-100 text-red-700';

                $absentPeople = [];
                if (!$studentPresent) {
                    $absentPeople[] = 'Student';
                }
                if (!$teacherPresent) {
                    $absentPeople[] = 'Teacher';
                }

                $description = 'Attendance was recorded with an absence.';
                if (!empty($absentPeople)) {
                    $description = implode(' and ', $absentPeople) . ' were absent.';
                }

                $absenceInfo = [
                    'absent_people' => $absentPeople,
                    'reason' => $schedule->attendance->remark ?: null,
                    'student_present' => $studentPresent,
                    'teacher_present' => $teacherPresent,
                ];
            }
        } elseif ($isInProgress) {
            $status = 'in_progress';
            $label = 'In Progress';
            $badgeClass = 'bg-yellow-100 text-yellow-800';
            $description = 'This class is currently in progress.';
        } elseif ($isPast) {
            $status = 'past';
            $label = 'Past';
            $badgeClass = 'bg-gray-100 text-gray-700';
            $description = 'This class has already passed without recorded attendance.';
        }

        return compact('status', 'label', 'badgeClass', 'description', 'absenceInfo');
    }

    public function rescheduleRequests()
    {
        $requests = \App\Models\RescheduleRequest::with(['schedule', 'teacher', 'student'])
            ->where('status', \App\Enums\RescheduleRequestStatus::Pending)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.schedules.requests', compact('requests'));
    }

    public function approveRescheduleRequest(\App\Models\RescheduleRequest $rescheduleRequest)
    {
        if ($rescheduleRequest->status !== \App\Enums\RescheduleRequestStatus::Pending) {
            return back()->with('error', 'Request is already processed.');
        }

        // Validate conflicts again just in case
        if (\App\Models\Schedule::hasTeacherConflict($rescheduleRequest->teacher_id, $rescheduleRequest->new_starts_at, $rescheduleRequest->new_ends_at, $rescheduleRequest->schedule_id) ||
            \App\Models\Schedule::hasStudentConflict($rescheduleRequest->student_id, $rescheduleRequest->new_starts_at, $rescheduleRequest->new_ends_at, $rescheduleRequest->schedule_id)) {
            return back()->with('error', 'There is now a conflict for this requested time. Cannot approve.');
        }

        // Update schedule
        $schedule = $rescheduleRequest->schedule;
        $schedule->update([
            'starts_at' => $rescheduleRequest->new_starts_at,
            'ends_at' => $rescheduleRequest->new_ends_at,
        ]);

        // Update request status
        $rescheduleRequest->update(['status' => \App\Enums\RescheduleRequestStatus::Approved]);

        return back()->with('success', 'Reschedule request approved. The schedule has been updated.');
    }

    public function rejectRescheduleRequest(\App\Models\RescheduleRequest $rescheduleRequest)
    {
        if ($rescheduleRequest->status !== \App\Enums\RescheduleRequestStatus::Pending) {
            return back()->with('error', 'Request is already processed.');
        }

        $rescheduleRequest->update(['status' => \App\Enums\RescheduleRequestStatus::Rejected]);

        return back()->with('success', 'Reschedule request rejected.');
    }
}
