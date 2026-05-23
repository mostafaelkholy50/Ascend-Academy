<?php

namespace App\Http\Controllers\Admin;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\InquiryService;
use App\Http\Requests\Admin\UpdateInquiryStatusRequest;

class InquiryController extends Controller
{
    protected $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    /**
     * عرض جميع الطلبات
     */
    public function index(Request $request)
    {
        $inquiries = $this->inquiryService->getIndexData($request);
        return view('admin.inquiries.index', compact('inquiries'));
    }

    /**
     * عرض تفاصيل طلب محدد
     */
    public function show(Inquiry $inquiry)
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    /**
     * تحويل الطلب إلى حساب Parent
     */
    public function convertToParent(Inquiry $inquiry)
    {
        try {
            $result = $this->inquiryService->convertToParent($inquiry);
            $parent = $result['parent'];
            $password = $result['password'];

            return redirect()
                ->route('admin.parents.show', $parent->id)
                ->with('success', "Parent account created successfully! Temporary password: {$password}");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(UpdateInquiryStatusRequest $request, Inquiry $inquiry)
    {
        $this->inquiryService->updateStatus($inquiry, $request->validated());

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * حذف طلب
     */
    public function destroy(Inquiry $inquiry)
    {
        $this->inquiryService->deleteInquiry($inquiry);
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }
}
