<x-dashboard-layout title="Teacher Dashboard">
    @php
        $user = auth()->user();
        $todayHours = $stats['today_hours'] ?? 0;
        $completionRate = $stats['completion_rate'] ?? 0;
        $avgSession = $stats['average_session_length'] ?? 0;
    @endphp

    <x-dashboard.hero-welcome
        :user="$user"
        message="Today: {{ $stats['today_classes'] }} sessions, {{ number_format($todayHours, 1) }} hours, and {{ $stats['pending_reports'] }} pending reports."
    />

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="flex-grow space-y-4 sm:space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 md:gap-4">
                <x-dashboard.stat-card icon="fa-users" title="My Students" value="{{ $stats['total_students'] }}" color="blue" />
                <x-dashboard.stat-card icon="fa-calendar-check" title="Today" value="{{ $stats['today_classes'] }}" subtitle="{{ $stats['upcoming_today'] }} upcoming" color="green" />
                <x-dashboard.stat-card icon="fa-clock" title="This Month" value="{{ number_format($stats['this_month_hours'], 1) }} hrs" subtitle="{{ $stats['bonus_hours'] > 0 ? 'bonus included' : 'clean total' }}" color="purple" />
                <x-dashboard.stat-card icon="fa-file-alt" title="Pending Reports" value="{{ $stats['pending_reports'] }}" subtitle="{{ $completionRate }}% weekly done" color="red" />
            </div>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Today at a glance</h3>
                        <p class="text-xs text-gray-500">Useful numbers, not decoration</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-vibrant-green/10 text-vibrant-green">
                        {{ number_format($todayHours, 1) }} hrs today
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Sessions</p>
                        <p class="text-lg font-black text-gray-800 mt-1">{{ $stats['today_classes'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Upcoming</p>
                        <p class="text-lg font-black text-gray-800 mt-1">{{ $stats['upcoming_today'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Avg session</p>
                        <p class="text-lg font-black text-gray-800 mt-1">{{ number_format($avgSession, 1) }}h</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Weekly done</p>
                        <p class="text-lg font-black text-gray-800 mt-1">{{ $completionRate }}%</p>
                    </div>
                </div>
            </section>

            <section>
                <x-dashboard.section-header title="Today's Schedule" linkText="View Full Schedule" linkHref="#" />
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @if($todaySchedules->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($todaySchedules as $schedule)
                                <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-gray-50">
                                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-book-quran text-green-600 text-sm sm:text-xl"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-semibold text-gray-800 text-sm sm:text-base truncate">{{ $schedule->course->title ?? 'Session' }}</h4>
                                            <p class="text-[11px] sm:text-xs text-gray-500 truncate">
                                                {{ $schedule->student->name }} · {{ $schedule->getStartsAtInTimezone($user->getUserTimezone())->format('g:i A') }} - {{ $schedule->getEndsAtInTimezone($user->getUserTimezone())->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 sm:justify-end">
                                        @if($schedule->starts_at->isFuture())
                                            @php $minutesUntil = $schedule->starts_at->diffInMinutes(now()); @endphp
                                            @if($minutesUntil < 60)
                                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[10px] sm:text-xs font-medium">In {{ $minutesUntil }} min</span>
                                                @if($schedule->zoom_link)
                                                    <a href="{{ $schedule->zoom_link }}" target="_blank" class="px-3 py-2 bg-vibrant-green text-white rounded-lg text-[10px] sm:text-xs font-semibold hover:bg-deep-blue transition">Start</a>
                                                @endif
                                            @else
                                                <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] sm:text-xs font-medium">{{ $schedule->getStartsAtInTimezone($user->getUserTimezone())->format('g:i A') }}</span>
                                            @endif
                                        @else
                                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] sm:text-xs font-medium">In Progress</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <i class="fa-solid fa-calendar-times text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No classes scheduled for today</p>
                        </div>
                    @endif
                </div>
            </section>

            <section>
                <x-dashboard.section-header title="My Students" linkText="View All" linkHref="{{ route('teacher.my-students') }}" />
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($myStudents as $student)
                        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm hover:shadow-md transition">
                            <div class="flex items-center gap-3 mb-3 min-w-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-orange-400 to-pink-500 flex items-center justify-center text-white font-bold shrink-0">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="font-semibold text-gray-800 text-xs sm:text-sm truncate">{{ $student->name }}</h4>
                                    @if($student->enrollments->first())
                                        <p class="text-[10px] sm:text-xs text-gray-500 truncate">{{ $student->enrollments->first()->course->title }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between text-[10px] sm:text-xs mb-2">
                                <span class="text-gray-600">Progress</span>
                                <span class="font-bold text-vibrant-green">{{ $student->progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-3">
                                <div class="bg-vibrant-green h-1.5 rounded-full" style="width:{{ $student->progress }}%"></div>
                            </div>
                            <a href="{{ route('teacher.student-evaluations.create', ['student_id' => $student->id]) }}" class="block w-full px-3 py-2 text-[10px] sm:text-xs bg-vibrant-green text-white rounded-lg font-semibold hover:bg-deep-blue transition text-center">
                                Create Evaluation
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="w-full lg:w-72 space-y-4 sm:space-y-6">
            <section class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-4">This Month</h3>
                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                    <div class="rounded-xl bg-gray-50 p-3">
                        <span class="block text-[10px] uppercase tracking-wide text-gray-400">Hours Worked</span>
                        <span class="block mt-1 font-bold text-gray-800 text-sm">{{ number_format($stats['this_month_hours'], 1) }} hrs</span>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3">
                        <span class="block text-[10px] uppercase tracking-wide text-gray-400">Rate</span>
                        <span class="block mt-1 font-bold text-gray-800 text-sm">${{ $user->hourly_rate ?? 0 }}/hr</span>
                    </div>
                    @if(isset($stats['bonus_hours']) && $stats['bonus_hours'] > 0)
                        <div class="col-span-2 flex justify-between text-xs text-amber-600 bg-amber-50 rounded-xl p-3">
                            <span><i class="fa-solid fa-gift mr-1"></i>Bonus included</span>
                            <span>+{{ $stats['bonus_hours'] }} hrs</span>
                        </div>
                    @endif
                    <div class="col-span-2 flex items-center justify-between rounded-xl bg-vibrant-green/10 p-3">
                        <span class="text-gray-700 text-sm font-medium">Total Earnings</span>
                        <span class="font-black text-vibrant-green text-lg">${{ number_format($stats['this_month_hours'] * ($user->hourly_rate ?? 0), 2) }}</span>
                    </div>
                </div>
            </section>

            @if($studentsNeedingReports->count() > 0)
                <section class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm">
                    <x-dashboard.section-header title="Pending Evaluations" linkText="View All" linkHref="{{ route('teacher.student-evaluations.index') }}" />
                    <div class="space-y-3">
                        @foreach($studentsNeedingReports as $student)
                            <div class="p-3 bg-red-50 rounded-xl border border-red-100">
                                <p class="text-sm font-medium text-gray-800">{{ $student->name }}</p>
                                <p class="text-xs text-gray-500">Needs evaluation for {{ now()->format('F') }}</p>
                                <a href="{{ route('teacher.student-evaluations.create', ['student_id' => $student->id]) }}" class="block mt-2 text-xs text-center bg-vibrant-green text-white px-3 py-2 rounded-lg hover:bg-deep-blue transition font-semibold">
                                    Evaluate
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($recentReports->count() > 0)
                <section class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm">
                    <x-dashboard.section-header title="Recent Evaluations" linkText="See all" linkHref="{{ route('teacher.student-evaluations.index') }}" />
                    <div class="space-y-3">
                        @foreach($recentReports as $evaluation)
                            <a href="{{ route('teacher.student-evaluations.show', $evaluation->id) }}" class="block p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $evaluation->student->name }}</p>
                                    <span class="text-xs font-bold text-vibrant-green">{{ $evaluation->total_score }}/100</span>
                                </div>
                                <p class="text-xs text-gray-500">Evaluated on {{ $evaluation->evaluation_date->format('M d, Y') }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($recentResources->count() > 0)
                <section class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm">
                    <x-dashboard.section-header title="Recent Resources" linkText="See all" linkHref="{{ route('teacher.resources.index') }}" />
                    <div class="space-y-3">
                        @foreach($recentResources as $resource)
                            <a href="{{ route('teacher.resources.show', $resource->id) }}" class="block p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                        @if($resource->type == 'pdf') bg-red-100
                                        @elseif($resource->type == 'image') bg-purple-100
                                        @elseif($resource->type == 'video') bg-blue-100
                                        @elseif($resource->type == 'audio') bg-green-100
                                        @elseif($resource->type == 'link') bg-yellow-100
                                        @else bg-gray-100
                                        @endif">
                                        <i class="fa-solid
                                            @if($resource->type == 'pdf') fa-file-pdf text-red-600
                                            @elseif($resource->type == 'image') fa-image text-purple-600
                                            @elseif($resource->type == 'video') fa-video text-blue-600
                                            @elseif($resource->type == 'audio') fa-music text-green-600
                                            @elseif($resource->type == 'link') fa-link text-yellow-600
                                            @else fa-file text-gray-600
                                            @endif text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $resource->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if($resource->student)
                                                {{ $resource->student->name }}
                                            @elseif($resource->course)
                                                {{ $resource->course->title }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="bg-gradient-to-br from-vibrant-green to-deep-blue text-white p-4 sm:p-5 rounded-2xl shadow-sm">
                <h3 class="font-bold text-sm mb-4">This Week Summary</h3>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-xl bg-white/10 p-2">
                        <span class="block text-[10px] uppercase tracking-wide opacity-80">Sessions</span>
                        <span class="block mt-1 font-black text-base">{{ $weekSchedules->count() }}</span>
                    </div>
                    <div class="rounded-xl bg-white/10 p-2">
                        <span class="block text-[10px] uppercase tracking-wide opacity-80">Done</span>
                        <span class="block mt-1 font-black text-base">{{ $stats['completed_this_week'] }}</span>
                    </div>
                    <div class="rounded-xl bg-white/10 p-2">
                        <span class="block text-[10px] uppercase tracking-wide opacity-80">Left</span>
                        <span class="block mt-1 font-black text-base">{{ $stats['scheduled_this_week'] }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-dashboard-layout>
