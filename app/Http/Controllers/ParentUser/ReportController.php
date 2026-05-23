<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StudentEvaluation;
use App\Models\Course;
use Exception;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $parent = auth()->user();
            $children = $parent->children;
            $childrenIds = $children->pluck('id')->toArray();

            // Fetch active teachers and courses for filtering
            $teachers = User::roleTeacher()->active()->get();
            $courses = Course::all();

            // Build dynamic query for children's monthly evaluations
            $query = StudentEvaluation::with(['student', 'teacher', 'course'])
                ->whereIn('student_id', $childrenIds);

            if ($request->filled('child_id')) {
                $query->where('student_id', $request->input('child_id'));
            }
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->input('course_id'));
            }
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->input('teacher_id'));
            }
            if ($request->filled('date_from')) {
                $query->where('evaluation_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('evaluation_date', '<=', $request->input('date_to'));
            }

            $evaluations = $query->latest('evaluation_date')->get();

            return view('parent.reports.index', compact('parent', 'children', 'teachers', 'courses', 'evaluations'));
            
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while loading evaluations. Please try again.');
        }
    }
    
    public function show($id)
    {
        try {
            $parent = auth()->user();
            $childrenIds = $parent->children->pluck('id')->toArray();
            
            // Fetch evaluation and ensure it belongs to one of parent's children
            $evaluation = StudentEvaluation::with(['student', 'teacher', 'course'])
                ->whereIn('student_id', $childrenIds)
                ->findOrFail($id);
            
            return view('parent.reports.show_evaluation', compact('evaluation'));
            
        } catch (Exception $e) {
            return redirect()->route('parent.reports.index')->with('error', 'An error occurred while loading the evaluation. Please try again.');
        }
    }
}
