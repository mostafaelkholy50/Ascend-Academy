<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Schedule class reminders to run daily at 8:00 AM
Schedule::command('class:send-reminders')->dailyAt('08:00');

// Schedule payment reminders to run monthly on the 1st at 9:00 AM
Schedule::command('payment:send-reminders')->monthlyOn(1, '09:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
