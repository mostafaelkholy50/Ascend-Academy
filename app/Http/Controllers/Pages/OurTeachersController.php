<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class OurTeachersController extends Controller
{
  function index() {
    $teachers = User::where('role', 'Teacher')->where('active', true)->latest()->get();
    return view('pages.OurTeachers', compact('teachers'));
  }
}
