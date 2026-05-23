<?php

namespace App\Services;

use App\Repositories\StudentEvaluationRepository;
use App\Filters\StudentEvaluationFilter;
use App\Models\StudentEvaluation;
use App\Models\User;
use App\Notifications\StudentEvaluationNotification;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class StudentEvaluationService
{
    protected $repository;
    protected $filter;

    public function __construct(StudentEvaluationRepository $repository, StudentEvaluationFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function storeEvaluation(User $teacher, array $data): StudentEvaluation
    {
        $data['teacher_id'] = $teacher->id;
        $data['evaluation_date'] = now();
        $data['evaluation_month'] = now()->month;
        $data['evaluation_year'] = now()->year;
        
        // Calculate total score
        $data['total_score'] = 0;
        for ($i = 1; $i <= 10; $i++) {
            $data['total_score'] += $data["q{$i}_score"] ?? 0;
        }

        $evaluation = \App\Models\StudentEvaluation::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'student_id' => $data['student_id'],
                'evaluation_month' => $data['evaluation_month'],
                'evaluation_year' => $data['evaluation_year'],
            ],
            $data
        );

        // Bonus Logic: Add 0.5 hours if all evaluations are completed
        $pendingCount = $this->getPendingEvaluations($teacher)->count();
        
        if ($pendingCount == 0) {
            $hasEvaluations = \App\Models\StudentEvaluation::where('teacher_id', $teacher->id)
                ->where('evaluation_month', now()->month)
                ->where('evaluation_year', now()->year)
                ->exists();
                
            if ($hasEvaluations) {
                $teacherHour = \App\Models\TeacherHour::firstOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'year' => now()->year,
                        'month' => now()->month,
                    ],
                    [
                        'total_hours' => 0,
                        'total_salary' => 0,
                        'is_paid' => false,
                    ]
                );
                
                if (!str_contains($teacherHour->notes ?? '', 'Evaluation Bonus')) {
                    $teacherHour->total_hours += 0.5;
                    $teacherHour->notes = ($teacherHour->notes ? $teacherHour->notes . "\n" : '') . 'Evaluation Bonus: +0.5 hours';
                    $teacherHour->save();
                    
                    session()->flash('bonus_success', '🎉 Congratulations! You completed all evaluations and received a 30-minute bonus!');
                }
            }
        }

        // Notify parent
        $student = User::find($data['student_id']);
        if ($student) {
            $parent = $student->parents()->first();
            if ($parent) {
                $parent->notify(new StudentEvaluationNotification($evaluation));
            }
        }

        return $evaluation;
    }

    public function updateEvaluation(StudentEvaluation $evaluation, array $data): bool
    {
        // Recalculate total score if scores are updated
        $scores = ['q1_score', 'q2_score', 'q3_score', 'q4_score', 'q5_score', 'q6_score', 'q7_score', 'q8_score', 'q9_score', 'q10_score'];
        $recalculate = false;
        foreach ($scores as $score) {
            if (isset($data[$score])) {
                $recalculate = true;
                break;
            }
        }

        if ($recalculate) {
            $total = 0;
            for ($i = 1; $i <= 10; $i++) {
                $total += $data["q{$i}_score"] ?? $evaluation->{"q{$i}_score"};
            }
            $data['total_score'] = $total;
        }

        return $this->repository->update($evaluation, $data);
    }

    public function deleteEvaluation(StudentEvaluation $evaluation): bool
    {
        return $this->repository->delete($evaluation);
    }

    public function getPendingEvaluations(User $teacher): Collection
    {
        return $this->repository->getPendingEvaluationsForTeacher($teacher, now()->month, now()->year);
    }

    public function getStudentMonthlyScores(int $studentId, int $year): Collection
    {
        return $this->repository->getStudentMonthlyScores($studentId, $year);
    }

    public function getStudentMonthlyAverages(int $studentId, int $year): Collection
    {
        return $this->repository->getStudentMonthlyScores($studentId, $year)
            ->groupBy('evaluation_month')
            ->map(function ($evaluations) {
                return round($evaluations->avg('total_score'));
            });
    }

    public function getStudentEvaluations(int $studentId): Collection
    {
        return $this->repository->getStudentEvaluations($studentId);
    }

    public function getAggregateScores(int $studentId): array
    {
        return $this->repository->getAggregateScores($studentId);
    }

    public function getEvaluationByMonth(int $studentId, int $month, int $year): ?StudentEvaluation
    {
        return $this->repository->getEvaluationByMonth($studentId, $month, $year);
    }

    public function getTeacherEvaluations(User $teacher): Collection
    {
        return $this->repository->getTeacherEvaluations($teacher);
    }

    public function getIndexData(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getEvaluationsQuery();
        $query = $this->filter->apply($query, $request);

        $evaluations = $query->paginate($perPage);

        // Get filter options
        $students = User::roleStudent()->orderBy('name')->get();
        $teachers = User::roleTeacher()->orderBy('name')->get();

        // Get monthly counts for the selected year
        $currentYear = $request->input('year', now()->year);
        $monthlyCounts = \App\Models\StudentEvaluation::where('evaluation_year', $currentYear)
            ->selectRaw('evaluation_month, count(*) as count')
            ->groupBy('evaluation_month')
            ->pluck('count', 'evaluation_month')
            ->toArray();

        return compact('evaluations', 'students', 'teachers', 'monthlyCounts', 'currentYear');
    }
}
