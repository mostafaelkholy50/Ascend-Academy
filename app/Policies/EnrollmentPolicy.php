<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->hasRole('Student')) {
            return $user->id === $enrollment->student_id;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('users.id', $enrollment->student_id)->exists();
        }

        return false;
    }
}
