<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherScheduleService;
use Illuminate\Http\Request;
use Exception;

class ScheduleController extends Controller
{
    protected $service;

    public function __construct(TeacherScheduleService $service)
    {
        $this->service = $service;
    }

    /**
     * Display weekly schedule view
     */
    public function index(Request $request)
    {
        try {
            $teacher = auth()->user();
            
            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $data = $this->service->getWeeklyData($teacher, $request);

            return view('teacher.schedule-weekly', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل الجدول الأسبوعي.');
        }
    }
    
    /**
     * Display daily schedule view
     */
    public function daily(Request $request)
    {
        try {
            $teacher = auth()->user();
            
            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $data = $this->service->getDailyData($teacher, $request);

            return view('teacher.schedule-daily', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل الجدول اليومي.');
        }
    }

    /**
     * Print the logged-in teacher's monthly schedule
     */
    public function print(Request $request)
    {
        try {
            $teacher = auth()->user();

            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $month = $request->get('month', now($teacher->getUserTimezone())->format('Y-m'));
            $data = $this->service->getPrintableMonthlyData($teacher, $month);

            return view('admin.schedules.print', $data);
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('Failed to load printable schedule.');
        }
    }
}
