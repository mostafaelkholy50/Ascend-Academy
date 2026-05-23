<?php

use App\Models\User;
use App\Models\StudentEvaluation;
use App\Models\Schedule;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Teacher']);
    Role::firstOrCreate(['name' => 'Parent']);
});

test('teacher can view student evaluations index', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.student-evaluations.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.student-evaluations.index');
});

test('teacher can store student evaluation', function () {
    // Arrange
    $teacher = User::factory()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');
    
    $student = User::factory()->create(['role' => 'Student']);
    
    // Create a schedule to make the student "pending"
    Schedule::create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'starts_at' => now(),
        'ends_at' => now()->addHour(),
        'status' => 'completed',
    ]);
    
    // Act
    $response = $this->actingAs($teacher)->post(route('teacher.student-evaluations.store'), [
        'student_id' => $student->id,
        'q1_score' => 10,
        'q2_score' => 9,
        'q3_score' => 8,
        'q4_score' => 7,
        'q5_score' => 6,
        'q6_score' => 5,
        'q7_score' => 4,
        'q8_score' => 3,
        'q9_score' => 2,
        'q10_score' => 1,
        'notes' => 'Good progress',
    ]);
    
    // Assert
    $response->assertRedirect(route('teacher.student-evaluations.index'));
    $this->assertDatabaseHas('student_evaluations', [
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'total_score' => 55, // Sum of 10+9+8+7+6+5+4+3+2+1 = 55
    ]);
});

test('teacher cannot store evaluation with invalid scores', function () {
    // Arrange
    $teacher = User::factory()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');
    
    $student = User::factory()->create(['role' => 'Student']);
    
    // Act
    $response = $this->actingAs($teacher)->post(route('teacher.student-evaluations.store'), [
        'student_id' => $student->id,
        'q1_score' => 11, // Invalid
        'q2_score' => 9,
    ]);
    
    // Assert
    $response->assertSessionHasErrors(['q1_score']);
});

test('admin can filter student evaluations', function () {
    // Arrange
    Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'Admin']);
    $admin->assignRole('Admin');
    
    $teacher1 = User::factory()->create(['role' => 'Teacher']);
    $teacher2 = User::factory()->create(['role' => 'Teacher']);
    
    $student1 = User::factory()->create(['role' => 'Student', 'name' => 'Student One']);
    $student2 = User::factory()->create(['role' => 'Student', 'name' => 'Student Two']);
    
    // Create evaluations
    StudentEvaluation::create([
        'teacher_id' => $teacher1->id,
        'student_id' => $student1->id,
        'evaluation_date' => now(),
        'evaluation_month' => now()->month,
        'evaluation_year' => now()->year,
        'total_score' => 80,
    ]);
    
    StudentEvaluation::create([
        'teacher_id' => $teacher2->id,
        'student_id' => $student2->id,
        'evaluation_date' => now(),
        'evaluation_month' => now()->month,
        'evaluation_year' => now()->year,
        'total_score' => 90,
    ]);
    
    // Act & Assert
    // Filter by student 1
    $response = $this->actingAs($admin)->get(route('admin.student-evaluations.index', ['student_id' => $student1->id]));
    $response->assertStatus(200);
    $response->assertSee('Student One');
    $response->assertSee('80/100');
    $response->assertDontSee('90/100');
    
    // Filter by teacher 2
    $response = $this->actingAs($admin)->get(route('admin.student-evaluations.index', ['teacher_id' => $teacher2->id]));
    $response->assertStatus(200);
    $response->assertSee('Student Two');
    $response->assertSee('90/100');
    $response->assertDontSee('80/100');
});

test('quality control can view student evaluations', function () {
    // Arrange
    Role::firstOrCreate(['name' => 'QualityControl']);
    $qc = User::factory()->create(['role' => 'QualityControl']);
    $qc->assignRole('QualityControl');
    
    // Act
    $response = $this->actingAs($qc)->get(route('admin.student-evaluations.index'));
    
    // Assert
    $response->assertStatus(200);
});
