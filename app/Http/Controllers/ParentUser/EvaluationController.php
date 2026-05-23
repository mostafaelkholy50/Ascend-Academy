<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViewEvaluationRequest;
use App\Services\StudentEvaluationService;
use App\Models\User;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    protected $evaluationService;

    public function __construct(StudentEvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function show(ViewEvaluationRequest $request, $childId)
    {
        $parent = auth()->user();
        
        // Verify child belongs to parent
        $child = $parent->children()->findOrFail($childId);
        
        // Get month and year from request or use current
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        // Fetch monthly evaluation
        $evaluation = $this->evaluationService->getEvaluationByMonth($child->id, $month, $year);
        
        // Fetch aggregate scores
        $aggregates = $this->evaluationService->getAggregateScores($child->id);
        
        // Navigation dates
        $currentDate = Carbon::create($year, $month, 1);
        $prevMonth = (clone $currentDate)->subMonth();
        $nextMonth = (clone $currentDate)->addMonth();
        
        $questions = [
            'q1' => 'Attendance & Punctuality',
            'q2' => 'Participation & Engagement',
            'q3' => 'Homework Completion',
            'q4' => 'Understanding & Comprehension',
            'q5' => 'Behavior & Discipline',
            'q6' => 'Focus & Attention',
            'q7' => 'Interaction with Teacher',
            'q8' => 'Progress & Improvement',
            'q9' => 'Effort & Motivation',
            'q10' => 'Retention of Previous Lessons',
        ];
        
        return view('parent.evaluations.show', compact(
            'child',
            'evaluation',
            'aggregates',
            'month',
            'year',
            'prevMonth',
            'nextMonth',
            'questions'
        ));
    }
}
