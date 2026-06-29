<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
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
                ['time' => '16:00', 'duration' => 45],
                ['time' => '17:00', 'duration' => 60],
            ],
            'Tuesday' => ['18:00']
        ];
        
        $enrollment->session_duration = 30; // default
        $enrollment->setSchedulePattern($pattern);
        
        $savedPattern = $enrollment->getSchedulePattern();
        
        $this->assertIsArray($savedPattern);
        $this->assertArrayHasKey('Monday', $savedPattern);
        $this->assertEquals(45, $savedPattern['Monday'][0]['duration']);
        $this->assertEquals('16:00', $savedPattern['Monday'][0]['time']);
        $this->assertEquals(60, $savedPattern['Monday'][1]['duration']);
        
        $this->assertArrayHasKey('Tuesday', $savedPattern);
        $this->assertEquals(30, $savedPattern['Tuesday'][0]['duration']);
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
        ];
        
        $count = $service->storeSchedule($data);
        
        $this->assertGreaterThan(0, $count);
        
        $enrollment = Enrollment::where('student_id', $student->id)->first();
        $this->assertNotNull($enrollment);
        
        $pattern = $enrollment->getSchedulePattern();
        $this->assertArrayHasKey($dayName, $pattern);
        $this->assertEquals(30, $pattern[$dayName][0]['duration']);
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
        ];
        
        $count = $service->storeSchedule($data);
        
        $this->assertGreaterThan(0, $count);
        
        $enrollment = Enrollment::where('student_id', $student->id)->first();
        $this->assertNotNull($enrollment);
        
        $pattern = $enrollment->getSchedulePattern();
        $this->assertArrayHasKey($day1Name, $pattern);
        $this->assertArrayHasKey($day2Name, $pattern);
        
        // Check day 1 (Monday)
        $this->assertEquals(30, $pattern[$day1Name][0]['duration']);
        $this->assertEquals('14:00', $pattern[$day1Name][0]['time']);
        $this->assertEquals(60, $pattern[$day1Name][1]['duration']);
        $this->assertEquals('16:00', $pattern[$day1Name][1]['time']);
        
        // Check day 2 (Tuesday)
        $this->assertEquals(45, $pattern[$day2Name][0]['duration']);
        $this->assertEquals('18:00', $pattern[$day2Name][0]['time']);
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
            ]
        ]);
        
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);
    }
}

