<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\ConsecutiveAbsenceNotification;
use Illuminate\Support\Facades\Log;

class AttendanceObserver
{
    /**
     * Handle the Attendance "saved" event.
     */
    public function saved(Attendance $attendance): void
    {
        // We only care if the student was marked absent
        if (!$attendance->student_present) {
            $this->checkConsecutiveAbsences($attendance->student_id);
        }
    }

    /**
     * Check consecutive absences for a student and notify admins if > 2
     */
    protected function checkConsecutiveAbsences($studentId)
    {
        try {
            // Get all attendances for this student, ordered by schedule start time descending
            $attendances = Attendance::select('attendances.student_present')
                ->join('schedules', 'attendances.schedule_id', '=', 'schedules.id')
                ->where('attendances.student_id', $studentId)
                ->where('schedules.status', '!=', 'cancelled')
                ->orderBy('schedules.starts_at', 'desc')
                ->get();

            $consecutiveAbsences = 0;

            foreach ($attendances as $record) {
                if (!$record->student_present) {
                    $consecutiveAbsences++;
                } else {
                    // Stop counting as soon as we hit a "present" record
                    break;
                }
            }

            // The user requested: "more than 2 classes" (i.e. >= 3)
            if ($consecutiveAbsences > 2) {
                $student = User::find($studentId);
                if (!$student) return;

                $admins = User::role(['Admin', 'SuperAdmin'])->get();

                foreach ($admins as $admin) {
                    $admin->notify(new ConsecutiveAbsenceNotification($student, $consecutiveAbsences));
                }
            }
        } catch (\Exception $e) {
            Log::error('Error checking consecutive absences: ' . $e->getMessage());
        }
    }
}
