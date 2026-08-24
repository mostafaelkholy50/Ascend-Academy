<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MonthlyAttendanceStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::firstOrCreate(['name' => 'SuperAdmin']);
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Student']);
        Role::firstOrCreate(['name' => 'Teacher']);
        
        Permission::firstOrCreate(['name' => 'manage schedules']);
    }

    public function test_it_calculates_correct_monthly_stats_for_students_and_teachers()
    {
        $admin = User::factory()->create(['role' => 'superadmin']);
        $admin->assignRole('SuperAdmin');
        $admin->givePermissionTo('manage schedules');

        $student = User::factory()->create(['role' => 'student']);
        $student->assignRole('Student');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $teacher->assignRole('Teacher');

        // Create 1 session in the CURRENT month where Both are PRESENT
        $schedule1 = Schedule::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'starts_at' => now(),
            'ends_at' => now()->addHours(1),
            'status' => 'completed',
        ]);
        Attendance::create([
            'schedule_id' => $schedule1->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'student_present' => true,
            'teacher_present' => true,
        ]);

        // Create 1 session in the CURRENT month where STUDENT is ABSENT, Teacher PRESENT
        $schedule2 = Schedule::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'starts_at' => now(),
            'ends_at' => now()->addHours(1),
            'status' => 'completed',
        ]);
        Attendance::create([
            'schedule_id' => $schedule2->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'student_present' => false,
            'teacher_present' => true,
        ]);

        // Create 1 session in the CURRENT month where TEACHER is ABSENT, Student PRESENT
        $schedule3 = Schedule::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'starts_at' => now(),
            'ends_at' => now()->addHours(1),
            'status' => 'completed',
        ]);
        Attendance::create([
            'schedule_id' => $schedule3->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'student_present' => true,
            'teacher_present' => false,
        ]);

        // Create 1 session LAST month (should NOT be included in default current month stats)
        $scheduleOld = Schedule::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonths(2)->addHours(1),
            'status' => 'completed',
        ]);
        Attendance::create([
            'schedule_id' => $scheduleOld->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'student_present' => true,
            'teacher_present' => true,
        ]);

        // Access the index page without date filters
        $response = $this->actingAs($admin)->get(route('scheduler.attendance.index'));

        $response->assertStatus(200);

        // View should have studentStats and teacherStats
        $response->assertViewHas('studentStats');
        $response->assertViewHas('teacherStats');
        
        // View should have lists of users
        $response->assertViewHas('studentsList');
        $response->assertViewHas('teachersList');

        $studentStats = $response->viewData('studentStats');
        $teacherStats = $response->viewData('teacherStats');

        // Total sessions in current month = 3
        $this->assertEquals(3, $studentStats['total']);
        
        // Student Attended = schedule 1 + schedule 3 = 2
        $this->assertEquals(2, $studentStats['attended']);
        
        // Student Absent = schedule 2 = 1
        $this->assertEquals(1, $studentStats['absent']);
        
        // Teacher Absent for student = schedule 3 = 1
        $this->assertEquals(1, $studentStats['teacher_absent']);

        // Teacher Stats
        $this->assertEquals(3, $teacherStats['total']);
        // Teacher Attended = schedule 1 + schedule 2 = 2
        $this->assertEquals(2, $teacherStats['attended']);
        // Teacher Absent = schedule 3 = 1
        $this->assertEquals(1, $teacherStats['absent']);
        // Student Absent for teacher = schedule 2 = 1
        $this->assertEquals(1, $teacherStats['student_absent']);
    }
}
