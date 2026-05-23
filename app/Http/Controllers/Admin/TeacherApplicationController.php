<?php

namespace App\Http\Controllers\Admin;

use App\Models\TeacherApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TeacherApplicationService;
use App\Http\Requests\Admin\UpdateTeacherApplicationStatusRequest;

class TeacherApplicationController extends Controller
{
    protected $service;

    public function __construct(TeacherApplicationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $applications = $this->service->getIndexData($request);
        return view('admin.teacher-applications.index', compact('applications'));
    }

    public function show(TeacherApplication $application)
    {
        return view('admin.teacher-applications.show', compact('application'));
    }

    public function convertToTeacher(TeacherApplication $application)
    {
        try {
            $result = $this->service->convertToTeacher($application);
            $teacher = $result['teacher'];
            $password = $result['password'];

            return redirect()
                ->route('admin.teachers.show', $teacher->id)
                ->with('success', "Teacher account created successfully! Temporary password: {$password}");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(UpdateTeacherApplicationStatusRequest $request, TeacherApplication $application)
    {
        $this->service->updateStatus($application, $request->validated());

        return back()->with('success', 'Application status updated successfully.');
    }

    public function destroy(TeacherApplication $application)
    {
        $this->service->deleteApplication($application);
        return redirect()->route('admin.teacher-applications.index')
            ->with('success', 'Application deleted successfully.');
    }
}
