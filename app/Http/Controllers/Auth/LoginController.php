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
            // Authentication passed...
            if (auth()->user()->is_admin) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Login successful!');
            } elseif (auth()->user()->is_teacher) {
                return redirect()->intended(route('teacher.dashboard'))->with('success', 'Login successful!');
            } elseif (auth()->user()->is_student) {
                return redirect()->intended(route('student.dashboard'))->with('success', 'Login successful!');
            } elseif (auth()->user()->is_parent) {
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
