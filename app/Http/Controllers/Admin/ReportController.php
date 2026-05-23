<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of reports
     */
    public function index(Request $request)
    {
        $data = $this->reportService->getIndexData($request);
        return view('admin.reports.index', $data);
    }

    /**
     * Display the specified report
     */
    public function show(Report $report)
    {
        $report->load(['student', 'teacher', 'course']);

        return view('admin.reports.show', compact('report'));
    }
}
