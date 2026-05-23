<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ResourceController extends Controller
{
    protected $service;

    public function __construct(StudentResourceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            $data = $this->service->getIndexData($student, $request);

            return view('student.resources.index', $data);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل المصادر. الرجاء المحاولة مرة أخرى.');
        }
    }

    public function show($id)
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            $resource = $this->service->getResource($student, $id);

            return view('student.resources.show', compact('resource'));
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل المصدر. الرجاء المحاولة مرة أخرى.');
        }
    }

    public function download($id)
    {
        try {
            $student = auth()->user();
            
            if ($student->role !== 'Student') {
                abort(403);
            }

            $resource = $this->service->getResource($student, $id);

            if (!$resource->file_path || !Storage::disk('local')->exists($resource->file_path)) {
                return back()->with('error', 'File not found or access denied.');
            }

            // Sanitize filename
            $safeName = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $resource->title);
            $extension = pathinfo($resource->file_path, PATHINFO_EXTENSION);
            if (!str_ends_with($safeName, '.' . $extension)) {
                $safeName .= '.' . $extension;
            }

            return Storage::disk('local')->download($resource->file_path, $safeName, [
                'Content-Type' => $resource->mime_type ?? 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
                'Content-Security-Policy' => "default-src 'none'",
            ]);
            
        } catch (Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return $this->errorResponse('حدث خطأ أثناء تحميل الملف. الرجاء المحاولة مرة أخرى.');
        }
    }
}
