<?php

namespace App\Services;

use App\Repositories\ParentRepository;
use App\Filters\ParentFilter;
use App\Models\User;
use App\Models\Children;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ParentService
{
    protected $repository;
    protected $filter;

    public function __construct(ParentRepository $repository, ParentFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getIndexData(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getParentsQuery();
        $query = $this->filter->apply($query, $request);

        return $query->latest()->paginate($perPage);
    }

    public function storeParent(array $data)
    {
        return DB::transaction(function () use ($data) {
            $parent = $this->repository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'Parent',
                'phone' => $data['phone'] ?? null,
                'country' => $data['country'] ?? null,
                'active' => $data['active'] ?? true,
            ]);
            $parent->assignRole('Parent');

            if (!empty($data['children'])) {
                $parent->children()->attach($data['children']);
            }

            return $parent;
        });
    }

    public function addChild(User $parent, array $data)
    {
        return DB::transaction(function () use ($parent, $data) {
            $student = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'country' => $data['country'] ?? null,
                'role' => 'Student',
                'active' => true,
            ]);
            $student->assignRole('Student');

            Children::create([
                'parent_id' => $parent->id,
                'child_id' => $student->id,
            ]);

            return $student;
        });
    }

    public function attachStudents(User $parent, array $data)
    {
        return DB::transaction(function () use ($parent, $data) {
            $studentIds = array_unique($data['student_ids']);

            $parent->children()->attach($studentIds);

            return $studentIds;
        });
    }

    public function removeChild(User $parent, User $child)
    {
        return Children::where('parent_id', $parent->id)
            ->where('child_id', $child->id)
            ->delete();
    }

    public function updateParent(User $parent, array $data)
    {
        return $this->repository->update($parent, $data);
    }

    public function updatePassword(User $parent, string $password)
    {
        return $parent->update([
            'password' => Hash::make($password),
        ]);
    }

    public function deleteParent(User $parent)
    {
        return DB::transaction(function () use ($parent) {
            // Delete relationships in children table
            Children::where('parent_id', $parent->id)->delete();
            
            // Delete the parent
            return $this->repository->delete($parent);
        });
    }
}
