<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Traits\HasRegionalAccess;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentService
{
    use HasRegionalAccess;

    protected $repository;
    protected $scheduleService;

    public function __construct(PaymentRepository $repository, ScheduleService $scheduleService)
    {
        $this->repository = $repository;
        $this->scheduleService = $scheduleService;
    }

    public function getIndexData(Request $request)
    {
        $user = auth()->user();
        $allowedCountries = $this->getAllowedCountries($user);

        if (empty($allowedCountries)) {
            $allowedCountries = ['Canada', 'USA', 'UK', 'Egypt', 'KSA', 'UAE', 'Australia', 'Germany', 'France'];
        }

        // Fetch Enrollments for the grid view
        $query = $this->repository->getEnrollmentsQuery();

        // Apply country filtering
        $query = $this->applyRegionalFilter($query, 'student.country');

        // Apply other filters
        $query = (new \App\Filters\PaymentFilter)->apply($query, $request);

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(12);

        // Filter data for students
        $studentsQuery = $this->repository->getStudentsQuery();
        $studentsQuery = $this->applyRegionalFilter($studentsQuery, 'country');
        $students = $studentsQuery->orderBy('name')->get();
        
        $courses = $this->repository->getCourses();

        // Statistics for current month
        $currentMonth = now()->startOfMonth();
        $statsQuery = $this->repository->getStatsQuery($currentMonth->month, $currentMonth->year);
        $statsQuery = $this->applyRegionalFilter($statsQuery, 'enrollment.student.country');

        $statsResult = $statsQuery->selectRaw('
            COUNT(*) as total_this_month,
            SUM(CASE WHEN payment_status = "paid" THEN 1 ELSE 0 END) as paid_this_month,
            SUM(CASE WHEN payment_status = "unpaid" THEN 1 ELSE 0 END) as unpaid_this_month,
            SUM(amount) as total_amount_this_month
        ')->first();

        $stats = [
            'total_this_month' => (int) ($statsResult->total_this_month ?? 0),
            'paid_this_month' => (int) ($statsResult->paid_this_month ?? 0),
            'unpaid_this_month' => (int) ($statsResult->unpaid_this_month ?? 0),
            'total_amount_this_month' => (float) ($statsResult->total_amount_this_month ?? 0),
        ];

        return [
            'enrollments' => $enrollments,
            'students' => $students,
            'courses' => $courses,
            'stats' => $stats,
            'allowedCountries' => $allowedCountries,
        ];
    }

    public function getAdminIndexData(Request $request, int $perPage = 20)
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

        $payments = $query->orderBy('month', 'desc')->paginate($perPage);

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

        return compact('payments', 'students', 'courses', 'stats');
    }

    public function updatePaymentStatus(EnrollmentPayment $payment, array $data)
    {
        if ($data['payment_status'] === 'paid' && $payment->payment_status !== 'paid') {
            $data['paid_at'] = now();
            
            $result = null;
            \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $data, &$result) {
                // Auto-generate schedules for this month
                $result = $this->scheduleService->generateMonthlySchedules(
                    $payment->enrollment,
                    $payment->month->format('Y-m-d')
                );
                
                $payment->update($data);
            });
            
            return $result;
        } elseif ($data['payment_status'] !== 'paid') {
            $data['paid_at'] = null;
        }

        $payment->update($data);
        return ['success' => true, 'message' => 'Payment status updated successfully.'];
    }

    public function generateMonthlyPayments()
    {
        $currentMonth = now()->startOfMonth();
        $activeEnrollments = Enrollment::where('status', 'active')->get();

        $generated = 0;
        foreach ($activeEnrollments as $enrollment) {
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

        return $generated;
    }

    public function generatePaymentsForEnrollment(Enrollment $enrollment)
    {
        $startDate = $enrollment->start_date ?? $enrollment->created_at;
        $currentMonth = now()->startOfMonth();
        $enrollmentMonth = Carbon::parse($startDate)->startOfMonth();

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

    public function markAllPaid(Enrollment $enrollment, $baseMonth)
    {
        $baseMonth = $baseMonth ? Carbon::parse($baseMonth . '-01') : now();
        $startMonth = $baseMonth->copy()->startOfMonth();
        
        $updated = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($enrollment, $startMonth, &$updated) {
            for ($i = 0; $i < 6; $i++) {
                $month = $startMonth->copy()->addMonths($i);
                
                $payment = EnrollmentPayment::where('enrollment_id', $enrollment->id)
                    ->whereYear('month', $month->year)
                    ->whereMonth('month', $month->month)
                    ->first();
                    
                if (!$payment) {
                    $payment = EnrollmentPayment::create([
                        'enrollment_id' => $enrollment->id,
                        'month' => $month,
                        'amount' => $enrollment->admin_price,
                        'currency' => $enrollment->currency,
                        'payment_status' => 'unpaid',
                    ]);
                }
                
                if ($payment->payment_status !== 'paid') {
                    $payment->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                    ]);
                    
                    $this->scheduleService->generateMonthlySchedules(
                        $enrollment,
                        $month->format('Y-m-d')
                    );
                    
                    $updated++;
                }
            }
        });
        
        return $updated;
    }

    public function markAllUnpaid(Enrollment $enrollment, $baseMonth)
    {
        $baseMonth = $baseMonth ? Carbon::parse($baseMonth . '-01') : now();
        $startMonth = $baseMonth->copy()->startOfMonth();
        
        $updated = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($enrollment, $startMonth, &$updated) {
            for ($i = 0; $i < 6; $i++) {
                $month = $startMonth->copy()->addMonths($i);
                
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
        });
        
        return $updated;
    }
}
