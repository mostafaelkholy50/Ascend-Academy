<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::create(['name' => 'Student']);
});

test('student can view courses index', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('student.courses.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.courses.index');
    $response->assertViewHas('enrollments');
});
