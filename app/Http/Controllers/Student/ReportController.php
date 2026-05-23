<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentReportService;
use Illuminate\Http\Request;
use Exception;

class ReportController extends Controller
{
    protected $service;

    public function __construct(StudentReportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            $data = $this->service->getIndexData($student, $request);

            // Fetch new student evaluations with filters applied
            $query = \App\Models\StudentEvaluation::with(['teacher', 'course'])
                ->where('student_id', $student->id);

            if ($request->filled('course_id')) {
                $query->where('course_id', $request->input('course_id'));
            }
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->input('teacher_id'));
            }
            if ($request->filled('date_from')) {
                $query->where('evaluation_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('evaluation_date', '<=', $request->input('date_to'));
            }

            $data['evaluations'] = $query->latest('evaluation_date')->get();

            return view('student.reports.index', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('An error occurred while loading reports. Please try again.');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            // Check if viewing a new monthly evaluation
            if ($request->input('type') === 'evaluation') {
                $evaluation = \App\Models\StudentEvaluation::with(['teacher', 'course'])
                    ->where('student_id', $student->id)
                    ->findOrFail($id);

                return view('student.reports.show_evaluation', compact('evaluation'));
            }

            $report = $this->service->getReport($student, $id);

            return view('student.reports.show', compact('report'));
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('An error occurred while loading the report. Please try again.');
        }
    }
}
