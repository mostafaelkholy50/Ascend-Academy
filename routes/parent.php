<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParentUser\DashboardController as ParentDashboardController;
use App\Http\Controllers\ParentUser\ChildrenController;
use App\Http\Controllers\ParentUser\ScheduleController;
use App\Http\Controllers\ParentUser\ReportController;
use App\Http\Controllers\ParentUser\EvaluationController;
use App\Http\Controllers\ParentUser\AttendanceController;
use App\Http\Controllers\ParentUser\ProfileController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth', 'role:Parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

    // Children Management
    Route::get('/children', [ChildrenController::class, 'index'])->name('children.index');
    Route::get('/children/{child}', [ChildrenController::class, 'show'])->name('children.show');

    // Schedule Management
    Route::get('/schedule/weekly', [ScheduleController::class, 'weekly'])->name('schedule.weekly');
    Route::get('/schedule/daily', [ScheduleController::class, 'daily'])->name('schedule.daily');

    // Progress Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    // Detailed Evaluations
    Route::get('/children/{child}/evaluations', [EvaluationController::class, 'show'])->name('children.evaluations');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
});
