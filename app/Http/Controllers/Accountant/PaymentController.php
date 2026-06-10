<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Traits\HasRegionalAccess;

class PaymentController extends Controller
{
    use HasRegionalAccess;

    protected $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getIndexData($request);

        return view('accountant.payments.index', $data);
    }

    public function show(Enrollment $enrollment)
    {
        $user = auth()->user();

        $enrollment->load(['student', 'course', 'payments' => function($query) {
            $query->orderBy('month', 'desc');
        }]);

        // Security check
        if (!$this->hasAccessToCountry($user, $enrollment->student->country)) {
            abort(403, 'Unauthorized access to this student.');
        }

        return view('accountant.payments.show', compact('enrollment'));
    }

    public function updateEnrollment(Request $request, Enrollment $enrollment)
    {
        $user = auth()->user();

        $enrollment->load('student');

        // Security check
        if (!$this->hasAccessToCountry($user, $enrollment->student->country)) {
            abort(403, 'Unauthorized access to this student.');
        }

        $request->validate([
            'admin_price' => 'required|numeric|min:0',
            'currency' => 'required|in:CAD,USD,GBP,EUR,EGP',
        ]);

        $enrollment->update([
            'admin_price' => $request->admin_price,
            'currency' => $request->currency,
        ]);

        return back()->with('success', 'Enrollment updated successfully.');
    }

    public function updateStatus(Request $request, EnrollmentPayment $payment)
    {
        $user = auth()->user();

        $payment->load('enrollment.student');

        // Security check
        if (!$this->hasAccessToCountry($user, $payment->enrollment->student->country)) {
            abort(403, 'Unauthorized access to this payment.');
        }

        $request->validate([
            'payment_status' => 'required|in:paid,unpaid,partial',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ];

        $this->service->updatePaymentStatus($payment, $data);

        return $this->successResponse('Payment status updated successfully.');
    }
}
