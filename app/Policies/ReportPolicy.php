<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report): bool
    {
        if ($user->hasRole('Student')) {
            return $user->id === $report->student_id;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('users.id', $report->student_id)->exists();
        }

        return false;
    }
}
