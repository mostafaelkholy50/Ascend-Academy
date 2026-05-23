<?php

namespace App\Services;

use App\Repositories\TeacherRepository;
use App\Filters\TeacherFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherService
{
    protected $repository;
    protected $filter;

    public function __construct(TeacherRepository $repository, TeacherFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getTeachers(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getTeachersQuery();
        $query = $this->filter->apply($query, $request);

        return $query->latest()->paginate($perPage);
    }

    public function getTeacherDetails(User $teacher)
    {
        $teacher = $this->repository->getTeacherWithRelations($teacher);

        // Calculate stats
        $totalStudents = $teacher->teacherSchedules()->distinct('student_id')->count('student_id');
        $completedClasses = $teacher->teacherSchedules()->where('status', 'completed')->count();
        $upcomingClasses = $teacher->teacherSchedules()->where('status', 'scheduled')->where('starts_at', '>', now())->count();

        return [
            'teacher' => $teacher,
            'totalStudents' => $totalStudents,
            'completedClasses' => $completedClasses,
            'upcomingClasses' => $upcomingClasses,
        ];
    }

    public function storeTeacher(array $data, $avatarFile = null)
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'Teacher';
        $data['active'] = true;

        if ($avatarFile) {
            $data['avatar'] = $avatarFile->store('avatars', 'public');
        }

        return $this->repository->create($data);
    }

    public function updateTeacher(User $teacher, array $data, $avatarFile = null)
    {
        if ($avatarFile) {
            if ($teacher->avatar) {
                Storage::disk('public')->delete($teacher->avatar);
            }
            $data['avatar'] = $avatarFile->store('avatars', 'public');
        }

        return $this->repository->update($teacher, $data);
    }

    public function updatePassword(User $teacher, string $password)
    {
        return $this->repository->update($teacher, [
            'password' => Hash::make($password),
        ]);
    }

    public function updateRate(User $teacher, float $rate)
    {
        $this->repository->update($teacher, [
            'hourly_rate' => $rate,
        ]);

        // Recalculate all unpaid months for this teacher
        $unpaidMonths = \App\Models\TeacherHour::where('teacher_id', $teacher->id)
            ->where('is_paid', false)
            ->get();

        foreach ($unpaidMonths as $teacherHour) {
            $teacherHour->total_salary = $teacherHour->total_hours * $rate;
            $teacherHour->save();
        }

        return true;
    }

    public function deleteTeacher(User $teacher)
    {
        if ($teacher->avatar) {
            Storage::disk('public')->delete($teacher->avatar);
        }
        return $this->repository->delete($teacher);
    }
}
