<?php

namespace App\Services;

use App\Repositories\EnrollmentRepository;
use App\Filters\EnrollmentFilter;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    protected $repository;
    protected $filter;

    public function __construct(EnrollmentRepository $repository, EnrollmentFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getIndexData(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getEnrollmentsQuery();
        $query = $this->filter->apply($query, $request);

        $enrollments = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Enrollment::count(),
            'active' => Enrollment::where('status', 'active')->count(),
            'completed' => Enrollment::where('status', 'completed')->count(),
        ];

        return compact('enrollments', 'stats');
    }

    public function storeEnrollments(array $data)
    {
        $createdEnrollments = [];
        $alreadyEnrolledCount = 0;

        DB::transaction(function () use ($data, &$createdEnrollments, &$alreadyEnrolledCount) {
            foreach ($data['courses'] as $courseId) {
                // Check for duplicate enrollment
                $existingEnrollment = Enrollment::where('student_id', $data['student_id'])
                    ->where('course_id', $courseId)
                    ->first();

                if ($existingEnrollment) {
                    $alreadyEnrolledCount++;
                    continue;
                }

                $enrollment = $this->repository->create([
                    'student_id' => $data['student_id'],
                    'course_id' => $courseId,
                    'start_date' => null,
                    'status' => $data['status'] ?? 'active',
                    'days_per_week' => $data['days_per_week'],
                    'session_duration' => $data['session_duration'],
                    'admin_price' => $data['admin_price'],
                    'currency' => $data['currency'],
                ]);

                $createdEnrollments[] = $enrollment;

                // Automatically create the first month's payment record
                EnrollmentPayment::create([
                    'enrollment_id' => $enrollment->id,
                    'month' => now()->startOfMonth(),
                    'amount' => $enrollment->admin_price,
                    'currency' => $enrollment->currency,
                    'payment_status' => 'unpaid',
                ]);
            }
        });

        return [
            'created' => $createdEnrollments,
            'skipped' => $alreadyEnrolledCount,
        ];
    }

    public function updateEnrollment(Enrollment $enrollment, array $data)
    {
        return $this->repository->update($enrollment, $data);
    }

    public function deleteEnrollment(Enrollment $enrollment)
    {
        return DB::transaction(function () use ($enrollment) {
            // Delete attendance records attached to this enrollment's schedules first
            $scheduleIds = $enrollment->schedules()->pluck('id');
            if ($scheduleIds->isNotEmpty()) {
                \App\Models\Attendance::whereIn('schedule_id', $scheduleIds)->delete();
            }

            // Delete related payments
            $enrollment->payments()->delete();

            // Delete related schedules
            $enrollment->schedules()->delete();

            // Delete the enrollment
            return $this->repository->delete($enrollment);
        });
    }
}
