<x-dashboard-layout title="Weekly Schedule">
    @php $user = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Weekly Schedule</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('teacher.schedule.index', ['week' => $prevWeek->format('Y-m-d')]) }}"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-chevron-left mr-2"></i>Previous
                </a>
                <a href="{{ route('teacher.schedule.index') }}"
                    class="px-4 py-2 bg-vibrant-green text-white rounded-lg text-sm font-semibold hover:bg-deep-blue transition">
                    This Week
                </a>
                <a href="{{ route('teacher.schedule.index', ['week' => $nextWeek->format('Y-m-d')]) }}"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Next<i class="fa-solid fa-chevron-right ml-2"></i>
                </a>
                <a href="{{ route('teacher.schedule.daily') }}"
                    class="px-4 py-2 bg-deep-blue text-white rounded-lg text-sm font-semibold hover:bg-vibrant-green transition">
                    <i class="fa-solid fa-calendar-day mr-2"></i>Daily View
                </a>
            </div>
        </div>
    </div>

    <!-- Weekly Calendar Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
        @foreach ($schedulesByDay as $dayData)
            @php
                $date = $dayData['date'];
                $schedules = $dayData['schedules'];
                $isToday = $date->isToday();
            @endphp

            <div
                class="bg-white rounded-2xl shadow-sm overflow-hidden {{ $isToday ? 'ring-2 ring-vibrant-green' : '' }}">
                <!-- Day Header -->
                <div class="p-4 {{ $isToday ? 'bg-vibrant-green text-white' : 'bg-gray-50' }} border-b">
                    <div class="text-center">
                        <p class="text-xs font-semibold uppercase {{ $isToday ? 'text-white' : 'text-gray-500' }}">
                            {{ $date->format('D') }}
                        </p>
                        <p class="text-2xl font-bold {{ $isToday ? 'text-white' : 'text-gray-800' }} mt-1">
                            {{ $date->format('d') }}
                        </p>
                        @if ($isToday)
                            <span
                                class="inline-block px-2 py-0.5 bg-white/20 rounded-full text-xs font-medium mt-1">Today</span>
                        @endif
                    </div>
                </div>

                <!-- Sessions List -->
                <div class="p-3 space-y-2 min-h-[200px]">
                    @if ($schedules->count() > 0)
                        @foreach ($schedules as $schedule)
                            @php
                                $colors = ['green', 'blue', 'purple', 'orange', 'pink'];
                                $color = $colors[$loop->index % count($colors)];
                            @endphp

                            <div class="p-3 bg-{{ $color }}-50 border border-{{ $color }}-100 rounded-xl hover:shadow-md transition cursor-pointer"
                                onclick="window.location='{{ route('teacher.schedule.daily', ['date' => $date->format('Y-m-d')]) }}'">
                                <div class="flex items-start space-x-2">
                                    <div
                                        class="w-8 h-8 bg-{{ $color }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-book-quran text-{{ $color }}-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-800 text-xs truncate">
                                            {{ $schedule->course->name }}</h4>
                                        <p class="text-xs text-gray-600 truncate">{{ $schedule->student->name }}</p>
                                        <p class="text-xs text-{{ $color }}-600 font-medium mt-1">
                                            {{ $schedule->starts_at->format('g:i A') }}
                                        </p>
                                    </div>
                                </div>

                                @if ($schedule->attendance)
                                    <div class="mt-2 pt-2 border-t border-{{ $color }}-200 space-y-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600">Teacher:</span>
                                            @if ($schedule->attendance->teacher_present)
                                                <span class="text-green-600 font-medium"><i
                                                        class="fa-solid fa-check-circle mr-1"></i>Present</span>
                                            @else
                                                <span class="text-red-600 font-medium"><i
                                                        class="fa-solid fa-times-circle mr-1"></i>Absent</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
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
                                    <div class="mt-2 pt-2 border-t border-{{ $color }}-200 space-y-1">
                                        <button
                                            onclick="event.stopPropagation(); openAttendanceModal({
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
                                            class="w-full inline-flex items-center justify-center px-2 py-1.5 bg-vibrant-green text-white rounded-lg text-xs font-semibold hover:bg-deep-blue transition">
                                            <i class="fa-solid fa-clipboard-check mr-1"></i>Mark Attendance
                                        </button>
                                        <button onclick="event.stopPropagation(); notifyWaiting({{ $schedule->id }})"
                                            id="waitingBtn-{{ $schedule->id }}"
                                            class="w-full inline-flex items-center justify-center px-2 py-1 bg-white border border-yellow-500 text-yellow-600 rounded-lg text-[10px] font-semibold hover:bg-yellow-50 transition">
                                            <i class="fa-solid fa-clock mr-1"></i>I am waiting
                                        </button>
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
                    <a href="{{ route('teacher.schedule.daily', ['date' => $date->format('Y-m-d')]) }}"
                        class="text-xs text-vibrant-green hover:text-deep-blue font-medium flex items-center justify-center">
                        View Day
                        <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Week Summary -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $allSchedules = collect($schedulesByDay)->flatMap(fn($day) => $day['schedules']);
            $totalSessions = $allSchedules->count();
            $completedSessions = $allSchedules->where('status', 'completed')->count();
            $totalHours = $allSchedules->sum(fn($s) => $s->getDurationInHours());
            $uniqueStudents = $allSchedules->pluck('student_id')->unique()->count();
        @endphp

        <x-dashboard.stat-card icon="fa-calendar-check" title="Total Sessions" :value="$totalSessions . ' Sessions'" color="blue" />
        <x-dashboard.stat-card icon="fa-check-circle" title="Completed" :value="$completedSessions . ' Sessions'" color="green" />
        <x-dashboard.stat-card icon="fa-clock" title="Total Hours" :value="number_format($totalHours, 1) . ' hrs'" color="purple" />
        <x-dashboard.stat-card icon="fa-users" title="Students" :value="$uniqueStudents . ' Students'" color="orange" />
    </div>

    <!-- Include Attendance Modal -->
    @include('teacher.partials.attendance-modal')
</x-dashboard-layout>
