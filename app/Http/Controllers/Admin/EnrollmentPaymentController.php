<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Http\Requests\Admin\UpdatePaymentStatusRequest;

class EnrollmentPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $data = $this->paymentService->getAdminIndexData($request);
        return view('admin.payments.index', $data);
    }

    public function show(Enrollment $enrollment)
    {
        $this->paymentService->generatePaymentsForEnrollment($enrollment);

        $enrollment->load(['student', 'course', 'payments' => function($query) {
            $query->orderBy('month', 'desc');
        }]);

        return view('admin.payments.show', compact('enrollment'));
    }

    public function updatePaymentStatus(UpdatePaymentStatusRequest $request, EnrollmentPayment $payment)
    {
        $result = $this->paymentService->updatePaymentStatus($payment, $request->validated());

        if (isset($result['success']) && !$result['success']) {
            return back()->with('warning', 'Payment marked as paid but schedule generation failed: ' . $result['message']);
        }

        return back()->with('success', 'Payment status updated successfully.');
    }

    public function generateMonthlyPayments()
    {
        $generated = $this->paymentService->generateMonthlyPayments();

        return back()->with('success', "Generated {$generated} payment records for current month.");
    }

    public function markAllPaid(Enrollment $enrollment)
    {
        $updated = $this->paymentService->markAllPaid($enrollment, request('base_month'));
        
        return back()->with('success', "Marked {$updated} month(s) as paid and generated schedules.");
    }

    public function markAllUnpaid(Enrollment $enrollment)
    {
        $updated = $this->paymentService->markAllUnpaid($enrollment, request('base_month'));
        
        return back()->with('success', "Marked {$updated} month(s) as unpaid.");
    }
}
