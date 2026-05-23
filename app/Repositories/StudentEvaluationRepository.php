<?php

namespace App\Repositories;

use App\Models\StudentEvaluation;
use App\Models\User;
use Illuminate\Support\Collection;

class StudentEvaluationRepository
{
    public function create(array $data): StudentEvaluation
    {
        return StudentEvaluation::create($data);
    }

    public function update(StudentEvaluation $evaluation, array $data): bool
    {
        return $evaluation->update($data);
    }

    public function delete(StudentEvaluation $evaluation): bool
    {
        return $evaluation->delete();
    }

    public function find(int $id): ?StudentEvaluation
    {
        return StudentEvaluation::find($id);
    }

    public function getPendingEvaluationsForTeacher(User $teacher, int $month, int $year): Collection
    {
        $evaluatedStudentIds = StudentEvaluation::where('teacher_id', $teacher->id)
            ->where('evaluation_month', $month)
            ->where('evaluation_year', $year)
            ->pluck('student_id');

        return User::whereHas('schedules', function ($query) use ($teacher, $month, $year) {
                $query->where('teacher_id', $teacher->id)
                      ->whereMonth('starts_at', $month)
                      ->whereYear('starts_at', $year);
            })
            ->whereNotIn('id', $evaluatedStudentIds)
            ->get();
    }

    public function getStudentMonthlyScores(int $studentId, int $year): Collection
    {
        return StudentEvaluation::where('student_id', $studentId)
            ->where('evaluation_year', $year)
            ->orderBy('evaluation_month')
            ->get();
    }

    public function getStudentEvaluations(int $studentId): Collection
    {
        return StudentEvaluation::with(['teacher', 'course'])
            ->where('student_id', $studentId)
            ->orderBy('evaluation_date', 'desc')
            ->get();
    }

    public function getTeacherEvaluations(User $teacher): Collection
    {
        return StudentEvaluation::with(['student'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('evaluation_date', 'desc')
            ->get();
    }

    public function getAggregateScores(int $studentId): array
    {
        $evaluations = StudentEvaluation::where('student_id', $studentId)->get();
        
        if ($evaluations->isEmpty()) {
            return [];
        }

        $aggregates = [];
        for ($i = 1; $i <= 10; $i++) {
            $aggregates["q{$i}_score"] = round($evaluations->avg("q{$i}_score"), 1);
        }
        $aggregates['total_score'] = round($evaluations->avg('total_score'), 1);
        $aggregates['count'] = $evaluations->count();

        return $aggregates;
    }

    public function getEvaluationByMonth(int $studentId, int $month, int $year): ?StudentEvaluation
    {
        return StudentEvaluation::with(['teacher'])
            ->where('student_id', $studentId)
            ->where('evaluation_month', $month)
            ->where('evaluation_year', $year)
            ->first();
    }

    public function getEvaluationsQuery()
    {
        return StudentEvaluation::with(['student', 'teacher'])->latest('evaluation_date');
    }
}
