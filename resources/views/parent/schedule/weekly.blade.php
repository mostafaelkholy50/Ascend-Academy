<x-dashboard-layout title="Weekly Schedule">
    @php $parent = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Weekly Schedule</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Child Filter -->
                <form method="GET" class="flex items-center gap-2">
                    <select name="child_id" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all" {{ $selectedChildId == 'all' ? 'selected' : '' }}>All Children</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ $selectedChildId == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                        @endforeach
                    </select>
                </form>

                <!-- Week Navigation -->
                <a href="{{ route('parent.schedule.weekly', ['week_start' => $weekStart->copy()->subWeek()->format('Y-m-d'), 'child_id' => $selectedChildId]) }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-chevron-left mr-2"></i>Previous
                </a>
                <a href="{{ route('parent.schedule.weekly', ['child_id' => $selectedChildId]) }}" 
                   class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition">
                    This Week
                </a>
                <a href="{{ route('parent.schedule.weekly', ['week_start' => $weekStart->copy()->addWeek()->format('Y-m-d'), 'child_id' => $selectedChildId]) }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Next<i class="fa-solid fa-chevron-right ml-2"></i>
                </a>
                <a href="{{ route('parent.schedule.daily', ['child_id' => $selectedChildId]) }}" 
                   class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition">
                    <i class="fa-solid fa-calendar-day mr-2"></i>Daily View
                </a>
            </div>
        </div>
    </div>

    <!-- Weekly Calendar Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
        @foreach($schedulesByDay as $dayKey => $dayData)
            @php
                $date = $dayData['date'];
                $schedules = $dayData['schedules'];
                $isToday = $date->isToday();
            @endphp
            
            <div class="bg-white rounded-2xl shadow-md overflow-hidden {{ $isToday ? 'ring-2 ring-indigo-500' : '' }}">
                <!-- Day Header -->
                <div class="p-4 {{ $isToday ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gray-50' }} border-b">
                    <div class="text-center">
                        <p class="text-xs font-semibold uppercase {{ $isToday ? 'text-white' : 'text-gray-500' }}">
                            {{ $date->format('D') }}
                        </p>
                        <p class="text-2xl font-bold {{ $isToday ? 'text-white' : 'text-gray-800' }} mt-1">
                            {{ $date->format('d') }}
                        </p>
                        @if($isToday)
                            <span class="inline-block px-2 py-0.5 bg-white/20 rounded-full text-xs font-medium mt-1">Today</span>
                        @endif
                    </div>
                </div>

                <!-- Sessions List -->
                <div class="p-3 space-y-2 min-h-[200px]">
                    @if($schedules->count() > 0)
                        @foreach($schedules as $schedule)
                            @php
                                $colors = ['green', 'blue', 'purple', 'orange', 'pink'];
                                $color = $colors[$loop->index % count($colors)];
                            @endphp
                            
                            <div class="p-3 bg-{{ $color }}-50 border border-{{ $color }}-100 rounded-xl hover:shadow-md transition">
                                <div class="flex items-start space-x-2">
                                    <div class="w-8 h-8 bg-{{ $color }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-book-quran text-{{ $color }}-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-800 text-xs truncate">{{ $schedule->course->title ?? 'Session' }}</h4>
                                        <p class="text-xs text-gray-600 truncate">
                                            <i class="fa-solid fa-child mr-1"></i>{{ $schedule->student->name }}
                                        </p>
                                        <p class="text-xs text-{{ $color }}-600 font-medium mt-1">
                                            {{ $schedule->starts_at->format('g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                
                                @if($schedule->status === 'completed')
                                    <div class="mt-2 pt-2 border-t border-{{ $color }}-200">
                                        <span class="inline-flex items-center text-xs text-green-600 font-medium">
                                            <i class="fa-solid fa-check-circle mr-1"></i>Completed
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center justify-center h-32 text-gray-400">
                            <div class="text-center">
                                <i class="fa-solid fa-calendar-xmark text-2xl mb-2"></i>
                                <p class="text-xs">No sessions</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Day Footer -->
                <div class="px-4 py-2 bg-gray-50 border-t">
                    <a href="{{ route('parent.schedule.daily', ['date' => $date->format('Y-m-d'), 'child_id' => $selectedChildId]) }}" 
                       class="text-xs text-indigo-600 hover:text-purple-600 font-medium flex items-center justify-center">
                        View Day
                        <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Week Summary -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $allSchedules = collect($schedulesByDay)->flatMap(fn($day) => $day['schedules']);
            $totalSessions = $allSchedules->count();
            $completedSessions = $allSchedules->where('status', 'completed')->count();
            $uniqueChildren = $allSchedules->pluck('student_id')->unique()->count();
        @endphp
        
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-calendar-check text-2xl opacity-80"></i>
            </div>
            <p class="text-white/80 text-sm font-medium">Total Sessions</p>
            <p class="text-3xl font-bold mt-1">{{ $totalSessions }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-check-circle text-2xl opacity-80"></i>
            </div>
            <p class="text-white/80 text-sm font-medium">Completed</p>
            <p class="text-3xl font-bold mt-1">{{ $completedSessions }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-child text-2xl opacity-80"></i>
            </div>
            <p class="text-white/80 text-sm font-medium">Children</p>
            <p class="text-3xl font-bold mt-1">{{ $uniqueChildren }}</p>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-hourglass-half text-2xl opacity-80"></i>
            </div>
            <p class="text-white/80 text-sm font-medium">Remaining</p>
            <p class="text-3xl font-bold mt-1">{{ $totalSessions - $completedSessions }}</p>
        </div>
    </div>
</x-dashboard-layout>
