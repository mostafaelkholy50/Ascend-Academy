<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accountant\DashboardController;
use App\Http\Controllers\Accountant\PaymentController;
use App\Http\Controllers\Accountant\TeacherHourController;
use App\Enums\UserRole;

Route::middleware([
    'auth',
    'role_or_permission:' . implode('|', [
        UserRole::Accountant->value,
        UserRole::SuperAdmin->value,
        UserRole::Admin->value,
        'manage accounting',
    ]),
])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/enrollment/{enrollment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/enrollment/{enrollment}', [PaymentController::class, 'updateEnrollment'])->name('payments.enrollment.update');
    Route::delete('/payments/enrollment/{enrollment}', [PaymentController::class, 'destroyEnrollment'])->name('payments.enrollment.destroy');
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.update-status');

    // Teacher Hours
    Route::get('/teacher-hours', [TeacherHourController::class, 'index'])->name('teacher-hours.index');
    Route::get('/teacher-hours/{teacher}', [TeacherHourController::class, 'show'])->name('teacher-hours.show');
    Route::get('/teacher-hours/{teacher}/pdf', [TeacherHourController::class, 'exportPdf'])->name('teacher-hours.pdf');
    Route::post('/teacher-hours/mark-paid', [TeacherHourController::class, 'markAsPaid'])->name('teacher-hours.mark-paid');
    Route::post('/teacher-hours/mark-unpaid', [TeacherHourController::class, 'markAsUnpaid'])->name('teacher-hours.mark-unpaid');
    Route::patch('/teacher-hours/{teacher}/update-rate', [TeacherHourController::class, 'updateRate'])->name('teacher-hours.update-rate');
    Route::delete('/attendances/{attendance}', [TeacherHourController::class, 'destroyAttendance'])->name('attendances.destroy');
});
