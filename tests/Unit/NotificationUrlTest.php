<?php

use App\Models\Report;
use App\Models\User;
use App\Notifications\ReportAddedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('report added notification returns relative urls for database channel', function () {
    // Create necessary models
    $teacher = User::factory()->create(['role' => 'Teacher']);
    $student = User::factory()->create(['role' => 'Student']);
    $parent = User::factory()->create(['role' => 'Parent']);
    
    $report = Report::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'report_date' => now(),
    ]);

    $notification = new ReportAddedNotification($report);

    // Test for Parent
    $parentData = $notification->toArray($parent);
    expect($parentData['url'])->toStartWith('/')
        ->not->toContain('http://')
        ->not->toContain('https://')
        ->toContain('/parent/children');

    // Test for Student
    $studentData = $notification->toArray($student);
    expect($studentData['url'])->toStartWith('/')
        ->not->toContain('http://')
        ->not->toContain('https://')
        ->toContain('/student/reports');
});

test('url helper handles relative notification urls correctly', function () {
    $relativeUrl = '/parent/children/evaluations?child=1';
    $fullUrl = url($relativeUrl);
    
    expect($fullUrl)->toStartWith('http')
        ->toContain($relativeUrl);
});
