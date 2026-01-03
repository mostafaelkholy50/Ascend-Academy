<x-dashboard-layout title="Parent Dashboard">
    @php $parent = auth()->user(); @endphp

    <!-- Hero Welcome Section -->
    <div class="mb-8 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-32 -mb-32"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-5 mb-4">
                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white/30 shadow-xl">
                    <i class="fa-solid fa-users text-4xl"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-bold">Welcome, {{ $parent->name }}! 👋</h1>
                    <p class="text-white/90 mt-2 text-lg">Managing {{ $stats['total_children'] }} {{ Str::plural('child', $stats['total_children']) }} • {{ $stats['today_total_classes'] }} {{ Str::plural('class', $stats['today_total_classes']) }} today</p>
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
                    <h3 class="text-lg font-bold text-red-800">Payment Attention Required</h3>
                    <div class="mt-2 text-red-700">
                        <p class="mb-2">There are pending payments for this month ({{ now()->format('F Y') }}). Please review the following:</p>
                        <ul class="list-disc list-inside space-y-1 bg-white/50 p-3 rounded-lg border border-red-100">
                            @foreach($unpaidPayments as $payment)
                                <li class="font-medium">
                                    <span class="font-bold">{{ $payment->enrollment->student->name }}</span> - 
                                    <span class="font-semibold">{{ $payment->enrollment->course->title ?? 'Course' }}</span>: 
                                    <span class="font-mono bg-red-100 px-2 py-0.5 rounded">{{ $payment->getFormattedAmount() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-3 text-sm font-semibold"><i class="fa-solid fa-phone mr-1"></i> Please contact our administration office or the respective teacher to settle these payments.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
        <!-- Total Children -->
        <a href="{{ route('parent.children.index') }}">
            <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <i class="fa-solid fa-child text-2xl"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right text-white/50 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                    <p class="text-white/80 text-sm font-medium mb-1">My Children</p>
                    <p class="text-3xl font-bold">{{ $stats['total_children'] }}</p>
                    <p class="text-white/70 text-xs mt-1">{{ Str::plural('Student', $stats['total_children']) }}</p>
                </div>
            </div>
        </a>

        <!-- Active Courses -->
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <i class="fa-solid fa-book text-2xl"></i>
                    </div>
                </div>
                <p class="text-white/80 text-sm font-medium mb-1">Active Courses</p>
                <p class="text-3xl font-bold">{{ $stats['total_active_courses'] }}</p>
                <p class="text-white/70 text-xs mt-1">Total Enrollments</p>
            </div>
        </div>

        <!-- Today's Classes -->
        <a href="{{ route('parent.schedule.daily') }}">
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
                    <p class="text-3xl font-bold">{{ $stats['today_total_classes'] }}</p>
                    <p class="text-white/70 text-xs mt-1">Scheduled Sessions</p>
                </div>
            </div>
        </a>

        <!-- Recent Reports -->
        <a href="{{ route('parent.reports.index') }}">
            <div class="group bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <i class="fa-solid fa-chart-line text-2xl"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right text-white/50 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                    <p class="text-white/80 text-sm font-medium mb-1">New Reports</p>
                    <p class="text-3xl font-bold">{{ $stats['pending_reports'] }}</p>
                    <p class="text-white/70 text-xs mt-1">Last 7 Days</p>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content - Children Cards -->
        <div class="lg:col-span-2 space-y-6">
            <!-- My Children Section -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">My Children</h2>
                    <a href="{{ route('parent.children.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition flex items-center gap-1">
                        View All <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                @if($children->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($children as $child)
                            <a href="{{ route('parent.children.show', $child->id) }}" class="group block">
                                <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-indigo-300">
                                    <!-- Child Header -->
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($child->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow">
                                            <h3 class="font-bold text-gray-800 text-lg">{{ $child->name }}</h3>
                                            <p class="text-sm text-gray-500">
                                                <i class="fa-solid fa-envelope mr-1"></i>{{ $child->email }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Child Stats Grid -->
                                    <div class="grid grid-cols-3 gap-3 mt-4">
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-3 rounded-xl text-center border border-blue-100">
                                            <p class="text-2xl font-bold text-indigo-600">{{ $child->active_courses }}</p>
                                            <p class="text-xs text-gray-600 mt-1">Courses</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-3 rounded-xl text-center border border-green-100">
                                            <p class="text-2xl font-bold text-green-600">{{ $child->today_classes }}</p>
                                            <p class="text-xs text-gray-600 mt-1">Today</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-3 rounded-xl text-center border border-purple-100">
                                            <p class="text-2xl font-bold text-purple-600">{{ $child->attendance_rate }}%</p>
                                            <p class="text-xs text-gray-600 mt-1">Attendance</p>
                                        </div>
                                    </div>

                                    <!-- Latest Report Badge -->
                                    @if($child->latest_report)
                                        <div class="mt-4 p-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-file-lines text-amber-600"></i>
                                                    <span class="text-xs font-medium text-gray-700">Latest Report</span>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $child->latest_report->report_date->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-12 rounded-3xl shadow-md text-center border border-indigo-200">
                        <div class="bg-gradient-to-br from-indigo-100 to-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-child text-indigo-600 text-3xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">No children registered yet</p>
                        <p class="text-gray-500 text-sm mt-1">Contact admin to add your children</p>
                    </div>
                @endif
            </section>

            <!-- Today's Schedule -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Today's Schedule</h2>
                    <a href="{{ route('parent.schedule.daily') }}" class="text-sm font-semibold text-green-600 hover:text-green-700 transition flex items-center gap-1">
                        View Full Schedule <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                @if($todaySchedules->count() > 0)
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="divide-y divide-gray-100">
                            @foreach($todaySchedules as $schedule)
                                <div class="p-5 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-md text-white">
                                                <i class="fa-solid fa-book-quran text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="font-bold text-gray-800">{{ $schedule->course->title ?? 'Session' }}</h4>
                                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold">
                                                        {{ $schedule->student->name }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fa-solid fa-user-tie mr-1"></i>{{ $schedule->teacher->name }}
                                                    <span class="mx-2">•</span>
                                                    <i class="fa-solid fa-clock mr-1"></i>{{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            @if($schedule->starts_at->isFuture())
                                                @php $minutesUntil = $schedule->starts_at->diffInMinutes(now()); @endphp
                                                @if($minutesUntil < 60)
                                                    <span class="px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 rounded-xl text-sm font-bold border border-green-200">
                                                        <i class="fa-solid fa-clock mr-1"></i>In {{ $minutesUntil }} min
                                                    </span>
                                                @else
                                                    <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium">
                                                        {{ $schedule->starts_at->format('g:i A') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 rounded-xl text-sm font-bold border border-blue-200">
                                                    <i class="fa-solid fa-circle-dot mr-1"></i>In Progress
                                                </span>
                                            @endif
                                        </div>
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
        <div class="space-y-6">
            <!-- Upcoming Schedule -->
            @if($upcomingSchedules->count() > 0)
                <section class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-calendar-days text-blue-600 mr-2"></i>
                        Upcoming Classes
                    </h3>
                    <div class="space-y-3">
                        @foreach($upcomingSchedules->take(5) as $schedule)
                            <div class="p-3 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl hover:from-blue-50 hover:to-indigo-50 transition border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $schedule->course->title ?? 'Session' }}</p>
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold">
                                        {{ substr($schedule->student->name, 0, 10) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600">
                                    <i class="fa-solid fa-calendar mr-1"></i>{{ $schedule->starts_at->format('M d') }}
                                    <span class="mx-1">•</span>
                                    <i class="fa-solid fa-clock mr-1"></i>{{ $schedule->starts_at->format('g:i A') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Quick Actions -->
            <section class="bg-gradient-to-br from-purple-600 via-pink-600 to-red-600 text-white p-6 rounded-3xl shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative z-10">
                    <h3 class="font-bold text-lg mb-4 flex items-center">
                        <i class="fa-solid fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('parent.schedule.weekly') }}" class="block p-3 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-calendar-week text-xl"></i>
                                <span class="font-medium">View Weekly Schedule</span>
                            </div>
                        </a>
                        <a href="{{ route('parent.reports.index') }}" class="block p-3 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-chart-line text-xl"></i>
                                <span class="font-medium">Progress Reports</span>
                            </div>
                        </a>
                        <a href="{{ route('parent.attendance.index') }}" class="block p-3 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-clipboard-check text-xl"></i>
                                <span class="font-medium">Attendance Records</span>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-dashboard-layout>
