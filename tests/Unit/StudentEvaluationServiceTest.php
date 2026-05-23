<?php

use App\Services\StudentEvaluationService;
use App\Models\StudentEvaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('it can calculate aggregate scores for a student', function () {
    $service = app(StudentEvaluationService::class);
    
    $student = User::factory()->create();
    $teacher = User::factory()->create();
    
    // Create evaluations
    StudentEvaluation::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'evaluation_month' => 1,
        'evaluation_year' => 2026,
        'q1_score' => 8,
        'q2_score' => 6,
        'total_score' => 14,
        'evaluation_date' => now(),
    ]);
    
    StudentEvaluation::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'evaluation_month' => 2,
        'evaluation_year' => 2026,
        'q1_score' => 10,
        'q2_score' => 8,
        'total_score' => 18,
        'evaluation_date' => now(),
    ]);
    
    $aggregates = $service->getAggregateScores($student->id);
    
    expect($aggregates)->not->toBeEmpty();
    expect($aggregates['q1_score'])->toBe(9.0); // (8 + 10) / 2
    expect($aggregates['q2_score'])->toBe(7.0); // (6 + 8) / 2
    expect($aggregates['total_score'])->toBe(16.0); // (14 + 18) / 2
    expect($aggregates['count'])->toBe(2);
});

test('it can fetch evaluation by month and year', function () {
    $service = app(StudentEvaluationService::class);
    
    $student = User::factory()->create();
    $teacher = User::factory()->create();
    
    $evaluation = StudentEvaluation::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'evaluation_month' => 5,
        'evaluation_year' => 2026,
        'q1_score' => 8,
        'total_score' => 8,
        'evaluation_date' => now(),
    ]);
    
    $fetched = $service->getEvaluationByMonth($student->id, 5, 2026);
    
    expect($fetched)->not->toBeNull();
    expect($fetched->id)->toBe($evaluation->id);
});
