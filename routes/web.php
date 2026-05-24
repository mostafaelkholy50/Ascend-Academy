<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ContactController;
use App\Http\Controllers\Pages\CoursesController;
use App\Http\Controllers\Pages\OurProgramsController;
use App\Http\Controllers\Pages\OurTeachersController;
use App\Http\Controllers\TeacherApplicationController;
use App\Http\Controllers\Pages\NewsController;
use Illuminate\Support\Facades\Mail;

Route::middleware('throttle:60,1')->group(function () {

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

    Route::get('/refresh-csrf', function () {
        return response()->json(['token' => csrf_token()]);
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
            if ($user->hasRole('SuperAdmin')) {
                return redirect()->route('admin.dashboard');
            }
            return match (strtolower($user->role)) {
                'admin' => redirect()->route('admin.dashboard'),
                'schedulermanager' => redirect()->route('scheduler.dashboard'),
                'teacher' => redirect()->route('teacher.dashboard'),
                'parent' => redirect()->route('parent.dashboard'),
                'accountant' => redirect()->route('accountant.dashboard'),
                'qualitycontrol' => redirect()->route('qualitycontrol.dashboard'),
                default => redirect()->route('student.dashboard'),
            };
        })->name('dashboard');

        // Books Management & Viewer
        Route::get('/dashboard/books', [\App\Http\Controllers\Dashboard\BookController::class, 'index'])->name('books.index');
        Route::get('/dashboard/books/create', [\App\Http\Controllers\Dashboard\BookController::class, 'create'])->name('books.create');
        Route::post('/dashboard/books', [\App\Http\Controllers\Dashboard\BookController::class, 'store'])->name('books.store');
        Route::get('/dashboard/books/{book}', [\App\Http\Controllers\Dashboard\BookController::class, 'show'])->name('books.show');
        Route::get('/dashboard/books/{book}/edit', [\App\Http\Controllers\Dashboard\BookController::class, 'edit'])->name('books.edit');
        Route::patch('/dashboard/books/{book}', [\App\Http\Controllers\Dashboard\BookController::class, 'update'])->name('books.update');
        Route::delete('/dashboard/books/{book}', [\App\Http\Controllers\Dashboard\BookController::class, 'destroy'])->name('books.destroy');
        Route::get('/dashboard/books/{book}/stream', [\App\Http\Controllers\Dashboard\BookController::class, 'stream'])->name('books.stream');
        Route::get('/dashboard/books/{book}/download', [\App\Http\Controllers\Dashboard\BookController::class, 'download'])->name('books.download');
    });
});

require __DIR__ . '/accountant.php';

// Fallback to serve storage files if public/storage symlink is missing or broken on Windows/XAMPP
Route::get('/storage/{path}', function ($path) {
    $storagePath = storage_path('app/public');
    $fullPath = realpath($storagePath . '/' . $path);

    if (!$fullPath || !str_starts_with($fullPath, realpath($storagePath))) {
        abort(403, 'Unauthorized access.');
    }

    if (!file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*');
// route مؤقت للاختبار
Route::get('/upload-limits', function () {
    return [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
        'memory_limit' => ini_get('memory_limit'),
    ];
});
