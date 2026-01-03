<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TeacherApplication;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherApplication::query()->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15);

        return view('admin.teacher-applications.index', compact('applications'));
    }

    public function show(TeacherApplication $application)
    {
        return view('admin.teacher-applications.show', compact('application'));
    }

    public function convertToTeacher(TeacherApplication $application)
    {
        // Check if email already exists
        if (User::where('email', $application->email)->exists()) {
            return back()->with('error', 'This email is already registered.');
        }

        try {
            // Create Teacher account
            $teacher = User::create([
                'name' => $application->full_name,
                'email' => $application->email,
                'password' => Hash::make('teacher123'), // Temporary password
                'phone' => $application->phone,
                'gender' => $application->gender,
                'birth_date' => $application->birth_date,
                'role' => 'Teacher',
                'active' => true,
            ]);

            // Update application status
            $application->update([
                'status' => 'converted',
                'admin_notes' => 'Converted to teacher account on ' . now()->format('Y-m-d H:i:s')
            ]);

            return redirect()
                ->route('admin.teachers.show', $teacher->id)
                ->with('success', "Teacher account created successfully! Temporary password: teacher123");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create teacher account: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, TeacherApplication $application)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,approved,rejected,converted',
            'admin_notes' => 'nullable|string|max:2000'
        ]);

        $application->update($request->only(['status', 'admin_notes']));

        return back()->with('success', 'Application status updated successfully.');
    }

    public function destroy(TeacherApplication $application)
    {
        // Delete CV file if exists
        if ($application->cv_path && Storage::exists('public/' . $application->cv_path)) {
            Storage::delete('public/' . $application->cv_path);
        }

        $application->delete();
        return redirect()->route('admin.teacher-applications.index')
            ->with('success', 'Application deleted successfully.');
    }
}
