<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Services\ScheduleService;
use App\Services\TeacherScheduleService;
use App\Repositories\StudentScheduleRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ScheduleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $course;
    protected $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'SuperAdmin']);
        $this->teacher = User::factory()->create(['role' => 'Teacher', 'active' => true]);
        $this->student = User::factory()->create(['role' => 'Student', 'active' => true]);
        
        $this->course = Course::create(['title' => 'Test Course']);

        $this->enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'start_date' => now(),
            'days_per_week' => 2,
            'session_duration' => 60,
            'admin_price' => 100,
            'currency' => 'USD',
        ]);
    }

    public function test_cancelled_schedules_are_hidden_from_admin_calendar()
    {
        $startDate = now()->startOfWeek();

        $activeSchedule = Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'starts_at' => $startDate->copy()->addDays(1)->setHour(10),
            'ends_at' => $startDate->copy()->addDays(1)->setHour(11),
            'status' => 'scheduled',
        ]);

        $cancelledSchedule = Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'starts_at' => $startDate->copy()->addDays(2)->setHour(10),
            'ends_at' => $startDate->copy()->addDays(2)->setHour(11),
            'status' => 'cancelled',
        ]);

        $service = app(ScheduleService::class);
        $request = new Request(['week' => $startDate->format('Y-m-d')]);
        
        $data = $service->getCalendarData($request);
        
        $day1Schedules = $data['weekDays'][1]['schedules'];
        $this->assertCount(1, $day1Schedules);
        $this->assertEquals($activeSchedule->id, $day1Schedules->first()->id);
        
        $day2Schedules = $data['weekDays'][2]['schedules'];
        $this->assertCount(0, $day2Schedules);
    }

    public function test_cancelled_schedules_are_hidden_from_teacher_weekly_calendar()
    {
        $startDate = now()->startOfWeek();

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'starts_at' => $startDate->copy()->addDays(1)->setHour(10),
            'ends_at' => $startDate->copy()->addDays(1)->setHour(11),
            'status' => 'scheduled',
        ]);

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'starts_at' => $startDate->copy()->addDays(2)->setHour(10),
            'ends_at' => $startDate->copy()->addDays(2)->setHour(11),
            'status' => 'cancelled',
        ]);

        $service = app(TeacherScheduleService::class);
        $request = new Request(['week' => $startDate->format('Y-m-d')]);
        
        $data = $service->getWeeklyData($this->teacher, $request);
        
        $day1Key = $startDate->copy()->addDays(1)->format('Y-m-d');
        $day2Key = $startDate->copy()->addDays(2)->format('Y-m-d');

        $this->assertCount(1, $data['schedulesByDay'][$day1Key]['schedules']);
        $this->assertCount(0, $data['schedulesByDay'][$day2Key]['schedules']);
    }

    public function test_cancelled_schedules_are_hidden_from_student_schedule()
    {
        $startDate = now()->startOfWeek();

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'starts_at' => $startDate->copy()->addDays(1)->setHour(10),
            'ends_at' => $startDate->copy()->addDays(1)->setHour(11),
            'status' => 'scheduled',
        ]);

        Schedule::create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'starts_at' => $startDate->copy()->addDays(2)->setHour(10),
            'ends_at' => $startDate->copy()->addDays(2)->setHour(11),
            'status' => 'cancelled',
        ]);

        $repository = app(StudentScheduleRepository::class);
        
        $schedules = $repository->getSchedulesForRange(
            $this->student, 
            $startDate, 
            $startDate->copy()->endOfWeek()
        );

        $this->assertCount(1, $schedules);
        
        $dailySchedulesDay1 = $repository->getSchedulesForDate($this->student, $startDate->copy()->addDays(1));
        $this->assertCount(1, $dailySchedulesDay1);

        $dailySchedulesDay2 = $repository->getSchedulesForDate($this->student, $startDate->copy()->addDays(2));
        $this->assertCount(0, $dailySchedulesDay2);
    }
}
