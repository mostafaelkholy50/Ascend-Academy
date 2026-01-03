<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnrollmentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = EnrollmentPayment::with(['enrollment.student', 'enrollment.course']);

        // Filter by student
        if ($request->filled('student_id')) {
            $query->whereHas('enrollment', function($q) use ($request) {
                $q->where('student_id', $request->student_id);
            });
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('enrollment', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('month', Carbon::parse($request->month)->month)
                  ->whereYear('month', Carbon::parse($request->month)->year);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $payments = $query->orderBy('month', 'desc')->paginate(20);

        // Get filter data
        $students = User::where('role', 'Student')->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        // Statistics for current month
        $currentMonth = now()->startOfMonth();
        $stats = [
            'total_this_month' => EnrollmentPayment::whereMonth('month', $currentMonth->month)
                ->whereYear('month', $currentMonth->year)->count(),
            'paid_this_month' => EnrollmentPayment::whereMonth('month', $currentMonth->month)
                ->whereYear('month', $currentMonth->year)->paid()->count(),
            'unpaid_this_month' => EnrollmentPayment::whereMonth('month', $currentMonth->month)
                ->whereYear('month', $currentMonth->year)->unpaid()->count(),
            'total_amount_this_month' => EnrollmentPayment::whereMonth('month', $currentMonth->month)
                ->whereYear('month', $currentMonth->year)->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'students', 'courses', 'stats'));
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course', 'payments' => function($query) {
            $query->orderBy('month', 'desc');
        }]);

        // Generate missing payment records for past months since enrollment start
        $this->generatePaymentsForEnrollment($enrollment);

        // Reload payments after generation
        $enrollment->load(['payments' => function($query) {
            $query->orderBy('month', 'desc');
        }]);

        return view('admin.payments.show', compact('enrollment'));
    }

    public function updatePaymentStatus(Request $request, EnrollmentPayment $payment)
    {
        $request->validate([
            'payment_status' => 'required|in:paid,unpaid,partial',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ];

        // Set paid_at timestamp if marking as paid
        if ($request->payment_status === 'paid' && $payment->payment_status !== 'paid') {
            $data['paid_at'] = now();
            
            // Auto-generate schedules for this month
            $enrollment = $payment->enrollment;
            $result = \App\Http\Controllers\Admin\ScheduleController::generateMonthlySchedules(
                $enrollment,
                $payment->month
            );
            
            $payment->update($data);
            
            if ($result['success']) {
                return back()->with('success', 'Payment marked as paid and ' . $result['message']);
            } else {
                return back()->with('warning', 'Payment marked as paid but schedule generation failed: ' . $result['message']);
            }
        } elseif ($request->payment_status !== 'paid') {
            $data['paid_at'] = null;
        }

        $payment->update($data);

        return back()->with('success', 'Payment status updated successfully.');
    }

    public function generateMonthlyPayments()
    {
        $currentMonth = now()->startOfMonth();
        $activeEnrollments = Enrollment::where('status', 'active')->get();

        $generated = 0;
        foreach ($activeEnrollments as $enrollment) {
            // Check if payment record already exists for this month
            $exists = EnrollmentPayment::where('enrollment_id', $enrollment->id)
                ->whereMonth('month', $currentMonth->month)
                ->whereYear('month', $currentMonth->year)
                ->exists();

            if (!$exists) {
                EnrollmentPayment::create([
                    'enrollment_id' => $enrollment->id,
                    'month' => $currentMonth,
                    'amount' => $enrollment->admin_price,
                    'currency' => $enrollment->currency,
                    'payment_status' => 'unpaid',
                ]);
                $generated++;
            }
        }

        return back()->with('success', "Generated {$generated} payment records for current month.");
    }

    private function generatePaymentsForEnrollment(Enrollment $enrollment)
    {
        $startDate = $enrollment->start_date ?? $enrollment->created_at;
        $currentMonth = now()->startOfMonth();
        $enrollmentMonth = Carbon::parse($startDate)->startOfMonth();

        // Generate payments from enrollment start to current month
        while ($enrollmentMonth->lte($currentMonth)) {
            $exists = EnrollmentPayment::where('enrollment_id', $enrollment->id)
                ->whereMonth('month', $enrollmentMonth->month)
                ->whereYear('month', $enrollmentMonth->year)
                ->exists();

            if (!$exists && $enrollment->status === 'active') {
                EnrollmentPayment::create([
                    'enrollment_id' => $enrollment->id,
                    'month' => $enrollmentMonth->copy(),
                    'amount' => $enrollment->admin_price,
                    'currency' => $enrollment->currency,
                    'payment_status' => 'unpaid',
                ]);
            }

            $enrollmentMonth->addMonth();
        }
    }

    /**
     * Mark all visible months as paid for an enrollment
     */
    public function markAllPaid(Enrollment $enrollment)
    {
        // Get the visible months from the current view
        $baseMonth = request('base_month') ? \Carbon\Carbon::parse(request('base_month') . '-01') : now();
        $startMonth = $baseMonth->copy()->startOfMonth();
        
        $updated = 0;
        for ($i = 0; $i < 6; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            
            // Explicitly search by year/month to avoid date format issues
            $payment = EnrollmentPayment::where('enrollment_id', $enrollment->id)
                ->whereYear('month', $month->year)
                ->whereMonth('month', $month->month)
                ->first();
                
            if (!$payment) {
                // Create if doesn't exist
                $payment = EnrollmentPayment::create([
                    'enrollment_id' => $enrollment->id,
                    'month' => $month,
                    'amount' => $enrollment->admin_price,
                    'currency' => $enrollment->currency,
                    'payment_status' => 'unpaid',
                ]);
            }
            
            // Update to paid if not already
            if ($payment->payment_status !== 'paid') {
                $payment->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
                
                // Auto-generate schedules for this month
                \App\Http\Controllers\Admin\ScheduleController::generateMonthlySchedules(
                    $enrollment,
                    $month
                );
                
                $updated++;
            }
        }
        
        return back()->with('success', "Marked {$updated} month(s) as paid and generated schedules.");
    }

    /**
     * Mark all visible months as unpaid for an enrollment
     */
    public function markAllUnpaid(Enrollment $enrollment)
    {
        // Get the visible months from the current view
        $baseMonth = request('base_month') ? \Carbon\Carbon::parse(request('base_month') . '-01') : now();
        $startMonth = $baseMonth->copy()->startOfMonth();
        
        $updated = 0;
        for ($i = 0; $i < 6; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            
            // Find payment for this month using explicit date matching
            $payment = EnrollmentPayment::where('enrollment_id', $enrollment->id)
                ->whereYear('month', $month->year)
                ->whereMonth('month', $month->month)
                ->first();
            
            if ($payment && $payment->payment_status !== 'unpaid') {
                $payment->update([
                    'payment_status' => 'unpaid',
                    'paid_at' => null,
                ]);
                $updated++;
            }
        }
        
        return back()->with('success', "Marked {$updated} month(s) as unpaid.");
    }
}
