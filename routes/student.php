<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ScheduleController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\ResourceController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Enums\UserRole;

Route::middleware([
    'auth',
    'role_or_permission:' . implode('|', [
        UserRole::Student->value,
        UserRole::SuperAdmin->value,
        UserRole::Admin->value,
    ]),
])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Schedule Management
    Route::get('/schedule/weekly', [ScheduleController::class, 'weekly'])->name('schedule.weekly');
    Route::get('/schedule/daily', [ScheduleController::class, 'daily'])->name('schedule.daily');

    // My Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

    // Resources
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');

    // Progress Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

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
