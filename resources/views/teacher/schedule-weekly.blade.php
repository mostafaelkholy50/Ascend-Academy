<x-dashboard-layout title="Weekly Schedule">
    @php $user = auth()->user(); @endphp
    <style>
        .schedule-block {
            min-height: var(--schedule-block-height-mobile);
        }

        @media (min-width: 768px) {
            .schedule-block {
                min-height: var(--schedule-block-height-desktop);
            }
        }
    </style>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Weekly Schedule</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">{{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}</p>
            </div>
            <div class="flex flex-col md:flex-row items-center gap-2 md:space-x-2 rtl:md:space-x-reverse bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 w-full md:w-auto">
                <div class="flex items-center justify-center space-x-1 sm:space-x-2 rtl:space-x-reverse w-full md:w-auto">
                    <a href="{{ route('teacher.schedule.index', ['week' => $prevWeek->format('Y-m-d')]) }}"
                        class="px-2 sm:px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition flex items-center flex-1 justify-center whitespace-nowrap">
                        <i class="fa-solid fa-chevron-left mr-1 rtl:ml-1 rtl:mr-0 rtl:rotate-180"></i> Previous
                    </a>
                    <a href="{{ route('teacher.schedule.index') }}"
                        class="px-2 sm:px-4 py-2 bg-vibrant-green/10 text-vibrant-green rounded-lg text-sm font-bold hover:bg-vibrant-green hover:text-white transition shadow-sm flex-1 text-center whitespace-nowrap">
                        This Week
                    </a>
                    <a href="{{ route('teacher.schedule.index', ['week' => $nextWeek->format('Y-m-d')]) }}"
                        class="px-2 sm:px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition flex items-center flex-1 justify-center whitespace-nowrap">
                        Next <i class="fa-solid fa-chevron-right ml-1 rtl:mr-1 rtl:ml-0 rtl:rotate-180"></i>
                    </a>
                </div>
                
                <div class="hidden md:block w-px h-6 bg-gray-200 mx-2"></div>
                <div class="md:hidden w-full h-px bg-gray-100"></div>
                
                <div class="flex items-center justify-center space-x-1 sm:space-x-2 rtl:space-x-reverse w-full md:w-auto mt-1 md:mt-0">
                    <a href="{{ route('teacher.schedule.daily') }}"
                        class="px-3 sm:px-4 py-2 bg-deep-blue text-white rounded-lg text-sm font-semibold hover:bg-opacity-90 transition shadow-sm flex items-center flex-1 justify-center whitespace-nowrap">
                        <i class="fa-solid fa-calendar-day mr-2"></i> Daily View
                    </a>
                    <button type="button" onclick="openPrintMonthModal()"
                        class="px-3 sm:px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg transition shadow-sm flex items-center flex-1 justify-center whitespace-nowrap">
                        <i class="fa-solid fa-print mr-2"></i> Print Month
                    </button>
                </div>
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
                    <div class="w-12 md:w-20 flex-shrink-0 border-r border-gray-100 flex items-end justify-center pb-2 sticky left-0 bg-white/95 z-50">
                        <span class="text-[8px] md:text-[10px] text-gray-400 font-medium uppercase tracking-wider">GMT+3</span>
                    </div>
                    
                    <!-- Days Columns -->
                    @foreach ($schedulesByDay as $dayData)
                        @php $isToday = $dayData['date']->isToday(); @endphp
                        <div class="flex-1 py-2 md:py-3 px-1 md:px-2 text-center border-r border-gray-100 {{ $isToday ? 'bg-vibrant-green/5' : '' }} group transition-colors min-w-[110px] md:min-w-[150px]">
                            <div class="text-[10px] md:text-xs font-semibold uppercase tracking-wider {{ $isToday ? 'text-vibrant-green' : 'text-gray-500 group-hover:text-gray-700' }}">
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
                        @php
                            $hourSchedules = collect($schedulesByDay)->flatMap(fn ($dayData) => $dayData['schedules'])->filter(function ($schedule) use ($hour) {
                                return (int) $schedule->starts_at->format('G') === $hour;
                            });
                            $hasPastShortSession = $hourSchedules->contains(function ($schedule) {
                                return now()->greaterThan($schedule->ends_at) && $schedule->getDurationInMinutes() <= 60;
                            });
                            $rowMobileHeight = max(
                                70,
                                $hourSchedules->map(function ($schedule) {
                                    return max(1, (int) ceil($schedule->getDurationInMinutes() / 60));
                                })->map(fn ($durationHours) => ($durationHours * 70) - 8)->max() ?: 70
                            );
                            if ($hasPastShortSession) {
                                $rowMobileHeight = max($rowMobileHeight, 120);
                            }
                            $rowDesktopHeight = max(
                                90,
                                $hourSchedules->map(function ($schedule) {
                                    return max(1, (int) ceil($schedule->getDurationInMinutes() / 60));
                                })->map(fn ($durationHours) => ($durationHours * 90) - 8)->max() ?: 90
                            );
                        @endphp
                        <div class="flex border-b border-gray-100 min-h-[70px] md:min-h-[90px] w-full">
                            
                            <!-- Time Label -->
                            <div class="w-12 md:w-20 flex-shrink-0 border-r border-gray-100 bg-white sticky left-0 z-30 flex items-start justify-end pr-1 md:pr-2 pt-1 md:pt-2" style="min-height: {{ $rowMobileHeight }}px;">
                                <span class="text-[9px] md:text-[11px] font-medium text-gray-400">
                                    {{ $hour == 0 ? '12 AM' : ($hour < 12 ? $hour . ' AM' : ($hour == 12 ? '12 PM' : ($hour - 12) . ' PM')) }}
                                </span>
                            </div>

                            <!-- Day Cells for this Hour -->
                            @foreach ($schedulesByDay as $dayData)
                            <div class="flex-1 border-r border-gray-100 p-0.5 md:p-1 flex flex-col gap-1 min-w-[110px] md:min-w-[150px] relative bg-white transition-colors hover:bg-gray-50/50 overflow-visible" style="min-height: {{ $rowMobileHeight }}px;">
                                    
                                    <!-- Render Appointments for this specific hour -->
                                    @foreach ($dayData['schedules'] as $schedule)
                                            @if ((int) $schedule->starts_at->format('G') === $hour)
                                                @php
                                                    $durationHours = max(1, (int) ceil($schedule->getDurationInMinutes() / 60));
                                                    $mobileBlockHeight = ($durationHours * 70) - 8;
                                                    $desktopBlockHeight = ($durationHours * 90) - 8;
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
                                                <div class="schedule-block relative z-20 rounded-lg border shadow-sm transition-all duration-200 hover:shadow-md cursor-pointer flex flex-col {{ $statusClass }} {{ 'border-'.$statusColor.'-300' }} w-full overflow-hidden"
                                                     style="--schedule-block-height-mobile: {{ $mobileBlockHeight }}px; --schedule-block-height-desktop: {{ $desktopBlockHeight }}px;"
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
                                                    @if($schedule->status !== 'completed' && !$schedule->attendance)
                                                        @if(!\App\Models\RescheduleRequest::where('schedule_id', $schedule->id)->where('status', 'pending')->exists())
                                                            <button onclick="event.stopPropagation(); openRescheduleModal({{ $schedule->id }})" 
                                                                class="w-full py-1 px-1 bg-white border border-blue-400 hover:bg-blue-50 text-blue-700 rounded text-[8px] font-bold transition flex items-center justify-center gap-1 leading-tight whitespace-nowrap">
                                                                <i class="fa-solid fa-calendar-alt"></i> Reschedule
                                                            </button>
                                                        @else
                                                            <div class="w-full py-1 px-1 bg-gray-100 text-gray-500 rounded text-[8px] font-bold border border-gray-200 text-center leading-tight whitespace-nowrap">
                                                                Pending Reschedule
                                                            </div>
                                                        @endif
                                                        <div class="grid grid-cols-2 gap-1 mt-0.5 mt-auto">
                                                            <!-- Attend Button -->
                                                            <button onclick="event.stopPropagation(); openAttendanceModal({
                                                                        id: {{ $schedule->id }},
                                                                        student: { id: {{ $schedule->student->id }}, name: '{{ addslashes($schedule->student->name) }}' },
                                                                        course: { name: '{{ addslashes($schedule->course->name) }}' },
                                                                        starts_at_formatted: '{{ $schedule->starts_at->format('g:i A') }}',
                                                                        attendance: null
                                                                    })" 
                                                                    class="py-1 px-1 bg-vibrant-green hover:bg-green-600 text-white rounded text-[8px] font-bold transition flex items-center justify-center gap-1 shadow-sm leading-tight whitespace-nowrap">
                                                                    <i class="fa-solid fa-check"></i> Attend
                                                                </button>
                                                                
                                                                <!-- Waiting Button -->
                                                            <button onclick="event.stopPropagation(); notifyWaiting({{ $schedule->id }})" id="waitingBtn-{{ $schedule->id }}" 
                                                                class="py-1 px-1 bg-white border border-yellow-400 hover:bg-yellow-50 text-yellow-700 rounded text-[8px] font-bold transition flex items-center justify-center gap-1 leading-tight whitespace-nowrap">
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

    <!-- Include Reschedule Modal -->
    @include('teacher.partials.reschedule-modal')

    <!-- Include Attendance Modal -->
    @include('teacher.partials.attendance-modal')

    <!-- Print Month Modal -->
    <div id="printMonthModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-start justify-between p-5 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Print Month</h3>
                    <p class="text-sm text-gray-500 mt-1">Choose a month to open the printable schedule.</p>
                </div>
                <button type="button" onclick="closePrintMonthModal()" class="text-gray-400 hover:text-gray-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="GET" action="{{ route('teacher.schedule.print') }}" target="_blank" class="p-5 space-y-4" onsubmit="closePrintMonthModal()">
                <div>
                    <label for="printMonth" class="block text-sm font-semibold text-gray-700 mb-2">Month</label>
                    <input
                        id="printMonth"
                        type="month"
                        name="month"
                        value="{{ request('month', now()->format('Y-m')) }}"
                        class="w-full px-3 py-2 rounded-lg text-sm border border-gray-200 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                        required
                    >
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closePrintMonthModal()"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold hover:shadow-lg">
                        Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPrintMonthModal() {
            const modal = document.getElementById('printMonthModal');
            const input = document.getElementById('printMonth');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (input) {
                input.focus();
            }
        }

        function closePrintMonthModal() {
            const modal = document.getElementById('printMonthModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closePrintMonthModal();
            }
        });

        document.getElementById('printMonthModal').addEventListener('click', function (event) {
            if (event.target === this) {
                closePrintMonthModal();
            }
        });
    </script>
</x-dashboard-layout>
