<?php

namespace App\Http\Controllers\Scheduler;

use App\Models\User;
use App\Models\Schedule;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchedulerDashboardRequest;
use App\Services\SchedulerDashboardService;
use App\Repositories\SchedulerDashboardRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class DashboardController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(SchedulerDashboardService $service, SchedulerDashboardRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(SchedulerDashboardRequest $request)
    {
        try {
            $data = $this->service->getDashboardData(auth()->user(), $request);
            return view('scheduler.dashboard', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل لوحة التحكم. الرجاء المحاولة مرة أخرى.');
        }
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->input('search');
        if (!$search) return '';

        $searchResults = $this->repository->searchUsers($search);

        return view('scheduler.partials.search-results', compact('searchResults', 'search'))->render();
    }

    public function showStudent(User $student)
    {
        if ($student->role !== 'Student') abort(404);

        $student->load([
            'parents',
            'enrollments.course',
            'schedules.teacher',
            'reports.teacher',
            'attendances'
        ]);

        return view('scheduler.students.show', compact('student'));
    }

    public function showTeacher(User $teacher)
    {
        if ($teacher->role !== 'Teacher') abort(404);

        $teacher->load([
            'teacherSchedules.student',
            'teacherReports.student',
            'teacherAttendances'
        ]);

        return view('scheduler.teachers.show', compact('teacher'));
    }

    public function students(Request $request)
    {
        $search = $request->input('search');
        $students = $this->repository->getStudentsQuery($request)->paginate(20)->withQueryString();
        
        return view('scheduler.students.index', compact('students', 'search'));
    }

    public function ajaxSearchStudents(Request $request)
    {
        $search = $request->input('search');
        $students = $this->repository->getStudentsQuery($request)->paginate(20)->withQueryString();
        
        return view('scheduler.students.partials.table', compact('students', 'search'))->render();
    }

    public function teachers(Request $request)
    {
        $search = $request->input('search');
        $teachers = $this->repository->getTeachersQuery($request)->paginate(20)->withQueryString();
        
        return view('scheduler.teachers.index', compact('teachers', 'search'));
    }

    public function ajaxSearchTeachers(Request $request)
    {
        $search = $request->input('search');
        $teachers = $this->repository->getTeachersQuery($request)->paginate(20)->withQueryString();
        
        return view('scheduler.teachers.partials.table', compact('teachers', 'search'))->render();
    }

    public function schedule()
    {
        $schedules = Schedule::with(['student', 'teacher', 'course'])
            ->orderBy('starts_at', 'desc')
            ->paginate(20);
        return view('scheduler.schedules.index', compact('schedules'));
    }

    public function availability(User $user)
    {
        // Permission check: Anyone with 'manage availability' permission OR self
        $isAuthorized = auth()->user()->can('manage availability') || 
                        auth()->user()->hasAnyRole(['SuperAdmin', 'Admin', 'SchedulerManager']) || 
                        auth()->id() === $user->id;
        
        if (!$isAuthorized) {
            abort(403, 'You do not have permission to view availability.');
        }

        $user->load('availabilities');
        return view('scheduler.availability', compact('user'));
    }

    public function saveAvailability(Request $request, User $user = null)
    {
        // If no user provided, assume current authenticated user (for self-manage)
        if (!$user) {
            $user = auth()->user();
        }

        // Permission Check:
        // 1. Must be a Teacher (the target user)
        // 2. The logged-in user must have 'manage availability' permission OR be an Admin/Scheduler OR be the target user themselves
        $isAuthorizedEditor = auth()->user()->can('manage availability') || 
                              auth()->user()->hasAnyRole(['SuperAdmin', 'Admin', 'SchedulerManager']) || 
                              auth()->id() === $user->id;

        if (!$isAuthorizedEditor) {
            abort(403, 'You do not have permission to edit this availability.');
        }

        $request->validate([
            'availabilities' => 'sometimes|array',
        ]);

        // Clear existing availabilities
        $user->availabilities()->delete();

        foreach ($request->input('availabilities', []) as $day => $slots) {
            foreach ($slots as $slot) {
                if ($slot['start_time'] && $slot['end_time']) {
                    $user->availabilities()->create([
                        'day_of_week' => $day,
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ]);
                }
            }
        }

        return back()->with('success', 'Availability updated successfully.');
    }
}
