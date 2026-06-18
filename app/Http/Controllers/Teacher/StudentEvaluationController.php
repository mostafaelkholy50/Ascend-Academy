<?php

namespace App\Http\Controllers\Teacher;

use App\Models\StudentEvaluation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StudentEvaluationService;
use App\Http\Requests\StoreStudentEvaluationRequest;
use Exception;

class StudentEvaluationController extends Controller
{
    protected $service;

    public function __construct(StudentEvaluationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $teacher = auth()->user();
            
            // Get teacher's evaluations with filter
            $query = \App\Models\StudentEvaluation::where('teacher_id', $teacher->id)
                ->with(['student'])
                ->latest('evaluation_date');
            
            $query = app(\App\Filters\StudentEvaluationFilter::class)->apply($query, $request);
            $evaluations = $query->paginate(10);
            
            // Get students for this teacher for the filter dropdown
            $students = User::whereHas('schedules', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })->orderBy('name')->get();
            
            return view('teacher.student-evaluations.index', compact('evaluations', 'students'));
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred while loading evaluations.');
        }
    }

    public function pending(Request $request)
    {
        try {
            $teacher = auth()->user();
            $pendingStudents = $this->service->getPendingEvaluations($teacher);
            
            return view('teacher.student-evaluations.pending', compact('pendingStudents'));
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred while loading pending evaluations.');
        }
    }

    public function summary(Request $request)
    {
        try {
            $teacher = auth()->user();
            
            // Get students for this teacher
            $students = User::role('Student')->whereHas('schedules', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id)
                      ->whereMonth('starts_at', now()->month)
                      ->whereYear('starts_at', now()->year);
                })->orderBy('name')->get();
                
            // Calculate totals/averages for each student
            $summaryData = $students->map(function ($student) use ($teacher) {
                $evaluations = \App\Models\StudentEvaluation::where('teacher_id', $teacher->id)
                    ->where('student_id', $student->id)
                    ->get();
                    
                return [
                    'student' => $student,
                    'total_evaluations' => $evaluations->count(),
                    'average_score' => round($evaluations->avg('total_score') ?? 0),
                    'highest_score' => $evaluations->max('total_score') ?? 0,
                ];
            });
            
            return view('teacher.student-evaluations.summary', compact('summaryData'));
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred while loading the evaluation summary.');
        }
    }

    public function create(Request $request)
    {
        try {
            $teacher = auth()->user();
            $pendingStudents = $this->service->getPendingEvaluations($teacher);
            
            $selectedStudent = null;
            if ($request->has('student_id')) {
                $selectedStudent = \App\Models\User::find($request->student_id);
            }
            
            return view('teacher.student-evaluations.create', compact('pendingStudents', 'selectedStudent'));
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred while loading the create page.');
        }
    }

    public function store(StoreStudentEvaluationRequest $request)
    {
        try {
            $teacher = auth()->user();
            $this->service->storeEvaluation($teacher, $request->validated());

            $message = 'Evaluation saved successfully!';
            
            return redirect()->route('teacher.student-evaluations.index')
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to save evaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(StudentEvaluation $studentEvaluation)
    {
        try {
            \Illuminate\Support\Facades\Gate::authorize('view', $studentEvaluation);

            $studentEvaluation->load(['student', 'teacher']);

            return view('teacher.student-evaluations.show', compact('studentEvaluation'));
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('An error occurred while loading the evaluation.');
        }
    }
}
