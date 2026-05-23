<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\StudentEvaluationController;
use App\Http\Controllers\Teacher\ResourceController;
use App\Http\Controllers\Teacher\HoursController;
use App\Http\Controllers\Teacher\ScheduleController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\ProfileController;
use App\Http\Controllers\Teacher\ReportController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    // Reports Management
    Route::get('/reports/student-courses/{student}', [ReportController::class, 'getStudentCourses'])->name('reports.student-courses');
    Route::get('/reports/quick-create/{schedule}', [ReportController::class, 'quickCreate'])->name('reports.quick-create');
    Route::resource('reports', ReportController::class);

    // Student Evaluations Management
    Route::get('/student-evaluations', [StudentEvaluationController::class, 'index'])->name('student-evaluations.index');
    Route::get('/student-evaluations/create', [StudentEvaluationController::class, 'create'])->name('student-evaluations.create');
    Route::get('/student-evaluations/pending', [StudentEvaluationController::class, 'pending'])->name('student-evaluations.pending');
    Route::get('/student-evaluations/summary', [StudentEvaluationController::class, 'summary'])->name('student-evaluations.summary');
    Route::post('/student-evaluations', [StudentEvaluationController::class, 'store'])->name('student-evaluations.store');
    Route::get('/student-evaluations/{studentEvaluation}', [StudentEvaluationController::class, 'show'])->name('student-evaluations.show');

    // Resources Management
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/edit', [ResourceController::class, 'edit'])->name('resources.edit');
    Route::patch('/resources/{resource}', [ResourceController::class, 'update'])->name('resources.update');
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');

    // Hours & Earnings
    Route::get('/hours', [HoursController::class, 'index'])->name('hours.index');

    // My Students page
    Route::get('/my-students', function () {
        $teacher = auth()->user();
        $students = \App\Models\User::roleStudent()->whereHas('schedules', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)
                ->whereMonth('starts_at', now()->month)
                ->whereYear('starts_at', now()->year);
        })->with(['enrollments.course'])->get();

        return view('teacher.my-students', compact('students'));
    })->name('my-students');

    // Schedule Management
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/daily', [ScheduleController::class, 'daily'])->name('schedule.daily');

    // Attendance Management
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/waiting', [AttendanceController::class, 'notifyWaiting'])->name('attendance.notify-waiting');
    Route::get('/attendance/{schedule}', [AttendanceController::class, 'show'])->name('attendance.show');

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
