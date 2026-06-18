<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Scheduler\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ReportController;

Route::middleware(['auth', 'role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations'])->prefix('scheduler')->name('scheduler.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/search', [DashboardController::class, 'ajaxSearch'])->name('dashboard.search');
    
    // Students & Teachers (View only + Timezone info)
    Route::get('/students', [DashboardController::class, 'students'])->name('students.index');
    Route::get('/students/search', [DashboardController::class, 'ajaxSearchStudents'])->name('students.search');
    Route::get('/students/{student}', [DashboardController::class, 'showStudent'])->name('students.show');
    
    Route::get('/teachers', [DashboardController::class, 'teachers'])->name('teachers.index');
    Route::get('/teachers/search', [DashboardController::class, 'ajaxSearchTeachers'])->name('teachers.search');
    Route::get('/teachers/{teacher}', [DashboardController::class, 'showTeacher'])->name('teachers.show');
    
    Route::get('/availability/{user}', [DashboardController::class, 'availability'])->name('availability');
    Route::post('/availability/{user}', [DashboardController::class, 'saveAvailability']);

    // Schedule Management
    Route::get('/schedules/print', [ScheduleController::class, 'print'])->name('schedules.print');
    Route::resource('schedules', ScheduleController::class);
    
    // Attendance Management
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::get('/attendance/verify/{schedule}', [AttendanceController::class, 'verify'])->name('attendance.verify');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    
    // Reports (Scheduler might need to see/write reports)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Dedicated Teacher Availability Route (for teachers themselves)
Route::post('/teacher/availability/save', [DashboardController::class, 'saveAvailability'])->name('teacher.availability.save')->middleware('auth');
