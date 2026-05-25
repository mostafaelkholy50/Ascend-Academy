@can('view dashboard')
<a href="{{ route('scheduler.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('scheduler.dashboard') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
</a>
@endcan

@can('manage schedules')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Scheduling</div>
<a href="{{ route('scheduler.schedules.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('scheduler.schedules.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-calendar-alt mr-3 text-base"></i> All Schedules
</a>
<a href="{{ route('scheduler.attendance.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('scheduler.attendance.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-check mr-3 text-base"></i> Attendance
</a>
@endcan

<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Directories</div>
<a href="{{ route('scheduler.students.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('scheduler.students.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-graduate mr-3 text-base"></i> Students List
</a>
<a href="{{ route('scheduler.teachers.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('scheduler.teachers.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chalkboard-teacher mr-3 text-base"></i> Teachers List
</a>

@can('manage accounting')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Finance</div>
<a href="{{ route('accountant.payments.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('accountant.payments.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-money-bill-wave mr-3 text-base"></i> Payments
</a>
@endcan

@can('manage quality')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Quality</div>
<a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chart-line mr-3 text-base"></i> Reports
</a>
@endcan

<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Settings</div>
<a href="{{ route('admin.profile.show') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-circle mr-3 text-base"></i> My Profile
</a>
