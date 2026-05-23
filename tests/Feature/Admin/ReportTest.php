<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Report;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $report;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $student = User::factory()->create(['role' => 'Student']);
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course = Course::create(['title' => 'Test Course']);

        $this->report = Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'report_date' => now(),
            'mastery_score' => 85,
        ]);
    }

    public function test_admin_can_view_reports_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reports.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_report_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reports.show', $this->report->id));

        $response->assertStatus(200);
    }
}
