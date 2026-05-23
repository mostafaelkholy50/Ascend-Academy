<?php

use App\Models\User;
use App\Models\Resource;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::create(['name' => 'Student']);
});

test('student can view resources index', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('student.resources.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.resources.index');
});

test('student can view specific resource', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    $teacher = User::factory()->teacher()->create();
    $course = Course::create(['title' => 'Math']);
    
    $resource = Resource::create([
        'student_id' => $user->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'title' => 'Math Book',
        'type' => 'link',
        'external_url' => 'https://example.com',
    ]);
    
    // Act
    $response = $this->actingAs($user)->get(route('student.resources.show', $resource->id));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.resources.show');
    $response->assertViewHas('resource');
});

test('student cannot view resource of another student', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    $anotherStudent = User::factory()->student()->create();
    $teacher = User::factory()->teacher()->create();
    $course = Course::create(['title' => 'Math']);
    
    $resource = Resource::create([
        'student_id' => $anotherStudent->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'title' => 'Math Book',
        'type' => 'link',
        'external_url' => 'https://example.com',
    ]);
    
    // Act
    $response = $this->actingAs($user)->get(route('student.resources.show', $resource->id));
    
    // Assert
    $response->assertStatus(302); // Redirects due to errorResponse in catch block
});
