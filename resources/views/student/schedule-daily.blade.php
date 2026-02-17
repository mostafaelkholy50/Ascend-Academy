<x-dashboard-layout title="Daily Schedule">
    @php 
        $user = auth()->user();
        $totalSessions = $schedules->count();
        $completedSessions = $schedules->where('status', 'completed')->count();
        $totalHours = $schedules->sum(fn($s) => $s->getDurationInHours());
    @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daily Schedule</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $date->format('l, F d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('student.schedule.daily', ['date' => $prevDay->format('Y-m-d')]) }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-chevron-left mr-2"></i>Previous Day
                </a>
                <a href="{{ route('student.schedule.daily') }}" 
                   class="px-4 py-2 bg-vibrant-green text-white rounded-lg text-sm font-semibold hover:bg-deep-blue transition">
                    Today
                </a>
                <a href="{{ route('student.schedule.daily', ['date' => $nextDay->format('Y-m-d')]) }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Next Day<i class="fa-solid fa-chevron-right ml-2"></i>
                </a>
                <a href="{{ route('student.schedule.weekly') }}" 
                   class="px-4 py-2 bg-deep-blue text-white rounded-lg text-sm font-semibold hover:bg-vibrant-green transition">
                    <i class="fa-solid fa-calendar-week mr-2"></i>Weekly View
                </a>
            </div>
        </div>
    </div>

    <!-- Day Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-dashboard.stat-card icon="fa-calendar-check" title="Total Sessions" :value="$totalSessions . ' Sessions'" color="blue" />
        <x-dashboard.stat-card icon="fa-check-circle" title="Completed" :value="$completedSessions . ' Sessions'" color="green" />
        <x-dashboard.stat-card icon="fa-clock" title="Total Hours" :value="number_format($totalHours, 1) . ' hrs'" color="purple" />
    </div>

    <!-- Sessions Timeline -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Sessions Timeline</h2>
            <p class="text-sm text-gray-500 mt-1">All sessions scheduled for this day</p>
        </div>

        @if($schedules->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($schedules as $schedule)
                    @php
                        $colors = ['green', 'blue', 'purple', 'orange', 'pink'];
                        $color = $colors[$loop->index % count($colors)];
                        $now = now();
                        // Convert times to user timezone
                        $startsAt = $schedule->getStartsAtInTimezone($userTimezone);
                        $endsAt = $schedule->getEndsAtInTimezone($userTimezone);
                        $startsAtOriginal = $schedule->starts_at;
                        $endsAtOriginal = $schedule->ends_at;
                        $minutesUntil = $now->diffInMinutes($startsAtOriginal, false);
                        $isInProgress = $now->between($startsAtOriginal, $endsAtOriginal);
                        $isPast = $now->greaterThan($endsAtOriginal);
                        // Get timezone abbreviation
                        $timezoneAbbr = $startsAt->format('T');
                    @endphp
                    
                    <div class="p-6 hover:bg-gray-50 transition {{ $isInProgress ? 'bg-yellow-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <!-- Session Info -->
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Time Badge -->
                                <div class="text-center min-w-[80px]">
                                    <div class="text-2xl font-bold text-{{ $color }}-600">
                                        {{ $startsAt->format('g:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 uppercase">
                                        {{ $startsAt->format('A') }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $schedule->getDurationInMinutes() }} min
                                    </div>
                                    <div class="text-xs text-blue-600 font-medium mt-1">
                                        {{ $timezoneAbbr }}
                                    </div>
                                </div>

                                <!-- Session Details -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">{{ $schedule->course->title }}</h3>
                                            <div class="flex items-center space-x-4 mt-2">
                                                <div class="flex items-center space-x-2">
                                                    @if($schedule->teacher->avatar)
                                                        <img src="{{ asset('storage/' . $schedule->teacher->avatar) }}" 
                                                             alt="{{ $schedule->teacher->name }}" 
                                                             class="w-8 h-8 rounded-full object-cover">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-pink-500 flex items-center justify-center">
                                                            <span class="text-white font-bold text-xs">{{ substr($schedule->teacher->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    <span class="text-sm font-medium text-gray-700">{{ $schedule->teacher->name }}</span>
                                                </div>
                                                <span class="text-sm text-gray-500">•</span>
                                                <span class="text-sm text-gray-500">{{ $startsAt->format('g:i A') }} - {{ $endsAt->format('g:i A') }} {{ $timezoneAbbr }}</span>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <div>
                                            @if($schedule->status === 'completed')
                                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-check mr-1"></i>Completed
                                                </span>
                                            @elseif($isInProgress)
                                                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium animate-pulse">
                                                    <i class="fa-solid fa-circle mr-1"></i>In Progress
                                                </span>
                                            @elseif($minutesUntil > 0 && $minutesUntil <= 60)
                                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-clock mr-1"></i>Starting in {{ $minutesUntil }} min
                                                </span>
                                            @elseif($isPast)
                                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-history mr-1"></i>Past
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-calendar mr-1"></i>Scheduled
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Additional Info -->
                                    <div class="mt-4 flex items-center space-x-6">
                                        @if($schedule->zoom_link)
                                            <a href="{{ $schedule->zoom_link }}" target="_blank" 
                                               class="inline-flex items-center px-4 py-2 bg-vibrant-green text-white rounded-lg text-sm font-semibold hover:bg-deep-blue transition">
                                                <i class="fa-solid fa-video mr-2"></i>Join Zoom
                                            </a>
                                        @endif
                                    </div>

                                    @if($schedule->notes)
                                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm text-gray-600"><strong>Notes:</strong> {{ $schedule->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fa-solid fa-calendar-xmark text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Sessions Scheduled</h3>
                <p class="text-gray-500">There are no sessions scheduled for this day.</p>
            </div>
        @endif
    </div>
</x-dashboard-layout>
