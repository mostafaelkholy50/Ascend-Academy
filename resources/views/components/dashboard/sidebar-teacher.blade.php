<!-- Teacher Sidebar Links - REPLACE components/dashboard/sidebar-teacher.blade.php -->
<a href="{{ route('teacher.dashboard') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.dashboard') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
</a>

<a href="{{ route('teacher.my-students') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.my-students') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-graduate mr-3 text-base"></i> My Students
</a>

<a href="{{ route('teacher.schedule.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl hover:bg-white/10 transition opacity-80 hover:opacity-100">
    <i class="fa-regular fa-calendar mr-3 text-base"></i> My Schedule
</a>


<a href="{{ route('teacher.student-evaluations.pending') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.student-evaluations.pending') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-clock mr-3 text-base"></i> Pending Evaluations
    @php
        $pendingEvalsCount = app(\App\Services\StudentEvaluationService::class)->getPendingEvaluations(auth()->user())->count();
    @endphp
    @if ($pendingEvalsCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingEvalsCount }}</span>
    @endif
</a>

<a href="{{ route('teacher.student-evaluations.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.student-evaluations.index') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-list mr-3 text-base"></i> Evaluations History
</a>

<a href="{{ route('teacher.student-evaluations.summary') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.student-evaluations.summary') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chart-bar mr-3 text-base"></i> Performance Summary
</a>

<a href="{{ route('teacher.resources.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.resources.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-folder-open mr-3 text-base"></i> Resources
</a>

<a href="{{ route('books.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('books.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-book-open mr-3 text-base"></i> Books
</a>

<a href="{{ route('teacher.hours.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.hours.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clock mr-3 text-base"></i> My Hours
</a>

<a href="{{ route('teacher.profile.show') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-cog mr-3 text-base"></i> Settings
</a>
