<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherHoursService;
use Illuminate\Http\Request;
use Exception;

class HoursController extends Controller
{
    protected $service;

    public function __construct(TeacherHoursService $service)
    {
        $this->service = $service;
    }

    /**
     * Display teacher's hours and earnings for selected month
     */
    public function index(Request $request)
    {
        try {
            $teacher = auth()->user();
            
            if ($teacher->role !== 'Teacher') {
                abort(403);
            }

            $data = $this->service->getHoursData($teacher, $request);

            return view('teacher.hours.index', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل الساعات والأرباح.');
        }
    }
}
