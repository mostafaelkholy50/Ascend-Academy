<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
    Storage::fake('public');
});

test('teacher can view profile', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.profile.show'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.profile.show');
});

test('teacher can update profile info', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->patch(route('teacher.profile.update'), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '1234567890',
    ]);
    
    // Assert
    $response->assertRedirect(route('teacher.profile.show'));
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('teacher can update avatar', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');
    
    // Act
    $response = $this->actingAs($user)->post(route('teacher.profile.avatar.update'), [
        'avatar' => $file,
    ]);
    
    // Assert
    $response->assertRedirect();
    $user->refresh();
    $this->assertNotNull($user->avatar);
    Storage::disk('public')->assertExists($user->avatar);
});
