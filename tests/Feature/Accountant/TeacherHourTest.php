<?php

use App\Models\User;
use App\Models\TeacherHour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'Accountant']);
    Role::create(['name' => 'Teacher']);
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $this->accountant = User::factory()->create([
        'role' => 'Accountant',
        'can_access_payroll' => true,
    ]);
    $this->accountant->assignRole('Accountant');

    $this->teacher = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'Teacher',
    ]);
    $this->teacher->assignRole('Teacher');
});

test('login page works', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('accountant can view teacher hours', function () {
    $this->actingAs($this->accountant);

    $response = $this->get(route('accountant.teacher-hours.index'));
    
    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertSee(route('accountant.teacher-hours.show', $this->teacher->id));
});

test('accountant can search teachers', function () {
    $this->actingAs($this->accountant);

    $response = $this->get(route('accountant.teacher-hours.index', ['search' => 'John']));

    $response->assertStatus(200);
    $response->assertSee('John Doe');

    $response2 = $this->get(route('accountant.teacher-hours.index', ['search' => 'Jane']));
    $response2->assertDontSee('John Doe');
});

test('accountant can mark payroll as paid', function () {
    $this->actingAs($this->accountant);

    $response = $this->post(route('accountant.teacher-hours.mark-paid'), [
        'teacher_id' => $this->teacher->id,
        'month' => 5,
        'year' => 2026,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Payroll marked as paid.');

    $this->assertDatabaseHas('teacher_hours', [
        'teacher_id' => $this->teacher->id,
        'month' => 5,
        'year' => 2026,
        'is_paid' => true,
    ]);
});

test('accountant can mark payroll as unpaid', function () {
    $this->actingAs($this->accountant);

    // Create a record first
    TeacherHour::create([
        'teacher_id' => $this->teacher->id,
        'month' => 5,
        'year' => 2026,
        'is_paid' => true,
        'total_hours' => 10,
        'total_salary' => 100,
    ]);

    $response = $this->post(route('accountant.teacher-hours.mark-unpaid'), [
        'teacher_id' => $this->teacher->id,
        'month' => 5,
        'year' => 2026,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Payroll marked as unpaid.');

    $this->assertDatabaseHas('teacher_hours', [
        'teacher_id' => $this->teacher->id,
        'month' => 5,
        'year' => 2026,
        'is_paid' => false,
    ]);
});

test('unauthorized user cannot access payroll', function () {
    $user = User::factory()->create(['can_access_payroll' => false]);
    // No role assigned, should be blocked by middleware or controller

    $this->actingAs($user);

    $response = $this->get(route('accountant.teacher-hours.index'));

    // If blocked by middleware (role:Accountant), it returns 403 or redirects.
    // If redirected to login (because not accountant), it might return 404 if login not defined.
    // Let's assume it should return 403 if we assign a different role or just don't have the role.
    // Let's assert status is 403 or 302.
    // To be safe, let's create a user with a different role or just without the Accountant role.
    
    $response->assertStatus(403);
});

test('teacher hours are calculated on index load', function () {
    $this->actingAs($this->accountant);

    $student = User::factory()->create(['role' => 'Student']);
    $course = \App\Models\Course::create(['title' => 'Test Course']);

    $schedule = \App\Models\Schedule::create([
        'teacher_id' => $this->teacher->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'starts_at' => now()->startOfMonth()->addHours(10),
        'ends_at' => now()->startOfMonth()->addHours(11),
        'status' => 'completed',
    ]);

    \App\Models\Attendance::create([
        'schedule_id' => $schedule->id,
        'teacher_id' => $this->teacher->id,
        'student_id' => $student->id,
        'teacher_present' => true,
        'student_present' => true,
    ]);

    $response = $this->get(route('accountant.teacher-hours.index'));

    $response->assertStatus(200);

    $this->assertDatabaseHas('teacher_hours', [
        'teacher_id' => $this->teacher->id,
        'month' => now()->month,
        'year' => now()->year,
        'total_hours' => 1.0,
    ]);
});

test('accountant can view specific teacher detailed hours', function () {
    $this->actingAs($this->accountant);

    $response = $this->get(route('accountant.teacher-hours.show', $this->teacher->id));
    
    $response->assertStatus(200);
    $response->assertSee($this->teacher->name . "'s Hours");
});
