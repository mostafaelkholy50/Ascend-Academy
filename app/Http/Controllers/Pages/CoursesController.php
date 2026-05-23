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
                'Beginner' => $courses->where('level', 'Beginner')->count(),
                'Intermediate' => $courses->where('level', 'Intermediate')->count(),
                'Advanced' => $courses->where('level', 'Advanced')->count(),
            ],
            'age_groups' => [
                'Kids' => $courses->where('age_group', 'Kids')->count(),
                'Teens' => $courses->where('age_group', 'Teens')->count(),
                'Adults' => $courses->where('age_group', 'Adults')->count(),
            ],
            'languages' => [
                'English' => $courses->where('language', 'English')->count(),
                'Arabic' => $courses->where('language', 'Arabic')->count(),
            ],
            'prices' => [
                'All' => $courses->count(),
                'Free' => $courses->where('is_free', true)->count(),
                'Paid' => $courses->where('is_free', false)->count(),
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
