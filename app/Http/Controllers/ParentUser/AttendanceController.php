<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Schedule;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $parent = auth()->user();
        $children = $parent->children;
        
        // Get selected child or all children
        $selectedChildId = $request->get('child_id', 'all');
        
        // Get date range (default to current month)
        $dateFrom = $request->get('date_from') 
            ? Carbon::parse($request->date_from)
            : Carbon::now()->startOfMonth();
        
        $dateTo = $request->get('date_to')
            ? Carbon::parse($request->date_to)
            : Carbon::now()->endOfMonth();
        
        // Build query for attendances
        $query = Attendance::with(['student', 'schedule.course', 'schedule.teacher']);
        
        if ($selectedChildId === 'all') {
            $query->whereIn('student_id', $children->pluck('id'));
        } else {
            // Verify child belongs to parent
            $parent->children()->findOrFail($selectedChildId);
            $query->where('student_id', $selectedChildId);
        }
        
        $attendances = $query->whereBetween('created_at', [$dateFrom, $dateTo])
            ->latest('created_at')
            ->paginate(20);
        
        // Calculate statistics
        $stats = [];
        foreach ($children as $child) {
            if ($selectedChildId !== 'all' && $child->id != $selectedChildId) {
                continue;
            }
            
            $totalSchedules = Schedule::where('student_id', $child->id)
                ->whereBetween('starts_at', [$dateFrom, $dateTo])
                ->count();
            
            $presentCount = Attendance::where('student_id', $child->id)
                ->where('student_present', true)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();
            
            $absentCount = Attendance::where('student_id', $child->id)
                ->where('student_present', false)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();
            
            // Note: There's no 'late' status in current schema, so we'll set it to 0
            $lateCount = 0;
            
            $stats[$child->id] = [
                'name' => $child->name,
                'total' => $totalSchedules,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'rate' => $totalSchedules > 0 ? round(($presentCount / $totalSchedules) * 100) : 0,
            ];
        }
        
        return view('parent.attendance.index', compact(
            'parent',
            'children',
            'attendances',
            'stats',
            'selectedChildId',
            'dateFrom',
            'dateTo'
        ));
    }
}
