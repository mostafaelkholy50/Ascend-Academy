<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentCourseService;
use Exception;

class CourseController extends Controller
{
    protected $service;

    public function __construct(StudentCourseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            $enrollments = $this->service->getCoursesData($student);

            return view('student.courses.index', compact('enrollments'));
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل الكورسات.');
        }
    }
}
