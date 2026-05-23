<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'Accountant']);
    Role::create(['name' => 'Student']);

    $this->accountant = User::factory()->create([
        'role' => 'Accountant',
        'can_access_payroll' => true,
        'allowed_countries' => ['Egypt'],
    ]);
    $this->accountant->assignRole('Accountant');

    $this->student = User::factory()->create([
        'name' => 'Jane Student',
        'email' => 'jane.student@example.com',
        'country' => 'Egypt',
        'role' => 'Student',
    ]);
    $this->student->assignRole('Student');

    // Create course manually if factory doesn't exist
    $this->course = Course::create([
        'title' => 'Math 101',
        'description' => 'Math course',
    ]);

    $this->enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'days_per_week' => 2,
        'session_duration' => 60,
    ]);

    $this->payment = EnrollmentPayment::create([
        'enrollment_id' => $this->enrollment->id,
        'month' => now()->startOfMonth(),
        'amount' => 100,
        'payment_status' => 'unpaid',
    ]);
});

test('accountant can view payments', function () {
    $this->actingAs($this->accountant);

    $response = $this->get(route('accountant.payments.index'));

    $response->assertStatus(200);
    $response->assertSee('Jane Student');
});

test('accountant can search payments', function () {
    $this->actingAs($this->accountant);

    $response = $this->get(route('accountant.payments.index', ['search' => 'Jane']));

    $response->assertStatus(200);
    $response->assertSee('Jane Student');

    $response2 = $this->get(route('accountant.payments.index', ['search' => 'John']));
    $response2->assertDontSee('Jane Student');
});

test('accountant can update payment status', function () {
    $this->actingAs($this->accountant);

    $response = $this->patch(route('accountant.payments.update-status', $this->payment), [
        'payment_status' => 'paid',
        'notes' => 'Paid in full',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Payment status updated successfully.');

    $this->assertDatabaseHas('enrollment_payments', [
        'id' => $this->payment->id,
        'payment_status' => 'paid',
        'notes' => 'Paid in full',
    ]);
});

test('accountant cannot access student from unauthorized country', function () {
    $this->student->update(['country' => 'Canada']);
    // Accountant only has access to Egypt (set in beforeEach)

    $this->actingAs($this->accountant);

    $response = $this->get(route('accountant.payments.show', $this->enrollment));

    $response->assertStatus(403);
});
