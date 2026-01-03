<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::withCount(['enrollments', 'schedules']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->latest()->paginate(15);

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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_free' => 'nullable|boolean',
        ]);

        try {
            $data = $request->only(['title', 'description']);
            
            // Set default values for removed fields
            $data['level'] = 'Beginner';
            $data['age_group'] = 'Adults';
            $data['language'] = 'English';
            $data['duration_weeks'] = 12; // Default duration
            $data['is_free'] = $request->input('is_free', 0);
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('courses', 'public');
            }
            
            $course = Course::create($data);

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

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_free' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'description']);
        
        // Update is_free if provided
        if ($request->has('is_free')) {
            $data['is_free'] = $request->input('is_free', 0);
        }
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($course->photo) {
                \Storage::disk('public')->delete($course->photo);
            }
            $data['photo'] = $request->file('photo')->store('courses', 'public');
        }
        
        $course->update($data);

        return back()->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
