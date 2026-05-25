<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class EnrollmentPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;
    protected $enrollment;
    protected $payment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Student']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->student = User::factory()->create(['role' => 'Student']);
        $this->student->assignRole('Student');

        $course = Course::create(['title' => 'Test Course']);

        $this->enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'admin_price' => 100,
            'currency' => 'USD',
            'start_date' => now(),
        ]);

        $this->payment = EnrollmentPayment::create([
            'enrollment_id' => $this->enrollment->id,
            'month' => now()->startOfMonth(),
            'amount' => 100,
            'currency' => 'USD',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_admin_can_view_payments_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('accountant.payments.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_enrollment_payments()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('accountant.payments.show', $this->enrollment->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_payment_status()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('accountant.payments.update-status', $this->payment->id), [
            'payment_status' => 'paid',
            'notes' => 'Paid in full',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollment_payments', [
            'id' => $this->payment->id,
            'payment_status' => 'paid',
        ]);
    }
}
