<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentDashboardService;
use Exception;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(StudentDashboardService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $student = auth()->user();
            
            // تأكيد الأمان: التأكد من أن المستخدم طالب
            if (!$student->isStudent() && !$student->isAdmin()) {
                abort(403);
            }

            $data = $this->service->getDashboardData($student);

            return view('student.dashboard', array_merge([
                'student' => $student,
            ], $data));
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل لوحة التحكم. الرجاء المحاولة مرة أخرى.');
        }
    }
}
