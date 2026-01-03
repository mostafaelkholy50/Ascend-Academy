<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class DashboardLayout extends Component
{
    public string $title;

    public function __construct(string $title = 'Dashboard')
    {
        $this->title = $title;
    }

    public function render(): View
    {
        return view('layouts.dashboard');
    }
}
