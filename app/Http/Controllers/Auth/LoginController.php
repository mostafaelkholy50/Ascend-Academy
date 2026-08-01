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
        request()->session()->regenerateToken();

        return response()
            ->view('auth.login')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
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
