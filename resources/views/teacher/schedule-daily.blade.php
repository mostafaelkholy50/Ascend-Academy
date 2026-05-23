<x-dashboard-layout title="Daily Schedule">
    @php $user = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daily Schedule</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $date->format('l, F d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('teacher.schedule.daily', ['date' => $prevDay->format('Y-m-d')]) }}"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-chevron-left mr-2"></i>Previous Day
                </a>
                <a href="{{ route('teacher.schedule.daily') }}"
                    class="px-4 py-2 bg-vibrant-green text-white rounded-lg text-sm font-semibold hover:bg-deep-blue transition">
                    Today
                </a>
                <a href="{{ route('teacher.schedule.daily', ['date' => $nextDay->format('Y-m-d')]) }}"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Next Day<i class="fa-solid fa-chevron-right ml-2"></i>
                </a>
                <a href="{{ route('teacher.schedule.index') }}"
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

        @if ($schedules->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach ($schedules as $schedule)
                    @php
                        $colors = ['green', 'blue', 'purple', 'orange', 'pink'];
                        $color = $colors[$loop->index % count($colors)];
                        $now = now();
                        $startsAt = $schedule->starts_at;
                        $endsAt = $schedule->ends_at;
                        $minutesUntil = $now->diffInMinutes($startsAt, false);
                        $isInProgress = $now->between($startsAt, $endsAt);
                        $isPast = $now->greaterThan($endsAt);
                    @endphp

                    <div class="p-6 hover:bg-gray-50 transition {{ $isInProgress ? 'bg-yellow-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <!-- Session Info -->
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Time Badge -->
                                <div class="text-center min-w-[80px]">
                                    <div class="text-2xl font-bold text-{{ $color }}-600">
                                        {{ $schedule->starts_at->format('g:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 uppercase">
                                        {{ $schedule->starts_at->format('A') }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $schedule->getDurationInMinutes() }} min
                                    </div>
                                </div>

                                <!-- Session Details -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">{{ $schedule->course->name }}
                                            </h3>
                                            <div class="flex items-center space-x-4 mt-2">
                                                <div class="flex items-center space-x-2">
                                                    @if ($schedule->student->avatar)
                                                        <img src="{{ asset('storage/' . $schedule->student->avatar) }}"
                                                            alt="{{ $schedule->student->name }}"
                                                            class="w-8 h-8 rounded-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-pink-500 flex items-center justify-center">
                                                            <span
                                                                class="text-white font-bold text-xs">{{ substr($schedule->student->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="text-sm font-medium text-gray-700">{{ $schedule->student->name }}</span>
                                                </div>
                                                <span class="text-sm text-gray-500">•</span>
                                                <span
                                                    class="text-sm text-gray-500">{{ $schedule->starts_at->format('g:i A') }}
                                                    - {{ $schedule->ends_at->format('g:i A') }}</span>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <div>
                                            @if ($schedule->status === 'completed')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-check mr-1"></i>Completed
                                                </span>
                                            @elseif($isInProgress)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium animate-pulse">
                                                    <i class="fa-solid fa-circle mr-1"></i>In Progress
                                                </span>
                                            @elseif($minutesUntil > 0 && $minutesUntil <= 60)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-clock mr-1"></i>Starting in
                                                    {{ $minutesUntil }} min
                                                </span>
                                            @elseif($isPast)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-history mr-1"></i>Past
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                                    <i class="fa-solid fa-calendar mr-1"></i>Scheduled
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Additional Info -->
                                    <div class="mt-4 flex items-center space-x-6">
                                        @if ($schedule->zoom_link)
                                            <a href="{{ $schedule->zoom_link }}" target="_blank"
                                                class="inline-flex items-center px-4 py-2 bg-vibrant-green text-white rounded-lg text-sm font-semibold hover:bg-deep-blue transition">
                                                <i class="fa-solid fa-video mr-2"></i>Join Zoom
                                            </a>
                                        @endif

                                        @if ($schedule->attendance)
                                            <div class="flex items-center space-x-4 text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-600">Teacher:</span>
                                                    @if ($schedule->attendance->teacher_present)
                                                        <span class="text-green-600 font-medium"><i
                                                                class="fa-solid fa-check-circle mr-1"></i>Present</span>
                                                    @else
                                                        <span class="text-red-600 font-medium"><i
                                                                class="fa-solid fa-times-circle mr-1"></i>Absent</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-600">Student:</span>
                                                    @if ($schedule->attendance->student_present)
                                                        <span class="text-green-600 font-medium"><i
                                                                class="fa-solid fa-check-circle mr-1"></i>Present</span>
                                                    @else
                                                        <span class="text-red-600 font-medium"><i
                                                                class="fa-solid fa-times-circle mr-1"></i>Absent</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <button
                                                onclick="openAttendanceModal({
                                                    id: {{ $schedule->id }},
                                                    student: {
                                                        id: {{ $schedule->student->id }},
                                                        name: '{{ addslashes($schedule->student->name) }}'
                                                    },
                                                    course: {
                                                        name: '{{ addslashes($schedule->course->name) }}'
                                                    },
                                                    starts_at_formatted: '{{ $schedule->starts_at->format('g:i A') }}',
                                                    attendance: {{ $schedule->attendance ? json_encode($schedule->attendance) : 'null' }}
                                                })"
                                                class="inline-flex items-center px-4 py-2 bg-vibrant-green text-white rounded-lg text-sm font-semibold hover:bg-deep-blue transition">
                                                <i class="fa-solid fa-clipboard-check mr-2"></i>Mark Attendance
                                            </button>
                                            <button onclick="notifyWaiting({{ $schedule->id }})"
                                                id="waitingBtn-{{ $schedule->id }}"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-yellow-500 text-yellow-600 rounded-lg text-sm font-semibold hover:bg-yellow-50 transition">
                                                <i class="fa-solid fa-clock mr-2"></i>I am waiting
                                            </button>
                                        @endif

                                        <button
                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                            <i class="fa-solid fa-file-alt mr-2"></i>Add Report
                                        </button>
                                    </div>

                                    @if ($schedule->notes)
                                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm text-gray-600"><strong>Notes:</strong>
                                                {{ $schedule->notes }}</p>
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

    <!-- Include Attendance Modal -->
    @include('teacher.partials.attendance-modal')
</x-dashboard-layout>
