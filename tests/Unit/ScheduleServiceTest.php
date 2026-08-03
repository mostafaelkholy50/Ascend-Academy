<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalize_schedule_pattern_in_enrollment()
    {
        $student = User::factory()->create(['role' => 'Student']);
        $course = Course::factory()->create();
        
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'start_date' => Carbon::now(),
            'status' => 'active',
            'session_duration' => 30
        ]);
        
        $pattern = [
            'Monday' => [
                'active' => true,
                'slots' => [
                    ['time' => '16:00', 'duration' => 45],
                    ['time' => '17:00', 'duration' => 60],
                ],
            ],
            'Tuesday' => [
                'active' => true,
                'slots' => [['time' => '18:00', 'duration' => 30]],
            ],
        ];
        
        $enrollment->session_duration = 30; // default
        $enrollment->setSchedulePattern($pattern);
        
        $savedPattern = $enrollment->getSchedulePattern();
        
        $this->assertIsArray($savedPattern);
        $this->assertArrayHasKey('Monday', $savedPattern);
        $this->assertTrue($savedPattern['Monday']['active']);
        $this->assertEquals(45, $savedPattern['Monday']['slots'][0]['duration']);
        $this->assertEquals('16:00', $savedPattern['Monday']['slots'][0]['time']);
        $this->assertEquals(60, $savedPattern['Monday']['slots'][1]['duration']);
        
        $this->assertArrayHasKey('Tuesday', $savedPattern);
        $this->assertEquals(30, $savedPattern['Tuesday']['slots'][0]['duration']);
    }
    
    public function test_store_schedule_creates_sessions()
    {
        $student = User::factory()->create(['role' => 'Student']);
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course = Course::factory()->create();
        
        $service = app(ScheduleService::class);
        
        $startDate = Carbon::now()->startOfMonth()->addDays(2)->format('Y-m-d');
        $dayName = Carbon::parse($startDate)->format('l'); // Get the day of the week for the start date
        
        $data = [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'start_date' => $startDate,
            'days' => [$dayName],
            'schedule_times' => [
                $dayName => ['14:00', '16:00']
            ],
            'durations' => [
                $dayName => [30, 60]
            ],
            'day_active' => [
                $dayName => true,
            ],
        ];
        
        $count = $service->storeSchedule($data);
        
        $this->assertGreaterThan(0, $count);
        
        $enrollment = Enrollment::where('student_id', $student->id)->first();
        $this->assertNotNull($enrollment);
        
        $pattern = $enrollment->getSchedulePattern();
        $this->assertArrayHasKey($dayName, $pattern);
        $this->assertEquals(30, $pattern[$dayName]['slots'][0]['duration']);
    }

    public function test_store_schedule_with_different_times_and_durations()
    {
        $student = User::factory()->create(['role' => 'Student']);
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course = Course::factory()->create();
        
        $service = app(ScheduleService::class);
        
        // Use a fixed start date
        $startDate = Carbon::now()->startOfMonth()->addDays(2)->format('Y-m');
        $day1Name = 'Monday';
        $day2Name = 'Tuesday';
        
        $data = [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'start_date' => $startDate,
            'days' => [$day1Name, $day2Name],
            'schedule_times' => [
                $day1Name => ['14:00', '16:00'],
                $day2Name => ['18:00']
            ],
            'durations' => [
                $day1Name => [30, 60],
                $day2Name => [45]
            ],
            'day_active' => [
                $day1Name => true,
                $day2Name => true,
            ],
        ];
        
        $count = $service->storeSchedule($data);
        
        $this->assertGreaterThan(0, $count);
        
        $enrollment = Enrollment::where('student_id', $student->id)->first();
        $this->assertNotNull($enrollment);
        
        $pattern = $enrollment->getSchedulePattern();
        $this->assertArrayHasKey($day1Name, $pattern);
        $this->assertArrayHasKey($day2Name, $pattern);
        
        // Check day 1 (Monday)
        $this->assertEquals(30, $pattern[$day1Name]['slots'][0]['duration']);
        $this->assertEquals('14:00', $pattern[$day1Name]['slots'][0]['time']);
        $this->assertEquals(60, $pattern[$day1Name]['slots'][1]['duration']);
        $this->assertEquals('16:00', $pattern[$day1Name]['slots'][1]['time']);
        
        // Check day 2 (Tuesday)
        $this->assertEquals(45, $pattern[$day2Name]['slots'][0]['duration']);
        $this->assertEquals('18:00', $pattern[$day2Name]['slots'][0]['time']);
    }

    public function test_store_schedule_http_request()
    {
        $this->withoutMiddleware();
        
        $admin = User::factory()->create(['role' => 'Admin']);
        $student = User::factory()->create(['role' => 'Student']);
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course = Course::factory()->create();
        
        $startDate = Carbon::now()->startOfMonth()->addMonths(1)->format('Y-m');
        
        $response = $this->actingAs($admin)->post(route('admin.schedules.store'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'start_date' => $startDate,
            'days' => ['Monday', 'Tuesday'],
            'schedule_times' => [
                'Monday' => ['14:00', '16:00'],
                'Tuesday' => ['18:00']
            ],
            'durations' => [
                'Monday' => [30, 60],
                'Tuesday' => [45]
            ],
            'day_active' => [
                'Monday' => true,
                'Tuesday' => true,
            ]
        ]);
        
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);
    }

    public function test_updateSchedulePattern_only_updates_current_month()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 3, 12, 0, 0));

        $student = User::factory()->create(['role' => 'Student']);
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course = Course::factory()->create();

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'start_date' => Carbon::create(2026, 7, 1),
            'status' => 'active',
            'days_per_week' => 1,
            'session_duration' => 60,
            'schedule_pattern' => [
                'Monday' => [
                    'active' => true,
                    'slots' => [['time' => '10:00', 'duration' => 60]],
                ],
            ],
        ]);

        $julySchedule = Schedule::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'starts_at' => Carbon::create(2026, 7, 6, 10, 0, 0),
            'ends_at' => Carbon::create(2026, 7, 6, 11, 0, 0),
            'status' => 'scheduled',
        ]);

        $augustSchedule = Schedule::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'starts_at' => Carbon::create(2026, 8, 3, 10, 0, 0),
            'ends_at' => Carbon::create(2026, 8, 3, 11, 0, 0),
            'status' => 'scheduled',
        ]);

        $service = app(ScheduleService::class);
        $service->updateSchedulePattern($enrollment, [
            'teacher_id' => $teacher->id,
            'day_active' => ['Monday' => 1],
            'days' => ['Monday'],
            'schedule_times' => [
                'Monday' => ['12:00'],
            ],
            'durations' => [
                'Monday' => [60],
            ],
        ], Carbon::create(2026, 8, 1));

        $this->assertDatabaseHas('schedules', [
            'id' => $julySchedule->id,
            'starts_at' => $julySchedule->starts_at->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseMissing('schedules', [
            'id' => $augustSchedule->id,
        ]);

        $this->assertEquals(1, Schedule::where('enrollment_id', $enrollment->id)->whereMonth('starts_at', 7)->count());
        $this->assertGreaterThan(0, Schedule::where('enrollment_id', $enrollment->id)->whereMonth('starts_at', 8)->whereTime('starts_at', '12:00:00')->count());
    }
}
