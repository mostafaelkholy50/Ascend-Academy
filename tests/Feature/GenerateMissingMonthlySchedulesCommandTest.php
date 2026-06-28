<?php

use App\Models\Enrollment;
use App\Services\ScheduleService;
use Carbon\Carbon;

beforeEach(function () {
    // Setup might be needed if there are global states, but we will mock the ScheduleService
});

test('it targets both current and next month if run on or after the 25th', function () {
    // Mock the ScheduleService to verify the target month
    $mockService = Mockery::mock(ScheduleService::class);
    $this->app->instance(ScheduleService::class, $mockService);

    // Create a dummy enrollment so the chunk block executes at least once
    // We need an active enrollment for the command to call generateMonthlySchedules
    $enrollment = Enrollment::factory()->create(['status' => 'active']);

    // Set the current date to the 28th of the current month
    Carbon::setTestNow(Carbon::now()->startOfMonth()->addDays(27)); // 28th day
    $expectedCurrentMonth = Carbon::now()->startOfMonth();
    $expectedNextMonth = Carbon::now()->addMonth()->startOfMonth();

    $mockService->shouldReceive('generateMonthlySchedules')
        ->twice()
        ->withArgs(function ($passedEnrollment, $targetMonth) use ($enrollment, $expectedCurrentMonth, $expectedNextMonth) {
            return $passedEnrollment->id === $enrollment->id && 
                   ($targetMonth->isSameDay($expectedCurrentMonth) || $targetMonth->isSameDay($expectedNextMonth));
        })
        ->andReturn(['success' => true, 'count' => 4]);

    $this->artisan('schedules:generate-missing')
        ->assertSuccessful();

    Carbon::setTestNow(); // Reset
});

test('it targets the current month if run before the 25th', function () {
    $mockService = Mockery::mock(ScheduleService::class);
    $this->app->instance(ScheduleService::class, $mockService);

    $enrollment = Enrollment::factory()->create(['status' => 'active']);

    // Set the current date to the 10th of the current month
    Carbon::setTestNow(Carbon::now()->startOfMonth()->addDays(9)); // 10th day
    $expectedMonth = Carbon::now()->startOfMonth();

    $mockService->shouldReceive('generateMonthlySchedules')
        ->once()
        ->withArgs(function ($passedEnrollment, $targetMonth) use ($enrollment, $expectedMonth) {
            return $passedEnrollment->id === $enrollment->id && 
                   $targetMonth->isSameDay($expectedMonth);
        })
        ->andReturn(['success' => true, 'count' => 4]);

    $this->artisan('schedules:generate-missing')
        ->assertSuccessful();

    Carbon::setTestNow(); // Reset
});
