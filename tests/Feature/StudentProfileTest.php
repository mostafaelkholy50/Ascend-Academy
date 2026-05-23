<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::create(['name' => 'Student']);
});

test('student can view profile', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('student.profile.show'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.profile.show');
    $response->assertViewHas('user');
    $response->assertViewHas('stats');
});

test('student can update profile', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->patch(route('student.profile.update'), [
        'name' => 'New Name',
        'email' => 'newemail@example.com',
        'phone' => '1234567890',
    ]);
    
    // Assert
    $response->assertRedirect(route('student.profile.show'));
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'newemail@example.com',
    ]);
});

test('student can update password', function () {
    // Arrange
    $user = User::factory()->student()->create([
        'password' => Hash::make('password123'),
    ]);
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->patch(route('student.profile.password'), [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);
    
    // Assert
    $response->assertRedirect();
    $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
});
