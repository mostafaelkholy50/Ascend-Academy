<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\Attendance;
use Database\Seeders\RolePermissionSeeder;

class AttendanceProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /** @test */
    public function an_admin_can_view_student_attendance_profile()
    {
        // 1. Arrange
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        
        $student = User::factory()->create();
        $student->assignRole('Student');

        $teacher = User::factory()->create();
        $teacher->assignRole('Teacher');

        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'Test Description',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $schedule = Schedule::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'status' => 'completed',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->subDay()->addHour(),
        ]);

        Attendance::create([
            'schedule_id' => $schedule->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'student_present' => true,
            'teacher_present' => true,
        ]);

        // 2. Act
        $response = $this->actingAs($admin)->get(route('admin.attendances.student', $student->id));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.attendances.student');
        $response->assertViewHas('student');
        $response->assertViewHas('teachers');
        $response->assertViewHas('attendances');

        $teachers = $response->viewData('teachers');
        $this->assertCount(1, $teachers);
        $this->assertEquals($teacher->id, $teachers->first()->id);
        $this->assertEquals(1, $teachers->first()->attended_count);
    }

    /** @test */
    public function an_admin_can_view_teacher_attendance_profile()
    {
        // 1. Arrange
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        
        $student = User::factory()->create();
        $student->assignRole('Student');

        $teacher = User::factory()->create();
        $teacher->assignRole('Teacher');

        $course = Course::create([
            'title' => 'Test Course 2',
            'description' => 'Test Description',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $schedule = Schedule::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'status' => 'completed',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->subDay()->addHour(),
        ]);

        Attendance::create([
            'schedule_id' => $schedule->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'student_present' => false,
            'teacher_present' => true,
        ]);

        // 2. Act
        $response = $this->actingAs($admin)->get(route('admin.attendances.teacher', $teacher->id));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.attendances.teacher');
        $response->assertViewHas('teacher');
        $response->assertViewHas('students');
        $response->assertViewHas('attendances');

        $students = $response->viewData('students');
        $this->assertCount(1, $students);
        $this->assertEquals($student->id, $students->first()->id);
        $this->assertEquals(1, $students->first()->student_absent_count);
    }
}
