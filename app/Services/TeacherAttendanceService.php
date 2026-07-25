<?php

namespace App\Services;

use App\Repositories\TeacherAttendanceRepository;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use Exception;

class TeacherAttendanceService
{
    protected $repository;

    public function __construct(TeacherAttendanceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function storeAttendance(User $teacher, array $data): Attendance
    {
        // Verify the schedule belongs to this teacher
        $schedule = $this->repository->getScheduleForTeacher($data['schedule_id'], $teacher->id);

        if (!$schedule) {
            throw new Exception('Unauthorized access to this schedule.');
        }

        // Create or update attendance
        $attendance = $this->repository->updateOrCreateAttendance(
            [
                'schedule_id' => $data['schedule_id'],
                'student_id' => $data['student_id'],
            ],
            [
                'teacher_id' => $teacher->id,
                'teacher_present' => $data['teacher_present'],
                'student_present' => $data['student_present'],
                'remark' => $data['remark'] ?? null,
            ]
        );

        // Update schedule status if both are present
        if ($attendance->isBothPresent()) {
            $schedule->update(['status' => 'completed']);
        }

        return $attendance;
    }

    public function notifyParentWaiting(User $teacher, int $scheduleId, bool $waitedHalfTime = false): array
    {
        $schedule = $this->repository->getScheduleForTeacher($scheduleId, $teacher->id);

        if (!$schedule) {
            throw new Exception('Unauthorized access to this schedule.');
        }

        $student = $schedule->student;

        // 1. Add bonus time if requested (PRIORITY)
        if ($waitedHalfTime) {
            $this->repository->updateOrCreateAttendance(
                [
                    'schedule_id' => $scheduleId,
                    'student_id' => $student->id,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'teacher_present' => true,
                    'student_present' => false,
                    'remark' => 'Waited Half Time',
                ]
            );
            
            // Do not update status to 'completed'. 
            // The frontend will automatically show 'Student Absent' because attendance has student_present = false.
        }

        // 2. Try to send email
        $emailSent = false;
        try {
            $parents = $student->parents;

            if ($parents->isEmpty()) {
                // Fallback: Notify student if no parent is linked
                $student->notify(new \App\Notifications\TeacherWaitingNotification($schedule));
            } else {
                foreach ($parents as $parent) {
                    $parent->notify(new \App\Notifications\TeacherWaitingNotification($schedule));
                }
            }
            $emailSent = true;
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Illuminate\Support\Facades\Log::error('Failed to send waiting notification: ' . $e->getMessage());
        }

        return [
            'time_added' => $waitedHalfTime,
            'email_sent' => $emailSent
        ];
    }
}
