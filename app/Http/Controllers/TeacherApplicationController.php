<?php

namespace App\Http\Controllers;

use App\Models\TeacherApplication;
use App\Http\Requests\TeacherApplicationRequest;
use Illuminate\Support\Facades\Storage;

class TeacherApplicationController extends Controller
{
    public function create()
    {
        return view('pages.teacher-application');
    }

    public function store(TeacherApplicationRequest $request)
    {
        $validated = $request->validated();

        // Handle CV upload
        if ($request->hasFile('cv')) {
            $validated['cv_path'] = $request->file('cv')->store('teacher-cvs', 'public');
        }

        $application = TeacherApplication::create($validated);

        // Send email notification to admin
        try {
            $adminEmail = env('ADMIN_EMAIL', 'admin@ascendacademy.com');
            \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                ->notify(new \App\Notifications\NewTeacherApplicationNotification($application));
        } catch (\Exception $e) {
            \Log::error('Failed to send teacher application notification: ' . $e->getMessage());
        }

        return redirect()->route('teacher-application.success')
            ->with('success', 'Thank you for your application! We will review it and contact you within 3-5 business days.');
    }

    public function success()
    {
        return view('pages.application-success');
    }
}
