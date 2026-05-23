<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Http\Requests\Admin\StoreEnrollmentRequest;
use App\Http\Requests\Admin\UpdateEnrollmentRequest;

class EnrollmentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    public function index(Request $request)
    {
        $data = $this->enrollmentService->getIndexData($request);
        return view('admin.enrollments.index', $data);
    }

    public function create(Request $request)
    {
        $students = User::where('role', 'Student')->where('active', true)->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();
        $pricingTiers = \App\Models\PricingTier::active()->orderBy('days_per_week')->orderBy('session_duration')->get();

        $selectedStudent = $request->query('student_id');
        $selectedCourse = $request->query('course_id');

        return view('admin.enrollments.create', compact('students', 'courses', 'pricingTiers', 'selectedStudent', 'selectedCourse'));
    }

    public function store(StoreEnrollmentRequest $request)
    {
        try {
            $result = $this->enrollmentService->storeEnrollments($request->validated());

            if (count($result['created']) === 0 && $result['skipped'] > 0) {
                return back()->with('error', 'Student is already enrolled in all selected courses.');
            }

            $message = count($result['created']) . ' enrollment(s) created successfully.';
            if ($result['skipped'] > 0) {
                $message .= " (Skipped {$result['skipped']} duplicates)";
            }

            if (count($result['created']) === 1) {
                return redirect()->route('admin.enrollments.show', $result['created'][0]->id)
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

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $this->enrollmentService->updateEnrollment($enrollment, $request->validated());

        return back()->with('success', 'Enrollment updated successfully.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $this->enrollmentService->deleteEnrollment($enrollment);
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }
}
