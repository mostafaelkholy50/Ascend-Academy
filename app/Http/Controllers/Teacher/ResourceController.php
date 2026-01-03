<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Resource;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ResourceRequest\StoreResourceRequest;
use App\Http\Requests\ResourceRequest\UpdateResourceRequest;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources created by the teacher
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();

        $query = Resource::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest();

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $resources = $query->paginate(15);

        // Get all students this teacher has taught
        $students = User::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('name')->get();

        // Get all courses this teacher teaches
        $courses = Course::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('title')->get();

        return view('teacher.resources.index', compact('resources', 'students', 'courses'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create(Request $request)
    {
        $teacher = Auth::user();

        // Get students from schedules
        $students = User::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('name')->get();

        // Get courses this teacher teaches
        $courses = Course::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('title')->get();

        // Pre-select student if provided
        $selectedStudent = $request->query('student_id');
        $selectedCourse = $request->query('course_id');

        return view('teacher.resources.create', compact('students', 'courses', 'selectedStudent', 'selectedCourse'));
    }

    /**
     * Store a newly created resource
     */
    public function store(StoreResourceRequest $request)
    {
        try {
            $data = [
                'teacher_id' => Auth::id(),
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
            ];

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('resources', 'public');
                $data['file_path'] = $path;
                $data['mime_type'] = $file->getMimeType();
            }

            // Handle external URL
            if ($request->filled('external_url')) {
                $data['external_url'] = $request->external_url;
            }

            $resource = Resource::create($data);

            // Load relationships for email
            $resource->load(['student', 'teacher', 'course']);

            // Send email notification to student
            try {
                $resource->student->notify(new \App\Notifications\ResourceAddedNotification($resource));
            } catch (\Exception $e) {
                \Log::error('Failed to send resource notification to student: ' . $e->getMessage());
            }

            // Send email notification to parent(s)
            try {
                $parents = $resource->student->parents;
                foreach ($parents as $parent) {
                    $parent->notify(new \App\Notifications\ResourceAddedNotification($resource));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send resource notification to parents: ' . $e->getMessage());
            }

            return redirect()->route('teacher.resources.index')
                ->with('success', 'Resource uploaded successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload resource: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource
     */
    public function show(Resource $resource)
    {
        // Ensure teacher can only view their own resources
        if ($resource->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $resource->load(['student', 'course', 'teacher']);

        return view('teacher.resources.show', compact('resource'));
    }

    /**
     * Show the form for editing the resource
     */
    public function edit(Resource $resource)
    {
        // Ensure teacher can only edit their own resources
        if ($resource->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $teacher = Auth::user();

        $students = User::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('name')->get();

        $courses = Course::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('title')->get();

        return view('teacher.resources.edit', compact('resource', 'students', 'courses'));
    }

    /**
     * Update the specified resource
     */
    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        // Ensure teacher can only update their own resources
        if ($resource->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $data = [
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
            ];

            // Handle file replacement
            if ($request->hasFile('file')) {
                // Delete old file
                if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                    Storage::disk('public')->delete($resource->file_path);
                }

                $file = $request->file('file');
                $path = $file->store('resources', 'public');
                $data['file_path'] = $path;
                $data['mime_type'] = $file->getMimeType();
            }

            // Handle external URL
            if ($request->filled('external_url')) {
                $data['external_url'] = $request->external_url;
            }

            $resource->update($data);

            return redirect()->route('teacher.resources.show', $resource->id)
                ->with('success', 'Resource updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update resource: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource
     */
    public function destroy(Resource $resource)
    {
        // Ensure teacher can only delete their own resources
        if ($resource->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Delete file from storage
            if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $resource->delete();

            return redirect()->route('teacher.resources.index')
                ->with('success', 'Resource deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete resource: ' . $e->getMessage());
        }
    }

    /**
     * Download the resource file
     */
    public function download(Resource $resource)
    {
        // Ensure teacher can only download their own resources
        if ($resource->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($resource->file_path, $resource->title);
    }
}
