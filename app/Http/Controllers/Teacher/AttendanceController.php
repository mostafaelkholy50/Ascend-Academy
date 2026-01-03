<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Store or update attendance for a schedule
     */
    public function store(StoreAttendanceRequest $request)
    {
        $teacher = auth()->user();
        
        // Verify the schedule belongs to this teacher
        $schedule = Schedule::where('id', $request->schedule_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();
        
        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'schedule_id' => $request->schedule_id,
                'student_id' => $request->student_id,
            ],
            [
                'teacher_id' => $teacher->id,
                'teacher_present' => $request->teacher_present,
                'student_present' => $request->student_present,
                'remark' => $request->remark,
            ]
        );
        
        // Update schedule status if both are present
        if ($attendance->isBothPresent()) {
            $schedule->update(['status' => 'completed']);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully.',
            'attendance' => $attendance->load(['student', 'teacher']),
        ]);
    }
    
    /**
     * Get attendance data for a schedule (for pre-filling the form)
     */
    public function show(Schedule $schedule)
    {
        $teacher = auth()->user();
        
        // Verify the schedule belongs to this teacher
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to this schedule.');
        }
        
        $attendance = $schedule->attendance;
        
        return response()->json([
            'success' => true,
            'attendance' => $attendance,
        ]);
    }
}
