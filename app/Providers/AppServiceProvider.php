<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        \App\Models\Attendance::observe(\App\Observers\AttendanceObserver::class);
        
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        // Implicitly grant "SuperAdmin" role all permissions
        // This works in the app by using gate-related functions like auth()->user()->can() and @can()
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('SuperAdmin') ? true : null;
        });

        \Illuminate\Support\Facades\View::share('countries', ['Canada', 'USA', 'UK', 'Egypt', 'KSA', 'UAE', 'Australia', 'Germany', 'France']);

        // Protect against Hostinger's anti-abuse filter:
        // 1) Suppress identical messages repeated within the dedup window
        //    (queue retries, overlapping cron runs, double-picked jobs).
        // 2) Pace bursts: only pause once several emails go out in the same minute.
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Mail\Events\MessageSending::class,
            function (\Illuminate\Mail\Events\MessageSending $event) {
                $message = $event->message;

                $recipients = collect($message->getTo())
                    ->map(fn ($address) => $address->getAddress())
                    ->implode(',');

                $fingerprint = sha1($recipients.'|'.$message->getSubject().'|'.($message->getHtmlBody() ?? '').'|'.($message->getTextBody() ?? ''));

                if (! \Illuminate\Support\Facades\Cache::add('mail_dedup:'.$fingerprint, true, now()->addMinutes(15))) {
                    logger()->warning('Duplicate email suppressed by dedup guard.', [
                        'to' => $recipients,
                        'subject' => $message->getSubject(),
                    ]);

                    return false;
                }

                $minuteBucket = now()->format('YmdHi');
                $paceKey = 'mail_pace:'.$minuteBucket;
                $sentThisMinute = (int) \Illuminate\Support\Facades\Cache::get($paceKey, 0);
                \Illuminate\Support\Facades\Cache::put($paceKey, $sentThisMinute + 1, now()->addMinute());

                if ($sentThisMinute >= 5) {
                    sleep(2);
                }
            }
        );

        $this->applySafeLocale(request());
    }

    /**
     * Apply a sanitized locale from the request header.
     *
     * Browsers may send headers like "en_US,en;q=0.7" or "en-US,en;q=0.7".
     * Carbon and Symfony expect a single locale token, so we keep only the
     * first language tag and normalize it to a safe format.
     */
    protected function applySafeLocale(?Request $request): void
    {
        if (!$request) {
            return;
        }

        $header = (string) $request->header('Accept-Language', '');
        if ($header === '') {
            return;
        }

        $candidate = trim(explode(',', $header)[0] ?? '');
        if ($candidate === '') {
            return;
        }

        $candidate = str_replace('-', '_', $candidate);

        if (!preg_match('/^[a-z]{2}(?:_[A-Z]{2})?$/', $candidate)) {
            return;
        }

        App::setLocale($candidate);
        Carbon::setLocale($candidate);
    }
}
