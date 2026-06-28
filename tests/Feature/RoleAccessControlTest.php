<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist for testing
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'SuperAdmin']);
        Role::firstOrCreate(['name' => 'Teacher']);
        Role::firstOrCreate(['name' => 'Student']);
        Role::firstOrCreate(['name' => 'Parent']);
    }

    public function test_teacher_cannot_access_admin_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $teacher->assignRole('Teacher');

        $response = $this->actingAs($teacher)->get(route('admin.dashboard'));

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_student_cannot_access_teacher_dashboard()
    {
        $student = User::factory()->create(['role' => 'Student']);
        $student->assignRole('Student');

        $response = $this->actingAs($student)->get(route('teacher.dashboard'));

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_student_dashboard()
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $parent->assignRole('Parent');

        $response = $this->actingAs($parent)->get(route('student.dashboard'));

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // Should NOT be 403. It might redirect or be 200.
        $this->assertNotEquals(403, $response->status());
    }

    public function test_teacher_can_access_teacher_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $teacher->assignRole('Teacher');

        $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

        // Should NOT be 403.
        $this->assertNotEquals(403, $response->status());
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect('/login');
    }
}
