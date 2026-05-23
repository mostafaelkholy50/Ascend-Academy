<?php

namespace App\Repositories;

use App\Models\TeacherEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Filters\EvaluationFilter;

class EvaluationRepository
{
    protected $filter;

    public function __construct(EvaluationFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getEvaluationsQuery(Request $request): Builder
    {
        $query = TeacherEvaluation::with(['teacher', 'evaluator'])->orderBy('week_start_date', 'desc');
        
        $this->filter->apply($query, $request);
        
        return $query;
    }

    public function getTeacherEvaluations(int $teacherId): Collection
    {
        return TeacherEvaluation::where('teacher_id', $teacherId)
            ->orderBy('week_start_date', 'desc')
            ->get();
    }

    public function getMonthlyAverages(int $teacherId): Collection
    {
        $isSqlite = config('database.default') === 'sqlite';
        
        if ($isSqlite) {
            return TeacherEvaluation::where('teacher_id', $teacherId)
                ->selectRaw("strftime('%Y', week_start_date) as year, strftime('%m', week_start_date) as month, AVG(total_score) as average")
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        }

        return TeacherEvaluation::where('teacher_id', $teacherId)
            ->selectRaw('YEAR(week_start_date) as year, MONTH(week_start_date) as month, AVG(total_score) as average')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    public function getYearlyAverages(int $teacherId): Collection
    {
        $isSqlite = config('database.default') === 'sqlite';
        
        if ($isSqlite) {
            return TeacherEvaluation::where('teacher_id', $teacherId)
                ->selectRaw("strftime('%Y', week_start_date) as year, AVG(total_score) as average")
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get();
        }

        return TeacherEvaluation::where('teacher_id', $teacherId)
            ->selectRaw('YEAR(week_start_date) as year, AVG(total_score) as average')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();
    }

    public function updateOrCreate(array $attributes, array $values): TeacherEvaluation
    {
        return TeacherEvaluation::updateOrCreate($attributes, $values);
    }

    public function getExistingEvaluation(int $teacherId, string $weekStartDate): ?TeacherEvaluation
    {
        return TeacherEvaluation::where('teacher_id', $teacherId)
            ->where('week_start_date', $weekStartDate)
            ->first();
    }

    public function getPerformanceStats(Collection $teacherIds, string $startOfWeek): Collection
    {
        $isSqlite = config('database.default') === 'sqlite';
        $currentMonth = now()->month;
        $currentYear = now()->year;

        if ($isSqlite) {
            $currentMonthStr = sprintf('%02d', $currentMonth);
            $currentYearStr = (string)$currentYear;

            return TeacherEvaluation::whereIn('teacher_id', $teacherIds)
                ->selectRaw("teacher_id, 
                    COUNT(*) as total_evals, 
                    AVG(total_score) as avg_score,
                    AVG(CASE WHEN strftime('%m', week_start_date) = ? AND strftime('%Y', week_start_date) = ? THEN total_score ELSE NULL END) as monthly_avg,
                    AVG(CASE WHEN strftime('%Y', week_start_date) = ? THEN total_score ELSE NULL END) as yearly_avg,
                    MAX(CASE WHEN week_start_date = ? THEN 1 ELSE 0 END) as has_eval_this_week,
                    MAX(evaluation_date) as last_eval_date", [$currentMonthStr, $currentYearStr, $currentYearStr, $startOfWeek])
                ->groupBy('teacher_id')
                ->get()
                ->keyBy('teacher_id');
        }

        return TeacherEvaluation::whereIn('teacher_id', $teacherIds)
            ->selectRaw('teacher_id, 
                COUNT(*) as total_evals, 
                AVG(total_score) as avg_score,
                AVG(CASE WHEN MONTH(week_start_date) = ? AND YEAR(week_start_date) = ? THEN total_score ELSE NULL END) as monthly_avg,
                AVG(CASE WHEN YEAR(week_start_date) = ? THEN total_score ELSE NULL END) as yearly_avg,
                MAX(CASE WHEN week_start_date = ? THEN 1 ELSE 0 END) as has_eval_this_week,
                MAX(evaluation_date) as last_eval_date', [$currentMonth, $currentYear, $currentYear, $startOfWeek])
            ->groupBy('teacher_id')
            ->get()
            ->keyBy('teacher_id');
    }
}
