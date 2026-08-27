<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ParentTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $parent;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Parent']);
        Role::create(['name' => 'Student']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->parent = User::factory()->create(['role' => 'Parent']);
        $this->parent->assignRole('Parent');
    }

    public function test_admin_can_view_parents_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.parents.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_parent_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.parents.show', $this->parent->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_store_parent()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.parents.store'), [
            'name' => 'New Parent',
            'email' => 'newparent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newparent@example.com',
            'role' => 'Parent',
        ]);
    }

    public function test_admin_can_add_child_to_parent()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.parents.add-child', $this->parent->id), [
            'name' => 'New Child',
            'email' => 'newchild@example.com',
            'password' => 'password123',
            'gender' => 'male',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newchild@example.com',
            'role' => 'Student',
        ]);

        $child = User::where('email', 'newchild@example.com')->first();

        $this->assertDatabaseHas('children', [
            'parent_id' => $this->parent->id,
            'child_id' => $child->id,
        ]);
    }

    public function test_parent_show_page_displays_available_students()
    {
        $student = User::factory()->create([
            'role' => 'Student',
            'active' => true,
        ]);
        $student->assignRole('Student');

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.parents.show', $this->parent->id));

        $response->assertStatus(200);
        $response->assertSee($student->name);
        $response->assertSee('Attach Student(s)');
    }

    public function test_admin_can_attach_existing_student_to_parent()
    {
        $student = User::factory()->create([
            'role' => 'Student',
            'active' => true,
        ]);
        $student->assignRole('Student');

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.parents.attach-students', $this->parent->id), [
            'student_ids' => [$student->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('children', [
            'parent_id' => $this->parent->id,
            'child_id' => $student->id,
        ]);
    }

    public function test_admin_can_attach_multiple_existing_students_to_parent()
    {
        $studentOne = User::factory()->create(['role' => 'Student', 'active' => true]);
        $studentOne->assignRole('Student');

        $studentTwo = User::factory()->create(['role' => 'Student', 'active' => true]);
        $studentTwo->assignRole('Student');

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.parents.attach-students', $this->parent->id), [
            'student_ids' => [$studentOne->id, $studentTwo->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('children', [
            'parent_id' => $this->parent->id,
            'child_id' => $studentOne->id,
        ]);
        $this->assertDatabaseHas('children', [
            'parent_id' => $this->parent->id,
            'child_id' => $studentTwo->id,
        ]);
    }

    public function test_attach_students_requires_at_least_one_student_id()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.parents.attach-students', $this->parent->id), [
            'student_ids' => [],
        ]);

        $response->assertSessionHasErrors('student_ids');
    }

    public function test_attach_students_rejects_invalid_student_ids()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.parents.attach-students', $this->parent->id), [
            'student_ids' => [99999],
        ]);

        $response->assertSessionHasErrors('student_ids.0');
    }

    public function test_already_attached_student_is_not_shown_in_available_students()
    {
        $student = User::factory()->create([
            'role' => 'Student',
            'active' => true,
        ]);
        $student->assignRole('Student');
        $this->parent->children()->attach($student->id);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.parents.show', $this->parent->id));

        $response->assertStatus(200);
        $response->assertDontSee('Attach Student(s)');
    }
}
