<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage accounting']);
        Permission::create(['name' => 'manage quality']);
        
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Teacher']);
        Role::create(['name' => 'Student']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');
        $this->admin->givePermissionTo(['view dashboard', 'manage accounting', 'manage quality']);
    }

    public function test_admin_can_view_all_dashboard_sections()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Executive Insights');
        $response->assertSee('Enrollment Trends');
        $response->assertSee('Revenue Velocity');
        $response->assertSee('Attendance Fidelity');
    }

    public function test_accountant_can_view_only_revenue_sections()
    {
        $accountantRole = Role::create(['name' => 'Accountant']);
        $accountantRole->givePermissionTo('view dashboard');
        $accountantRole->givePermissionTo('manage accounting');
        
        $accountant = User::factory()->create(['role' => 'Accountant']);
        $accountant->assignRole('Accountant');

        $this->actingAs($accountant);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Revenue Velocity');
        
        // Should NOT see admin-only or quality-only sections
        $response->assertDontSee('Executive Insights');
        $response->assertDontSee('Attendance Fidelity');
    }

    public function test_quality_control_can_view_only_quality_sections()
    {
        $qcRole = Role::create(['name' => 'QualityControl']);
        $qcRole->givePermissionTo('view dashboard');
        $qcRole->givePermissionTo('manage quality');
        
        $qc = User::factory()->create(['role' => 'QualityControl']);
        $qc->assignRole('QualityControl');

        $this->actingAs($qc);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Attendance Fidelity');
        $response->assertSee('Quality Score');
        
        // Should NOT see revenue or admin-only sections
        $response->assertDontSee('Revenue Velocity');
        $response->assertDontSee('Executive Insights');
    }

    public function test_unauthorized_user_cannot_view_dashboard()
    {
        $user = User::factory()->create(['role' => 'Student']);
        
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertDontSee('Executive Insights');
        $response->assertDontSee('Revenue Velocity');
        $response->assertDontSee('Attendance Fidelity');
    }
}
