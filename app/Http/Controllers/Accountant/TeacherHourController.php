<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TeacherHourService;
use App\Traits\HasRegionalAccess;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherHourController extends Controller
{
    use HasRegionalAccess;

    protected $service;

    public function __construct(TeacherHourService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $allowedCountries = $this->getAllowedCountries($user);
        
        if (!$this->canAccessPayroll($user)) {
            abort(403, 'Unauthorized access to payroll records.');
        }

        $data = $this->service->getPayrollData($request);

        return view('accountant.teacher-hours.index', array_merge($data, [
            'allowedCountries' => $allowedCountries
        ]));
    }

    public function markAsPaid(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
        ]);

        $user = auth()->user();
        
        if (!$this->canAccessPayroll($user)) {
            abort(403, 'Unauthorized access to payroll records.');
        }

        $this->service->markAsPaid($request->teacher_id, $request->month, $request->year);

        return $this->successResponse('Payroll marked as paid.');
    }

    public function markAsUnpaid(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
        ]);

        $user = auth()->user();
        
        if (!$this->canAccessPayroll($user)) {
            abort(403, 'Unauthorized access to payroll records.');
        }

        $this->service->markAsUnpaid($request->teacher_id, $request->month, $request->year);

        return $this->successResponse('Payroll marked as unpaid.');
    }

    public function updateRate(Request $request, \App\Models\User $teacher)
    {
        if (!auth()->user()->can('edit-teacher-rate')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        $teacher->update(['hourly_rate' => $request->hourly_rate]);

        return back()->with('success', 'Hourly rate updated successfully.');
    }

    public function show(\App\Models\User $teacher, Request $request, \App\Services\TeacherHoursService $teacherHoursService)
    {
        $user = auth()->user();
        
        if (!$this->canAccessPayroll($user)) {
            abort(403, 'Unauthorized access to payroll records.');
        }

        $data = $teacherHoursService->getHoursData($teacher, $request);
        
        // Pass the teacher to the view as well
        $data['teacher'] = $teacher;

        return view('accountant.teacher-hours.show', $data);
    }
    public function destroyAttendance(\App\Models\Attendance $attendance)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['SuperAdmin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // Keep a reference to the schedule to revert its status
        $schedule = $attendance->schedule;
        
        $attendance->delete();

        // Revert schedule status if it was completed
        if ($schedule && $schedule->status === 'completed') {
            $schedule->update(['status' => 'scheduled']);
        }

        return back()->with('success', 'Attendance record deleted successfully.');
    }

    public function exportPdf(\App\Models\User $teacher, Request $request, \App\Services\TeacherHoursService $teacherHoursService)
    {
        $user = auth()->user();
        
        if (!$this->canAccessPayroll($user)) {
            abort(403, 'Unauthorized access to payroll records.');
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $data = $teacherHoursService->getPdfData($teacher, (int)$month, (int)$year);

        $pdf = Pdf::loadView('accountant.teacher-hours.pdf', $data);
        
        $filename = "{$teacher->name}_Hours_{$year}_{$month}.pdf";

        return $pdf->download($filename);
    }
}
