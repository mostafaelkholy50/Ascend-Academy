<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherScheduleService;
use Illuminate\Http\Request;
use Exception;

class ScheduleController extends Controller
{
    protected $service;

    public function __construct(TeacherScheduleService $service)
    {
        $this->service = $service;
    }

    /**
     * Display weekly schedule view
     */
    public function index(Request $request)
    {
        try {
            $teacher = auth()->user();
            
            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $data = $this->service->getWeeklyData($teacher, $request);

            return view('teacher.schedule-weekly', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('An error occurred while loading the weekly schedule.');
        }
    }
    
    /**
     * Display daily schedule view
     */
    public function daily(Request $request)
    {
        try {
            $teacher = auth()->user();
            
            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $data = $this->service->getDailyData($teacher, $request);

            return view('teacher.schedule-daily', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('An error occurred while loading the daily schedule.');
        }
    }

    /**
     * Print the logged-in teacher's monthly schedule
     */
    public function print(Request $request)
    {
        try {
            $teacher = auth()->user();

            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $month = $request->get('month', now($teacher->getUserTimezone())->format('Y-m'));
            $data = $this->service->getPrintableMonthlyData($teacher, $month);

            return view('admin.schedules.print', $data);
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('Failed to load printable schedule.');
        }
    }

    /**
     * Submit a reschedule request for a schedule
     */
    public function requestReschedule(Request $request, \App\Models\Schedule $schedule)
    {
        $teacher = auth()->user();

        if ($teacher->role !== 'Teacher' || $schedule->teacher_id !== $teacher->id) {
            abort(403);
        }

        $request->validate([
            'new_starts_at' => 'required|date',
        ]);

        $newStartsAt = \Carbon\Carbon::parse($request->new_starts_at, $teacher->getUserTimezone());
        if ($newStartsAt->isPast()) {
            return back()->with('error', 'The new time must be in the future.');
        }
        $newStartsAt->setTimezone('Africa/Cairo'); // Convert to app timezone

        $duration = $schedule->getDurationInMinutes();
        $newEndsAt = $newStartsAt->copy()->addMinutes($duration);

        if (\App\Models\Schedule::hasTeacherConflict($teacher->id, $newStartsAt, $newEndsAt, $schedule->id)) {
            return back()->with('error', 'You have a schedule conflict at this time.');
        }

        if (\App\Models\Schedule::hasStudentConflict($schedule->student_id, $newStartsAt, $newEndsAt, $schedule->id)) {
            return back()->with('error', 'The student has a schedule conflict at this time.');
        }

        // Check if there's already a pending request
        $existingRequest = \App\Models\RescheduleRequest::where('schedule_id', $schedule->id)
            ->where('status', \App\Enums\RescheduleRequestStatus::Pending)
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'A reschedule request is already pending for this session.');
        }

        \App\Models\RescheduleRequest::create([
            'schedule_id' => $schedule->id,
            'teacher_id' => $teacher->id,
            'student_id' => $schedule->student_id,
            'new_starts_at' => $newStartsAt,
            'new_ends_at' => $newEndsAt,
            'status' => \App\Enums\RescheduleRequestStatus::Pending,
        ]);

        return back()->with('success', 'Reschedule request submitted successfully.');
    }
}
