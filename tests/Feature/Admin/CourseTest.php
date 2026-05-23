<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $course;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->course = Course::create([
            'title' => 'Test Course',
            'description' => 'Test Description',
        ]);
    }

    public function test_admin_can_view_courses_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.courses.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_course_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.courses.show', $this->course->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_store_course()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.courses.store'), [
            'title' => 'New Course',
            'description' => 'New Description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'title' => 'New Course',
        ]);
    }
}
