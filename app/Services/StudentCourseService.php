<?php

namespace App\Services;

use App\Repositories\StudentCourseRepository;
use App\Models\User;
use Illuminate\Support\Collection;

class StudentCourseService
{
    protected $repository;

    public function __construct(StudentCourseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getCoursesData(User $student): Collection
    {
        $enrollments = $this->repository->getEnrollments($student);
        $courseStats = $this->repository->getCourseStats($student);
        $masteryScores = $this->repository->getMasteryScores($student);
        $nextSessions = $this->repository->getNextSessions($student);

        foreach ($enrollments as $enrollment) {
            $stats = $courseStats->get($enrollment->course_id);
            $scores = $masteryScores->get($enrollment->course_id);
            
            $totalSessions = $stats->total ?? 0;
            $completedSessions = $stats->completed ?? 0;
            $upcomingSessions = $stats->upcoming ?? 0;

            $enrollment->progress = $totalSessions > 0
                ? round(($completedSessions / $totalSessions) * 100)
                : 0;
            
            $enrollment->total_sessions = $totalSessions;
            $enrollment->completed_sessions = $completedSessions;
            $enrollment->upcoming_sessions = $upcomingSessions;
            $enrollment->next_session = $nextSessions->get($enrollment->course_id);

            $averageScore = $scores->avg_score ?? null;
            $enrollment->average_mastery = $averageScore ? round($averageScore) : null;
        }

        return $enrollments;
    }
}
