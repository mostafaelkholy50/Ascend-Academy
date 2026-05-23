<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherDashboardService;
use Exception;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(TeacherDashboardService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $teacher = auth()->user();

            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $data = $this->service->getDashboardData($teacher);

            return view('teacher.dashboard', $data);

        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل لوحة التحكم للمدرس.');
        }
    }
}
