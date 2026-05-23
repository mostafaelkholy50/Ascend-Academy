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
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            return route('teacher.dashboard');
        } elseif ($user->isStudent()) {
            return route('student.dashboard');
        } elseif ($user->isParent()) {
            return route('parent.dashboard');
        }

        return route('home'); // Fallback
    }
}
