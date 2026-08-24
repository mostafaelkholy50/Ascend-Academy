<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Notifications\ConsecutiveAbsenceNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class ConsecutiveAbsenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        Role::firstOrCreate(['name' => 'SuperAdmin']);
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Student']);
        Role::firstOrCreate(['name' => 'Teacher']);
    }

    public function test_it_dispatches_notification_after_3_consecutive_absences()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $student = User::factory()->create();
        $student->assignRole('Student');

        $teacher = User::factory()->create();
        $teacher->assignRole('Teacher');

        // Create 3 schedules and 3 absent attendances
        for ($i = 0; $i < 3; $i++) {
            $schedule = Schedule::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'starts_at' => now()->addDays($i),
                'ends_at' => now()->addDays($i)->addHours(1),
                'status' => 'completed',
            ]);

            Attendance::create([
                'schedule_id' => $schedule->id,
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'student_present' => false,
                'teacher_present' => true,
            ]);
        }

        Notification::assertSentTo(
            $admin,
            ConsecutiveAbsenceNotification::class,
            function ($notification) use ($student) {
                return $notification->student->id === $student->id && $notification->absenceCount === 3;
            }
        );
    }

    public function test_it_does_not_dispatch_notification_if_less_than_3_absences()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $student = User::factory()->create();
        $student->assignRole('Student');

        $teacher = User::factory()->create();
        $teacher->assignRole('Teacher');

        // Create 2 absent attendances
        for ($i = 0; $i < 2; $i++) {
            $schedule = Schedule::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'starts_at' => now()->addDays($i),
                'ends_at' => now()->addDays($i)->addHours(1),
                'status' => 'completed',
            ]);

            Attendance::create([
                'schedule_id' => $schedule->id,
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'student_present' => false,
                'teacher_present' => true,
            ]);
        }

        Notification::assertNotSentTo($admin, ConsecutiveAbsenceNotification::class);
    }

    public function test_it_resets_count_if_student_is_present()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $student = User::factory()->create();
        $student->assignRole('Student');

        $teacher = User::factory()->create();
        $teacher->assignRole('Teacher');

        // 2 absent, 1 present, 1 absent
        $statuses = [false, false, true, false];

        foreach ($statuses as $i => $status) {
            $schedule = Schedule::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'starts_at' => now()->addDays($i),
                'ends_at' => now()->addDays($i)->addHours(1),
                'status' => 'completed',
            ]);

            Attendance::create([
                'schedule_id' => $schedule->id,
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'student_present' => $status,
                'teacher_present' => true,
            ]);
        }

        // Should not have reached 3 consecutive absences
        Notification::assertNotSentTo($admin, ConsecutiveAbsenceNotification::class);
    }
}
