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
}
