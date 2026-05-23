<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Enrollment;
use App\Models\Attendance;
use Carbon\Carbon;

class ChildrenController extends Controller
{
    protected $evaluationService;

    public function __construct(\App\Services\StudentEvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function index()
    {
        $parent = auth()->user();
        
        // Get all children with detailed information
        $children = $parent->children()
            ->with(['enrollments.course'])
            ->get();
        
        // Calculate detailed statistics for each child
        foreach ($children as $child) {
            $child->stats = [
                'total_courses' => $child->enrollments()->count(),
                'active_courses' => $child->enrollments()->where('status', 'active')->count(),
                'completed_courses' => $child->enrollments()->where('status', 'completed')->count(),
                'total_sessions' => Schedule::where('student_id', $child->id)->count(),
                'completed_sessions' => Schedule::where('student_id', $child->id)
                    ->where('status', 'completed')->count(),
                'attendance_rate' => $this->calculateAttendanceRate($child->id),
                'latest_report_date' => Report::where('student_id', $child->id)
                    ->latest('report_date')->value('report_date'),
            ];
        }
        
        return view('parent.children.index', compact('parent', 'children'));
    }
    
    public function show($childId)
    {
        $parent = auth()->user();
        
        // Verify this child belongs to the parent
        $child = $parent->children()->findOrFail($childId);
        
        // Get child's enrollments
        $enrollments = Enrollment::with(['course'])
            ->where('student_id', $child->id)
            ->get();
        
        // Calculate progress for each enrollment
        foreach ($enrollments as $enrollment) {
            $totalSessions = Schedule::where('student_id', $child->id)
                ->where('course_id', $enrollment->course_id)
                ->count();
            
            $completedSessions = Schedule::where('student_id', $child->id)
                ->where('course_id', $enrollment->course_id)
                ->where('status', 'completed')
                ->count();
            
            $enrollment->progress = $totalSessions > 0
                ? round(($completedSessions / $totalSessions) * 100)
                : 0;
        }
        
        // Get recent schedules
        $recentSchedules = Schedule::with(['teacher', 'course'])
            ->where('student_id', $child->id)
            ->latest('starts_at')
            ->take(10)
            ->get();
        
        // Get recent reports
        $recentReports = Report::with(['teacher', 'course'])
            ->where('student_id', $child->id)
            ->latest('report_date')
            ->take(10)
            ->get();
        
        // Get recent evaluations
        $recentEvaluations = $this->evaluationService->getStudentEvaluations($child->id);
        
        // Get monthly averages for the current year
        $monthlyAverages = $this->evaluationService->getStudentMonthlyAverages($child->id, now()->year);
        
        // Calculate yearly average
        $yearlyAverage = $monthlyAverages->isNotEmpty() ? round($monthlyAverages->avg()) : 0;
        
        // Statistics
        $stats = [
            'total_courses' => $enrollments->count(),
            'active_courses' => $enrollments->where('status', 'active')->count(),
            'total_sessions' => Schedule::where('student_id', $child->id)->count(),
            'completed_sessions' => Schedule::where('student_id', $child->id)
                ->where('status', 'completed')->count(),
            'attendance_rate' => $this->calculateAttendanceRate($child->id),
            'total_reports' => $recentEvaluations->count(),
        ];
        
        return view('parent.children.show', compact(
            'parent',
            'child',
            'enrollments',
            'recentSchedules',
            'recentEvaluations',
            'monthlyAverages',
            'yearlyAverage',
            'stats'
        ));
    }
    
    private function calculateAttendanceRate($studentId)
    {
        // Count total schedules (all scheduled sessions)
        $totalSchedules = Schedule::where('student_id', $studentId)->count();
        
        // Count how many times student was present
        $presentCount = Attendance::where('student_id', $studentId)
            ->where('student_present', true)
            ->count();
        
        return $totalSchedules > 0 ? round(($presentCount / $totalSchedules) * 100) : 0;
    }
}
