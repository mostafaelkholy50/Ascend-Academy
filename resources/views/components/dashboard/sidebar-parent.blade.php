<!-- Parent Sidebar Links -->
<a href="{{ route('parent.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('parent.dashboard') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
</a>

<a href="{{ route('parent.children.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('parent.children.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-child mr-3 text-base"></i> My Children
</a>

<a href="{{ route('parent.schedule.weekly') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('parent.schedule.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-regular fa-calendar mr-3 text-base"></i> Schedule
</a>

<a href="{{ route('parent.reports.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('parent.reports.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chart-line mr-3 text-base"></i> Progress Reports
</a>

<a href="{{ route('parent.attendance.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('parent.attendance.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clipboard-check mr-3 text-base"></i> Attendance
</a>

<a href="{{ route('parent.profile.show') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('parent.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-cog mr-3 text-base"></i> Settings
</a>
