@can('view dashboard')
<a href="{{ route('admin.dashboard') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
</a>
@endcan

@can('manage users')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">User Management</div>
<a href="{{ route('admin.inquiries.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.inquiries.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clipboard-question mr-3 text-base"></i> Registrations
    @php $pendingCount = \App\Models\Inquiry::where('status', 'pending')->count(); @endphp
    @if($pendingCount > 0) <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span> @endif
</a>

<a href="{{ route('admin.parents.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.parents.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-friends mr-3 text-base"></i> Parents
</a>

<a href="{{ route('admin.students.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.students.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-graduate mr-3 text-base"></i> Students
</a>

<a href="{{ route('admin.teachers.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.teachers.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chalkboard-teacher mr-3 text-base"></i> Teachers
</a>

<a href="{{ route('admin.teacher-applications.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.teacher-applications.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-plus mr-3 text-base"></i> Applications
    @php $pendingApps = \App\Models\TeacherApplication::where('status', 'pending')->count(); @endphp
    @if($pendingApps > 0) <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingApps }}</span> @endif
</a>
@endcan

@canany(['manage schedules', 'manage news'])
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Academic</div>
@can('manage schedules')
<a href="{{ route('admin.courses.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.courses.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-book mr-3 text-base"></i> Courses
</a>
@endcan

@can('manage news')
<a href="{{ route('admin.news.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.news.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-newspaper mr-3 text-base"></i> News
</a>
@endcan

@can('manage schedules')
<a href="{{ route('admin.enrollments.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.enrollments.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-graduation-cap mr-3 text-base"></i> Enrollments
</a>

<a href="{{ route('admin.schedules.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.schedules.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-calendar-check mr-3 text-base"></i> Schedules
</a>

<a href="{{ route('admin.attendances.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.attendances.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clipboard-list mr-3 text-base"></i> Attendance
</a>
@endcan
@endcanany

@can('manage accounting')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Finance</div>
<a href="{{ route('accountant.payments.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('accountant.payments.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-money-bill-wave mr-3 text-base"></i> Payments
</a>
<a href="{{ route('admin.pricing-tiers.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.pricing-tiers.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-dollar-sign mr-3 text-base"></i> Pricing Tiers
</a>
<a href="{{ route('admin.teacher-hours.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.teacher-hours.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clock mr-3 text-base"></i> Teacher Hours
</a>
@endcan

@can('manage quality')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Quality</div>
<a href="{{ route('admin.reports.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-file-alt mr-3 text-base"></i> Reports
</a>
<a href="{{ route('admin.student-evaluations.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.student-evaluations.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chart-bar mr-3 text-base"></i> Student Evaluations
</a>
@endcan

<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">System</div>
@if(auth()->user()->hasRole('SuperAdmin'))
<a href="{{ route('superadmin.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('superadmin.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-shield-halved mr-3 text-base"></i> Roles & Permissions
</a>
@endif

<a href="{{ route('admin.profile.show') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-cog mr-3 text-base"></i> Settings
</a>