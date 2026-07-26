<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\TeacherHour;
use Carbon\Carbon;

class TeacherHoursLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_hours_log_renders_correctly()
    {
        // Arrange
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $admin = User::factory()->create(['role' => 'Admin']);
        $admin->assignRole('Admin');
        
        $teacher = User::factory()->create(['role' => 'Teacher']);

        // Create 15 teacher hour logs to ensure all render and pagination/limit isn't restricted
        for ($i = 1; $i <= 15; $i++) {
            $isPaid = $i % 2 === 0;
            TeacherHour::create([
                'teacher_id' => $teacher->id,
                'year' => $i > 12 ? 2027 : 2026,
                'month' => $i > 12 ? $i - 12 : $i,
                'total_hours' => 10.5 + $i,
                'total_salary' => 100.00 + ($i * 10),
                'is_paid' => $isPaid,
                'paid_at' => $isPaid ? Carbon::now()->subDays($i) : null,
            ]);
        }

        // Act
        $response = $this->actingAs($admin)->get(route('admin.teachers.show', $teacher->id));

        // Assert
        $response->assertStatus(200);

        // Check if the title exists and counts the logs properly
        $response->assertSee('Teaching Hours Log (15)');

        // Check if correct data appears for a specific month
        // e.g. i=1 => Month 1 (January), 11.50 hours, Unpaid
        $response->assertSee('January 2026');
        $response->assertSee('11.50 hours');
        $response->assertSee('Unpaid');

        // Check a paid one
        // i=2 => Month 2 (February), 12.50 hours, Paid
        $response->assertSee('February 2026');
        $response->assertSee('12.50 hours');
        $response->assertSee('Paid');

        // Check that all 15 elements are present
        $content = $response->getContent();
        // Since we created 15 records, they should all be in the list
        $this->assertEquals(15, substr_count($content, 'bg-gray-50 rounded-lg'));
    }
}
