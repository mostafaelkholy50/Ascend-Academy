<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QualityControl\DashboardController;
use App\Http\Controllers\QualityControl\EvaluationController;
use App\Http\Controllers\Scheduler\ScheduleController;

Route::middleware(['auth', 'role_or_permission:SuperAdmin|QualityControl|manage quality|view evaluations'])->prefix('quality-control')->name('qualitycontrol.')->group(function () {
    // The ONE Table Center (The main dashboard)
    Route::get('/', [EvaluationController::class, 'center'])->name('dashboard');
    Route::get('/evaluation-center', [EvaluationController::class, 'center'])->name('reports.center');
    
    // Evaluate a teacher
    Route::get('/evaluate/{teacher}', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/evaluate/{teacher}', [EvaluationController::class, 'store'])->name('evaluations.store');

    // History and Reports
    Route::get('/reports/teacher/{teacher}', [EvaluationController::class, 'teacherReport'])->name('reports.teacher');
});
