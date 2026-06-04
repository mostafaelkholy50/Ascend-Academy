<x-dashboard-layout title="Student Dashboard">
    @php $user = auth()->user(); @endphp

    <!-- Hero Welcome with Gradient -->
    <div class="mb-8 bg-gradient-to-br from-vibrant-green via-emerald-500 to-deep-blue rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white/30">
                    <span class="text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Welcome back, {{ $user->name }}! 👋</h1>
                    <p class="text-white/90 mt-1">Keep learning and growing! You have <strong>{{ $stats['today_classes'] }}</strong> {{ Str::plural('class', $stats['today_classes']) }} scheduled today.</p>
                </div>
            </div>
        </div>
    </div>

    @if(isset($unpaidPayments) && $unpaidPayments->count() > 0)
        <!-- Payment Alert -->
        <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-3xl shadow-md animate-pulse">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-red-800">Payment Due for {{ now()->format('F Y') }}</h3>
                    <div class="mt-2 text-red-700">
                        <p class="mb-2">We noticed pending payments for your active courses. To ensure uninterrupted access to your classes, please settle the following dues:</p>
                        <ul class="list-disc list-inside space-y-1 bg-white/50 p-3 rounded-lg border border-red-100">
                            @foreach($unpaidPayments as $payment)
                                <li class="font-medium">
                                    <span class="font-bold">{{ $payment->enrollment->course->title ?? 'Course' }}</span>: 
                                    <span class="font-mono bg-red-100 px-2 py-0.5 rounded">{{ $payment->getFormattedAmount() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-3 text-sm font-semibold"><i class="fa-solid fa-phone mr-1"></i> Please contact our administration office or your teacher to make a payment.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-grow space-y-6">
            <!-- Quick Stats with Gradients -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                <!-- My Courses Stat -->
                 <a href="{{ route('student.courses.index') }}">
                <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                <i class="fa-solid fa-book text-2xl"></i>
                            </div>
                            <i class="fa-solid fa-arrow-right text-white/50 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <p class="text-white/80 text-sm font-medium mb-1">My Courses</p>
                        <p class="text-3xl font-bold">{{ $stats['total_courses'] }}</p>
                        <p class="text-white/70 text-xs mt-1">Active Enrollments</p>
                    </div>
                </div>
                </a>

                <!-- Today's Classes Stat -->
                <a href ="{{ route('student.schedule.weekly') }}">
                <div class="group bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                <i class="fa-solid fa-calendar-check text-2xl"></i>
                            </div>
                            <i class="fa-solid fa-arrow-right text-white/50 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <p class="text-white/80 text-sm font-medium mb-1">Today's Classes</p>
                        <p class="text-3xl font-bold">{{ $stats['today_classes'] }}</p>
                        <p class="text-white/70 text-xs mt-1">{{ Str::plural('Session', $stats['today_classes']) }} Scheduled</p>
                    </div>
                </div>
            </a>

                <!-- Progress Stat -->
                 <a href="{{ route('student.reports.index') }}">
                <div class="group bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                <i class="fa-solid fa-chart-line text-2xl"></i>
                            </div>
                            <i class="fa-solid fa-arrow-right text-white/50 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <p class="text-white/80 text-sm font-medium mb-1">Progress</p>
                        <p class="text-3xl font-bold">{{ $stats['completed_sessions'] }}/{{ $stats['total_sessions'] }}</p>
                        <p class="text-white/70 text-xs mt-1">Sessions Completed</p>
                    </div>
                </div>
            </div>
</a>

            <!-- My Courses Section -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">My Courses</h2>
                    <a href="{{ route('student.courses.index') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700 transition flex items-center gap-1">
                        See all <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
                @if($enrollments->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($enrollments->take(4) as $enrollment)
                            <div class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-purple-300">
                                <div class="flex items-start space-x-4 mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 via-pink-500 to-red-500 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-book-quran text-xl"></i>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-gray-800 mb-1">{{ $enrollment->course->title ?? 'N/A' }}</h4>
                                        <p class="text-xs text-gray-500">{{ $enrollment->course->level ?? 'Course' }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="text-gray-600 font-medium">Progress</span>
                                    <span class="font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ $enrollment->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 h-2.5 rounded-full transition-all duration-500" style="width:{{ $enrollment->progress }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500">{{ $enrollment->completed_sessions }} of {{ $enrollment->total_sessions }} sessions completed</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-12 rounded-3xl shadow-md text-center border border-purple-200">
                        <div class="bg-gradient-to-br from-purple-100 to-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-book text-purple-600 text-3xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">No active courses yet</p>
                    </div>
                @endif
            </section>

            <!-- Today's Schedule Section -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Today's Schedule</h2>
                    <a href="{{ route('student.schedule.weekly') }}" class="text-sm font-semibold text-green-600 hover:text-green-700 transition flex items-center gap-1">
                        View Full Schedule <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
                @if($todaySchedules->count() > 0)
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="divide-y divide-gray-100">
                            @foreach($todaySchedules as $schedule)
                                <div class="p-5 flex items-center justify-between hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                                            <i class="fa-solid fa-book-quran text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ $schedule->course->title ?? 'Session' }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fa-solid fa-user-tie mr-1"></i>{{ $schedule->teacher->name }} 
                                                <span class="mx-2">•</span>
                                                <i class="fa-solid fa-clock mr-1"></i>{{ $schedule->getStartsAtInTimezone($user->getUserTimezone())->format('g:i A') }} - {{ $schedule->getEndsAtInTimezone($user->getUserTimezone())->format('g:i A') }} {{ $schedule->getStartsAtInTimezone($user->getUserTimezone())->format('T') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @if($schedule->starts_at->isFuture())
                                            @php
                                                $minutesUntil = $schedule->starts_at->diffInMinutes(now());
                                            @endphp
                                            @if($minutesUntil < 60)
                                                <span class="px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 rounded-xl text-sm font-bold border border-green-200">
                                                    <i class="fa-solid fa-clock mr-1"></i>In {{ $minutesUntil }} min
                                                </span>
                                                @if($schedule->zoom_link)
                                                    <a href="{{ $schedule->zoom_link }}" target="_blank" class="px-5 py-2 bg-gradient-to-r from-vibrant-green to-emerald-600 text-white rounded-xl text-sm font-bold hover:shadow-lg hover:scale-105 transition-all duration-200">
                                                        <i class="fa-solid fa-video mr-1"></i>Join
                                                    </a>
                                                @endif
                                            @else
                                                <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium">{{ $schedule->getStartsAtInTimezone($user->getUserTimezone())->format('g:i A') }}</span>
                                            @endif
                                        @else
                                            <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 rounded-xl text-sm font-bold border border-blue-200">
                                                <i class="fa-solid fa-circle-dot mr-1"></i>In Progress
                                            </span>
                                            @if($schedule->zoom_link)
                                                <a href="{{ $schedule->zoom_link }}" target="_blank" class="px-5 py-2 bg-gradient-to-r from-vibrant-green to-emerald-600 text-white rounded-xl text-sm font-bold hover:shadow-lg hover:scale-105 transition-all duration-200">
                                                    <i class="fa-solid fa-video mr-1"></i>Join
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-12 rounded-3xl shadow-md text-center border border-green-200">
                        <div class="bg-gradient-to-br from-green-100 to-emerald-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-calendar-times text-green-600 text-3xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">No classes scheduled for today</p>
                        <p class="text-gray-500 text-sm mt-1">Enjoy your free time!</p>
                    </div>
                @endif
            </section>
        </div>

        <!-- Right Sidebar -->
        <div class="w-full lg:w-80 space-y-6">
            <!-- My Teachers -->
            @if($enrollments->count() > 0)
                <section class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-user-tie text-blue-600 mr-2"></i>
                        My Teachers
                    </h3>
                    <div class="space-y-4">
                        @php
                            $teachers = $weekSchedules->pluck('teacher')->unique('id')->take(3);
                        @endphp
                        @foreach($teachers as $teacher)
                            <div class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-400 to-pink-500 flex items-center justify-center text-white font-bold shadow-md">
                                    {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $teacher->name }}</p>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Recent Reports -->
            @if($recentReports->count() > 0)
                <section class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center">
                            <i class="fa-solid fa-chart-line text-purple-600 mr-2"></i>
                            Recent Reports
                        </h3>
                        <a href="{{ route('student.reports.index') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-700">See all</a>
                    </div>
                    <div class="space-y-3">
                        @foreach($recentReports as $report)
                            <a href="{{ route('student.reports.show', $report->id) }}" class="block p-4 bg-gradient-to-r from-gray-50 to-purple-50 rounded-xl hover:from-purple-50 hover:to-pink-50 transition-all border border-gray-200 hover:border-purple-300">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $report->course->title ?? 'General Report' }}</p>
                                    @if($report->mastery_score)
                                        <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $report->mastery_score >= 80 ? 'bg-green-100 text-green-700' : ($report->mastery_score >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ $report->mastery_score }}%
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600"><i class="fa-solid fa-user-tie mr-1"></i>{{ $report->teacher->name }}</p>
                                <p class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-calendar mr-1"></i>{{ $report->report_date->format('M d, Y') }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Recent Resources -->
            @if($recentResources->count() > 0)
                <section class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center">
                            <i class="fa-solid fa-folder-open text-amber-600 mr-2"></i>
                            Recent Resources
                        </h3>
                        <a href="{{ route('student.resources.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700">See all</a>
                    </div>
                    <div class="space-y-3">
                        @foreach($recentResources as $resource)
                            <div class="p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-sm
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
                                        <p class="text-xs text-gray-500">{{ $resource->course->title ?? 'General' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- This Week Summary -->
            <section class="bg-gradient-to-br from-vibrant-green via-emerald-500 to-deep-blue text-white p-6 rounded-3xl shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative z-10">
                    <h3 class="font-bold text-lg mb-4 flex items-center">
                        <i class="fa-solid fa-calendar-week mr-2"></i>
                        This Week Summary
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                            <span class="text-sm font-medium">Total Sessions</span>
                            <span class="text-2xl font-bold">{{ $weekSchedules->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                            <span class="text-sm font-medium">Completed</span>
                            <span class="text-2xl font-bold">{{ $stats['completed_this_week'] }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                            <span class="text-sm font-medium">Remaining</span>
                            <span class="text-2xl font-bold">{{ $weekSchedules->where('status', 'scheduled')->count() }}</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-dashboard-layout>
