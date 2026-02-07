<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ContactController;
use App\Http\Controllers\Pages\CoursesController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\ParentUser\DashboardController as ParentDashboardController;
use App\Http\Controllers\Pages\OurProgramsController;
use App\Http\Controllers\Pages\OurTeachersController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\ParentController as AdminParentController;
use App\Http\Controllers\TeacherApplicationController;
use App\Http\Controllers\Admin\TeacherApplicationController as AdminTeacherApplicationController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Pages\NewsController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;

Route::middleware('throttle:60,1')->group(function () {

    Route::get('/test-mail', function () {
        Mail::raw('This is a test email from Laravel!', function ($message) {
            $message->to('mostafaelkholy4321@gmail.com') // غيرها بإيميلك الشخصي
                ->subject('Test Email');
        });
        return 'Email sent!';
    });

    // ============================================
// Public Pages
// ============================================
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/our-programs', [OurProgramsController::class, 'index'])->name('our-programs');
    Route::get('/our-teachers', [OurTeachersController::class, 'index'])->name('our-teachers');
    Route::get('/courses', [CoursesController::class, 'index'])->name('courses');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::get('/news', [NewsController::class, 'index'])->name('news');
    Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

    // ============================================
// Unified Inquiry System
// ============================================
    Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
    Route::get('/get-started', [InquiryController::class, 'getStarted'])->name('get-started');
    Route::get('/register', fn() => redirect()->route('get-started'))->name('register');

    // ============================================
// Teacher Application (Public)
// ============================================
    Route::get('/teacher-application', [TeacherApplicationController::class, 'create'])->name('teacher-application.create');
    Route::post('/teacher-application', [TeacherApplicationController::class, 'store'])->name('teacher-application.store');
    Route::get('/teacher-application/success', [TeacherApplicationController::class, 'success'])->name('teacher-application.success');

    // ============================================
// Authentication
// ============================================
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });

    // ============================================
// Authenticated Routes
// ============================================
    Route::middleware('auth')->group(function () {
        Route::post('/logout', function () {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/')->with('success', 'Logged out successfully.');
        })->name('logout');

        // Generic dashboard redirect based on role
        Route::get('/dashboard', function () {
            $user = auth()->user();
            return match (strtolower($user->role)) {
                'admin' => redirect()->route('admin.dashboard'),
                'teacher' => redirect()->route('teacher.dashboard'),
                'parent' => redirect()->route('parent.dashboard'),
                default => redirect()->route('student.dashboard'),
            };
        })->name('dashboard');
    });

    // ============================================
// Admin Routes
// ============================================
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Add more admin routes here

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
        Route::resource('enrollments', \App\Http\Controllers\Admin\EnrollmentController::class);
        Route::patch('enrollments/{enrollment}/payment-status', [\App\Http\Controllers\Admin\EnrollmentController::class, 'updatePaymentStatus'])
            ->name('enrollments.update-payment-status');

        // Payment Management
        Route::get('payments', [\App\Http\Controllers\Admin\EnrollmentPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/enrollment/{enrollment}', [\App\Http\Controllers\Admin\EnrollmentPaymentController::class, 'show'])->name('payments.show');
        Route::patch('payments/{payment}/status', [\App\Http\Controllers\Admin\EnrollmentPaymentController::class, 'updatePaymentStatus'])->name('payments.update-status');
        Route::post('payments/generate-monthly', [\App\Http\Controllers\Admin\EnrollmentPaymentController::class, 'generateMonthlyPayments'])->name('payments.generate-monthly');
        Route::post('payments/{enrollment}/mark-all-paid', [\App\Http\Controllers\Admin\EnrollmentPaymentController::class, 'markAllPaid'])->name('payments.mark-all-paid');
        Route::post('payments/{enrollment}/mark-all-unpaid', [\App\Http\Controllers\Admin\EnrollmentPaymentController::class, 'markAllUnpaid'])->name('payments.mark-all-unpaid');

        // Schedules Management
        Route::resource('schedules', \App\Http\Controllers\Admin\ScheduleController::class);
        Route::post('schedules/bulk-cancel/{enrollment}', [\App\Http\Controllers\Admin\ScheduleController::class, 'bulkCancel'])
            ->name('schedules.bulk-cancel');
        Route::delete('schedules/bulk-delete/{enrollment}', [\App\Http\Controllers\Admin\ScheduleController::class, 'bulkDelete'])
            ->name('schedules.bulk-delete');

        // Teacher Hours Management
        Route::get('/teacher-hours', [\App\Http\Controllers\Admin\TeacherHourController::class, 'index'])->name('teacher-hours.index');
        Route::patch('/teacher-hours/{teacher}/update-rate', [\App\Http\Controllers\Admin\TeacherHourController::class, 'updateRate'])->name('teacher-hours.update-rate');
        Route::patch('/teacher-hours/{teacherHour}/mark-paid', [\App\Http\Controllers\Admin\TeacherHourController::class, 'markAsPaid'])->name('teacher-hours.mark-paid');
        Route::patch('/teacher-hours/{teacherHour}/mark-unpaid', [\App\Http\Controllers\Admin\TeacherHourController::class, 'markAsUnpaid'])->name('teacher-hours.mark-unpaid');

        // Attendance Management
        Route::get('/attendances', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('attendances.show');

        // Reports Management
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');

        // Profile & Settings
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/avatar', [\App\Http\Controllers\Admin\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');

        // Pricing Tiers Management
        Route::resource('pricing-tiers', \App\Http\Controllers\Admin\PricingTierController::class);

        // News Management
        Route::resource('news', AdminNewsController::class);
    });

    // ============================================
// Teacher Routes
// ============================================
    Route::middleware(['auth'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        // Reports Management
        Route::get('/reports', [\App\Http\Controllers\Teacher\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [\App\Http\Controllers\Teacher\ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [\App\Http\Controllers\Teacher\ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [\App\Http\Controllers\Teacher\ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{report}/edit', [\App\Http\Controllers\Teacher\ReportController::class, 'edit'])->name('reports.edit');
        Route::patch('/reports/{report}', [\App\Http\Controllers\Teacher\ReportController::class, 'update'])->name('reports.update');
        Route::delete('/reports/{report}', [\App\Http\Controllers\Teacher\ReportController::class, 'destroy'])->name('reports.destroy');
        // AJAX endpoint for getting student courses
        Route::get('/reports/student/{student}/courses', [\App\Http\Controllers\Teacher\ReportController::class, 'getStudentCourses'])->name('reports.student-courses');

        // Quick report creation from schedule
        Route::get('/reports/quick/{schedule}', [\App\Http\Controllers\Teacher\ReportController::class, 'quickCreate'])->name('reports.quick-create');

        // Resources Management
        Route::get('/resources', [\App\Http\Controllers\Teacher\ResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/create', [\App\Http\Controllers\Teacher\ResourceController::class, 'create'])->name('resources.create');
        Route::post('/resources', [\App\Http\Controllers\Teacher\ResourceController::class, 'store'])->name('resources.store');
        Route::get('/resources/{resource}', [\App\Http\Controllers\Teacher\ResourceController::class, 'show'])->name('resources.show');
        Route::get('/resources/{resource}/edit', [\App\Http\Controllers\Teacher\ResourceController::class, 'edit'])->name('resources.edit');
        Route::patch('/resources/{resource}', [\App\Http\Controllers\Teacher\ResourceController::class, 'update'])->name('resources.update');
        Route::delete('/resources/{resource}', [\App\Http\Controllers\Teacher\ResourceController::class, 'destroy'])->name('resources.destroy');
        Route::get('/resources/{resource}/download', [\App\Http\Controllers\Teacher\ResourceController::class, 'download'])->name('resources.download');

        // Hours & Earnings
        Route::get('/hours', [\App\Http\Controllers\Teacher\HoursController::class, 'index'])->name('hours.index');

        // My Students page (for reference)
        Route::get('/my-students', function () {
            $teacher = auth()->user();
            $students = \App\Models\User::whereHas('schedules', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->with(['enrollments.course'])->get();

            return view('teacher.my-students', compact('students'));
        })->name('my-students');
        // Schedule Management
        Route::get('/schedule', [\App\Http\Controllers\Teacher\ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/schedule/daily', [\App\Http\Controllers\Teacher\ScheduleController::class, 'daily'])->name('schedule.daily');

        // Attendance Management
        Route::post('/attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/{schedule}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'show'])->name('attendance.show');

        // Profile & Settings
        Route::get('/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [\App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/avatar', [\App\Http\Controllers\Teacher\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [\App\Http\Controllers\Teacher\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    });

    // ============================================
// Student Routes
// ============================================
    Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Schedule Management
        Route::get('/schedule/weekly', [\App\Http\Controllers\Student\ScheduleController::class, 'weekly'])->name('schedule.weekly');
        Route::get('/schedule/daily', [\App\Http\Controllers\Student\ScheduleController::class, 'daily'])->name('schedule.daily');

        // My Courses
        Route::get('/courses', [\App\Http\Controllers\Student\CourseController::class, 'index'])->name('courses.index');

        // Resources
        Route::get('/resources', [\App\Http\Controllers\Student\ResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/{resource}', [\App\Http\Controllers\Student\ResourceController::class, 'show'])->name('resources.show');
        Route::get('/resources/{resource}/download', [\App\Http\Controllers\Student\ResourceController::class, 'download'])->name('resources.download');

        // Progress Reports
        Route::get('/reports', [\App\Http\Controllers\Student\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [\App\Http\Controllers\Student\ReportController::class, 'show'])->name('reports.show');

        // Profile & Settings
        Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [\App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/avatar', [\App\Http\Controllers\Student\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [\App\Http\Controllers\Student\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    });

    // ============================================
// Parent Routes
// ============================================
    Route::middleware(['auth'])->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        // Children Management
        Route::get('/children', [\App\Http\Controllers\ParentUser\ChildrenController::class, 'index'])->name('children.index');
        Route::get('/children/{child}', [\App\Http\Controllers\ParentUser\ChildrenController::class, 'show'])->name('children.show');

        // Schedule Management
        Route::get('/schedule/weekly', [\App\Http\Controllers\ParentUser\ScheduleController::class, 'weekly'])->name('schedule.weekly');
        Route::get('/schedule/daily', [\App\Http\Controllers\ParentUser\ScheduleController::class, 'daily'])->name('schedule.daily');

        // Progress Reports
        Route::get('/reports', [\App\Http\Controllers\ParentUser\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [\App\Http\Controllers\ParentUser\ReportController::class, 'show'])->name('reports.show');

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\ParentUser\AttendanceController::class, 'index'])->name('attendance.index');

        // Profile & Settings
        Route::get('/profile', [\App\Http\Controllers\ParentUser\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\App\Http\Controllers\ParentUser\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\ParentUser\ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [\App\Http\Controllers\ParentUser\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/avatar', [\App\Http\Controllers\ParentUser\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [\App\Http\Controllers\ParentUser\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    });
});