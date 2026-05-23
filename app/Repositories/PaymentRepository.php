<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Collection;

class PaymentRepository
{
    public function getEnrollmentsQuery()
    {
        return Enrollment::with(['student', 'course', 'payments'])
            ->where('status', 'active');
    }

    public function getStudentsQuery()
    {
        return User::roleStudent();
    }

    public function getStatsQuery(int $month, int $year)
    {
        return EnrollmentPayment::whereMonth('month', $month)
            ->whereYear('month', $year);
    }

    public function getCourses(): Collection
    {
        return Course::orderBy('title')->get();
    }
}
