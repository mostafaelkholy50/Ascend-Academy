<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Student']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->student = User::factory()->create(['role' => 'Student']);
        $this->student->assignRole('Student');
    }

    public function test_admin_can_view_students_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.students.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_student_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.students.show', $this->student->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_store_student()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.students.store'), [
            'name' => 'New Student',
            'email' => 'newstudent@example.com',
            'password' => 'password123',
            'gender' => 'male',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newstudent@example.com',
            'role' => 'Student',
        ]);
    }

    public function test_store_student_validation_fails()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.students.store'), [
            'name' => 'New Student',
            // missing email and password
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }
}
