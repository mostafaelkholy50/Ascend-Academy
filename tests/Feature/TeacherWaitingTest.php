<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TeacherWaitingNotification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Carbon\Carbon;

class TeacherWaitingTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;
    protected $student;
    protected $parent;
    protected $course;
    protected $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles if they don't exist
        Role::firstOrCreate(['name' => 'Teacher']);
        Role::firstOrCreate(['name' => 'Student']);
        Role::firstOrCreate(['name' => 'Parent']);

        $this->teacher = User::factory()->create(['hourly_rate' => 20]);
        $this->teacher->assignRole('Teacher');

        $this->student = User::factory()->create();
        $this->student->assignRole('Student');

        $this->parent = User::factory()->create();
        $this->parent->assignRole('Parent');

        // Link student and parent
        $this->student->parents()->attach($this->parent->id);

        $this->course = Course::factory()->create();

        // 1 hour schedule
        $this->schedule = Schedule::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'starts_at' => Carbon::now()->startOfHour(),
            'ends_at' => Carbon::now()->startOfHour()->addHour(),
            'status' => 'scheduled',
            'type' => 'regular'
        ]);
    }

    public function test_teacher_can_notify_waiting_without_half_time()
    {
        Notification::fake();

        $response = $this->actingAs($this->teacher)
            ->postJson(route('teacher.attendance.notify-waiting'), [
                'schedule_id' => $this->schedule->id,
                'waited_half_time' => false,
            ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        Notification::assertSentTo(
            [$this->parent],
            TeacherWaitingNotification::class
        );

        $this->assertDatabaseMissing('attendances', [
            'schedule_id' => $this->schedule->id,
            'remark' => 'Waited Half Time',
        ]);
    }

    public function test_teacher_can_notify_waiting_with_half_time_bonus()
    {
        Notification::fake();

        $response = $this->actingAs($this->teacher)
            ->postJson(route('teacher.attendance.notify-waiting'), [
                'schedule_id' => $this->schedule->id,
                'waited_half_time' => true,
            ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        Notification::assertSentTo(
            [$this->parent],
            TeacherWaitingNotification::class
        );

        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $this->schedule->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'teacher_present' => true,
            'student_present' => false,
            'remark' => 'Waited Half Time',
        ]);

        $this->assertDatabaseHas('schedules', [
            'id' => $this->schedule->id,
            'status' => 'scheduled',
        ]);
        
        // Also test the TeacherHoursService calculation
        $service = app(\App\Services\TeacherHoursService::class);
        
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
        ]);
        
        $data = $service->getHoursData($this->teacher, $request);
        
        // The schedule was 1 hour, so bonus is 0.5 hours
        $this->assertEquals(0.5, $data['totalHours']);
    }

    public function test_teacher_bonus_added_even_if_notification_fails()
    {
        // Force notification to throw an exception
        Notification::shouldReceive('send')->andThrow(new \Exception('SMTP Error'));

        $response = $this->actingAs($this->teacher)
            ->postJson(route('teacher.attendance.notify-waiting'), [
                'schedule_id' => $this->schedule->id,
                'waited_half_time' => true,
            ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'time_added' => true,
                     'email_sent' => false
                 ]);

        // Assert attendance was still recorded despite the email failure
        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $this->schedule->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'teacher_present' => true,
            'student_present' => false,
            'remark' => 'Waited Half Time',
        ]);

        $this->assertDatabaseHas('schedules', [
            'id' => $this->schedule->id,
            'status' => 'scheduled',
        ]);
    }
}
