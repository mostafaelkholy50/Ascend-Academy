<!-- Student Sidebar Links -->
<a href="{{ route('student.dashboard') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('student.dashboard') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-th-large mr-3 text-base"></i> Dashboard
</a>

<a href="{{ route('student.courses.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('student.courses.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-book mr-3 text-base"></i> My Courses
</a>


<a href="{{ route('student.schedule.weekly') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('student.schedule.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-regular fa-calendar mr-3 text-base"></i> Schedule
</a>


<a href="{{ route('student.resources.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('student.resources.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-folder-open mr-3 text-base"></i> Resources
</a>

<a href="{{ route('books.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('books.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-book-open mr-3 text-base"></i> Books
</a>

<a href="{{ route('student.reports.index') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('student.reports.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-chart-line mr-3 text-base"></i> Progress Reports
</a>


<a href="{{ route('student.profile.show') }}"
    class="flex items-center px-4 py-2.5 rounded-xl {{ request()->routeIs('student.profile.*') ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid fa-cog mr-3 text-base"></i> Settings
</a>
