<?php

namespace Tests\Feature\Accountant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PaymentEnrollmentUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required roles and permissions if they don't exist
        $permission = Permission::firstOrCreate(['name' => 'manage accounting']);
        $role = Role::firstOrCreate(['name' => 'Accountant']);
        $role->givePermissionTo($permission);
    }

    public function test_accountant_can_update_enrollment_price_and_currency_successfully(): void
    {
        // Arrange
        $accountant = User::factory()->create(['country' => 'Egypt']);
        $accountant->assignRole('Accountant');
        // Give regional access if needed (assuming based on country for tests)
        $accountant->allowed_countries = ['Egypt'];
        $accountant->save();

        $student = User::factory()->create(['country' => 'Egypt']);
        $course = Course::factory()->create();
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'admin_price' => 100,
            'currency' => 'USD',
        ]);

        // Act
        $response = $this->actingAs($accountant)->patch(route('accountant.payments.enrollment.update', $enrollment->id), [
            'admin_price' => 150.50,
            'currency' => 'EGP',
        ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'admin_price' => 150.50,
            'currency' => 'EGP',
        ]);
    }

    public function test_update_fails_when_currency_is_invalid(): void
    {
        // Arrange
        $accountant = User::factory()->create(['country' => 'Egypt']);
        $accountant->assignRole('Accountant');
        $accountant->allowed_countries = ['Egypt'];
        $accountant->save();

        $student = User::factory()->create(['country' => 'Egypt']);
        $course = Course::factory()->create();
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'admin_price' => 100,
            'currency' => 'USD',
        ]);

        // Act
        $response = $this->actingAs($accountant)->patch(route('accountant.payments.enrollment.update', $enrollment->id), [
            'admin_price' => 150.50,
            'currency' => 'XYZ', // Invalid currency
        ]);

        // Assert
        $response->assertSessionHasErrors(['currency']);
        
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'admin_price' => 100,
            'currency' => 'USD',
        ]);
    }

    public function test_update_fails_when_accountant_does_not_have_regional_access(): void
    {
        // Arrange
        $accountant = User::factory()->create(['country' => 'Egypt']);
        $accountant->assignRole('Accountant');
        $accountant->allowed_countries = ['Egypt'];
        $accountant->save();

        // Student is from Canada, accountant only has access to Egypt
        $student = User::factory()->create(['country' => 'Canada']);
        $course = Course::factory()->create();
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'admin_price' => 100,
            'currency' => 'USD',
        ]);

        // Act
        $response = $this->actingAs($accountant)->patch(route('accountant.payments.enrollment.update', $enrollment->id), [
            'admin_price' => 150.50,
            'currency' => 'EGP',
        ]);

        // Assert
        $response->assertStatus(403);
    }
}
