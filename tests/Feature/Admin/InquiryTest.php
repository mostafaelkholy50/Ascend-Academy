<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $inquiry;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Parent']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->inquiry = Inquiry::create([
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'type' => 'general',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_view_inquiries_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.inquiries.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_inquiry_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.inquiries.show', $this->inquiry->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_convert_inquiry_to_parent()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.inquiries.convert', $this->inquiry->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'Parent',
        ]);
        
        $this->assertDatabaseHas('inquiries', [
            'id' => $this->inquiry->id,
            'status' => 'converted',
        ]);
    }
}
