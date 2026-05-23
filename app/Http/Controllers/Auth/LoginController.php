<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }
    public function store(LoginRequest $request, AuthService $authService)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        
        // Handle post-login actions (timezone and redirect route)
        $redirectUrl = $authService->afterLogin($user, $request);

        return redirect()->intended($redirectUrl)->with('success', 'Login successful!');
    }
}
