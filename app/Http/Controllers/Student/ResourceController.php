<?php

namespace App\Http\Controllers\Student;

use App\Models\Resource;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources assigned to the student
     */
    public function index(Request $request)
    {
        $student = Auth::user();

        $query = Resource::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest();

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

        // Get all courses the student is enrolled in
        $courses = Course::whereHas('enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->orderBy('title')->get();

        return view('student.resources.index', compact('resources', 'courses'));
    }

    /**
     * Display the specified resource
     */
    public function show(Resource $resource)
    {
        // Ensure student can only view their own resources
        if ($resource->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $resource->load(['teacher', 'course']);

        return view('student.resources.show', compact('resource'));
    }

    /**
     * Open/view the resource file in browser
     */
    public function download(Resource $resource)
    {
        // Ensure student can only access their own resources
        if ($resource->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            return back()->with('error', 'File not found.');
        }

        $filePath = Storage::disk('public')->path($resource->file_path);
        
        return response()->file($filePath);
    }
}
