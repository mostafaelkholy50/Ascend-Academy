<x-dashboard-layout title="Weekly Schedule">
    @php $user = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Weekly Schedule</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">{{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse bg-white p-1.5 rounded-xl shadow-sm border border-gray-100">
                <a href="{{ route('teacher.schedule.index', ['week' => $prevWeek->format('Y-m-d')]) }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition flex items-center">
                    <i class="fa-solid fa-chevron-left mr-1 rtl:ml-1 rtl:mr-0 rtl:rotate-180"></i> Previous
                </a>
                <a href="{{ route('teacher.schedule.index') }}"
                    class="px-4 py-2 bg-vibrant-green/10 text-vibrant-green rounded-lg text-sm font-bold hover:bg-vibrant-green hover:text-white transition shadow-sm">
                    This Week
                </a>
                <a href="{{ route('teacher.schedule.index', ['week' => $nextWeek->format('Y-m-d')]) }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition flex items-center">
                    Next <i class="fa-solid fa-chevron-right ml-1 rtl:mr-1 rtl:ml-0 rtl:rotate-180"></i>
                </a>
                <div class="w-px h-6 bg-gray-200 mx-2"></div>
                <a href="{{ route('teacher.schedule.daily') }}"
                    class="px-4 py-2 bg-deep-blue text-white rounded-lg text-sm font-semibold hover:bg-opacity-90 transition shadow-sm flex items-center">
                    <i class="fa-solid fa-calendar-day mr-2"></i> Daily View
                </a>
            </div>
        </div>
    </div>

    <!-- Google Calendar Style Grid -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[calc(100vh-12rem)] min-h-[600px]">
        
        <!-- Scrollable Container -->
        <div class="overflow-auto relative scroll-smooth flex-1" id="calendar-container">
            <div class="min-w-[900px] lg:w-full flex flex-col relative select-none">
                
                <!-- Header: Days -->
                <div class="flex border-b border-gray-200 sticky top-0 bg-white/95 backdrop-blur-md z-40 shadow-sm">
                    <!-- Top Left Empty Cell for Time Column -->
                    <div class="w-20 flex-shrink-0 border-r border-gray-100 flex items-end justify-center pb-2 sticky left-0 bg-white/95 z-50">
                        <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">GMT+3</span>
                    </div>
                    
                    <!-- Days Columns -->
                    @foreach ($schedulesByDay as $dayData)
                        @php $isToday = $dayData['date']->isToday(); @endphp
                        <div class="flex-1 py-3 px-2 text-center border-r border-gray-100 {{ $isToday ? 'bg-vibrant-green/5' : '' }} group transition-colors min-w-[150px]">
                            <div class="text-xs font-semibold uppercase tracking-wider {{ $isToday ? 'text-vibrant-green' : 'text-gray-500 group-hover:text-gray-700' }}">
                                {{ $dayData['date']->format('D') }}
                            </div>
                            <div class="mt-1 flex justify-center">
                                <div class="w-10 h-10 flex items-center justify-center rounded-full text-xl font-bold transition-all {{ $isToday ? 'bg-vibrant-green text-white shadow-md shadow-vibrant-green/30 scale-110' : 'text-gray-700 group-hover:bg-gray-100' }}">
                                    {{ $dayData['date']->format('d') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Grid Body -->
                <div class="flex flex-col relative bg-gray-50/30">
                    
                    @for ($hour = 0; $hour < 24; $hour++)
                        <div class="flex border-b border-gray-100 min-h-[90px] w-full">
                            
                            <!-- Time Label -->
                            <div class="w-20 flex-shrink-0 border-r border-gray-100 bg-white sticky left-0 z-30 flex items-start justify-end pr-2 pt-2">
                                <span class="text-[11px] font-medium text-gray-400">
                                    {{ $hour == 0 ? '12 AM' : ($hour < 12 ? $hour . ' AM' : ($hour == 12 ? '12 PM' : ($hour - 12) . ' PM')) }}
                                </span>
                            </div>

                            <!-- Day Cells for this Hour -->
                            @foreach ($schedulesByDay as $dayData)
                                <div class="flex-1 border-r border-gray-100 p-1 flex flex-col gap-1 min-w-[150px] relative bg-white transition-colors hover:bg-gray-50/50">
                                    
                                    <!-- Render Appointments for this specific hour -->
                                    @foreach ($dayData['schedules'] as $schedule)
                                        @if ((int) $schedule->starts_at->format('G') === $hour)
                                            @php
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
                                            <div class="rounded-lg border shadow-sm transition-all duration-200 hover:shadow-md cursor-pointer flex flex-col {{ $statusClass }} {{ 'border-'.$statusColor.'-300' }} w-full"
                                                 onclick="window.location='{{ route('teacher.schedule.daily', ['date' => $dayData['date']->format('Y-m-d')]) }}'">
                                                 
                                                <div class="p-1.5 flex flex-col gap-1 h-full bg-{{$statusColor}}-50/90 relative">
                                                    <!-- Header: Time and Status -->
                                                    <div class="flex flex-wrap justify-between items-start gap-1">
                                                        <div class="flex items-center gap-1 min-w-0">
                                                            <span class="text-[10px] font-black text-{{$statusColor}}-800 tracking-tight leading-none whitespace-nowrap">
                                                                {{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}
                                                            </span>
                                                            @if($isInProgress)
                                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse mt-0.5 flex-shrink-0"></span>
                                                            @endif
                                                        </div>
                                                        <span class="px-1 py-0.5 rounded bg-white/70 border border-{{$statusColor}}-200 text-{{$statusColor}}-700 text-[8px] font-bold uppercase tracking-wider flex items-center gap-1 whitespace-nowrap leading-none shadow-sm flex-shrink-0">
                                                            <i class="fa-solid {{ $iconClass }} text-[8px]"></i> <span class="hidden xl:inline">{{ $statusText }}</span>
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Body: Student and Course -->
                                                    <div class="flex-1 min-w-0 mt-0.5">
                                                        <h4 class="text-xs font-bold text-gray-900 leading-tight mb-0.5 min-w-0 break-words whitespace-normal">{{ $schedule->student->name }}</h4>
                                                        <p class="text-[9px] font-medium text-gray-600 flex items-center gap-1 leading-none min-w-0 break-words whitespace-normal">
                                                            <i class="fa-solid fa-book-open text-gray-400 flex-shrink-0"></i> <span>{{ $schedule->course->name }}</span>
                                                        </p>
                                                    </div>

                                                    <!-- Footer: Buttons -->
                                                    <div class="flex flex-col gap-1 mt-1">
                                                        @if(!$schedule->attendance && !$isPast && $schedule->status !== 'completed')
                                                            <div class="grid grid-cols-2 gap-1 mt-0.5">
                                                                <!-- Attend Button -->
                                                                <button onclick="event.stopPropagation(); openAttendanceModal({
                                                                        id: {{ $schedule->id }},
                                                                        student: { id: {{ $schedule->student->id }}, name: '{{ addslashes($schedule->student->name) }}' },
                                                                        course: { name: '{{ addslashes($schedule->course->name) }}' },
                                                                        starts_at_formatted: '{{ $schedule->starts_at->format('g:i A') }}',
                                                                        attendance: null
                                                                    })" 
                                                                    class="py-1 px-1 bg-vibrant-green hover:bg-green-600 text-white rounded text-[9px] font-bold transition flex items-center justify-center gap-1 shadow-sm leading-tight whitespace-nowrap">
                                                                    <i class="fa-solid fa-check"></i> Attend
                                                                </button>
                                                                
                                                                <!-- Waiting Button -->
                                                                <button onclick="event.stopPropagation(); notifyWaiting({{ $schedule->id }})" id="waitingBtn-{{ $schedule->id }}" 
                                                                    class="py-1 px-1 bg-white border border-yellow-400 hover:bg-yellow-50 text-yellow-700 rounded text-[9px] font-bold transition flex items-center justify-center gap-1 leading-tight whitespace-nowrap">
                                                                    <i class="fa-solid fa-clock"></i> Waiting
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Week Summary Statistics -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $allSchedules = collect($schedulesByDay)->flatMap(fn($day) => $day['schedules']);
            $totalSessions = $allSchedules->count();
            $completedSessions = $allSchedules->where('status', 'completed')->count();
            $totalHours = $allSchedules->sum(fn($s) => $s->getDurationInHours());
            $uniqueStudents = $allSchedules->pluck('student_id')->unique()->count();
        @endphp

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Sessions</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalSessions }}</p>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Completed</p>
                <p class="text-xl font-bold text-gray-900">{{ $completedSessions }}</p>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Hours</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($totalHours, 1) }}</p>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Students</p>
                <p class="text-xl font-bold text-gray-900">{{ $uniqueStudents }}</p>
            </div>
        </div>
    </div>

    <!-- Scroll to current time JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($weekStart <= now() && $weekEnd >= now())
                // Scroll container to current hour minus 2 hours for better view
                const container = document.getElementById('calendar-container');
                const currentHour = {{ (int) now()->format('G') }};
                const scrollTo = Math.max(0, (currentHour - 2) * 90);
                
                setTimeout(() => {
                    container.scrollTo({ top: scrollTo, behavior: 'smooth' });
                }, 100);
            @endif
        });
    </script>

    <!-- Include Attendance Modal -->
    @include('teacher.partials.attendance-modal')
</x-dashboard-layout>
