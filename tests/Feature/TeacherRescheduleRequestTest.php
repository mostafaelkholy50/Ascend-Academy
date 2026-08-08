<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Enrollment;
use App\Enums\UserRole;

class TeacherRescheduleRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_request_reschedule()
    {
        $teacher = User::factory()->create(['role' => UserRole::Teacher->value]);
        $student = User::factory()->create(['role' => UserRole::Student->value]);
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id, 'course_id' => $course->id]);

        $schedule = Schedule::create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'starts_at' => now()->addDays(2)->setHour(10)->setMinute(0),
            'ends_at' => now()->addDays(2)->setHour(11)->setMinute(0),
            'status' => 'scheduled',
        ]);

        $newStartsAt = now()->addDays(3)->setHour(12)->setMinute(0);

        $response = $this->actingAs($teacher)->post(route('teacher.schedule.request-reschedule', $schedule->id), [
            'new_starts_at' => $newStartsAt->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reschedule_requests', [
            'schedule_id' => $schedule->id,
            'teacher_id' => $teacher->id,
            'status' => 'pending',
        ]);
    }

    public function test_teacher_can_request_reschedule_for_past_session()
    {
        $teacher = User::factory()->create(['role' => UserRole::Teacher->value]);
        $student = User::factory()->create(['role' => UserRole::Student->value]);
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id, 'course_id' => $course->id]);

        // Create a schedule in the past
        $schedule = Schedule::create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'starts_at' => now()->subDays(2)->setHour(10)->setMinute(0),
            'ends_at' => now()->subDays(2)->setHour(11)->setMinute(0),
            'status' => 'scheduled',
        ]);

        $newStartsAt = now()->addDays(3)->setHour(12)->setMinute(0);

        $response = $this->actingAs($teacher)->post(route('teacher.schedule.request-reschedule', $schedule->id), [
            'new_starts_at' => $newStartsAt->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reschedule_requests', [
            'schedule_id' => $schedule->id,
            'teacher_id' => $teacher->id,
            'status' => 'pending',
        ]);
    }
}
