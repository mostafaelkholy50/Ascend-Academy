<?php

namespace App\Services;

use App\Repositories\TeacherApplicationRepository;
use App\Models\TeacherApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherApplicationService
{
    protected $repository;

    public function __construct(TeacherApplicationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function processApplication(array $validatedData, ?UploadedFile $cvFile): TeacherApplication
    {
        if ($cvFile) {
            // Using 'local' disk instead of 'public' for privacy/security of CVs
            $validatedData['cv_path'] = $cvFile->store('teacher-cvs', 'local');
        }

        $application = $this->repository->createApplication($validatedData);

        // $this->sendAdminNotification($application); // Temporarily disabled to prevent bot spam suspending email

        return $application;
    }

    protected function sendAdminNotification(TeacherApplication $application): void
    {
        try {
            $adminEmail = env('ADMIN_EMAIL', 'ascend.quran@gmail.com');
            Notification::route('mail', $adminEmail)
                ->notify(new \App\Notifications\NewTeacherApplicationNotification($application));
        } catch (\Exception $e) {
            Log::error('Failed to send teacher application notification: ' . $e->getMessage());
        }
    }

    public function getIndexData($request)
    {
        $query = TeacherApplication::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(10);
    }

    public function convertToTeacher(TeacherApplication $application): array
    {
        if ($application->status === 'converted') {
            throw new \Exception('This application has already been converted to a teacher.');
        }

        $password = 'welcome123';

        $user = \App\Models\User::create([
            'name' => $application->full_name,
            'email' => $application->email,
            'role' => 'Teacher',
            'password' => Hash::make($password),
            'teacher_application_id' => $application->id,
            'timezone' => 'Africa/Cairo',
        ]);

        $user->assignRole('Teacher');

        $application->update(['status' => 'converted']);

        return [
            'teacher' => $user,
            'password' => $password,
        ];
    }

    public function updateStatus(TeacherApplication $application, array $data): void
    {
        $application->update($data);
    }

    public function deleteApplication(TeacherApplication $application): void
    {
        $application->delete();
    }
}
