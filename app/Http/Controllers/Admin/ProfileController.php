<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProfileService;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdateProfilePasswordRequest;
use App\Http\Requests\Admin\UpdateAvatarRequest;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        $data = $this->service->getProfileData(auth()->user());
        return view('admin.profile.show', $data);
    }

    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->service->updateProfile(auth()->user(), $request->validated());

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(UpdateProfilePasswordRequest $request)
    {
        $this->service->updatePassword(auth()->user(), $request->password);

        return back()->with('success', 'Password changed successfully!');
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $this->service->updateAvatar(auth()->user(), $request->file('avatar'));

        return back()->with('success', 'Profile picture updated successfully!');
    }

    public function deleteAvatar()
    {
        $this->service->deleteAvatar(auth()->user());

        return back()->with('success', 'Profile picture removed successfully!');
    }
}
