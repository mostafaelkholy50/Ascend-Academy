<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');
    }

    public function test_admin_can_view_profile()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.profile.show'));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_profile()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.profile.update'), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }
}
