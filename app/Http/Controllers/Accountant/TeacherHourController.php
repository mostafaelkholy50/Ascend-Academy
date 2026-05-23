<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TeacherHourService;
use App\Traits\HasRegionalAccess;

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
}
