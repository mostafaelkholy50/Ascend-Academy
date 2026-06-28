<?php

test('class reminders are scheduled to run at midnight', function () {
    $consoleRoutes = file_get_contents(base_path('routes/console.php'));

    expect($consoleRoutes)->toContain("Schedule::command('class:send-reminders')->dailyAt('00:00');");
});

test('generate missing schedules is scheduled correctly', function () {
    $consoleRoutes = file_get_contents(base_path('routes/console.php'));

    expect($consoleRoutes)->toContain("Schedule::command('schedules:generate-missing')");
    expect($consoleRoutes)->toContain("->dailyAt('00:00')");
    expect($consoleRoutes)->toContain("->when(function () {");
});
