<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Report;

class TeacherProfileRepository
{
    public function getTeacherStats(int $teacherId): array
    {
        return [
            'total_students' => Schedule::where('teacher_id', $teacherId)
                ->distinct('student_id')->count('student_id'),
            'total_sessions' => Schedule::where('teacher_id', $teacherId)->count(),
            'total_reports' => Report::where('teacher_id', $teacherId)->count(),
        ];
    }

    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
