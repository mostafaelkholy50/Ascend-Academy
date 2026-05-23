@php
    $user = auth()->user();
    $pendingEvaluationsCount = 0;
    if ($user->can('view evaluations')) {
        $startOfWeek = now()->startOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
        $allTeachersCount = \App\Models\User::role('Teacher')->count();
        $evaluatedCount = \App\Models\TeacherEvaluation::where('week_start_date', $startOfWeek)->count();
        $pendingEvaluationsCount = max(0, $allTeachersCount - $evaluatedCount);
    }
@endphp

<!-- Core Dashboard -->
@can('view dashboard')
    @php
        $dashboardRoute = 'admin.dashboard';
        if ($user->hasRole('Accountant')) {
            $dashboardRoute = 'accountant.dashboard';
        } elseif ($user->hasRole('SchedulerManager')) {
            $dashboardRoute = 'scheduler.dashboard';
        } elseif ($user->hasRole('QualityControl')) {
            $dashboardRoute = 'qualitycontrol.dashboard';
        }
    @endphp
    <a href="{{ route($dashboardRoute) }}"
        class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs($dashboardRoute) ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
        <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
    </a>
@endcan

<!-- User Management -->
@can('manage users')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">User Management</div>
<a href="{{ route('admin.inquiries.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.inquiries.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clipboard-question mr-3 text-base"></i> Registrations
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
@endcan

<!-- Academic & Scheduling -->
@if($user->can('manage schedules') || $user->can('manage availability') || $user->can('view evaluations') || $user->can('manage books') || $user->can('view books') || $user->can('manage news'))
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Academic & Scheduling</div>

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

@if($user->can('manage books') || $user->can('view books'))
<a href="{{ route('books.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('books.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-book-open mr-3 text-base"></i> Books
</a>
@endif

@if($user->can('manage schedules') || $user->can('view evaluations'))
<a href="{{ route('scheduler.schedules.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('*.schedules.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-calendar-check mr-3 text-base"></i> Schedules
</a>
@endif

@can('manage schedules')
<a href="{{ route('scheduler.attendance.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('*.attendance.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clipboard-list mr-3 text-base"></i> Attendance
</a>
@endcan

@can('manage availability')
<a href="{{ route('scheduler.teachers.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('scheduler.teachers.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clock mr-3 text-base"></i> Availability
</a>
@endcan
@endif

<!-- Finance -->
@can('manage accounting')
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Finance</div>



<a href="{{ $user->hasRole('Accountant') ? route('accountant.payments.index') : route('admin.payments.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('accountant.payments.*') || request()->routeIs('admin.payments.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-money-bill-wave mr-3 text-base"></i> Payments
</a>
@if($user->canAccessPayroll())
<a href="{{ route('accountant.teacher-hours.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('accountant.teacher-hours.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clock mr-3 text-base"></i> Teacher Hours
</a>
@endif
@endcan

<!-- Quality Control -->
@if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->hasRole('Admin') || auth()->user()->can('view evaluations'))
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">Quality Control</div>

<a href="{{ route('qualitycontrol.reports.center', ['view' => 'weekly']) }}"
    class="flex items-center justify-between px-4 py-2.5 rounded-xl {{ request('view') == 'weekly' || !request('view') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <div class="flex items-center">
        <i class="fa-solid fa-calendar-week mr-3 text-base"></i> Weekly Tasks
    </div>
    @if($pendingEvaluationsCount > 0)
        <span class="bg-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg animate-bounce">
            {{ $pendingEvaluationsCount }}
        </span>
    @endif
</a>

<a href="{{ route('qualitycontrol.reports.center', ['view' => 'monthly']) }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request('view') == 'monthly' ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-calendar-check mr-3 text-base"></i> Monthly Report
</a>

<a href="{{ route('qualitycontrol.reports.center', ['view' => 'yearly']) }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request('view') == 'yearly' ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chart-line mr-3 text-base"></i> Yearly Report
</a>

<a href="{{ route('qualitycontrol.reports.center', ['view' => 'log']) }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request('view') == 'log' ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-history mr-3 text-base"></i> Evaluation Log
</a>

<a href="{{ route('admin.student-evaluations.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.student-evaluations.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-graduate mr-3 text-base"></i> Student Reports
</a>
@endif

<!-- System Admin -->
<div class="px-4 pt-4 pb-2 text-[10px] uppercase tracking-wider text-white/50 font-bold">System</div>
@can('manage permissions')
<a href="{{ route('superadmin.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('superadmin.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-shield-halved mr-3 text-base"></i> Roles & Permissions
</a>
@endcan

<a href="{{ route('admin.profile.show') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-cog mr-3 text-base"></i> Settings
</a>
