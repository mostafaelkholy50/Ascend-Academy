<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParentDashboardRequest;
use App\Services\ParentDashboardService;
use Exception;

class DashboardController extends Controller
{
    /**
     * Display the parent dashboard.
     *
     * @param ParentDashboardRequest $request
     * @param ParentDashboardService $service
     * @return \Illuminate\Http\Response
     */
    public function index(ParentDashboardRequest $request, ParentDashboardService $service)
    {
        try {
            $parent = auth()->user();
            
            $data = $service->getDashboardData($parent, $request);
            
            return view('parent.dashboard', array_merge([
                'parent' => $parent,
            ], $data));
            
        } catch (Exception $e) {
            // Log the error or handle it as needed
            // For now, return with error message using base controller method
            return $this->errorResponse('حدث خطأ أثناء تحميل لوحة التحكم. الرجاء المحاولة مرة أخرى.');
        }
    }
}
