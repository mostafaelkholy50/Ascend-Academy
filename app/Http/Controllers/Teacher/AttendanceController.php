<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest\StoreAttendanceRequest;
use App\Models\Schedule;
use App\Services\TeacherAttendanceService;
use Exception;

class AttendanceController extends Controller
{
    protected $service;

    public function __construct(TeacherAttendanceService $service)
    {
        $this->service = $service;
    }

    /**
     * Store or update attendance for a schedule
     */
    public function store(StoreAttendanceRequest $request)
    {
        try {
            $teacher = auth()->user();
            
            if ($teacher->role !== 'Teacher') {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }

            $attendance = $this->service->storeAttendance($teacher, $request->validated());
            
            $message = 'Attendance marked successfully.';
            
            if ($attendance->isBothPresent()) {
                $attendance->load('schedule');
                $schedule = $attendance->schedule;
                $duration = $schedule->getDurationInHours();
                
                $timeStr = $duration == 1 ? '1 hour' : "{$duration} hours";
                
                $message = "Attendance marked successfully! Added {$timeStr} to your total hours.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'attendance' => $attendance->load(['student', 'teacher']),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Get attendance data for a schedule (for pre-filling the form)
     */
    public function show(Schedule $schedule)
    {
        try {
            $teacher = auth()->user();
            
            if ($teacher->role !== 'Teacher') {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            
            if ($schedule->teacher_id !== $teacher->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to this schedule.'], 403);
            }
            
            $attendance = $schedule->attendance;
            
            return response()->json([
                'success' => true,
                'attendance' => $attendance,
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load attendance.'], 500);
        }
    }

    /**
     * Notify parent that the teacher is waiting
     */
    public function notifyWaiting(\Illuminate\Http\Request $request)
    {
        try {
            $teacher = auth()->user();
            $scheduleId = $request->input('schedule_id');

            $this->service->notifyParentWaiting($teacher, $scheduleId);

            return response()->json([
                'success' => true,
                'message' => 'Notification sent to the parent successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
