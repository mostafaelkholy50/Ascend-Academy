<x-dashboard-layout title="{{ $child->name }} - Details">
    @php $parent = auth()->user(); @endphp

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('parent.children.index') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i> Back to My Children
        </a>
    </div>

    <!-- Child Profile Header -->
    <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-3xl shadow-2xl p-8 text-white mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-4xl font-bold shadow-xl border-2 border-white/30">
                    {{ strtoupper(substr($child->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $child->name }}</h1>
                    <div class="flex flex-wrap gap-4 text-white/90">
                        <span><i class="fa-solid fa-envelope mr-2"></i>{{ $child->email }}</span>
                        @if($child->phone)
                            <span><i class="fa-solid fa-phone mr-2"></i>{{ $child->phone }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                    <i class="fa-solid fa-book text-2xl"></i>
                </div>
            </div>
            <p class="text-white/80 text-sm font-medium mb-1">Total Courses</p>
            <p class="text-3xl font-bold">{{ $stats['total_courses'] }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                    <i class="fa-solid fa-check-circle text-2xl"></i>
                </div>
            </div>
            <p class="text-white/80 text-sm font-medium mb-1">Completed Sessions</p>
            <p class="text-3xl font-bold">{{ $stats['completed_sessions'] }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                    <i class="fa-solid fa-clipboard-check text-2xl"></i>
                </div>
            </div>
            <p class="text-white/80 text-sm font-medium mb-1">Attendance Rate</p>
            <p class="text-3xl font-bold">{{ $stats['attendance_rate'] }}%</p>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                    <i class="fa-solid fa-file-lines text-2xl"></i>
                </div>
            </div>
            <p class="text-white/80 text-sm font-medium mb-1">Total Reports</p>
            <p class="text-3xl font-bold">{{ $stats['total_reports'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Enrolled Courses -->
            <section>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">Enrolled Courses</h2>
                @if($enrollments->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($enrollments as $enrollment)
                            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 via-pink-500 to-red-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                                        <i class="fa-solid fa-book-quran text-xl"></i>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-gray-800 mb-1">{{ $enrollment->course->title }}</h4>
                                        <p class="text-xs text-gray-500">{{ $enrollment->course->level ?? 'Course' }}</p>
                                        <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold
                                            @if($enrollment->status == 'active') bg-green-100 text-green-700
                                            @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="text-gray-600 font-medium">Progress</span>
                                    <span class="font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ $enrollment->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 h-2.5 rounded-full transition-all duration-500" style="width:{{ $enrollment->progress }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 p-12 rounded-3xl text-center">
                        <i class="fa-solid fa-book text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No active enrollments</p>
                    </div>
                @endif
            </section>

            <!-- Recent Schedules -->
            <section>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-4">Recent Schedules</h2>
                @if($recentSchedules->count() > 0)
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="divide-y divide-gray-100">
                            @foreach($recentSchedules as $schedule)
                                <div class="p-5 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center text-white">
                                                <i class="fa-solid fa-book-quran"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800">{{ $schedule->course->title ?? 'Session' }}</h4>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fa-solid fa-user-tie mr-1"></i>{{ $schedule->teacher->name }}
                                                    <span class="mx-2">•</span>
                                                    <i class="fa-solid fa-clock mr-1"></i>{{ $schedule->starts_at->format('M d, g:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            @if($schedule->status == 'completed') bg-green-100 text-green-700
                                            @elseif($schedule->status == 'scheduled') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 p-12 rounded-3xl text-center">
                        <i class="fa-solid fa-calendar text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No recent schedules</p>
                    </div>
                @endif
            </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Recent Reports -->
            @if($recentReports->count() > 0)
                <section class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-chart-line text-purple-600 mr-2"></i>
                        Recent Reports
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentReports as $report)
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-purple-50 rounded-xl border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $report->course->title ?? 'General' }}</p>
                                    @if($report->mastery_score)
                                        <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $report->mastery_score >= 80 ? 'bg-green-100 text-green-700' : ($report->mastery_score >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ $report->mastery_score }}%
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600"><i class="fa-solid fa-user-tie mr-1"></i>{{ $report->teacher->name }}</p>
                                <p class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-calendar mr-1"></i>{{ $report->report_date->format('M d, Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Quick Actions -->
            <section class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 text-white p-6 rounded-3xl shadow-xl">
                <h3 class="font-bold text-lg mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('parent.schedule.weekly', ['child_id' => $child->id]) }}" class="block p-3 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-calendar-week"></i>
                            <span class="font-medium">View Schedule</span>
                        </div>
                    </a>
                    <a href="{{ route('parent.reports.index', ['child_id' => $child->id]) }}" class="block p-3 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-line"></i>
                            <span class="font-medium">View All Reports</span>
                        </div>
                    </a>
                    <a href="{{ route('parent.attendance.index', ['child_id' => $child->id]) }}" class="block p-3 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clipboard-check"></i>
                            <span class="font-medium">Attendance Records</span>
                        </div>
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-dashboard-layout>
