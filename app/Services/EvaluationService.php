<?php

namespace App\Services;

use App\Repositories\EvaluationRepository;
use App\Models\User;
use App\Models\TeacherEvaluation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EvaluationService
{
    protected $repository;

    public function __construct(EvaluationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPerformanceData(Collection $teachers): Collection
    {
        $teacherIds = $teachers->pluck('id');
        $startOfWeek = now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
        
        $stats = $this->repository->getPerformanceStats($teacherIds, $startOfWeek);

        return $teachers->map(function ($teacher) use ($stats) {
            $teacherStats = $stats->get($teacher->id);

            return (object) [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'total_evals' => $teacherStats->total_evals ?? 0,
                'avg_score' => $teacherStats->avg_score ?? 0,
                'monthly_avg' => $teacherStats->monthly_avg ?? 0,
                'yearly_avg' => $teacherStats->yearly_avg ?? 0,
                'has_eval_this_week' => isset($teacherStats->has_eval_this_week) ? (bool)$teacherStats->has_eval_this_week : false,
                'last_eval_date' => $teacherStats->last_eval_date ?? null,
            ];
        })->sortByDesc('avg_score');
    }

    public function storeEvaluation(User $teacher, array $data, int $evaluatorId): TeacherEvaluation
    {
        $totalScore = $data['q1_score'] + $data['q2_score'] + $data['q3_score'] + 
                      $data['q4_score'] + $data['q5_score'] + $data['q6_score'] +
                      $data['q7_score'] + $data['q8_score'] + $data['q9_score'] + $data['q10_score'];

        $startOfWeek = now()->startOfWeek(Carbon::SATURDAY);

        return $this->repository->updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'week_start_date' => $startOfWeek->format('Y-m-d'),
            ],
            [
                'evaluator_id' => $evaluatorId,
                'evaluation_date' => now()->format('Y-m-d'),
                'q1_score' => $data['q1_score'],
                'q2_score' => $data['q2_score'],
                'q3_score' => $data['q3_score'],
                'q4_score' => $data['q4_score'],
                'q5_score' => $data['q5_score'],
                'q6_score' => $data['q6_score'],
                'q7_score' => $data['q7_score'],
                'q8_score' => $data['q8_score'],
                'q9_score' => $data['q9_score'],
                'q10_score' => $data['q10_score'],
                'total_score' => $totalScore,
                'notes' => $data['notes'] ?? null,
            ]
        );
    }

    public function getTeacherReportData(User $teacher): array
    {
        $evaluations = $this->repository->getTeacherEvaluations($teacher->id);
        $monthlyAverages = $this->repository->getMonthlyAverages($teacher->id);
        $yearlyAverages = $this->repository->getYearlyAverages($teacher->id);

        $currentMonthAvg = $evaluations->filter(fn($e) => $e->week_start_date->isCurrentMonth())->avg('total_score');
        $currentYearAvg = $evaluations->filter(fn($e) => $e->week_start_date->isCurrentYear())->avg('total_score');

        return compact(
            'evaluations',
            'monthlyAverages',
            'yearlyAverages',
            'currentMonthAvg',
            'currentYearAvg'
        );
    }
}
