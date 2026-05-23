<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Teacher']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create([
            'role' => 'Admin',
        ]);
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create([
            'name' => 'Test Teacher',
            'email' => 'teacher@example.com',
            'role' => 'Teacher',
            'active' => true,
        ]);
        $this->teacher->assignRole('Teacher');
    }

    public function test_admin_can_view_teachers_list()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.teachers.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Teacher');
    }

    public function test_admin_can_search_teachers()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.teachers.index', ['search' => 'Test']));

        $response->assertStatus(200);
        $response->assertSee('Test Teacher');

        $response2 = $this->get(route('admin.teachers.index', ['search' => 'NonExistent']));
        $response2->assertDontSee('Test Teacher');
    }

    public function test_admin_can_create_teacher()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.teachers.store'), [
            'name' => 'New Teacher',
            'email' => 'new@example.com',
            'password' => 'password123',
            'gender' => 'male',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'New Teacher',
            'email' => 'new@example.com',
            'role' => 'Teacher',
        ]);
    }

    public function test_admin_can_update_teacher()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.teachers.update', $this->teacher->id), [
            'name' => 'Updated Name',
            'email' => 'teacher@example.com',
            'active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->teacher->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_teacher()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.teachers.destroy', $this->teacher->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', [
            'id' => $this->teacher->id,
        ]);
    }

    public function test_admin_can_update_teacher_password()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.teachers.update-password', $this->teacher->id), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(\Hash::check('newpassword123', $this->teacher->fresh()->password));
    }

    public function test_admin_can_update_teacher_rate()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.teacher-hours.update-rate', $this->teacher->id), [
            'hourly_rate' => 25.5,
        ]);

        $response->assertRedirect();
        $this->assertEquals(25.5, $this->teacher->fresh()->hourly_rate);
    }
}
