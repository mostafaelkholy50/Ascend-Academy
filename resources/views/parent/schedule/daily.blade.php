<x-dashboard-layout title="Daily Schedule">
    @php $parent = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1
                    class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    Daily Schedule</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $selectedDate->format('l, F d, Y') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Child Filter -->
                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                    <select name="child_id" onchange="this.form.submit()"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="all" {{ $selectedChildId == 'all' ? 'selected' : '' }}>All Children</option>
                        @foreach ($children as $child)
                            <option value="{{ $child->id }}" {{ $selectedChildId == $child->id ? 'selected' : '' }}>
                                {{ $child->name }}</option>
                        @endforeach
                    </select>
                </form>

                <!-- Date Navigation -->
                <a href="{{ route('parent.schedule.daily', ['date' => $selectedDate->copy()->subDay()->format('Y-m-d'), 'child_id' => $selectedChildId]) }}"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-chevron-left mr-2"></i>Previous
                </a>
                <a href="{{ route('parent.schedule.daily', ['child_id' => $selectedChildId]) }}"
                    class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition">
                    Today
                </a>
                <a href="{{ route('parent.schedule.daily', ['date' => $selectedDate->copy()->addDay()->format('Y-m-d'), 'child_id' => $selectedChildId]) }}"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Next<i class="fa-solid fa-chevron-right ml-2"></i>
                </a>
                <a href="{{ route('parent.schedule.weekly', ['child_id' => $selectedChildId]) }}"
                    class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition">
                    <i class="fa-solid fa-calendar-week mr-2"></i>Weekly View
                </a>
            </div>
        </div>
    </div>

    <!-- Schedule List -->
    @if ($schedules->count() > 0)
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
            <div class="divide-y divide-gray-100">
                @foreach ($schedules as $schedule)
                    @php
                        // Convert times to user timezone
                        $startsAt = $schedule->getStartsAtInTimezone($userTimezone);
                        $endsAt = $schedule->getEndsAtInTimezone($userTimezone);
                        $timezoneAbbr = $startsAt->format('T');
                    @endphp
                    <div
                        class="p-6 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-grow">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg text-white">
                                    <i class="fa-solid fa-book-quran text-2xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-bold text-gray-800 text-lg">
                                            {{ $schedule->course->title ?? 'Session' }}</h4>
                                        <span
                                            class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-bold">
                                            {{ $schedule->student->name }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                        <span><i
                                                class="fa-solid fa-user-tie mr-2 text-green-600"></i>{{ $schedule->teacher->name }}</span>
                                        <span><i
                                                class="fa-solid fa-clock mr-2 text-green-600"></i>{{ $startsAt->format('g:i A') }}
                                            - {{ $endsAt->format('g:i A') }} <span
                                                class="text-blue-600 font-medium">{{ $timezoneAbbr }}</span></span>
                                        <span><i
                                                class="fa-solid fa-hourglass-half mr-2 text-green-600"></i>{{ $schedule->getDurationInMinutes() }}
                                            minutes</span>
                                    </div>
                                    @if ($schedule->zoom_link)
                                        <div class="mt-2">
                                            <a href="{{ $schedule->zoom_link }}" target="_blank"
                                                class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                                <i class="fa-solid fa-video mr-1"></i>Zoom Link Available
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span
                                    class="px-4 py-2 rounded-xl text-sm font-bold
                                    @if ($schedule->status == 'completed') bg-green-100 text-green-700
                                    @elseif($schedule->status == 'scheduled') bg-blue-100 text-blue-700
                                    @elseif($schedule->status == 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    <i
                                        class="fa-solid 
                                        @if ($schedule->status == 'completed') fa-check-circle
                                        @elseif($schedule->status == 'scheduled') fa-clock
                                        @elseif($schedule->status == 'cancelled') fa-times-circle
                                        @else fa-circle @endif mr-1"></i>
                                    {{ ucfirst($schedule->status) }}
                                </span>

                                @if ($schedule->starts_at->isFuture())
                                    @php $minutesUntil = $schedule->starts_at->diffInMinutes(now()); @endphp
                                    @if ($minutesUntil < 60)
                                        <span
                                            class="px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 rounded-lg text-xs font-bold">
                                            <i class="fa-solid fa-clock mr-1"></i>In {{ $minutesUntil }} min
                                        </span>
                                    @endif
                                @elseif($schedule->starts_at->isPast() && $schedule->ends_at->isFuture())
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 rounded-lg text-xs font-bold animate-pulse">
                                        <i class="fa-solid fa-circle-dot mr-1"></i>In Progress
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Attendance Info -->
                        @if ($schedule->attendance)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-4 text-sm">
                                    @php $attendance = $schedule->attendance; @endphp
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-user text-gray-500"></i>
                                        <span class="font-medium">Student:</span>
                                        <span
                                            class="px-2 py-1 rounded-lg text-xs font-bold {{ $attendance->student_present ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $attendance->student_present ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-user-tie text-gray-500"></i>
                                        <span class="font-medium">Teacher:</span>
                                        <span
                                            class="px-2 py-1 rounded-lg text-xs font-bold {{ $attendance->teacher_present ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $attendance->teacher_present ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                    @if ($attendance->remark)
                                        <span class="flex items-center gap-2 text-gray-600">
                                            <i class="fa-solid fa-comment text-gray-500"></i>
                                            <span>{{ $attendance->remark }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Day Summary -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $totalSessions = $schedules->count();
                $completedSessions = $schedules->where('status', 'completed')->count();
                $uniqueChildren = $schedules->pluck('student_id')->unique()->count();
            @endphp

            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fa-solid fa-calendar-check text-2xl opacity-80 mb-2"></i>
                <p class="text-white/80 text-sm font-medium">Total Sessions</p>
                <p class="text-3xl font-bold mt-1">{{ $totalSessions }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fa-solid fa-check-circle text-2xl opacity-80 mb-2"></i>
                <p class="text-white/80 text-sm font-medium">Completed</p>
                <p class="text-3xl font-bold mt-1">{{ $completedSessions }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fa-solid fa-child text-2xl opacity-80 mb-2"></i>
                <p class="text-white/80 text-sm font-medium">Children</p>
                <p class="text-3xl font-bold mt-1">{{ $uniqueChildren }}</p>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fa-solid fa-hourglass-half text-2xl opacity-80 mb-2"></i>
                <p class="text-white/80 text-sm font-medium">Remaining</p>
                <p class="text-3xl font-bold mt-1">{{ $schedules->where('status', 'scheduled')->count() }}</p>
            </div>
        </div>
    @else
        <div
            class="bg-gradient-to-br from-green-50 to-emerald-50 p-16 rounded-3xl shadow-md text-center border border-green-200">
            <div
                class="bg-gradient-to-br from-green-100 to-emerald-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-calendar-times text-green-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Classes Scheduled</h3>
            <p class="text-gray-600">There are no classes scheduled for this date.</p>
        </div>
    @endif
</x-dashboard-layout>
