<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest\RegisterRequest;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }
 function store(RegisterRequest $request)
 {
        // Validate the incoming request data
        $validated = $request->validated();

        // Create a new user (assuming you have a User model)
       User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Redirect to a desired location after registration
        return redirect()->route('home')->with('success', 'Registration successful! You can now log in.');
 }
}
