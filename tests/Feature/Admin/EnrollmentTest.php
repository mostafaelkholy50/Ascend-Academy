<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;
    protected $course;

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

        $this->course = Course::create(['title' => 'Test Course']);
    }

    public function test_admin_can_view_enrollments_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.enrollments.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_enrollment_create_page()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.enrollments.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_store_enrollment()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.enrollments.store'), [
            'student_id' => $this->student->id,
            'courses' => [$this->course->id],
            'status' => 'active',
            'days_per_week' => 3,
            'session_duration' => 60,
            'admin_price' => 100,
            'currency' => 'USD',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }

    public function test_store_enrollment_validation_fails()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.enrollments.store'), [
            'student_id' => $this->student->id,
            // missing courses
        ]);

        $response->assertSessionHasErrors(['courses']);
    }
}
