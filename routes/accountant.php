<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accountant\DashboardController;
use App\Http\Controllers\Accountant\PaymentController;
use App\Http\Controllers\Accountant\TeacherHourController;

Route::middleware(['auth', 'role_or_permission:Accountant|SuperAdmin|Admin|manage accounting'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/enrollment/{enrollment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/enrollment/{enrollment}', [PaymentController::class, 'updateEnrollment'])->name('payments.enrollment.update');
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.update-status');

    // Teacher Hours
    Route::get('/teacher-hours', [TeacherHourController::class, 'index'])->name('teacher-hours.index');
    Route::post('/teacher-hours/mark-paid', [TeacherHourController::class, 'markAsPaid'])->name('teacher-hours.mark-paid');
    Route::post('/teacher-hours/mark-unpaid', [TeacherHourController::class, 'markAsUnpaid'])->name('teacher-hours.mark-unpaid');
    Route::patch('/teacher-hours/{teacher}/update-rate', [TeacherHourController::class, 'updateRate'])->name('teacher-hours.update-rate');
});
