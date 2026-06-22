<?php

test('class reminders are scheduled to run at midnight', function () {
    $consoleRoutes = file_get_contents(base_path('routes/console.php'));

    expect($consoleRoutes)->toContain("Schedule::command('class:send-reminders')->dailyAt('00:00');");
});
