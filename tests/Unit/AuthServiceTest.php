<?php

use App\Models\User;
use App\Services\AuthService;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => UserRole::Teacher->value]);
});

test('teacher login redirects to the schedule page', function () {
    $teacher = User::factory()->create([
        'role' => UserRole::Teacher->value,
    ]);
    $teacher->assignRole(UserRole::Teacher->value);

    $request = Request::create('/login', 'POST', [
        'timezone' => 'Africa/Cairo',
    ]);

    $service = app(AuthService::class);

    expect($service->afterLogin($teacher, $request))
        ->toBe(route('teacher.schedule.index'));
});
