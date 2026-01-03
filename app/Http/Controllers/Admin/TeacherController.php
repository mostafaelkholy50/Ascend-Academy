<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'Teacher')
            ->withCount(['teacherSchedules', 'teacherReports']);

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

        $teachers = $query->latest()->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function show(User $teacher)
    {
        $teacher->load([
            'teacherSchedules.student',
            'teacherReports.student',
            'teacherHours',
            'teacherResources'
        ]);

        // Calculate stats
        $totalStudents = $teacher->teacherSchedules()->distinct('student_id')->count('student_id');
        $completedClasses = $teacher->teacherSchedules()->where('status', 'completed')->count();
        $upcomingClasses = $teacher->teacherSchedules()->where('status', 'scheduled')->where('starts_at', '>', now())->count();

        return view('admin.teachers.show', compact('teacher', 'totalStudents', 'completedClasses', 'upcomingClasses'));
    }

    public function create()
    {
        return view('admin.teachers.create');
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'role' => 'Teacher',
                'active' => true,
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $teacher = User::create($data);

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

    public function update(Request $request, User $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            'active' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'gender', 'birth_date', 'active']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($teacher->avatar) {
                \Storage::disk('public')->delete($teacher->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $teacher->update($data);

        return back()->with('success', 'Teacher updated successfully.');
    }


    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }

    public function updatePassword(Request $request, User $teacher)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $teacher->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
