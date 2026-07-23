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
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // Create roles and permissions
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Teacher']);
        Role::create(['name' => 'Student']);
        \Spatie\Permission\Models\Permission::create(['name' => 'manage schedules']);
        
        $adminRole = Role::findByName('Admin');
        $adminRole->givePermissionTo('manage schedules');
        
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

        $response = $this->get(route('admin.schedules.index', ['view' => 'calendar']));

        $response->assertStatus(200);
        $response->assertSee('Print Schedule');
        $response->assertSee('Create New Schedule');
        $response->assertSee('id="calendar-container"', false);
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

        Carbon::setTestNow(Carbon::create(2026, 6, 1, 9, 0, 0, 'Africa/Cairo'));

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => Carbon::create(2026, 6, 7, 10, 0, 0, 'Africa/Cairo'),
            'ends_at' => Carbon::create(2026, 6, 7, 11, 0, 0, 'Africa/Cairo'),
            'status' => 'scheduled',
        ]);

        $beforeCount = Schedule::count();

        $response = $this->post(route('admin.schedules.store'), [
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'days' => ['Monday', 'Wednesday'],
            'schedule_times' => [
                'Monday' => ['10:00'],
                'Wednesday' => ['14:00'],
            ],
            'durations' => [
                'Monday' => [60],
                'Wednesday' => [60],
            ],
            'start_date' => '2026-06-01',
        ]);

        $response->assertRedirect();

        expect(Schedule::count())->toBeGreaterThan($beforeCount);

        $this->assertDatabaseHas('schedules', [
            'enrollment_id' => $this->enrollment->id,
            'starts_at' => '2026-06-07 10:00:00',
            'teacher_id' => $this->teacher->id,
        ]);

        $this->assertDatabaseHas('schedules', [
            'enrollment_id' => $this->enrollment->id,
            'starts_at' => '2026-06-03 14:00:00',
            'teacher_id' => $this->teacher->id,
        ]);

        Carbon::setTestNow();
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
            'schedule_times' => ['Monday' => ['10:00', '14:00']],
            'durations' => ['Monday' => [60, 60]],
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
            'schedule_times' => ['Monday' => ['10:00']],
            'durations' => ['Monday' => [60]],
            'start_date' => $startDate->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        
        // Assert error message exists in session
        $this->assertTrue(session()->has('error'));
        $this->assertStringContainsString('Teacher conflict', session('error'));
    }

    public function test_generate_monthly_schedule_adds_missing_sessions_without_duplicates()
    {
        $this->actingAs($this->admin);

        Carbon::setTestNow(Carbon::create(2026, 6, 1, 9, 0, 0, 'Africa/Cairo'));

        $this->enrollment->setSchedulePattern([
            'Monday' => ['10:00'],
        ]);

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => Carbon::create(2026, 6, 1, 10, 0, 0, 'Africa/Cairo'),
            'ends_at' => Carbon::create(2026, 6, 1, 11, 0, 0, 'Africa/Cairo'),
            'status' => 'scheduled',
        ]);

        $beforeCount = Schedule::where('enrollment_id', $this->enrollment->id)->count();

        $result = app(\App\Services\ScheduleService::class)->generateMonthlySchedules(
            $this->enrollment,
            '2026-06',
            $this->teacher->id
        );

        expect($result['success'])->toBeTrue();
        expect($result['count'])->toBeGreaterThan(0);
        expect(Schedule::where('enrollment_id', $this->enrollment->id)->count())->toBeGreaterThan($beforeCount);
        expect(Schedule::where('enrollment_id', $this->enrollment->id)->where('starts_at', '2026-06-01 10:00:00')->count())->toBe(1);

        Carbon::setTestNow();
    }
    public function test_admin_can_print_monthly_schedule()
    {
        $this->actingAs($this->admin);

        $targetMonth = now()->format('Y-m');

        $this->enrollment->setSchedulePattern([
            'Monday' => [
                'active' => true,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
            'Tuesday' => [
                'active' => false,
                'slots' => [['time' => '12:00', 'duration' => 60]],
            ],
        ]);

        // Active schedule should show up in print
        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => Carbon::create(2026, 7, 6, 10, 0, 0, 'Africa/Cairo'),
            'ends_at' => Carbon::create(2026, 7, 6, 11, 0, 0, 'Africa/Cairo'),
            'status' => 'scheduled',
        ]);

        // Inactive schedule should be hidden from print
        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->enrollment->course_id,
            'teacher_id' => $this->teacher->id,
            'starts_at' => Carbon::create(2026, 7, 7, 12, 0, 0, 'Africa/Cairo'),
            'ends_at' => Carbon::create(2026, 7, 7, 13, 0, 0, 'Africa/Cairo'),
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
        
        // Assert active schedule is present and inactive schedule is hidden
        $response->assertSee($this->teacher->name);
        $response->assertSee('10:00');
        $response->assertDontSee('12:00');
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
        $response->assertSee('12:30am');
        $response->assertSee('1:30am');
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
