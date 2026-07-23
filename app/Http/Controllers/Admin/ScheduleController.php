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
        return view('admin.schedules.show', compact('schedule'));
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
        
        $currentTeacherId = null;
        $lastSchedule = $enrollment->schedules()->latest('starts_at')->first();
        if ($lastSchedule) {
            $currentTeacherId = $lastSchedule->teacher_id;
        }

        return view('admin.schedules.edit-pattern', compact('enrollment', 'teachers', 'currentTeacherId'));
    }

    public function updatePattern(Request $request, Enrollment $enrollment)
    {
        $request->validate([
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

        try {
            $result = $this->scheduleService->updateSchedulePattern($enrollment, $request->all());

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
            ->with('success', 'Schedule deleted successfully.');
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

        $timezone = auth()->user()->getUserTimezone();
        $teacher = User::findOrFail($teacherId);
        $targetMonth = Carbon::parse($month, $timezone);
        
        // Convert local month boundaries to App Timezone for database querying
        $startOfMonthApp = $targetMonth->copy()->startOfMonth()->setTimezone(config('app.timezone'));
        $endOfMonthApp = $targetMonth->copy()->endOfMonth()->setTimezone(config('app.timezone'));

        $schedules = Schedule::with(['student', 'course'])
            ->where('teacher_id', $teacherId)
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

        return view('admin.schedules.print', compact('teacher', 'targetMonth', 'monthDays'));
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
        $pattern = $enrollment->schedule_pattern ?? [];
        if (isset($pattern[$day])) {
            $newStatus = !($pattern[$day]['active'] ?? true);
            $pattern[$day]['active'] = $newStatus;
            $enrollment->schedule_pattern = $pattern;
            $enrollment->save();

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

            return back()->with('success', $message);
        }
        return back()->with('error', "Day {$day} not found in schedule pattern.");
    }

    public function toggleAllDays(Enrollment $enrollment)
    {
        $pattern = $enrollment->schedule_pattern ?? [];
        if (empty($pattern)) {
            return back()->with('error', 'No schedule pattern found.');
        }

        $anyActive = false;
        foreach ($pattern as $day => $dayData) {
            if (!empty($dayData['active'])) {
                $anyActive = true;
                break;
            }
        }

        $newStatus = !$anyActive;
        
        foreach ($pattern as $day => $dayData) {
            $pattern[$day]['active'] = $newStatus;
        }
        
        $enrollment->schedule_pattern = $pattern;
        $enrollment->save();

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

        return back()->with('success', $message);
    }
}
