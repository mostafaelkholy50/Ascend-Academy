<?php

namespace App\Services;

use App\Repositories\TeacherResourceRepository;
use App\Models\User;
use App\Models\Resource;
use App\Filters\TeacherResourceFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ResourceRequest\StoreResourceRequest;
use App\Http\Requests\ResourceRequest\UpdateResourceRequest;

class TeacherResourceService
{
    protected $repository;

    public function __construct(TeacherResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndexData(User $teacher, Request $request, TeacherResourceFilter $filter): array
    {
        $resources = $this->repository->getPaginatedResources($teacher, $request, $filter);
        $students = $this->repository->getTeacherStudents($teacher);
        $courses = $this->repository->getTeacherCourses($teacher);

        return compact('resources', 'students', 'courses');
    }

    public function getCreateData(User $teacher, Request $request): array
    {
        $students = $this->repository->getTeacherStudents($teacher);
        $courses = $this->repository->getTeacherCourses($teacher);
        
        $selectedStudent = $request->query('student_id');
        $selectedCourse = $request->query('course_id');

        return compact('students', 'courses', 'selectedStudent', 'selectedCourse');
    }

    public function getEditData(User $teacher, Resource $resource): array
    {
        $students = $this->repository->getTeacherStudents($teacher);
        $courses = $this->repository->getTeacherCourses($teacher);

        return compact('resource', 'students', 'courses');
    }

    public function storeResource(User $teacher, StoreResourceRequest $request): Resource
    {
        $data = [
            'teacher_id' => $teacher->id,
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Secure storage: Use local (private) disk
            $path = $file->store('resources', 'local');
            $data['file_path'] = $path;
            $data['mime_type'] = $file->getMimeType();
        }

        // Handle external URL
        if ($request->filled('external_url')) {
            $data['external_url'] = $request->external_url;
        }

        $resource = $this->repository->createResource($data);

        $this->sendResourceNotifications($resource);

        return $resource;
    }

    public function updateResource(Resource $resource, UpdateResourceRequest $request): bool
    {
        $data = [
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
        ];

        // Handle file replacement
        if ($request->hasFile('file')) {
            // Delete old file from secure storage
            if ($resource->file_path && Storage::disk('local')->exists($resource->file_path)) {
                Storage::disk('local')->delete($resource->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('resources', 'local');
            $data['file_path'] = $path;
            $data['mime_type'] = $file->getMimeType();
        }

        // Handle external URL
        if ($request->filled('external_url')) {
            $data['external_url'] = $request->external_url;
        }

        return $this->repository->updateResource($resource, $data);
    }

    public function deleteResource(Resource $resource): bool
    {
        // Delete file from secure storage
        if ($resource->file_path && Storage::disk('local')->exists($resource->file_path)) {
            Storage::disk('local')->delete($resource->file_path);
        }

        return $this->repository->deleteResource($resource);
    }

    public function downloadResource(Resource $resource)
    {
        if (!$resource->file_path || !Storage::disk('local')->exists($resource->file_path)) {
            throw new \Exception('File not found or access denied.');
        }

        // Sanitize filename for download to prevent XSS/Injection in headers
        $safeName = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $resource->title);
        $extension = pathinfo($resource->file_path, PATHINFO_EXTENSION);
        if (!str_ends_with($safeName, '.' . $extension)) {
            $safeName .= '.' . $extension;
        }

        // Secure download with specific headers
        return Storage::disk('local')->download($resource->file_path, $safeName, [
            'Content-Type' => $resource->mime_type ?? 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'",
        ]);
    }

    protected function sendResourceNotifications(Resource $resource): void
    {
        // Load relationships for email
        $resource->load(['student', 'teacher', 'course']);

        // Send email notification to student
        try {
            $resource->student->notify(new \App\Notifications\ResourceAddedNotification($resource));
        } catch (\Exception $e) {
            Log::error('Failed to send resource notification to student: ' . $e->getMessage());
        }

        // Send email notification to parent(s)
        try {
            $parents = $resource->student->parents;
            if ($parents) {
                foreach ($parents as $parent) {
                    $parent->notify(new \App\Notifications\ResourceAddedNotification($resource));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send resource notification to parents: ' . $e->getMessage());
        }
    }
}
