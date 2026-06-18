<?php

namespace App\Policies;

use App\Models\StudentEvaluation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentEvaluationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentEvaluation $studentEvaluation): bool
    {
        if ($user->hasRole('Teacher')) {
            return $user->id === $studentEvaluation->teacher_id;
        }

        if ($user->hasRole('Student')) {
            return $user->id === $studentEvaluation->student_id;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('users.id', $studentEvaluation->student_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentEvaluation $studentEvaluation): bool
    {
        if ($user->hasRole('Teacher')) {
            return $user->id === $studentEvaluation->teacher_id;
        }

        return false;
    }
}
