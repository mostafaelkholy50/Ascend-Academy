<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Teacher']);
        Role::create(['name' => 'Student']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create(['role' => 'Teacher']);
        $this->teacher->assignRole('Teacher');

        $this->student = User::factory()->create(['role' => 'Student']);
        $this->student->assignRole('Student');

        $course = \App\Models\Course::create(['title' => 'Test Course']);

        $this->schedule = Schedule::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'course_id' => $course->id,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(2),
            'status' => 'scheduled',
        ]);
    }

    public function test_admin_can_view_attendance_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.attendances.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_attendance_create_page()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('scheduler.attendance.create')); // Using scheduler route name as it points to consolidated controller

        $response->assertStatus(200);
    }

    public function test_admin_can_store_attendance()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('scheduler.attendance.store'), [
            'schedule_id' => $this->schedule->id,
            'student_present' => true,
            'teacher_present' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $this->schedule->id,
            'student_present' => true,
            'teacher_present' => true,
        ]);

        $this->assertEquals('completed', $this->schedule->fresh()->status);
    }

    public function test_store_attendance_validation_fails()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('scheduler.attendance.store'), [
            'schedule_id' => $this->schedule->id,
            'student_present' => false,
            // missing teacher_present
        ]);

        $response->assertSessionHasErrors(['teacher_present']);
    }

    public function test_admin_can_verify_attendance()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('scheduler.attendance.verify', $this->schedule->id));

        $response->assertStatus(200);
    }
}
