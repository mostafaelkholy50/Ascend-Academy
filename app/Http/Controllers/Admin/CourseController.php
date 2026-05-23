<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $courses = $this->courseService->getIndexData($request);
        return view('admin.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load(['enrollments.student', 'schedules', 'resources']);

        $totalStudents = $course->enrollments()->count();
        $activeEnrollments = $course->enrollments()->where('status', 'active')->count();
        $completedEnrollments = $course->enrollments()->where('status', 'completed')->count();

        return view('admin.courses.show', compact('course', 'totalStudents', 'activeEnrollments', 'completedEnrollments'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $course = $this->courseService->storeCourse(
                $request->validated(),
                $request->file('photo')
            );

            return redirect()->route('admin.courses.show', $course->id)
                ->with('success', 'Course created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->courseService->updateCourse(
            $course,
            $request->validated(),
            $request->file('photo')
        );

        return back()->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course);
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
