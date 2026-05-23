<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Collection;

class StudentCourseRepository
{
    public function getEnrollments(User $student): Collection
    {
        return Enrollment::with(['course'])
            ->where('student_id', $student->id)
            ->get();
    }

    public function getCourseStats(User $student): Collection
    {
        return Schedule::where('student_id', $student->id)
            ->selectRaw('course_id, 
                COUNT(*) as total, 
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "scheduled" AND starts_at > ? THEN 1 ELSE 0 END) as upcoming', [now()])
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');
    }

    public function getMasteryScores(User $student): Collection
    {
        return Report::where('student_id', $student->id)
            ->whereNotNull('mastery_score')
            ->selectRaw('course_id, AVG(mastery_score) as avg_score')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');
    }

    public function getNextSessions(User $student): Collection
    {
        return Schedule::with(['teacher'])
            ->where('student_id', $student->id)
            ->where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->get()
            ->groupBy('course_id')
            ->map(function($schedules) {
                return $schedules->first();
            });
    }
}
