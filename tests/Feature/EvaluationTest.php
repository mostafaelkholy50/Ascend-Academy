<?php

use App\Models\User;
use App\Models\TeacherEvaluation;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'QualityControl']);
    Role::create(['name' => 'Teacher']);
});

test('quality control user can view evaluations center', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'QualityControl']);
    $user->assignRole('QualityControl');
    
    // Act
    $response = $this->actingAs($user)->get(route('qualitycontrol.reports.center'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('qualitycontrol.reports.center');
});

test('quality control user can store evaluation', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'QualityControl']);
    $user->assignRole('QualityControl');
    
    $teacher = User::factory()->teacher()->create();
    
    $data = [
        'q1_score' => 8,
        'q2_score' => 9,
        'q3_score' => 10,
        'q4_score' => 7,
        'q5_score' => 8,
        'q6_score' => 9,
        'q7_score' => 10,
        'q8_score' => 8,
        'q9_score' => 9,
        'q10_score' => 10,
        'notes' => 'Good job',
    ];
    
    // Act
    $response = $this->actingAs($user)->post(route('qualitycontrol.evaluations.store', $teacher), $data);
    
    // Assert
    $response->assertStatus(302);
    $response->assertRedirect(route('qualitycontrol.reports.center'));
    
    $this->assertDatabaseHas('teacher_evaluations', [
        'teacher_id' => $teacher->id,
        'total_score' => 88, // Sum is 88
    ]);
});

test('validation fails when storing evaluation with invalid data', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'QualityControl']);
    $user->assignRole('QualityControl');
    
    $teacher = User::factory()->teacher()->create();
    
    $data = [
        'q1_score' => 11, // Invalid
    ];
    
    // Act
    $response = $this->actingAs($user)->post(route('qualitycontrol.evaluations.store', $teacher), $data);
    
    // Assert
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['q1_score']);
});
