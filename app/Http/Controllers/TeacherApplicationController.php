<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherApplicationRequest;
use App\Services\TeacherApplicationService;
use Exception;

class TeacherApplicationController extends Controller
{
    protected $service;

    public function __construct(TeacherApplicationService $service)
    {
        $this->service = $service;
    }

    public function create()
    {
        return view('pages.teacher-application');
    }

    public function store(TeacherApplicationRequest $request)
    {
        try {
            $this->service->processApplication($request->validated(), $request->file('cv'));

            return redirect()->route('teacher-application.success')
                ->with('success', 'Thank you for your application! We will review it and contact you within 3-5 business days.');
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تقديم الطلب، يرجى المحاولة مرة أخرى.');
        }
    }

    public function success()
    {
        return view('pages.application-success');
    }
}
