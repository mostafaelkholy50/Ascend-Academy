<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateStudentPasswordRequest;
use App\Http\Requests\UpdateStudentAvatarRequest;
use App\Services\StudentProfileService;
use Illuminate\Http\Request;
use Exception;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(StudentProfileService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        try {
            $user = auth()->user();
            $data = $this->service->getProfileData($user);
            return view('student.profile.show', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل الملف الشخصي.');
        }
    }

    public function edit()
    {
        $user = auth()->user();
        return view('student.profile.edit', compact('user'));
    }

    public function update(UpdateStudentProfileRequest $request)
    {
        try {
            $user = auth()->user();
            $this->service->updateProfile($user, $request->validated());

            return redirect()->route('student.profile.show')
                ->with('success', 'Profile updated successfully!');
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحديث الملف الشخصي.');
        }
    }

    public function updatePassword(UpdateStudentPasswordRequest $request)
    {
        try {
            $user = auth()->user();
            $this->service->updatePassword($user, $request->validated()['password']);

            return back()->with('success', 'Password changed successfully!');
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تغيير كلمة المرور.');
        }
    }

    public function updateAvatar(UpdateStudentAvatarRequest $request)
    {
        try {
            $user = auth()->user();
            $this->service->updateAvatar($user, $request->file('avatar'));

            return back()->with('success', 'Profile picture updated successfully!');
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحديث الصورة الشخصية.');
        }
    }

    public function deleteAvatar()
    {
        try {
            $user = auth()->user();
            $this->service->deleteAvatar($user);

            return back()->with('success', 'Profile picture removed successfully!');
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء حذف الصورة الشخصية.');
        }
    }
}
