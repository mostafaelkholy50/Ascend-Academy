<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * Handle actions after a successful login.
     *
     * @param User $user
     * @param Request $request
     * @return string Redirect route path
     */
    public function afterLogin(User $user, Request $request): string
    {
        // Auto-detect and set timezone for students/parents on first login
        if ($user->isStudent() || $user->isParent()) {
            $detectedTimezone = $request->input('timezone', 'Africa/Cairo');

            // Validate timezone (basic check)
            $validTimezones = \DateTimeZone::listIdentifiers();
            if (in_array($detectedTimezone, $validTimezones)) {
                $user->update(['timezone' => $detectedTimezone]);
            } else {
                // Fallback to Egypt timezone if invalid
                $user->update(['timezone' => 'Africa/Cairo']);
            }
        }

        // Determine redirect route based on role
        if ($this->hasRole($user, 'SuperAdmin')) {
            return route('superadmin.index');
        }

        if ($this->hasRole($user, 'Admin')) {
            return route('admin.dashboard');
        }

        if ($this->hasRole($user, 'SchedulerManager')) {
            return route('scheduler.dashboard');
        }

        if ($this->hasRole($user, 'Teacher')) {
            return route('teacher.schedule.index');
        }

        if ($this->hasRole($user, 'Student')) {
            return route('student.dashboard');
        }

        if ($this->hasRole($user, 'Parent')) {
            return route('parent.dashboard');
        }

        if ($this->hasRole($user, 'Accountant')) {
            return route('accountant.dashboard');
        }

        if ($this->hasRole($user, 'QualityControl')) {
            return route('qualitycontrol.dashboard');
        }

        return route('home'); // Fallback
    }

    private function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role) || strcasecmp((string) $user->role, $role) === 0;
    }
}
