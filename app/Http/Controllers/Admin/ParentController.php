<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\Children;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    /**
     * عرض جميع الآباء
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'Parent')->with('children');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $parents = $query->latest()->paginate(15);

        return view('admin.parents.index', compact('parents'));
    }

    /**
     * Show form to create new parent
     */
    public function create()
    {
        return view('admin.parents.create');
    }

    /**
     * Store new parent
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'active' => 'nullable|boolean',
        ]);

        try {
            $parent = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => 'Parent',
                'active' => $request->has('active') ? true : false,
            ]);

            return redirect()->route('admin.parents.show', $parent)
                ->with('success', 'Parent created successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create parent: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل Parent مع أبنائه
     */
    public function show(User $parent)
    {
        $parent->load(['children.enrollments.course', 'children.schedules', 'children.reports']);
        $courses = Course::all();

        return view('admin.parents.show', compact('parent', 'courses'));
    }

    /**
     * إضافة طالب (ابن) لولي الأمر
     */
    public function addChild(Request $request, User $parent)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'level' => 'nullable|string|max:100',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        try {
            // إنشاء حساب الطالب
            $student = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'role' => 'Student',
                'active' => true,
            ]);

            // ربط الطالب بولي الأمر
            Children::create([
                'parent_id' => $parent->id,
                'child_id' => $student->id,
            ]);

            // إضافة الطالب للكورس إذا تم تحديده
            if ($request->filled('course_id')) {
                $student->enrollments()->create([
                    'course_id' => $request->course_id,
                    'start_date' => now(),
                    'status' => 'active',
                ]);
            }

            return back()->with('success', 'Student added successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add student: ' . $e->getMessage());
        }
    }

    /**
     * حذف العلاقة بين Parent و Child
     */
    public function removeChild(User $parent, User $child)
    {
        Children::where('parent_id', $parent->id)
            ->where('child_id', $child->id)
            ->delete();

        return back()->with('success', 'Student removed from parent successfully.');
    }

    /**
     * تحديث بيانات Parent
     */
    public function update(Request $request, User $parent)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parent->id,
            'phone' => 'nullable|string|max:20',
            'active' => 'nullable|boolean',
        ]);

        $parent->update($request->only(['name', 'email', 'phone', 'active']));

        return back()->with('success', 'Parent updated successfully.');
    }


    /**
     * حذف Parent
     */
    public function destroy(User $parent)
    {
        $parent->delete();
        return redirect()->route('admin.parents.index')->with('success', 'Parent deleted successfully.');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request, User $parent)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $parent->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
