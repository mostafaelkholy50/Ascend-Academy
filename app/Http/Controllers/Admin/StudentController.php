<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'Student')->with(['parents', 'enrollments.course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        $students = $query->latest()->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    public function show(User $student)
    {
        $student->load([
            'parents',
            'enrollments.course',
            'schedules.teacher',
            'reports.teacher',
            'attendances'
        ]);

        $courses = Course::all();
        $teachers = User::where('role', 'Teacher')->where('active', true)->get();

        return view('admin.students.show', compact('student', 'courses', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        try {
            $student = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'role' => 'Student',
                'active' => true,
            ]);

            // Link to parent if provided
            if ($request->filled('parent_id')) {
                \App\Models\Children::create([
                    'parent_id' => $request->parent_id,
                    'child_id' => $student->id,
                ]);
            }

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', 'Student created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            'active' => 'nullable|boolean',
        ]);
$student->update([
    'name' => $request->name,
    'email' => $request->email,
    'phone' => $request->phone,
    'gender' => $request->gender,
    'birth_date' => $request->birth_date,
    'active' => $request->active,
]);

        return back()->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function updatePassword(Request $request, User $student)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $student->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
