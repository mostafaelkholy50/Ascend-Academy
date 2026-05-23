<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentScheduleService;
use Illuminate\Http\Request;
use Exception;

class ScheduleController extends Controller
{
    protected $service;

    public function __construct(StudentScheduleService $service)
    {
        $this->service = $service;
    }

    public function weekly(Request $request)
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            $data = $this->service->getWeeklyData($student, $request);

            return view('student.schedule-weekly', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل الجدول الأسبوعي.');
        }
    }

    public function daily(Request $request)
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            $data = $this->service->getDailyData($student, $request);

            return view('student.schedule-daily', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل الجدول اليومي.');
        }
    }
}
