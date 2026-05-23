<?php

use App\Models\User;
use App\Models\Resource;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
    Storage::fake('public');
});

test('teacher can view resources index', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.resources.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.resources.index');
});

test('teacher can store resource', function () {
    // Arrange
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');
    
    $student = User::factory()->student()->create();
    
    $file = UploadedFile::fake()->create('document.pdf', 100);
    
    // Act
    $response = $this->actingAs($teacher)->post(route('teacher.resources.store'), [
        'student_id' => $student->id,
        'title' => 'Test Document',
        'type' => 'pdf',
        'file' => $file,
    ]);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('resources', [
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'title' => 'Test Document',
        'type' => 'pdf',
    ]);
});
