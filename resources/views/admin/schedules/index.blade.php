<x-dashboard-layout title="Schedules">
    <!-- Page Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Schedule Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage all course schedules and sessions</p>
            </div>
            @can('manage schedules')
            <a href="{{ route('admin.schedules.create') }}" 
                class="inline-flex items-center justify-center px-4 md:px-6 py-2.5 md:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition text-sm md:text-base">
                <i class="fa-solid fa-plus mr-2"></i><span>Create New Schedule</span>
            </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium mb-1">Total Sessions</p>
                    <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium mb-1">Upcoming</p>
                    <p class="text-3xl font-bold">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium mb-1">Completed</p>
                    <p class="text-3xl font-bold">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium mb-1">Cancelled</p>
                    <p class="text-3xl font-bold">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="bg-white rounded-2xl shadow-lg p-2 mb-4 md:mb-6 inline-flex flex-col sm:flex-row gap-2 border border-gray-100 w-full sm:w-auto">
        <a href="{{ route('admin.schedules.index', ['view' => 'calendar']) }}" 
            class="px-4 md:px-6 py-2.5 md:py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2 text-sm md:text-base {{ $viewType === 'calendar' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fa-solid fa-calendar-week"></i>
            <span>Weekly Calendar</span>
        </a>
        <a href="{{ route('admin.schedules.index', ['view' => 'list']) }}" 
            class="px-4 md:px-6 py-2.5 md:py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2 text-sm md:text-base {{ $viewType === 'list' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fa-solid fa-list"></i>
            <span>Enrollment List</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <i class="fa-solid fa-check-circle text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <i class="fa-solid fa-exclamation-circle text-xl"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($viewType === 'calendar')
        <!-- Weekly Calendar View -->
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
            <!-- Week Navigation -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 md:p-6 border-b border-gray-200">
                <div class="flex items-center justify-between gap-2">
                    <a href="{{ route('admin.schedules.index', ['view' => 'calendar', 'week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" 
                        class="px-3 md:px-6 py-2 md:py-3 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold shadow-sm flex items-center gap-1 md:gap-2 text-sm md:text-base">
                        <i class="fa-solid fa-chevron-left"></i>
                        <span class="hidden sm:inline">Previous</span>
                    </a>
                    <div class="text-center flex-1">
                        <h2 class="text-base md:text-2xl font-bold text-gray-800">
                            {{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}
                        </h2>
                        <p class="text-xs md:text-sm text-gray-600 mt-1">Week {{ $weekStart->weekOfYear }}</p>
                    </div>
                    <a href="{{ route('admin.schedules.index', ['view' => 'calendar', 'week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" 
                        class="px-3 md:px-6 py-2 md:py-3 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold shadow-sm flex items-center gap-1 md:gap-2 text-sm md:text-base">
                        <span class="hidden sm:inline">Next</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- Calendar - Day by Day View -->
            <div class="space-y-4 md:space-y-6">
                @foreach($weekDays as $day)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 {{ $day['date']->isToday() ? 'border-indigo-500' : 'border-gray-100' }}">
                        <!-- Day Header -->
                        <div class="p-4 md:p-6 {{ $day['date']->isToday() ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gradient-to-r from-gray-50 to-gray-100 text-gray-800' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl md:text-2xl font-bold">{{ $day['date']->format('l') }}</h3>
                                    <p class="text-sm {{ $day['date']->isToday() ? 'text-white/80' : 'text-gray-600' }} mt-1">
                                        {{ $day['date']->format('F d, Y') }}
                                    </p>
                                </div>
                                @if($day['date']->isToday())
                                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold">
                                        Today
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Schedules for this day -->
                        <div class="p-4 md:p-6">
                            @if(count($day['schedules']) > 0)
                                <div class="space-y-3">
                                    @foreach($day['schedules']->sortBy('starts_at') as $schedule)
                                        <a href="{{ route('admin.schedules.show', $schedule->id) }}" 
                                            class="block p-4 rounded-xl border-2 transition hover:shadow-lg hover:-translate-y-1
                                                {{ $schedule->status === 'scheduled' ? 'bg-green-50 border-green-200 hover:border-green-400' : '' }}
                                                {{ $schedule->status === 'completed' ? 'bg-blue-50 border-blue-200 hover:border-blue-400' : '' }}
                                                {{ $schedule->status === 'cancelled' ? 'bg-red-50 border-red-200 hover:border-red-400' : '' }}">
                                            
                                            <div class="flex items-start gap-3">
                                                <!-- Time Badge -->
                                                <div class="flex-shrink-0 w-20 md:w-24 text-center">
                                                    <div class="px-3 py-2 rounded-lg font-bold text-sm md:text-base
                                                        {{ $schedule->status === 'scheduled' ? 'bg-green-600 text-white' : '' }}
                                                        {{ $schedule->status === 'completed' ? 'bg-blue-600 text-white' : '' }}
                                                        {{ $schedule->status === 'cancelled' ? 'bg-red-600 text-white' : '' }}">
                                                        {{ $schedule->starts_at->format('g:i A') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">{{ $schedule->getDurationInMinutes() }} min</div>
                                                </div>

                                                <!-- Schedule Details -->
                                                <div class="flex-1 min-w-0">
                                                    <!-- Student -->
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm md:text-base font-bold flex-shrink-0">
                                                            {{ strtoupper(substr($schedule->student->name, 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <h4 class="font-bold text-gray-900 text-sm md:text-base truncate">{{ $schedule->student->name }}</h4>
                                                            <p class="text-xs text-gray-500 truncate">Student</p>
                                                        </div>
                                                    </div>

                                                    <!-- Course & Teacher -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                                        <div class="flex items-center gap-2 text-gray-700">
                                                            <i class="fa-solid fa-book-quran text-indigo-600"></i>
                                                            <span class="truncate">{{ $schedule->course->title }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-gray-700">
                                                            <i class="fa-solid fa-chalkboard-teacher text-purple-600"></i>
                                                            <span class="truncate">{{ $schedule->teacher->name }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Status Badge -->
                                                    <div class="mt-2">
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold
                                                            {{ $schedule->status === 'scheduled' ? 'bg-green-100 text-green-700' : '' }}
                                                            {{ $schedule->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                                            {{ $schedule->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                            @if($schedule->status === 'scheduled')
                                                                <i class="fa-solid fa-clock"></i>
                                                            @elseif($schedule->status === 'completed')
                                                                <i class="fa-solid fa-check-circle"></i>
                                                            @else
                                                                <i class="fa-solid fa-times-circle"></i>
                                                            @endif
                                                            {{ ucfirst($schedule->status) }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Arrow Icon -->
                                                <div class="flex-shrink-0 hidden md:flex items-center">
                                                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No schedules for this day</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Enrollment List View -->
        <!-- Search and Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6 mb-4 md:mb-6 border border-gray-100">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap gap-4">
                <input type="hidden" name="view" value="list">
                <div class="flex-1 min-w-[250px]">
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Search by student or course..."
                            class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="w-48">
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-semibold">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.schedules.index', ['view' => 'list']) }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold">
                        <i class="fa-solid fa-times mr-2"></i>Clear
                    </a>
                @endif
            </form>
        </div>

        @if($enrollments->count() > 0)
            <div class="space-y-6">
                @foreach($enrollments as $enrollment)
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition">
                        <!-- Enrollment Header -->
                        <div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                            {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-800">{{ $enrollment->student->name }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $enrollment->course->title }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 ml-20">
                                        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg">
                                            <i class="fa-solid fa-calendar text-indigo-600"></i>
                                            <span>Started: {{ $enrollment->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg">
                                            <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                                            <span>{{ $enrollment->schedules->count() }} Sessions</span>
                                        </div>
                                        <span class="px-4 py-1.5 rounded-lg text-sm font-bold
                                            {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $enrollment->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <button onclick="toggleSchedules('enrollment-{{ $enrollment->id }}')" 
                                        class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-semibold text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-eye"></i>
                                        <span>View Sessions</span>
                                        <i class="fa-solid fa-chevron-down transition-transform" id="icon-enrollment-{{ $enrollment->id }}"></i>
                                    </button>
                                    @can('manage schedules')
                                    <form action="{{ route('admin.schedules.bulk-delete', $enrollment->id) }}" method="POST" 
                                        onsubmit="return confirm('Are you sure you want to delete ALL {{ $enrollment->schedules->count() }} sessions for this enrollment? This action cannot be undone!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="px-5 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 hover:shadow-lg transition font-semibold text-sm flex items-center gap-2">
                                            <i class="fa-solid fa-trash"></i>
                                            <span>Delete All</span>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <!-- Schedules Details (Hidden by default) -->
                        <div id="enrollment-{{ $enrollment->id }}" class="hidden">
                            <div class="p-6">
                                <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2 text-lg">
                                    <i class="fa-solid fa-list text-indigo-600"></i>
                                    All Sessions ({{ $enrollment->schedules->count() }})
                                </h4>
                                
                                @if($enrollment->schedules->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                                <tr>
                                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date & Time</th>
                                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Teacher</th>
                                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Duration</th>
                                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($enrollment->schedules->sortBy('starts_at') as $schedule)
                                                    <tr class="hover:bg-indigo-50/30 transition">
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                                                    <i class="fa-solid fa-calendar-day text-indigo-600"></i>
                                                                </div>
                                                                <div>
                                                                    <p class="font-semibold text-gray-800">{{ $schedule->starts_at->format('M d, Y') }}</p>
                                                                    <p class="text-sm text-gray-600">{{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white text-sm font-bold">
                                                                    {{ strtoupper(substr($schedule->teacher->name, 0, 1)) }}
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-800">{{ $schedule->teacher->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                                                {{ $schedule->getDurationInMinutes() }} min
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span class="px-4 py-2 rounded-lg text-sm font-bold
                                                                {{ $schedule->status === 'scheduled' ? 'bg-green-100 text-green-700' : '' }}
                                                                {{ $schedule->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                                                {{ $schedule->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                                {{ ucfirst($schedule->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center gap-3">
                                                                <a href="{{ route('admin.schedules.show', $schedule->id) }}" 
                                                                    class="text-indigo-600 hover:text-indigo-800 transition font-medium" title="View">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </a>
                                                                @can('manage schedules')
                                                                <a href="{{ route('admin.schedules.edit', $schedule->id) }}" 
                                                                    class="text-blue-600 hover:text-blue-800 transition font-medium" title="Edit">
                                                                    <i class="fa-solid fa-edit"></i>
                                                                </a>
                                                                @endcan
                                                                @if($schedule->zoom_link)
                                                                    <a href="{{ $schedule->zoom_link }}" target="_blank"
                                                                        class="text-purple-600 hover:text-purple-800 transition font-medium" title="Join Zoom">
                                                                        <i class="fa-solid fa-video"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-8">No schedules found for this enrollment.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $enrollments->appends(['view' => 'list'])->links() }}
            </div>
        @else
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl shadow-lg p-16 text-center border border-indigo-200">
                <div class="bg-gradient-to-br from-indigo-100 to-purple-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-calendar-xmark text-indigo-600 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Schedules Found</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first schedule.</p>
                <a href="{{ route('admin.schedules.create') }}" 
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-semibold">
                    <i class="fa-solid fa-plus mr-2"></i>Create Schedule
                </a>
            </div>
        @endif
    @endif

    <script>
        function toggleSchedules(enrollmentId) {
            const element = document.getElementById(enrollmentId);
            const icon = document.getElementById('icon-' + enrollmentId);
            
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                element.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
</x-dashboard-layout>
