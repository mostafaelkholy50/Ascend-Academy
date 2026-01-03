<!-- Admin Sidebar Links -->
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
</a>

<a href="{{ route('admin.inquiries.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.inquiries.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-inbox mr-3 text-base"></i> Inquiries
    @php
        $pendingCount = \App\Models\Inquiry::where('status', 'pending')->count();
    @endphp
    @if($pendingCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('admin.parents.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.parents.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-friends mr-3 text-base"></i> Parents
</a>

<a href="{{ route('admin.students.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.students.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-graduate mr-3 text-base"></i> Students
</a>

<a href="{{ route('admin.teachers.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.teachers.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chalkboard-teacher mr-3 text-base"></i> Teachers
</a>

<a href="{{ route('admin.teacher-applications.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.teacher-applications.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-user-plus mr-3 text-base"></i> Teacher Applications
    @php
        $pendingApplications = \App\Models\TeacherApplication::where('status', 'pending')->count();
    @endphp
    @if($pendingApplications > 0)
        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingApplications }}</span>
    @endif
</a>

<a href="{{ route('admin.courses.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.courses.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-book mr-3 text-base"></i> Courses
</a>

<a href="{{ route('admin.enrollments.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.enrollments.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-calendar-check mr-3 text-base"></i> Enrollments
</a>

<a href="{{ route('admin.payments.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.payments.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-money-bill-wave mr-3 text-base"></i> Payments
</a>

<a href="{{ route('admin.pricing-tiers.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.pricing-tiers.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-dollar-sign mr-3 text-base"></i> Pricing Tiers
</a>

<a href="{{ route('admin.schedules.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.schedules.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-calendar-check mr-3 text-base"></i> Schedules
</a>

<a href="{{ route('admin.attendances.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.attendances.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clipboard-list mr-3 text-base"></i> Attendance
</a>

<a href="{{ route('admin.teacher-hours.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.teacher-hours.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-clock mr-3 text-base"></i> Teacher Hours
</a>

<a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chart-line mr-3 text-base"></i> Reports
</a>

<a href="{{ route('admin.profile.show') }}" class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-cog mr-3 text-base"></i> Settings
</a>
