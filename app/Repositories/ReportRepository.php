<?php

namespace App\Repositories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;

class ReportRepository
{
    public function getReportsQuery(): Builder
    {
        return Report::with(['student', 'teacher', 'course']);
    }

    public function findOrFail(int $id): Report
    {
        return Report::findOrFail($id);
    }
}
