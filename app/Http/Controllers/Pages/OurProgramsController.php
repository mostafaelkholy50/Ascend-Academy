<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OurProgramsController extends Controller
{
    function index()
    {
        return view('pages.OurPrograms');
    }
}
