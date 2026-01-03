<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class InquiryController extends Controller
{
    /**
     * عرض جميع الطلبات
     */
    public function index(Request $request)
    {
        $query = Inquiry::query()->latest();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->paginate(15);

        return view('admin.inquiries.index', compact('inquiries'));
    }

    /**
     * عرض تفاصيل طلب محدد
     */
    public function show(Inquiry $inquiry)
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    /**
     * تحويل الطلب إلى حساب Parent
     */
    public function convertToParent(Inquiry $inquiry)
    {
        // التحقق من أن البريد الإلكتروني غير مستخدم
        if (User::where('email', $inquiry->email)->exists()) {
            return back()->with('error', 'This email is already registered.');
        }

        try {
            // إنشاء حساب Parent
            $parent = User::create([
                'name' => $inquiry->full_name,
                'email' => $inquiry->email,
                'password' => Hash::make('password123'), // كلمة مرور مؤقتة
                'phone' => $inquiry->phone,
                'role' => 'Parent',
                'active' => true,
            ]);

            // تحديث حالة الطلب
            $inquiry->update([
                'status' => 'converted',
                'admin_notes' => 'Converted to parent account on ' . now()->format('Y-m-d H:i:s')
            ]);

            return redirect()
                ->route('admin.parents.show', $parent->id)
                ->with('success', "Parent account created successfully! Temporary password: password123");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create parent account: ' . $e->getMessage());
        }
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,converted,cancelled',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $inquiry->update($request->only(['status', 'admin_notes']));

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * حذف طلب
     */
    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }
}
