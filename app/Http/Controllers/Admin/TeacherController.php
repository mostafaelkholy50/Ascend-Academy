<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherPasswordRequest;
use App\Http\Requests\Admin\UpdateTeacherRateRequest;

class TeacherController extends Controller
{
    protected $service;

    public function __construct(\App\Services\TeacherService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $teachers = $this->service->getTeachers($request);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function show(User $teacher)
    {
        $data = $this->service->getTeacherDetails($teacher);
        return view('admin.teachers.show', $data);
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request)
    {
        try {
            $data = $request->only(['name', 'email', 'password', 'phone', 'gender', 'birth_date', 'country']);
            $avatarFile = $request->file('avatar');

            $teacher = $this->service->storeTeacher($data, $avatarFile);

            return redirect()->route('admin.teachers.show', $teacher->id)
                ->with('success', 'Teacher created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create teacher: ' . $e->getMessage());
        }
    }

    public function edit(User $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, User $teacher)
    {
        $data = $request->only(['name', 'email', 'phone', 'gender', 'birth_date', 'active', 'country']);
        $avatarFile = $request->file('avatar');

        $this->service->updateTeacher($teacher, $data, $avatarFile);

        return back()->with('success', 'Teacher updated successfully.');
    }

    public function destroy(User $teacher)
    {
        $this->service->deleteTeacher($teacher);
        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }

    public function updatePassword(UpdateTeacherPasswordRequest $request, User $teacher)
    {
        $this->service->updatePassword($teacher, $request->password);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateRate(UpdateTeacherRateRequest $request, User $teacher)
    {
        $this->service->updateRate($teacher, (float) $request->hourly_rate);

        return back()->with('success', 'Hourly rate updated successfully. All unpaid months have been recalculated.');
    }
}
