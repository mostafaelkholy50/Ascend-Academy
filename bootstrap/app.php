<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/superadmin.php'));

            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/scheduler.php'));

            Route::middleware('web')
                ->group(base_path('routes/teacher.php'));

            Route::middleware('web')
                ->group(base_path('routes/student.php'));

            Route::middleware('web')
                ->group(base_path('routes/parent.php'));

            Route::middleware('web')
                ->group(base_path('routes/accountant.php'));

            Route::middleware('web')
                ->group(base_path('routes/qualitycontrol.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'login',
            '/login'
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session expired. Please try again.',
                ], 419);
            }

            if ($request->hasSession()) {
                $request->session()->regenerateToken();
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your session expired. Please refresh the login page and make sure browser cookies are enabled.',
                ])
                ->withHeaders([
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
        });
    })->create();
