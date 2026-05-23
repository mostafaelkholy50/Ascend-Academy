<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\PricingTier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PricingTierTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $pricingTier;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');

        $this->pricingTier = PricingTier::create([
            'days_per_week' => 2,
            'session_duration' => 60,
            'price_cad' => 100,
            'price_usd' => 80,
            'price_gbp' => 60,
        ]);
    }

    public function test_admin_can_view_pricing_tiers_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.pricing-tiers.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_pricing_tier_edit()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.pricing-tiers.edit', $this->pricingTier->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_store_pricing_tier()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.pricing-tiers.store'), [
            'days_per_week' => 3,
            'session_duration' => 60,
            'price_cad' => 150,
            'price_usd' => 120,
            'price_gbp' => 90,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pricing_tiers', [
            'days_per_week' => 3,
            'session_duration' => 60,
        ]);
    }
}
