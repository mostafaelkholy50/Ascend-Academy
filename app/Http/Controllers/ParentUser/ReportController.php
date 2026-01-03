<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $parent = auth()->user();
        $children = $parent->children;
        
        // Get selected child or all children
        $selectedChildId = $request->get('child_id', 'all');
        
        // Build query
        $query = Report::with(['student', 'teacher', 'course']);
        
        if ($selectedChildId === 'all') {
            $query->whereIn('student_id', $children->pluck('id'));
        } else {
            // Verify child belongs to parent
            $parent->children()->findOrFail($selectedChildId);
            $query->where('student_id', $selectedChildId);
        }
        
        // Apply filters
        if ($request->has('course_id') && $request->course_id != '') {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('report_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('report_date', '<=', $request->date_to);
        }
        
        $reports = $query->latest('report_date')
            ->paginate(15);
        
        // Get available courses for filter
        $courses = \App\Models\Enrollment::with('course')
            ->whereIn('student_id', $children->pluck('id'))
            ->get()
            ->pluck('course')
            ->unique('id');
        
        return view('parent.reports.index', compact(
            'parent',
            'children',
            'reports',
            'courses',
            'selectedChildId'
        ));
    }
    
    public function show($reportId)
    {
        $parent = auth()->user();
        $children = $parent->children;
        
        // Get report and verify it belongs to one of parent's children
        $report = Report::with(['student', 'teacher', 'course'])
            ->whereIn('student_id', $children->pluck('id'))
            ->findOrFail($reportId);
        
        return view('parent.reports.show', compact('parent', 'report'));
    }
}
