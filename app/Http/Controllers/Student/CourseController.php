<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Schedule;
use Carbon\Carbon;

class CourseController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get student's enrollments with courses
        $enrollments = Enrollment::with(['course'])
            ->where('student_id', $student->id)
            ->get();

        // Calculate progress and statistics for each enrollment
        foreach ($enrollments as $enrollment) {
            // Total sessions for this enrollment
            $totalSessions = Schedule::where('student_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->count();

            // Completed sessions
            $completedSessions = Schedule::where('student_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->where('status', 'completed')
                ->count();

            // Upcoming sessions
            $upcomingSessions = Schedule::where('student_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->where('status', 'scheduled')
                ->where('starts_at', '>', now())
                ->count();

            // Next session
            $nextSession = Schedule::with(['teacher'])
                ->where('student_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->where('status', 'scheduled')
                ->where('starts_at', '>', now())
                ->orderBy('starts_at')
                ->first();

            // Calculate progress percentage
            $enrollment->progress = $totalSessions > 0
                ? round(($completedSessions / $totalSessions) * 100)
                : 0;
            
            $enrollment->total_sessions = $totalSessions;
            $enrollment->completed_sessions = $completedSessions;
            $enrollment->upcoming_sessions = $upcomingSessions;
            $enrollment->next_session = $nextSession;

            // Get average mastery score from reports
            $averageScore = \App\Models\Report::where('student_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->whereNotNull('mastery_score')
                ->avg('mastery_score');
            
            $enrollment->average_mastery = $averageScore ? round($averageScore) : null;
        }

        return view('student.courses.index', compact('enrollments'));
    }
}
