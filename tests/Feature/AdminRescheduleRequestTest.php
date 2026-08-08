<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\RescheduleRequest;
use App\Enums\UserRole;
use App\Enums\RescheduleRequestStatus;

class AdminRescheduleRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_reschedule()
    {
        $admin = User::factory()->create(['role' => UserRole::Admin->value]);
        $teacher = User::factory()->create(['role' => UserRole::Teacher->value]);
        $student = User::factory()->create(['role' => UserRole::Student->value]);
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id, 'course_id' => $course->id]);

        $schedule = Schedule::factory()->create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'starts_at' => now()->addDays(2)->setHour(10)->setMinute(0),
            'ends_at' => now()->addDays(2)->setHour(11)->setMinute(0),
            'status' => 'scheduled',
        ]);

        $newStartsAt = now()->addDays(3)->setHour(12)->setMinute(0);
        $newEndsAt = now()->addDays(3)->setHour(13)->setMinute(0);

        $request = RescheduleRequest::create([
            'schedule_id' => $schedule->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'new_starts_at' => $newStartsAt,
            'new_ends_at' => $newEndsAt,
            'status' => RescheduleRequestStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.schedules.requests.approve', $request->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reschedule_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'starts_at' => $newStartsAt,
        ]);
    }
}
