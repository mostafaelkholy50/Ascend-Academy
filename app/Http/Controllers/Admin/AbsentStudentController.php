<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AbsentStudentController extends Controller
{
    public function index()
    {
        $this->authorizeAccess();
        $absentStudents = $this->getAbsentStudents();
        return view('admin.absent_students.index', compact('absentStudents'));
    }

    public function list()
    {
        $this->authorizeAccess();
        $absentStudents = $this->getAbsentStudents();
        return view('admin.absent_students.list', compact('absentStudents'));
    }

    private function authorizeAccess()
    {
        if (!auth()->user()->hasRole('SuperAdmin') && !auth()->user()->hasPermissionTo('view_absent_students')) {
            abort(403, 'Unauthorized access.');
        }
    }

    private function getAbsentStudents()
    {
        $students = User::role('Student')->where('active', true)->get();
        $absentStudents = collect();

        foreach ($students as $student) {
            $attendances = Attendance::select('attendances.student_present')
                ->join('schedules', 'attendances.schedule_id', '=', 'schedules.id')
                ->where('attendances.student_id', $student->id)
                ->where('schedules.status', '!=', 'cancelled')
                ->orderBy('schedules.starts_at', 'desc')
                ->get();

            $consecutiveAbsences = 0;

            foreach ($attendances as $record) {
                if (!$record->student_present) {
                    $consecutiveAbsences++;
                } else {
                    break;
                }
            }

            if ($consecutiveAbsences > 2) {
                $student->consecutive_absences = $consecutiveAbsences;
                $absentStudents->push($student);
            }
        }

        return $absentStudents->sortByDesc('consecutive_absences')->values();
    }
}
