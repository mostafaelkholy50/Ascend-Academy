<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Http\Requests\Admin\UpdateStudentPasswordRequest;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $students = $this->studentService->getIndexData($request);
        return view('admin.students.index', compact('students'));
    }

    public function show(User $student)
    {
        $student->load([
            'parents',
            'enrollments.course',
            'schedules.teacher',
            'schedules.attendance',
            'reports.teacher',
            'attendances'
        ]);

        $courses = Course::all();
        $teachers = User::where('role', 'Teacher')->where('active', true)->get();

        return view('admin.students.show', compact('student', 'courses', 'teachers'));
    }

    public function store(StoreStudentRequest $request)
    {
        try {
            $student = $this->studentService->storeStudent($request->validated());

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', 'Student created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    public function update(UpdateStudentRequest $request, User $student)
    {
        $this->studentService->updateStudent($student, $request->validated());

        return back()->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student)
    {
        $this->studentService->deleteStudent($student);
        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function updatePassword(UpdateStudentPasswordRequest $request, User $student)
    {
        $this->studentService->updatePassword($student, $request->password);

        return back()->with('success', 'Password updated successfully.');
    }
}
