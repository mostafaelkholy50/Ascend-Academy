<?php

namespace App\Http\Controllers\Admin;

use App\Models\Schedule;
use App\Models\Enrollment;
use App\Models\User;
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
        
        if ($viewType === 'calendar') {
            $data = $this->scheduleService->getCalendarData($request);
            return view('admin.schedules.index', array_merge($data, compact('viewType')));
        } else {
            $data = $this->scheduleService->getEnrollmentGroupedData($request);
            return view('admin.schedules.index', array_merge($data, compact('viewType')));
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

        return view('admin.schedules.create', compact('students', 'courses', 'teachers', 'selectedStudent', 'selectedCourse', 'selectedTeacher'));
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

    /**
     * Generate monthly schedules for an enrollment
     * Called when payment for a month is marked as paid
     */
    public static function generateMonthlySchedules(Enrollment $enrollment, $month, $teacherId = null)
    {
        return app(ScheduleService::class)->generateMonthlySchedules($enrollment, $month, $teacherId);
    }
}
