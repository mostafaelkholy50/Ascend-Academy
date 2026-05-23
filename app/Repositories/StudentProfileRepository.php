<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Report;
use Illuminate\Support\Facades\Hash;

class StudentProfileRepository
{
    public function updateProfile(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function updatePassword(User $user, string $password): bool
    {
        return $user->update([
            'password' => Hash::make($password),
        ]);
    }

    public function updateAvatar(User $user, ?string $path): bool
    {
        return $user->update(['avatar' => $path]);
    }

    public function getStats(User $user): array
    {
        return [
            'total_courses' => Enrollment::where('student_id', $user->id)->count(),
            'completed_sessions' => Schedule::where('student_id', $user->id)
                ->where('status', 'completed')->count(),
            'total_reports' => Report::where('student_id', $user->id)->count(),
            'member_since' => $user->created_at->format('F Y'),
        ];
    }
}
