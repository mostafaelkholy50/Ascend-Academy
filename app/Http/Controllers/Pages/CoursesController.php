<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Course;

class CoursesController extends Controller
{
    public function index() 
    {
        $courses = Course::latest()->get();
        
        $filterCounts = [
            'levels' => [
                'Beginner' => Course::where('level', 'Beginner')->count(),
                'Intermediate' => Course::where('level', 'Intermediate')->count(),
                'Advanced' => Course::where('level', 'Advanced')->count(),
            ],
            'age_groups' => [
                'Kids' => Course::where('age_group', 'Kids')->count(),
                'Teens' => Course::where('age_group', 'Teens')->count(),
                'Adults' => Course::where('age_group', 'Adults')->count(),
            ],
            'languages' => [
                'English' => Course::where('language', 'English')->count(),
                'Arabic' => Course::where('language', 'Arabic')->count(),
            ],
            'prices' => [
                'All' => Course::count(),
                'Free' => Course::where('is_free', true)->count(),
                'Paid' => Course::where('is_free', false)->count(),
            ],
        ];
        
        // Get pricing tiers for display
        $pricingTiers = \App\Models\PricingTier::active()
            ->orderBy('days_per_week')
            ->orderBy('session_duration')
            ->get();
        
        return view('pages.Courses', compact('courses', 'filterCounts', 'pricingTiers'));
    }
}
