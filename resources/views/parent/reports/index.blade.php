<x-dashboard-layout title="Progress Reports">
    @php $parent = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">Progress Reports</h1>
        <p class="text-gray-600">View your children's progress reports from teachers</p>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-md mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Child Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Child</label>
                <select name="child_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="all" {{ request('child_id') == 'all' ? 'selected' : '' }}>All Children</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ request('child_id') == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Course Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                <select name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        @if($course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('parent.reports.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    @if($reports->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($reports as $report)
                <a href="{{ route('parent.reports.show', $report->id) }}" class="group block">
                    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-purple-300">
                        <!-- Report Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                                    <i class="fa-solid fa-file-lines text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $report->course->title ?? 'General Report' }}</h3>
                                    <p class="text-sm text-gray-500">
                                        <i class="fa-solid fa-child mr-1"></i>{{ $report->student->name }}
                                    </p>
                                </div>
                            </div>
                            @if($report->mastery_score)
                                <span class="px-3 py-1 rounded-xl text-sm font-bold {{ $report->mastery_score >= 80 ? 'bg-green-100 text-green-700' : ($report->mastery_score >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $report->mastery_score }}%
                                </span>
                            @endif
                        </div>

                        <!-- Report Info -->
                        <div class="space-y-2 mb-4">
                            <p class="text-sm text-gray-600">
                                <i class="fa-solid fa-user-tie mr-2 text-purple-600"></i>
                                <span class="font-medium">Teacher:</span> {{ $report->teacher->name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fa-solid fa-calendar mr-2 text-purple-600"></i>
                                <span class="font-medium">Date:</span> {{ $report->report_date->format('M d, Y') }}
                            </p>
                        </div>

                        <!-- Report Preview -->
                        @if($report->notes)
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <p class="text-sm text-gray-700 line-clamp-2">{{ $report->notes }}</p>
                            </div>
                        @endif

                        <!-- View Button -->
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center gap-2 text-purple-600 font-semibold group-hover:gap-3 transition-all">
                                View Full Report <i class="fa-solid fa-arrow-right text-sm"></i>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @else
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-16 rounded-3xl shadow-md text-center border border-purple-200">
            <div class="bg-gradient-to-br from-purple-100 to-pink-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-file-lines text-purple-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Reports Found</h3>
            <p class="text-gray-600">No progress reports match your current filters.</p>
        </div>
    @endif
</x-dashboard-layout>
