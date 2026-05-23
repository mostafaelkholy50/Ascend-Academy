<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherReportRequest;
use App\Http\Requests\UpdateTeacherReportRequest;
use App\Filters\TeacherReportFilter;
use App\Services\TeacherReportService;
use Exception;

class ReportController extends Controller
{
    protected $service;

    public function __construct(TeacherReportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, TeacherReportFilter $filter)
    {
        try {
            $teacher = auth()->user();
            $data = $this->service->getIndexData($teacher, $request, $filter);
            
            return view('teacher.reports.index', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل التقارير.');
        }
    }

    public function create(Request $request)
    {
        try {
            $teacher = auth()->user();
            $data = $this->service->getCreateData($teacher, $request);
            
            return view('teacher.reports.create', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل صفحة الإضافة.');
        }
    }

    public function store(StoreTeacherReportRequest $request)
    {
        try {
            $teacher = auth()->user();
            $report = $this->service->storeReport($teacher, $request->validated());

            return redirect()->route('teacher.reports.show', $report->id)
                ->with('success', 'Report created successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to create report: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Report $report)
    {
        try {
            if ($report->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $report->load(['student', 'course', 'teacher']);

            return view('teacher.reports.show', compact('report'));
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء عرض التقرير.');
        }
    }

    public function edit(Report $report)
    {
        try {
            if ($report->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $teacher = auth()->user();
            $data = $this->service->getEditData($teacher, $report);

            return view('teacher.reports.edit', $data);
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل صفحة التعديل.');
        }
    }

    public function update(UpdateTeacherReportRequest $request, Report $report)
    {
        try {
            $this->service->updateReport($report, $request->validated());

            return back()->with('success', 'Report updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update report: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Report $report)
    {
        try {
            if ($report->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $this->service->deleteReport($report);

            return redirect()->route('teacher.reports.index')
                ->with('success', 'Report deleted successfully.');
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء حذف التقرير.');
        }
    }

    public function getStudentCourses(User $student)
    {
        try {
            $teacher = auth()->user();
            // Since this is just an AJAX endpoint for a dropdown, we can query it directly here or use repo.
            // Using repo is better for consistency.
            $repository = app(\App\Repositories\TeacherReportRepository::class);
            $courses = $repository->getTeacherCourses($teacher, $student->id)->map(function($course) {
                return ['id' => $course->id, 'title' => $course->title];
            });

            return response()->json($courses);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch courses.'], 500);
        }
    }

    public function quickCreate(Schedule $schedule)
    {
        try {
            if ($schedule->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            if ($schedule->status !== 'completed') {
                return back()->with('error', 'Can only create reports for completed sessions.');
            }

            $schedule->load(['student', 'course']);

            return view('teacher.reports.quick-create', compact('schedule'));
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء إعداد التقرير.');
        }
    }
}
