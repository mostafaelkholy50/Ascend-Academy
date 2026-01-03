<x-dashboard-layout title="My Progress Reports">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-vibrant-green to-deep-blue bg-clip-text text-transparent">
                    My Progress Reports
                </h1>
                <p class="text-gray-600 text-sm mt-1">Track your academic journey and teacher feedback</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-vibrant-green/10 to-deep-blue/10 px-4 py-2 rounded-xl">
                    <span class="text-sm font-semibold text-gray-700">{{ $reports->total() }} Total Reports</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-vibrant-green text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-vibrant-green mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex items-center mb-4">
            <i class="fa-solid fa-filter text-vibrant-green mr-2"></i>
            <h3 class="text-lg font-bold text-gray-800">Filter Reports</h3>
        </div>
        <form method="GET" action="{{ route('student.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Course Filter -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-book text-xs mr-1"></i>Course
                </label>
                <select name="course_id" class="w-full rounded-xl border-gray-300 focus:border-vibrant-green focus:ring-2 focus:ring-vibrant-green/20 transition-all duration-200">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Teacher Filter -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-user-tie text-xs mr-1"></i>Teacher
                </label>
                <select name="teacher_id" class="w-full rounded-xl border-gray-300 focus:border-vibrant-green focus:ring-2 focus:ring-vibrant-green/20 transition-all duration-200">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-calendar-day text-xs mr-1"></i>From Date
                </label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border-gray-300 focus:border-vibrant-green focus:ring-2 focus:ring-vibrant-green/20 transition-all duration-200">
            </div>

            <!-- Date To -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-calendar-check text-xs mr-1"></i>To Date
                </label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border-gray-300 focus:border-vibrant-green focus:ring-2 focus:ring-vibrant-green/20 transition-all duration-200">
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="bg-gradient-to-r from-vibrant-green to-emerald-600 text-white px-8 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold">
                    <i class="fa-solid fa-search mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('student.reports.index') }}" class="bg-gray-100 text-gray-700 px-8 py-3 rounded-xl hover:bg-gray-200 hover:shadow-md transition-all duration-200 font-semibold">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    @if($reports->count() > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach($reports as $report)
                <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-vibrant-green/30">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            <!-- Left Section: Teacher & Course Info -->
                            <div class="flex items-start gap-4 flex-1">
                                <!-- Teacher Avatar -->
                                <div class="relative">
                                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-green-400 via-emerald-500 to-blue-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        {{ strtoupper(substr($report->teacher->name, 0, 1)) }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 bg-vibrant-green rounded-full p-1">
                                        <i class="fa-solid fa-check text-white text-xs"></i>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-lg font-bold text-gray-800">{{ $report->teacher->name }}</h3>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Teacher</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $report->teacher->email }}</p>
                                    
                                    @if($report->course)
                                        <div class="flex items-center gap-2 text-sm">
                                            <div class="bg-gradient-to-r from-purple-100 to-pink-100 px-3 py-1 rounded-lg">
                                                <i class="fa-solid fa-book text-purple-600 mr-1"></i>
                                                <span class="font-semibold text-purple-700">{{ $report->course->title }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Middle Section: Stats -->
                            <div class="flex flex-wrap lg:flex-nowrap gap-4 lg:gap-6">
                                <!-- Level -->
                                @if($report->level)
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 mb-1 font-medium">Level</p>
                                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 px-4 py-2 rounded-xl border border-amber-200">
                                            <span class="text-sm font-bold text-amber-700">{{ $report->level }}</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Mastery Score -->
                                @if($report->mastery_score)
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 mb-1 font-medium">Mastery</p>
                                        <div class="relative">
                                            <div class="flex items-center gap-2">
                                                <span class="text-2xl font-bold {{ $report->mastery_score >= 80 ? 'text-vibrant-green' : ($report->mastery_score >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
                                                    {{ $report->mastery_score }}%
                                                </span>
                                            </div>
                                            <div class="w-24 bg-gray-200 rounded-full h-2 mt-2">
                                                <div class="h-2 rounded-full {{ $report->mastery_score >= 80 ? 'bg-gradient-to-r from-green-400 to-emerald-500' : ($report->mastery_score >= 60 ? 'bg-gradient-to-r from-yellow-400 to-orange-400' : 'bg-gradient-to-r from-red-400 to-pink-500') }}" 
                                                     style="width: {{ $report->mastery_score }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 mb-1 font-medium">Mastery</p>
                                        <span class="text-sm text-gray-400">N/A</span>
                                    </div>
                                @endif

                                <!-- Date -->
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1 font-medium">Report Date</p>
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 px-4 py-2 rounded-xl border border-blue-200">
                                        <i class="fa-solid fa-calendar text-blue-600 mr-1"></i>
                                        <span class="text-sm font-bold text-blue-700">{{ $report->report_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Section: Action -->
                            <div class="flex items-center">
                                <a href="{{ route('student.reports.show', $report->id) }}" 
                                   class="group/btn bg-gradient-to-r from-vibrant-green to-emerald-600 text-white px-6 py-3 rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-200 font-semibold flex items-center gap-2">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>View Details</span>
                                    <i class="fa-solid fa-arrow-right group-hover/btn:translate-x-1 transition-transform duration-200"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $reports->links() }}
        </div>
    @else
        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl shadow-lg p-16 text-center border border-gray-200">
            <div class="max-w-md mx-auto">
                <div class="bg-gradient-to-br from-blue-100 to-purple-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-file-alt text-blue-600 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">No Reports Yet</h3>
                <p class="text-gray-600 mb-6">Your teachers haven't created any progress reports yet. Check back soon to track your academic journey!</p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('student.dashboard') }}" class="bg-gradient-to-r from-vibrant-green to-emerald-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold">
                        <i class="fa-solid fa-home mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-dashboard-layout>
