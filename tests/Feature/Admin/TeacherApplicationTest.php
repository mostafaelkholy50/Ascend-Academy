<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\TeacherApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class TeacherApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $application;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Teacher']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->application = TeacherApplication::create([
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '123456789',
            'status' => 'pending',
            'country' => 'Canada',
            'gender' => 'female',
            'education_level' => 'Bachelor',
            'years_of_experience' => 5,
            'teaching_experience' => '5 years teaching Quran.',
            'subjects' => ['Quran', 'Tajweed'],
            'age_groups' => ['kids', 'teens'],
            'availability' => ['Monday', 'Wednesday'],
        ]);
    }

    public function test_admin_can_view_teacher_applications_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.teacher-applications.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_teacher_application_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.teacher-applications.show', $this->application->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_convert_application_to_teacher()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.teacher-applications.convert', $this->application->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'role' => 'Teacher',
        ]);
        
        $this->assertDatabaseHas('teacher_applications', [
            'id' => $this->application->id,
            'status' => 'converted',
        ]);
    }
}
