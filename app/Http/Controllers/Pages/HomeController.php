<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $courses = \App\Models\Course::latest()->take(6)->get();
        // بنجيب أول 2 رجالة
        $maleTeachers = \App\Models\User::where('role', 'Teacher')
            ->where('gender', 'Male')
            ->where('active', true)
            ->latest()
            ->take(2)
            ->get();

        // بنجيب أول 2 ستات
        $femaleTeachers = \App\Models\User::where('role', 'Teacher')
            ->where('gender', 'Female')
            ->where('active', true)
            ->latest()
            ->take(2)
            ->get();

        // ندمجهم مع بعض في متغير واحد
        $teachers = $maleTeachers->merge($femaleTeachers);
        return view('pages.welcome', compact('courses', 'teachers'));
    }
}
