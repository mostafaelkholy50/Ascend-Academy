<?php

use App\Enums\UserRole;

test('user role enum exposes all supported roles', function () {
    expect(UserRole::values())->toBe([
        'SuperAdmin',
        'Admin',
        'SchedulerManager',
        'Teacher',
        'Student',
        'Parent',
        'Accountant',
        'QualityControl',
    ]);
});
