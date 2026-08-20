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

    public function test_accountant_pdf_includes_duration_and_total_hours()
    {
        $accountant = User::factory()->create([
            'role' => 'Accountant',
            'can_access_payroll' => true,
        ]);
        $accountant->assignRole('Accountant');

        $teacher = User::factory()->create(['role' => 'Teacher', 'hourly_rate' => 20]);
        $student = User::factory()->create(['role' => 'Student']);
        $course = Course::first() ?? Course::factory()->create();

        $startsAt1 = Carbon::now()->startOfMonth()->addDays(1)->setHour(10);
        $schedule1 = Schedule::create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'starts_at' => $startsAt1,
            'ends_at' => $startsAt1->copy()->addMinutes(30),
            'status' => 'scheduled',
            'zoom_link' => 'https://zoom.us',
        ]);
        Attendance::create([
            'schedule_id' => $schedule1->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'teacher_present' => true,
            'student_present' => true,
        ]);

        $startsAt2 = Carbon::now()->startOfMonth()->addDays(2)->setHour(10);
        $schedule2 = Schedule::create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'starts_at' => $startsAt2,
            'ends_at' => $startsAt2->copy()->addMinutes(60),
            'status' => 'scheduled',
            'zoom_link' => 'https://zoom.us',
        ]);
        Attendance::create([
            'schedule_id' => $schedule2->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'teacher_present' => true,
            'student_present' => true,
        ]);

        $service = app(\App\Services\TeacherHoursService::class);
        $data = $service->getPdfData($teacher, now()->month, now()->year);

        $this->assertArrayHasKey($student->id, $data['studentStats']);
        $stats = $data['studentStats'][$student->id];

        $this->assertEquals(1.5, $stats['total_hours']);
        $this->assertArrayHasKey('30 mins', $stats['durations']);
        $this->assertArrayHasKey('1 hr', $stats['durations']);
        $this->assertEquals(1, $stats['durations']['30 mins']);
        $this->assertEquals(1, $stats['durations']['1 hr']);
    }

    public function test_teacher_absence_counts_as_student_miss_in_pdf_data()
    {
        $teacher = User::factory()->create(['role' => 'Teacher', 'hourly_rate' => 20]);
        $student = User::factory()->create(['role' => 'Student']);
        $course = Course::first() ?? Course::factory()->create();

        $startsAt = Carbon::now()->startOfMonth()->addDays(3)->setHour(9);
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
            'teacher_present' => false,
            'student_present' => false,
            'remark' => 'Teacher was late',
        ]);

        $service = app(\App\Services\TeacherHoursService::class);
        $data = $service->getPdfData($teacher, now()->month, now()->year);

        $stats = $data['studentStats'][$student->id];

        $this->assertEquals(1, $data['stats']['teacher_absences']);
        $this->assertEquals(1, $data['stats']['student_absences']);
        $this->assertEquals(1, $stats['missed']);
        $this->assertEquals(0, $data['totalHours']);
        $this->assertCount(1, $data['teacherAbsencesList']);
        $this->assertSame($student->name, $data['teacherAbsencesList'][0]['student']);
        $this->assertArrayHasKey('session', $data['teacherAbsencesList'][0]);
        $this->assertCount(1, $data['studentAbsencesList']);
        $this->assertSame('Teacher absent', $data['studentAbsencesList'][0]['remark']);
        $this->assertSame($student->name, $data['studentAbsencesList'][0]['student']);
        $this->assertArrayHasKey('session', $data['studentAbsencesList'][0]);
    }
}
