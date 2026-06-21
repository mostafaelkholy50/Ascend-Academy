<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;



// Schedule class reminders to run daily at 8:00 AM
Schedule::command('class:send-reminders')->dailyAt('08:00');

// Schedule payment reminders to run weekly (every Monday at 9:00 AM)
Schedule::command('payment:send-reminders')->weeklyOn(1, '09:00');

// Generate missing monthly schedules on the 1st of every month at midnight
Schedule::command('schedules:generate-missing')->monthlyOn(1, '00:00');
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
