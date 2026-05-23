<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Resource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceRequest\StoreResourceRequest;
use App\Http\Requests\ResourceRequest\UpdateResourceRequest;
use App\Filters\TeacherResourceFilter;
use App\Services\TeacherResourceService;
use Exception;

class ResourceController extends Controller
{
    protected $service;

    public function __construct(TeacherResourceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, TeacherResourceFilter $filter)
    {
        try {
            $teacher = auth()->user();
            $data = $this->service->getIndexData($teacher, $request, $filter);
            
            return view('teacher.resources.index', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل المصادر.');
        }
    }

    public function create(Request $request)
    {
        try {
            $teacher = auth()->user();
            $data = $this->service->getCreateData($teacher, $request);
            
            return view('teacher.resources.create', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل صفحة الإضافة.');
        }
    }

    public function store(StoreResourceRequest $request)
    {
        try {
            $teacher = auth()->user();
            $this->service->storeResource($teacher, $request);

            return redirect()->route('teacher.resources.index')
                ->with('success', 'Resource uploaded successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to upload resource: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Resource $resource)
    {
        try {
            if ($resource->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $resource->load(['student', 'course', 'teacher']);

            return view('teacher.resources.show', compact('resource'));
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء عرض المصدر.');
        }
    }

    public function edit(Resource $resource)
    {
        try {
            if ($resource->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $teacher = auth()->user();
            $data = $this->service->getEditData($teacher, $resource);

            return view('teacher.resources.edit', $data);
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل صفحة التعديل.');
        }
    }

    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        try {
            if ($resource->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $this->service->updateResource($resource, $request);

            return redirect()->route('teacher.resources.show', $resource->id)
                ->with('success', 'Resource updated successfully!');
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return back()->with('error', 'Failed to update resource: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Resource $resource)
    {
        try {
            if ($resource->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $this->service->deleteResource($resource);

            return redirect()->route('teacher.resources.index')
                ->with('success', 'Resource deleted successfully.');
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء حذف المصدر.');
        }
    }

    public function download(Resource $resource)
    {
        try {
            if ($resource->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            return $this->service->downloadResource($resource);
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
