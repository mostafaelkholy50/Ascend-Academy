<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentEvaluation;
use App\Services\StudentEvaluationService;
use Illuminate\Http\Request;
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
            $user = auth()->user();
            if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'QualityControl'])) {
                abort(403, 'Unauthorized action.');
            }

            $data = $this->service->getIndexData($request, 15);

            return view('admin.student-evaluations.index', $data);
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while loading evaluations.');
        }
    }

    public function show(StudentEvaluation $studentEvaluation)
    {
        try {
            $user = auth()->user();
            if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'QualityControl'])) {
                abort(403, 'Unauthorized action.');
            }

            $studentEvaluation->load(['student', 'teacher']);

            return view('admin.student-evaluations.show', compact('studentEvaluation'));
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while displaying the evaluation.');
        }
    }
}
