<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\ParentController as AdminParentController;
use App\Http\Controllers\Admin\TeacherApplicationController as AdminTeacherApplicationController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\AbsentStudentController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StudentEvaluationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PricingTierController;
use App\Http\Controllers\NotificationController;
use App\Enums\UserRole;

Route::middleware([
    'auth',
    'role_or_permission:' . implode('|', [
        UserRole::SuperAdmin->value,
        UserRole::Admin->value,
    ]),
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Inquiries Management
    Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [AdminInquiryController::class, 'show'])->name('inquiries.show');
    Route::post('/inquiries/{inquiry}/convert', [AdminInquiryController::class, 'convertToParent'])->name('inquiries.convert');
    Route::patch('/inquiries/{inquiry}/status', [AdminInquiryController::class, 'updateStatus'])->name('inquiries.update-status');
    Route::delete('/inquiries/{inquiry}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');

    // Parents Management
    Route::get('/parents', [AdminParentController::class, 'index'])->name('parents.index');
    Route::get('/parents/create', [AdminParentController::class, 'create'])->name('parents.create');
    Route::post('/parents', [AdminParentController::class, 'store'])->name('parents.store');
    Route::get('/parents/{parent}', [AdminParentController::class, 'show'])->name('parents.show');
    Route::patch('/parents/{parent}', [AdminParentController::class, 'update'])->name('parents.update');
    Route::patch('/parents/{parent}/password', [AdminParentController::class, 'updatePassword'])->name('parents.update-password');
    Route::delete('/parents/{parent}', [AdminParentController::class, 'destroy'])->name('parents.destroy');
    Route::post('/parents/{parent}/children', [AdminParentController::class, 'addChild'])->name('parents.add-child');
    Route::delete('/parents/{parent}/children/{child}', [AdminParentController::class, 'removeChild'])->name('parents.remove-child');

    // Teacher Applications Management
    Route::get('/teacher-applications', [AdminTeacherApplicationController::class, 'index'])->name('teacher-applications.index');
    Route::get('/teacher-applications/{application}', [AdminTeacherApplicationController::class, 'show'])->name('teacher-applications.show');
    Route::post('/teacher-applications/{application}/convert', [AdminTeacherApplicationController::class, 'convertToTeacher'])->name('teacher-applications.convert');
    Route::patch('/teacher-applications/{application}/status', [AdminTeacherApplicationController::class, 'updateStatus'])->name('teacher-applications.update-status');
    Route::delete('/teacher-applications/{application}', [AdminTeacherApplicationController::class, 'destroy'])->name('teacher-applications.destroy');

    // Students (Children) Management
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('students.show');
    Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
    Route::patch('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('students.destroy');
    Route::patch('/students/{student}/password', [AdminStudentController::class, 'updatePassword'])->name('students.update-password');
    Route::get('/absent-students', [AbsentStudentController::class, 'index'])->name('absent-students.index');
    Route::get('/absent-students/list', [AbsentStudentController::class, 'list'])->name('absent-students.list');

    // Teachers Management
    Route::get('/teachers', [AdminTeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [AdminTeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [AdminTeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}', [AdminTeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{teacher}/edit', [AdminTeacherController::class, 'edit'])->name('teachers.edit');
    Route::patch('/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('teachers.update');
    Route::patch('/teachers/{teacher}/password', [AdminTeacherController::class, 'updatePassword'])->name('teachers.update-password');
    Route::delete('/teachers/{teacher}', [AdminTeacherController::class, 'destroy'])->name('teachers.destroy');

    // Courses Management
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
    Route::patch('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    // Enrollments Management
    Route::resource('enrollments', EnrollmentController::class);
    Route::patch('enrollments/{enrollment}/payment-status', [EnrollmentController::class, 'updatePaymentStatus'])->name('enrollments.update-payment-status');


    // Schedules Management
    Route::get('/schedules/requests', [ScheduleController::class, 'rescheduleRequests'])->name('schedules.requests');
    Route::post('/schedules/requests/{rescheduleRequest}/approve', [ScheduleController::class, 'approveRescheduleRequest'])->name('schedules.requests.approve');
    Route::post('/schedules/requests/{rescheduleRequest}/reject', [ScheduleController::class, 'rejectRescheduleRequest'])->name('schedules.requests.reject');
    Route::resource('schedules', ScheduleController::class);
    Route::post('schedules/bulk-cancel/{enrollment}', [ScheduleController::class, 'bulkCancel'])->name('schedules.bulk-cancel');
    Route::delete('schedules/bulk-delete/{enrollment}', [ScheduleController::class, 'bulkDelete'])->name('schedules.bulk-delete');
    Route::get('schedules/enrollment/{enrollment}/edit-pattern', [ScheduleController::class, 'editPattern'])->name('schedules.edit-pattern');
    Route::put('schedules/enrollment/{enrollment}/update-pattern', [ScheduleController::class, 'updatePattern'])->name('schedules.update-pattern');
    Route::post('schedules/enrollment/{enrollment}/toggle-day/{day}', [ScheduleController::class, 'toggleDayStatus'])->name('schedules.toggle-day');
    Route::post('schedules/enrollment/{enrollment}/toggle-all', [ScheduleController::class, 'toggleAllDays'])->name('schedules.toggle-all');

    // Teacher Hours Management
    Route::patch('/teacher-hours/{teacher}/update-rate', [AdminTeacherController::class, 'updateRate'])->name('teacher-hours.update-rate');

    // Attendance Management
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');

    // Reports Management
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    // Student Evaluations Management
    Route::get('/student-evaluations', [StudentEvaluationController::class, 'index'])->name('student-evaluations.index');
    Route::get('/student-evaluations/{studentEvaluation}', [StudentEvaluationController::class, 'show'])->name('student-evaluations.show');

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

    // Pricing Tiers Management
    Route::resource('pricing-tiers', PricingTierController::class);

    // News Management
    Route::resource('news', AdminNewsController::class)->middleware('permission:manage news');
});
