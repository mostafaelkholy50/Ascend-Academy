<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

dataset('loginRoles', [
    ['SuperAdmin', 'superadmin.index'],
    ['Admin', 'admin.dashboard'],
    ['SchedulerManager', 'scheduler.dashboard'],
    ['Teacher', 'teacher.schedule.index'],
    ['Student', 'student.dashboard'],
    ['Parent', 'parent.dashboard'],
    ['Accountant', 'accountant.dashboard'],
    ['QualityControl', 'qualitycontrol.dashboard'],
]);

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'role' => 'Student',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('student.dashboard'));
});

test('users are redirected to the correct dashboard for each role', function (string $role, string $expectedRoute) {
    Role::firstOrCreate(['name' => $role]);

    $user = User::factory()->create([
        'role' => $role,
    ]);
    $user->assignRole($role);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route($expectedRoute));
})->with('loginRoles');

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('users are throttled after five failed login attempts', function () {
    $user = User::factory()->create();

    // 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors('email');
    }

    // 6th attempt should trigger throttle error
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $errors = session('errors')->get('email');
    $this->assertTrue(
        str_contains(implode(' ', $errors), 'Too many login attempts') ||
            str_contains(implode(' ', $errors), 'seconds')
    );
});

test('refresh csrf route returns token', function () {
    $response = $this->get('/refresh-csrf');

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});
