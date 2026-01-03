<!-- Teacher Sidebar Links - REPLACE components/dashboard/sidebar-teacher.blade.php -->
<a href="{{ route('teacher.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.dashboard') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
</a>

<a href="{{ route('teacher.my-students') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.my-students') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-graduate mr-3 text-base"></i> My Students
</a>

<a href="{{ route('teacher.schedule.index') }}" class="flex items-center px-4 py-2.5 rounded-xl hover:bg-white/10 transition opacity-80 hover:opacity-100">
    <i class="fa-regular fa-calendar mr-3 text-base"></i> My Schedule
</a>


<a href="{{ route('teacher.reports.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.reports.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-file-alt mr-3 text-base"></i> Progress Reports
    @php
        $pendingReportsCount = \App\Models\Schedule::where('teacher_id', auth()->id())
            ->where('status', 'completed')
            ->whereDoesntHave('attendance')
            ->where('starts_at', '>=', now()->subDays(7))
            ->count();
    @endphp
    @if($pendingReportsCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingReportsCount }}</span>
    @endif
</a>

<a href="{{ route('teacher.resources.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.resources.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-folder-open mr-3 text-base"></i> Resources
</a>

<a href="{{ route('teacher.hours.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.hours.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clock mr-3 text-base"></i> My Hours
</a>

<a href="{{ route('teacher.profile.show') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('teacher.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-cog mr-3 text-base"></i> Settings
</a>
