<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Services\AttendanceService;
use App\Http\Requests\Admin\StoreAttendanceRequest;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $service;

    public function __construct(AttendanceService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of attendance records
     */
    public function index(Request $request)
    {
        $data = $this->service->getAttendances($request);
        return view('admin.attendances.index', $data);
    }

    /**
     * Display the specified attendance record
     */
    public function show(Attendance $attendance)
    {
        $attendance = $this->service->getAttendanceDetails($attendance);
        return view('admin.attendances.show', compact('attendance'));
    }

    /**
     * Display a student's attendance profile
     */
    public function studentProfile(\App\Models\User $user)
    {
        abort_unless($user->hasRole('Student'), 404);
        
        $data = $this->service->getStudentProfileData($user);
        return view('admin.attendances.student', $data);
    }

    /**
     * Display a teacher's attendance profile
     */
    public function teacherProfile(\App\Models\User $user)
    {
        abort_unless($user->hasRole('Teacher'), 404);
        
        $data = $this->service->getTeacherProfileData($user);
        return view('admin.attendances.teacher', $data);
    }

    /**
     * Show the form for creating/marking attendance (Daily/Weekly view)
     */
    public function create(Request $request)
    {
        $data = $this->service->getCreateData($request);
        
        if (isset($data['view']) && $data['view'] === 'weekly') {
            return view('scheduler.attendance.weekly', $data);
        }

        return view('scheduler.attendance.create', $data);
    }

    /**
     * Store a newly created attendance record in storage
     */
    public function store(StoreAttendanceRequest $request)
    {
        $this->service->storeAttendance($request->validated());

        if ($request->filled('redirect_url')) {
            return redirect($request->redirect_url)->with('success', 'Attendance and reports updated successfully.');
        }

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance marked successfully.');
    }

    /**
     * Verify attendance for a specific schedule
     */
    public function verify(Schedule $schedule)
    {
        $schedule->load(['student', 'teacher', 'course', 'attendance']);
        $attendance = $schedule->attendance;
        
        return view('scheduler.attendance.verify', compact('schedule', 'attendance'));
    }
}
