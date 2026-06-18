<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $enrollment;

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

        $course = Course::create(['title' => 'Test Course']);

        $this->enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'session_duration' => 60,
        ]);
    }

    public function test_admin_can_view_schedule_calendar()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.schedules.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_schedule_create_page()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.schedules.create'));

        $response->assertStatus(200);
    }

    public function test_admin_schedule_detail_uses_admin_timezone()
    {
        $this->admin->update(['timezone' => 'Asia/Dubai']);
        $this->actingAs($this->admin);

        $schedule = Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => Carbon::create(2026, 6, 4, 10, 0, 0, 'Africa/Cairo'),
            'ends_at' => Carbon::create(2026, 6, 4, 11, 0, 0, 'Africa/Cairo'),
            'status' => 'scheduled',
        ]);

        $response = $this->get(route('admin.schedules.show', $schedule->id));

        $response->assertStatus(200);
        $response->assertSee('12:00 PM');
        $response->assertDontSee('10:00 AM');
    }

    public function test_admin_can_store_schedule_with_existing_enrollment()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.schedules.store'), [
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'days' => ['Monday'],
            'schedule_times' => ['Monday' => '10:00'],
            'duration_minutes' => 60,
            'start_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
        ]);
    }

    public function test_admin_can_store_schedule_and_auto_create_enrollment()
    {
        $this->actingAs($this->admin);
        
        $newCourse = Course::create(['title' => 'New Course']);

        $response = $this->post(route('admin.schedules.store'), [
            'student_id' => $this->student->id,
            'course_id' => $newCourse->id,
            'teacher_id' => $this->teacher->id,
            'days' => ['Monday'],
            'schedule_times' => ['Monday' => '10:00'],
            'duration_minutes' => 60,
            'start_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        
        // Assert enrollment was created dynamically
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'course_id' => $newCourse->id,
            'status' => 'active',
            'days_per_week' => 1,
            'session_duration' => 60,
        ]);

        $newEnrollment = Enrollment::where('student_id', $this->student->id)
            ->where('course_id', $newCourse->id)
            ->first();

        $this->assertDatabaseHas('schedules', [
            'enrollment_id' => $newEnrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $newCourse->id,
            'teacher_id' => $this->teacher->id,
        ]);
    }

    public function test_store_schedule_validation_fails()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.schedules.store'), [
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            // missing teacher_id
        ]);

        $response->assertSessionHasErrors(['teacher_id']);
    }

    public function test_store_schedule_fails_on_teacher_conflict()
    {
        $this->actingAs($this->admin);

        $startDate = now()->next('Monday');

        // Create an existing schedule that would conflict
        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => $startDate->copy()->setTime(10, 0),
            'ends_at' => $startDate->copy()->setTime(11, 0),
            'status' => 'scheduled',
        ]);

        // Attempt to book the same teacher at the same time for another student
        $anotherStudent = User::factory()->create(['role' => 'Student']);
        $anotherStudent->assignRole('Student');

        $response = $this->post(route('admin.schedules.store'), [
            'student_id' => $anotherStudent->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'days' => ['Monday'],
            'schedule_times' => ['Monday' => '10:00'],
            'duration_minutes' => 60,
            'start_date' => $startDate->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        
        // Assert error message exists in session
        $this->assertTrue(session()->has('error'));
        $this->assertStringContainsString('Teacher conflict', session('error'));
    }
    public function test_admin_can_print_monthly_schedule()
    {
        $this->actingAs($this->admin);

        $targetMonth = now()->format('Y-m');

        // Create a schedule for the teacher
        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => now()->copy()->startOfMonth()->setTime(10, 0),
            'ends_at' => now()->copy()->startOfMonth()->setTime(11, 0),
            'status' => 'scheduled',
        ]);

        $response = $this->get(route('scheduler.schedules.print', [
            'teacher_id' => $this->teacher->id,
            'month' => $targetMonth,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.schedules.print');
        $response->assertViewHas('teacher');
        $response->assertViewHas('monthDays');
        
        // Assert the schedule is present in the response
        $response->assertSee($this->teacher->name);
    }

    public function test_admin_can_print_monthly_schedule_with_late_night_sessions()
    {
        $this->actingAs($this->admin);

        $targetMonth = '2026-06';

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => Carbon::create(2026, 6, 4, 0, 30, 0, 'Africa/Cairo'),
            'ends_at' => Carbon::create(2026, 6, 4, 1, 30, 0, 'Africa/Cairo'),
            'status' => 'scheduled',
        ]);

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => Carbon::create(2026, 6, 4, 1, 30, 0, 'Africa/Cairo'),
            'ends_at' => Carbon::create(2026, 6, 4, 2, 30, 0, 'Africa/Cairo'),
            'status' => 'scheduled',
        ]);

        $response = $this->get(route('scheduler.schedules.print', [
            'teacher_id' => $this->teacher->id,
            'month' => $targetMonth,
        ]));

        $response->assertStatus(200);
        $response->assertSee('12:30 AM');
        $response->assertSee('1:30 AM');
        $response->assertSee($this->student->name);
    }

    public function test_admin_print_monthly_schedule_fails_without_params()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('scheduler.schedules.print'));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
