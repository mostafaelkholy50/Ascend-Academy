<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'course']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('course', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

    // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => Enrollment::count(),
            'active' => Enrollment::where('status', 'active')->count(),
            'completed' => Enrollment::where('status', 'completed')->count(),
        ];

        return view('admin.enrollments.index', compact('enrollments', 'stats'));
    }

    public function create(Request $request)
    {
        $students = User::where('role', 'Student')->where('active', true)->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();
        $pricingTiers = \App\Models\PricingTier::active()->orderBy('days_per_week')->orderBy('session_duration')->get();

        // Pre-select student or course if passed via query string
        $selectedStudent = $request->query('student_id');
        $selectedCourse = $request->query('course_id');

        return view('admin.enrollments.create', compact('students', 'courses', 'pricingTiers', 'selectedStudent', 'selectedCourse'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
            'status' => 'nullable|in:active,completed,cancelled',
            // Flexible scheduling
            'days_per_week' => 'required|integer|min:1|max:7',
            'session_duration' => 'required|in:30,60',
            // Admin pricing
            'admin_price' => 'required|numeric|min:0',
            'currency' => 'required|in:CAD,USD,GBP',
        ]);

        try {
            $createdEnrollments = [];
            $alreadyEnrolledCount = 0;

            foreach ($request->courses as $courseId) {
                // Check for duplicate enrollment (database has unique constraint on student_id + course_id)
                $existingEnrollment = Enrollment::where('student_id', $request->student_id)
                    ->where('course_id', $courseId)
                    ->first();

                if ($existingEnrollment) {
                    $alreadyEnrolledCount++;
                    continue; // Skip this course
                }

                $data = [
                    'student_id' => $request->student_id,
                    'course_id' => $courseId,
                    'start_date' => now(), // Auto-set to current date
                    'status' => $request->status ?? 'active',
                    // Flexible scheduling
                    'days_per_week' => $request->days_per_week,
                    'session_duration' => $request->session_duration,
                    // Admin pricing
                    'admin_price' => $request->admin_price,
                    'currency' => $request->currency,
                ];

                $enrollment = Enrollment::create($data);
                $createdEnrollments[] = $enrollment;

                // Automatically create the first month's payment record
                \App\Models\EnrollmentPayment::create([
                    'enrollment_id' => $enrollment->id,
                    'month' => now()->startOfMonth(),
                    'amount' => $enrollment->admin_price,
                    'currency' => $enrollment->currency,
                    'payment_status' => 'unpaid',
                ]);
            }

            if (count($createdEnrollments) === 0 && $alreadyEnrolledCount > 0) {
                return back()->with('error', 'Student is already enrolled in all selected courses.');
            }

            $message = count($createdEnrollments) . ' enrollment(s) created successfully.';
            if ($alreadyEnrolledCount > 0) {
                $message .= " (Skipped $alreadyEnrolledCount duplicates)";
            }

            // If single enrollment, redirect to show. If multiple, redirect to index.
            if (count($createdEnrollments) === 1) {
                return redirect()->route('admin.enrollments.show', $createdEnrollments[0]->id)
                    ->with('success', $message);
            }

            return redirect()->route('admin.enrollments.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create enrollment: ' . $e->getMessage());
        }
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit(Enrollment $enrollment)
    {
        $students = User::where('role', 'Student')->where('active', true)->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();
        
        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'nullable|in:active,completed,cancelled',
            // Flexible scheduling
            'days_per_week' => 'required|integer|min:1|max:7',
            'session_duration' => 'required|in:30,60',
            // Admin pricing
            'admin_price' => 'required|numeric|min:0',
            'currency' => 'required|in:CAD,USD,GBP',
        ]);

        $data = $request->only([
            'student_id', 'course_id', 'status', 'days_per_week', 
            'session_duration', 'admin_price', 'currency'
        ]);

        $enrollment->update($data);

        return back()->with('success', 'Enrollment updated successfully.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }
}
