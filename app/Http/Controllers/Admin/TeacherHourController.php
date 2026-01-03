<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\TeacherHour;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TeacherHourController extends Controller
{
    public function index(Request $request)
    {
        // Get selected month/year or default to current
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // Get all active teachers
        $teachers = User::where('role', 'Teacher')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        // Calculate hours for each teacher
        $teacherData = [];
        foreach ($teachers as $teacher) {
            $workedHours = $this->calculateWorkedHours($teacher->id, $year, $month);
            
            // Get or create teacher hour record (without hourly_rate)
            $teacherHour = TeacherHour::firstOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'total_hours' => 0,
                    'total_salary' => 0,
                    'is_paid' => false,
                ]
            );

            // Update total hours and calculate salary using teacher's hourly rate
            $teacherHour->total_hours = $workedHours;
            $teacherHour->total_salary = $workedHours * ($teacher->hourly_rate ?? 0);
            $teacherHour->save();

            $teacherData[] = [
                'teacher' => $teacher,
                'teacherHour' => $teacherHour,
                'workedHours' => $workedHours,
            ];
        }

        return view('admin.teacher-hours.index', compact('teacherData', 'year', 'month'));
    }

    public function updateRate(Request $request, User $teacher)
    {
        $request->validate([
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        $teacher->hourly_rate = $request->hourly_rate;
        $teacher->save();

        // Recalculate all unpaid months for this teacher
        $unpaidMonths = TeacherHour::where('teacher_id', $teacher->id)
            ->where('is_paid', false)
            ->get();

        foreach ($unpaidMonths as $teacherHour) {
            $teacherHour->total_salary = $teacherHour->total_hours * $teacher->hourly_rate;
            $teacherHour->save();
        }

        return back()->with('success', 'Hourly rate updated successfully. All unpaid months have been recalculated.');
    }

    public function markAsPaid(Request $request, TeacherHour $teacherHour)
    {
        $teacherHour->is_paid = true;
        $teacherHour->paid_at = now();
        $teacherHour->save();

        return back()->with('success', 'Payment marked as paid.');
    }

    public function markAsUnpaid(TeacherHour $teacherHour)
    {
        $teacherHour->is_paid = false;
        $teacherHour->paid_at = null;
        $teacherHour->save();

        return back()->with('success', 'Payment marked as unpaid.');
    }

    /**
     * Calculate worked hours for a teacher in a specific month
     */
    private function calculateWorkedHours(int $teacherId, int $year, int $month): float
    {
        // Get start and end of the month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Get all attendances where both teacher and student were present
        $attendances = Attendance::where('teacher_id', $teacherId)
            ->where('teacher_present', true)
            ->where('student_present', true)
            ->whereHas('schedule', function($query) use ($startDate, $endDate) {
                $query->whereBetween('starts_at', [$startDate, $endDate]);
            })
            ->with('schedule')
            ->get();

        $totalHours = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->schedule) {
                $duration = $attendance->schedule->getDurationInHours();
                $totalHours += $duration;
            }
        }

        return round($totalHours, 2);
    }
}
