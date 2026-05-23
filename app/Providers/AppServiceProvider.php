<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        // Implicitly grant "SuperAdmin" role all permissions
        // This works in the app by using gate-related functions like auth()->user()->can() and @can()
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('SuperAdmin') ? true : null;
        });

        \Illuminate\Support\Facades\View::share('countries', ['Canada', 'USA', 'UK', 'Egypt', 'KSA', 'UAE', 'Australia', 'Germany', 'France']);
    }
}
