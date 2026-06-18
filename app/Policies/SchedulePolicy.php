<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SchedulePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Schedule $schedule): bool
    {
        if ($user->hasRole('Teacher')) {
            return $user->id === $schedule->teacher_id;
        }

        if ($user->hasRole('Student')) {
            return $user->id === $schedule->student_id;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('users.id', $schedule->student_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Schedule $schedule): bool
    {
        if ($user->hasRole('Teacher')) {
            return $user->id === $schedule->teacher_id;
        }

        return false;
    }
}
