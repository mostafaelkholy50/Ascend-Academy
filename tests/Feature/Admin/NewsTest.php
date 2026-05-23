<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $news;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        Role::create(['name' => 'Admin']);
        Permission::firstOrCreate(['name' => 'manage news', 'guard_name' => 'web']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole('Admin');
        $this->admin->givePermissionTo('manage news');

        $this->news = News::create([
            'title' => 'Test News',
            'description' => 'Test Description',
            'slug' => 'test-news',
        ]);
    }

    public function test_admin_can_view_news_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.news.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_news_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.news.show', $this->news->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_store_news()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.news.store'), [
            'title' => 'New News',
            'description' => 'New Description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('news', [
            'title' => 'New News',
        ]);
    }

    public function test_unauthorized_staff_cannot_view_news_index()
    {
        $staff = User::factory()->create(['role' => 'Admin']);
        $staff->assignRole('Admin');
        // No 'manage news' permission

        $this->actingAs($staff);

        $response = $this->get(route('admin.news.index'));

        $response->assertStatus(403);
    }
}
