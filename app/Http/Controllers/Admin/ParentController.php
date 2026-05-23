<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ParentService;
use App\Http\Requests\Admin\StoreParentRequest;
use App\Http\Requests\Admin\AddChildRequest;
use App\Http\Requests\Admin\UpdateParentRequest;
use App\Http\Requests\Admin\UpdateParentPasswordRequest;

class ParentController extends Controller
{
    protected $parentService;

    public function __construct(ParentService $parentService)
    {
        $this->parentService = $parentService;
    }

    /**
     * عرض جميع الآباء
     */
    public function index(Request $request)
    {
        $parents = $this->parentService->getIndexData($request);
        return view('admin.parents.index', compact('parents'));
    }

    /**
     * Show form to create new parent
     */
    public function create()
    {
        $students = User::where('role', 'Student')->where('active', true)->get();
        return view('admin.parents.create', compact('students'));
    }

    /**
     * Store new parent
     */
    public function store(StoreParentRequest $request)
    {
        $this->parentService->storeParent($request->validated());

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent created successfully.');
    }

    /**
     * عرض تفاصيل Parent مع أبنائه
     */
    public function show(User $parent)
    {
        $parent->load(['children.enrollments.course', 'children.schedules', 'children.reports']);
        $courses = Course::all();

        return view('admin.parents.show', compact('parent', 'courses'));
    }

    /**
     * إضافة طالب (ابن) لولي الأمر
     */
    public function addChild(AddChildRequest $request, User $parent)
    {
        try {
            $this->parentService->addChild($parent, $request->validated());

            return back()->with('success', 'Student added successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add student: ' . $e->getMessage());
        }
    }

    /**
     * حذف العلاقة بين Parent و Child
     */
    public function removeChild(User $parent, User $child)
    {
        $this->parentService->removeChild($parent, $child);

        return back()->with('success', 'Student removed from parent successfully.');
    }

    /**
     * تحديث بيانات Parent
     */
    public function update(UpdateParentRequest $request, User $parent)
    {
        $this->parentService->updateParent($parent, $request->validated());

        return back()->with('success', 'Parent updated successfully.');
    }

    /**
     * حذف Parent
     */
    public function destroy(User $parent)
    {
        $this->parentService->deleteParent($parent);
        
        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent deleted successfully.');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(UpdateParentPasswordRequest $request, User $parent)
    {
        $this->parentService->updatePassword($parent, $request->password);

        return back()->with('success', 'Password updated successfully.');
    }
}
