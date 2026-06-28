<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;



// Schedule class reminders to run daily at midnight
Schedule::command('class:send-reminders')->dailyAt('00:00');

// Schedule payment reminders to run weekly (every Monday at 9:00 AM)
Schedule::command('payment:send-reminders')->weeklyOn(1, '09:00');

// Generate missing monthly schedules 3 days before the end of the month at midnight
Schedule::command('schedules:generate-missing')
    ->dailyAt('00:00')
    ->when(function () {
        $daysInMonth = now()->daysInMonth;
        $targetDay = min(28, $daysInMonth - 2);
        return now()->day === $targetDay;
    });
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
