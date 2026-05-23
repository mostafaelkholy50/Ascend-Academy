<?php

namespace App\Http\Controllers;

use App\Http\Requests\InquiryRequest\StoreInquiryRequest;
use App\Services\InquiryService;
use Exception;

class InquiryController extends Controller
{
    protected $service;

    public function __construct(InquiryService $service)
    {
        $this->service = $service;
    }

    public function store(StoreInquiryRequest $request)
    {
        try {
            $message = $this->service->processInquiry($request->validated());

            return $this->successResponse($message);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء إرسال طلبك، يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Show the get started / registration page
     */
    public function getStarted()
    {
        return view('pages.get-started');
    }
}
