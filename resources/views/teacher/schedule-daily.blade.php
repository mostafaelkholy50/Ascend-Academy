<x-dashboard-layout title="Daily Schedule">
    @php $user = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Daily Schedule</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">{{ $date->format('l, F d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse bg-white p-1.5 rounded-xl shadow-sm border border-gray-100">
                <a href="{{ route('teacher.schedule.daily', ['date' => $prevDay->format('Y-m-d')]) }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition flex items-center">
                    <i class="fa-solid fa-chevron-left mr-1 rtl:ml-1 rtl:mr-0 rtl:rotate-180"></i> Previous
                </a>
                <a href="{{ route('teacher.schedule.daily') }}"
                    class="px-4 py-2 bg-vibrant-green/10 text-vibrant-green rounded-lg text-sm font-bold hover:bg-vibrant-green hover:text-white transition shadow-sm">
                    Today
                </a>
                <a href="{{ route('teacher.schedule.daily', ['date' => $nextDay->format('Y-m-d')]) }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition flex items-center">
                    Next <i class="fa-solid fa-chevron-right ml-1 rtl:mr-1 rtl:ml-0 rtl:rotate-180"></i>
                </a>
                <div class="w-px h-6 bg-gray-200 mx-2"></div>
                <a href="{{ route('teacher.schedule.index') }}"
                    class="px-4 py-2 bg-deep-blue text-white rounded-lg text-sm font-semibold hover:bg-opacity-90 transition shadow-sm flex items-center">
                    <i class="fa-solid fa-calendar-week mr-2"></i> Weekly View
                </a>
            </div>
        </div>
    </div>

    <!-- Day Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
    </div>

    <!-- Google Calendar Style Grid (Single Day) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
        
        <div class="overflow-x-auto relative scroll-smooth" id="calendar-container">
            <div class="min-w-[500px] lg:w-full flex flex-col relative select-none">
                
                <!-- Header: Day -->
                <div class="flex border-b border-gray-200 sticky top-0 bg-white/95 backdrop-blur-md z-30 shadow-sm">
                    <div class="w-20 flex-shrink-0 border-r border-gray-100 flex items-end justify-center pb-2">
                        <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">GMT+3</span>
                    </div>
                    
                    <div class="flex-1 py-3 px-2 text-center {{ $date->isToday() ? 'bg-vibrant-green/5' : '' }}">
                        <div class="text-sm font-semibold uppercase tracking-wider {{ $date->isToday() ? 'text-vibrant-green' : 'text-gray-500' }}">
                            {{ $date->format('l') }}
                        </div>
                        <div class="mt-1 flex justify-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full text-2xl font-bold {{ $date->isToday() ? 'bg-vibrant-green text-white shadow-md shadow-vibrant-green/30' : 'text-gray-800' }}">
                                {{ $date->format('d') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid Body -->
                <div class="flex relative h-[2160px] bg-gray-50/30">
                    
                    <!-- Time Labels -->
                    <div class="w-20 flex-shrink-0 border-r border-gray-100 bg-white z-20 relative">
                        @for ($i = 0; $i < 24; $i++)
                            <div class="h-[90px] border-b border-gray-100/0 relative">
                                <div class="absolute -top-3 right-3 text-[11px] font-medium text-gray-400">
                                    {{ $i == 0 ? '12 AM' : ($i < 12 ? $i . ' AM' : ($i == 12 ? '12 PM' : ($i - 12) . ' PM')) }}
                                </div>
                            </div>
                        @endfor
                    </div>

                    <!-- Day Grid & Appointments -->
                    <div class="flex flex-1 relative bg-white">
                        
                        <!-- Grid Lines -->
                        <div class="absolute inset-0 pointer-events-none z-0">
                            @for ($i = 0; $i < 24; $i++)
                                <div class="h-[90px] border-b border-gray-100 w-full"></div>
                            @endfor
                        </div>

                        <!-- The Single Day Column -->
                        <div class="flex-1 relative z-10 px-2 md:px-12 lg:px-24">
                            
                            @foreach ($schedules as $schedule)
                                @php
                                    $startHour = (int) $schedule->starts_at->format('G');
                                    $startMinute = (int) $schedule->starts_at->format('i');
                                    $durationMinutes = $schedule->getDurationInMinutes();
                                    
                                    $top = ($startHour * 60 + $startMinute) * 1.5;
                                    $height = $durationMinutes * 1.5;
                                    
                                    $now = now();
                                    $isPast = $now->greaterThan($schedule->ends_at);
                                    $isInProgress = $now->between($schedule->starts_at, $schedule->ends_at);
                                    
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
                                
                                <!-- Appointment Card -->
                                <div class="absolute left-4 right-4 md:left-12 md:right-12 lg:left-24 lg:right-24 rounded-xl border shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 hover:z-50 {{ $statusClass }} flex items-stretch overflow-hidden"
                                     style="top: {{ $top }}px; min-height: {{ max($height, 60) }}px; z-index: 5;">
                                     
                                    <!-- Color Indicator Left Bar -->
                                    <div class="w-1.5 bg-{{$statusColor}}-500 h-full flex-shrink-0"></div>
                                    
                                    <div class="p-2 sm:p-3 flex-1 flex items-center justify-between gap-4 overflow-hidden bg-{{$statusColor}}-50/90 relative">
                                        
                                        <!-- Info Column -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="text-sm font-black text-{{$statusColor}}-800">
                                                    {{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}
                                                </div>
                                                @if($isInProgress)
                                                    <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                                                @endif
                                                <span class="px-2 py-0.5 rounded-full bg-white border border-{{$statusColor}}-200 text-{{$statusColor}}-700 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-sm whitespace-nowrap">
                                                    <i class="fa-solid {{ $iconClass }}"></i> {{ $statusText }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-baseline gap-3 truncate">
                                                <div class="text-base font-bold text-gray-900 truncate">{{ $schedule->student->name }}</div>
                                                <div class="text-xs font-medium text-gray-600 flex items-center gap-1 truncate">
                                                    <i class="fa-solid fa-book-open text-gray-400"></i> {{ $schedule->course->name }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Actions Column -->
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            @if(!$schedule->attendance && !$isPast && $schedule->status !== 'completed')
                                                <button onclick="openAttendanceModal({
                                                        id: {{ $schedule->id }},
                                                        student: { id: {{ $schedule->student->id }}, name: '{{ addslashes($schedule->student->name) }}' },
                                                        course: { name: '{{ addslashes($schedule->course->name) }}' },
                                                        starts_at_formatted: '{{ $schedule->starts_at->format('g:i A') }}',
                                                        attendance: null
                                                    })" 
                                                    class="px-3 py-1.5 bg-vibrant-green hover:bg-green-600 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm whitespace-nowrap">
                                                    <i class="fa-solid fa-clipboard-check"></i> Attend
                                                </button>
                                                
                                                <button onclick="notifyWaiting({{ $schedule->id }})" id="waitingBtn-{{ $schedule->id }}" 
                                                    class="px-3 py-1.5 bg-white border border-yellow-400 hover:bg-yellow-50 text-yellow-700 rounded-lg text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap">
                                                    <i class="fa-solid fa-clock"></i> Waiting
                                                </button>
                                            @endif
                                            
                                            <button class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap">
                                                <i class="fa-solid fa-file-alt"></i> Report
                                            </button>
                                        </div>
                                        
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($date->isToday())
                            @php
                                $currentHour = (int) now()->format('G');
                                $currentMinute = (int) now()->format('i');
                                $currentTop = ($currentHour * 60 + $currentMinute) * 1.5;
                            @endphp
                            <div class="absolute w-full border-t-2 border-red-500 z-20 pointer-events-none flex items-center" style="top: {{ $currentTop }}px;">
                                <div class="w-2 h-2 rounded-full bg-red-500 -ml-1"></div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to current time JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($date->isToday())
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
