<?php

namespace Tests\Feature\Accountant;

use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TeacherHoursPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::firstOrCreate(['name' => 'manage accounting']);
        $role = Role::firstOrCreate(['name' => 'Accountant']);
        $role->givePermissionTo($permission);
    }

    public function test_accountant_can_download_teacher_hours_pdf()
    {
        // Setup Accountant
        $accountant = User::factory()->create([
            'role' => 'Accountant',
            'can_access_payroll' => true,
        ]);
        $accountant->assignRole('Accountant');

        // Setup Teacher and Student
        $teacher = User::factory()->create(['role' => 'Teacher', 'hourly_rate' => 20]);
        $student = User::factory()->create(['role' => 'Student']);
        $course = Course::first() ?? Course::factory()->create();

        // Create Schedule and Attendance for current month
        $startsAt = Carbon::now()->startOfMonth()->addDays(1)->setHour(10);
        $schedule = Schedule::create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
            'status' => 'scheduled',
            'zoom_link' => 'https://zoom.us',
        ]);

        Attendance::create([
            'schedule_id' => $schedule->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'teacher_present' => true,
            'student_present' => true,
        ]);

        // Act
        $response = $this->actingAs($accountant)->get(route('accountant.teacher-hours.pdf', [
            'teacher' => $teacher->id,
            'month' => now()->month,
            'year' => now()->year,
        ]));

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_unauthorized_user_cannot_download_pdf()
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        
        $otherUser = User::factory()->create(['role' => 'Student']);

        $response = $this->actingAs($otherUser)->get(route('accountant.teacher-hours.pdf', [
            'teacher' => $teacher->id,
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertStatus(403);
    }
}
