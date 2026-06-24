<x-dashboard-layout title="Schedules">
    <!-- Page Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Schedule Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage all course schedules and sessions</p>
            </div>
            @can('manage schedules')
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('printScheduleModal').classList.remove('hidden')" 
                    class="inline-flex items-center justify-center px-4 md:px-6 py-2.5 md:py-3 bg-white text-indigo-600 border border-indigo-200 rounded-xl font-semibold hover:bg-indigo-50 hover:shadow-lg transition text-sm md:text-base">
                    <i class="fa-solid fa-print mr-2"></i><span>Print Schedule</span>
                </button>
                <a href="{{ route('admin.schedules.create') }}" 
                    class="inline-flex items-center justify-center px-4 md:px-6 py-2.5 md:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition text-sm md:text-base">
                    <i class="fa-solid fa-plus mr-2"></i><span>Create New Schedule</span>
                </a>
            </div>
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

            <!-- Google Calendar Style Grid -->
            <div class="overflow-x-auto relative scroll-smooth border-t border-gray-200" id="calendar-container">
                <div class="min-w-[900px] lg:w-full flex flex-col relative select-none">
                    
                    <!-- Header: Days -->
                    <div class="flex border-b border-gray-200 sticky top-0 bg-white/95 backdrop-blur-md z-30 shadow-sm">
                        <!-- Top Left Empty Cell for Time Column -->
                        <div class="w-20 flex-shrink-0 border-r border-gray-100 flex items-end justify-center pb-2">
                            <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">GMT+3</span>
                        </div>
                        
                        <!-- Days Columns -->
                        @foreach ($weekDays as $dayData)
                            @php $isToday = $dayData['date']->isToday(); @endphp
                            <div class="flex-1 py-3 px-2 text-center border-r border-gray-100 {{ $isToday ? 'bg-indigo-50' : '' }} group transition-colors">
                                <div class="text-xs font-semibold uppercase tracking-wider {{ $isToday ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">
                                    {{ $dayData['date']->format('D') }}
                                </div>
                                <div class="mt-1 flex justify-center">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-full text-xl font-bold transition-all {{ $isToday ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 scale-110' : 'text-gray-700 group-hover:bg-gray-100' }}">
                                        {{ $dayData['date']->format('d') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Grid Body -->
                    <div class="flex relative h-[2160px] bg-gray-50/30">
                        
                        <!-- Time Labels Column -->
                        <div class="w-20 flex-shrink-0 border-r border-gray-100 bg-white z-20 relative">
                            @for ($i = 0; $i < 24; $i++)
                                <div class="h-[90px] border-b border-gray-100/0 relative">
                                    <div class="absolute -top-3 right-3 text-[11px] font-medium text-gray-400">
                                        {{ $i == 0 ? '12 AM' : ($i < 12 ? $i . ' AM' : ($i == 12 ? '12 PM' : ($i - 12) . ' PM')) }}
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <!-- Days Grid & Appointments -->
                        <div class="flex flex-1 relative bg-white">
                            
                            <!-- Horizontal Grid Lines -->
                            <div class="absolute inset-0 pointer-events-none z-0">
                                @for ($i = 0; $i < 24; $i++)
                                    <div class="h-[90px] border-b border-gray-100 w-full"></div>
                                @endfor
                            </div>

                            <!-- Day Columns -->
                            @foreach ($weekDays as $dayData)
                                <div class="flex-1 border-r border-gray-100 relative z-10">
                                    
                                    <!-- Render Appointments for this Day -->
                                    @foreach ($dayData['schedules'] as $schedule)
                                        @php
                                            $startHour = (int) $schedule->starts_at->format('G');
                                            $startMinute = (int) $schedule->starts_at->format('i');
                                            $durationMinutes = $schedule->getDurationInMinutes();
                                            
                                            // 1.5px per minute
                                            $top = ($startHour * 60 + $startMinute) * 1.5;
                                            $height = $durationMinutes * 1.5;
                                            
                                            $now = now();
                                            $isPast = $now->greaterThan($schedule->ends_at);
                                            $isInProgress = $now->between($schedule->starts_at, $schedule->ends_at);
                                            
                                            // Determine Status & Styling
                                            $statusClass = 'bg-blue-50 border-blue-200 text-blue-800 shadow-blue-100/50';
                                            $statusText = 'Not Yet';
                                            $iconClass = 'fa-calendar';
                                            $statusColor = 'blue';

                                            if ($schedule->status === 'completed') {
                                                $statusClass = 'bg-green-50 border-green-200 text-green-800 shadow-green-100/50';
                                                $statusText = 'Completed';
                                                $iconClass = 'fa-check-circle';
                                                $statusColor = 'green';
                                            } elseif ($schedule->attendance) {
                                                if ($schedule->attendance->student_present && $schedule->attendance->teacher_present) {
                                                    $statusClass = 'bg-emerald-50 border-emerald-200 text-emerald-800 shadow-emerald-100/50';
                                                    $statusText = 'Attended';
                                                    $iconClass = 'fa-check-double';
                                                    $statusColor = 'emerald';
                                                } elseif (!$schedule->attendance->teacher_present) {
                                                    $statusClass = 'bg-red-50 border-red-200 text-red-800 shadow-red-100/50';
                                                    $statusText = 'Teacher Absent';
                                                    $iconClass = 'fa-times-circle';
                                                    $statusColor = 'red';
                                                } elseif (!$schedule->attendance->student_present) {
                                                    $statusClass = 'bg-orange-50 border-orange-200 text-orange-800 shadow-orange-100/50';
                                                    $statusText = 'Student Absent';
                                                    $iconClass = 'fa-user-slash';
                                                    $statusColor = 'orange';
                                                }
                                            } elseif ($isPast) {
                                                $statusClass = 'bg-gray-100 border-gray-200 text-gray-600 shadow-none';
                                                $statusText = 'Past';
                                                $iconClass = 'fa-history';
                                                $statusColor = 'gray';
                                            } elseif ($isInProgress) {
                                                $statusClass = 'bg-yellow-50 border-yellow-300 text-yellow-900 shadow-yellow-200/50 ring-2 ring-yellow-400 ring-offset-1';
                                                $statusText = 'In Progress';
                                                $iconClass = 'fa-spinner fa-spin';
                                                $statusColor = 'yellow';
                                            }
                                        @endphp
                                        
                                        <!-- Base Appointment Card -->
                                        <div class="absolute left-[2px] right-[4px] rounded-lg border shadow-sm transition-all duration-200 hover:z-50 cursor-pointer overflow-hidden flex flex-col {{ $statusClass }} {{ 'border-'.$statusColor.'-300' }}"
                                             style="top: {{ $top }}px; min-height: {{ max($height, 50) }}px; z-index: 10;"
                                             onclick="window.location='{{ route('admin.schedules.show', $schedule->id) }}'">
                                             
                                            <div class="p-1.5 flex flex-col gap-1 h-full bg-{{$statusColor}}-50/90 relative">
                                                <!-- Header: Time and Status -->
                                                <div class="flex flex-wrap justify-between items-start gap-1">
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] font-black text-{{$statusColor}}-800 tracking-tight leading-none whitespace-nowrap">
                                                            {{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}
                                                        </span>
                                                        @if($isInProgress)
                                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse mt-0.5"></span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Body: Student and Teacher -->
                                                <div class="flex-1 mt-0.5">
                                                    <!-- Student Info -->
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <i class="fa-solid fa-user-graduate text-[9px] text-gray-500"></i>
                                                        <h4 class="text-[10px] font-bold text-gray-900 leading-tight truncate" title="Student: {{ $schedule->student->name }}">{{ $schedule->student->name }}</h4>
                                                    </div>
                                                    <!-- Teacher Info -->
                                                    <div class="flex items-center gap-1">
                                                        <i class="fa-solid fa-chalkboard-teacher text-[9px] text-gray-500"></i>
                                                        <p class="text-[9px] font-medium text-gray-700 leading-tight truncate" title="Teacher: {{ $schedule->teacher->name }}">{{ $schedule->teacher->name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
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
                <div class="w-full md:w-48">
                    <select name="teacher_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <select name="student_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48">
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
                                            <span>Started: {{ $enrollment->start_date ? $enrollment->start_date->format('M d, Y') : 'N/A' }}</span>
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
                                    <a href="{{ route('admin.schedules.edit-pattern', $enrollment->id) }}" 
                                        class="px-5 py-3 bg-white text-indigo-600 border border-indigo-200 rounded-xl hover:bg-indigo-50 hover:shadow-lg transition font-semibold text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-alt"></i>
                                        <span>Edit Pattern</span>
                                    </a>
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
                                                                    <p class="font-semibold text-gray-800">{{ $schedule->getStartsAtInTimezone(auth()->user()->getUserTimezone())->format('l, M d, Y') }}</p>
                                                                    <p class="text-sm text-gray-600">{{ $schedule->getStartsAtInTimezone(auth()->user()->getUserTimezone())->format('g:i A') }} - {{ $schedule->getEndsAtInTimezone(auth()->user()->getUserTimezone())->format('g:i A') }} {{ $schedule->getStartsAtInTimezone(auth()->user()->getUserTimezone())->format('T') }}</p>
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
    <!-- Print Schedule Modal -->
    <div id="printScheduleModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-print text-indigo-600"></i>
                    Print Teacher Schedule
                </h3>
                <button type="button" onclick="document.getElementById('printScheduleModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <form action="{{ route('scheduler.schedules.print') }}" method="GET" target="_blank" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Teacher <span class="text-red-500">*</span></label>
                        <select name="teacher_id" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Choose a teacher...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Month <span class="text-red-500">*</span></label>
                        <input type="month" name="month" required value="{{ date('Y-m') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="document.getElementById('printScheduleModal').classList.add('hidden')" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold">
                        Cancel
                    </button>
                    <button type="submit" onclick="document.getElementById('printScheduleModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i>
                        Print Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
