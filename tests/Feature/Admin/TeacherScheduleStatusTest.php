<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;

class TeacherScheduleStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_schedules_render_correctly_with_english_statuses()
    {
        // Arrange: Create an admin and a teacher
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $admin = User::factory()->create(['role' => 'Admin']);
        $admin->assignRole('Admin');
        
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $student = User::factory()->create(['role' => 'Student']);

        // Create 68 schedules for this teacher with various statuses
        $scheduledCount = 20;
        $completedCount = 30;
        $cancelledCount = 18;

        $course = \App\Models\Course::factory()->create();
        $enrollment = \App\Models\Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        for ($i = 0; $i < $scheduledCount; $i++) {
            Schedule::create([
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'status' => 'scheduled',
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'starts_at' => now()->addDays(1),
                'ends_at' => now()->addDays(1)->addHours(1),
            ]);
        }

        for ($i = 0; $i < $completedCount; $i++) {
            Schedule::create([
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'status' => 'completed',
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->subDays(1)->addHours(1),
            ]);
        }

        for ($i = 0; $i < $cancelledCount; $i++) {
            Schedule::create([
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'status' => 'cancelled',
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'starts_at' => now()->addDays(2),
                'ends_at' => now()->addDays(2)->addHours(1),
            ]);
        }

        // Act: Visit the teacher's profile page as admin
        $response = $this->actingAs($admin)->get(route('admin.teachers.show', $teacher->id));

        // Assert: The page loads successfully
        $response->assertStatus(200);

        // Assert: The total count (68) is displayed correctly in the dropdown header
        $response->assertSee('Class Schedules (68)');

        // Assert: The English versions of statuses are present on the page
        $response->assertSee('Scheduled');
        $response->assertSee('Completed');
        $response->assertSee('Cancelled');

        // Verify that we are rendering all the schedules instead of a limited number
        // We can do this by counting the occurrences of 'Scheduled', 'Completed', 'Cancelled' 
        // in the returned HTML, but a simple assertSee is often sufficient for verifying the texts are rendered.
        
        $content = $response->getContent();
        $this->assertEquals(68, substr_count($content, 'div class="flex items-start justify-between"'));
    }
}
