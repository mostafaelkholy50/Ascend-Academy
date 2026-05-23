<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherProfileService;
use App\Http\Requests\ProfileRequest\UpdateTeacherProfileRequest;
use App\Http\Requests\ProfileRequest\UpdateTeacherPasswordRequest;
use App\Http\Requests\ProfileRequest\UpdateTeacherAvatarRequest;
use Exception;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(TeacherProfileService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        try {
            $data = $this->service->getProfileData(auth()->user());
            return view('teacher.profile.show', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل الملف الشخصي.');
        }
    }

    public function edit()
    {
        try {
            return view('teacher.profile.edit', ['user' => auth()->user()]);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل صفحة التعديل.');
        }
    }

    public function update(UpdateTeacherProfileRequest $request)
    {
        try {
            $this->service->updateProfile(auth()->user(), $request->validated());

            return redirect()->route('teacher.profile.show')
                ->with('success', 'Profile updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updatePassword(UpdateTeacherPasswordRequest $request)
    {
        try {
            $this->service->updatePassword(auth()->user(), $request->validated()['password']);

            return back()->with('success', 'Password changed successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to change password: ' . $e->getMessage());
        }
    }

    public function updateAvatar(UpdateTeacherAvatarRequest $request)
    {
        try {
            $this->service->updateAvatar(auth()->user(), $request->file('avatar'));

            return back()->with('success', 'Profile picture updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update profile picture: ' . $e->getMessage());
        }
    }

    public function deleteAvatar()
    {
        try {
            $this->service->deleteAvatar(auth()->user());

            return back()->with('success', 'Profile picture removed successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to remove profile picture: ' . $e->getMessage());
        }
    }
}
