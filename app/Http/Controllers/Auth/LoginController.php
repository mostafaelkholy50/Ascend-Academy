<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest\LoginRequest;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }
    public function store(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Attempt to log the user in
        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            // Auto-detect and set timezone for students/parents on first login
            if (($user->isStudent() || $user->isParent())) {
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

            // Authentication passed - redirect to dashboard
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Login successful!');
            } elseif ($user->isTeacher()) {
                return redirect()->intended(route('teacher.dashboard'))->with('success', 'Login successful!');
            } elseif ($user->isStudent()) {
                return redirect()->intended(route('student.dashboard'))->with('success', 'Login successful!');
            } elseif ($user->isParent()) {
                return redirect()->intended(route('parent.dashboard'))->with('success', 'Login successful!');
            }
        }

        // Authentication failed...
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'email' => ['required', 'email'],
    //         'password' => ['required'],
    //     ]);

    //  User::create([
    //         'name' => 'Admin',
    //         'email' => $validated['email'],
    //         'password' => bcrypt($validated['password']),
    //     ]);

    //     // Redirect to a desired location after registration
    //     return redirect()->back()->with('success', 'Registration successful! You can now log in.');
    // }
}
