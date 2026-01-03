<x-dashboard-layout title="Student Reports">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Student Reports</h1>
                <p class="text-sm text-gray-500 mt-1">View and manage all student progress reports</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
            <i class="fa-solid fa-file-alt text-2xl opacity-80 mb-2"></i>
            <p class="text-white/80 text-sm font-medium">Total Reports</p>
            <p class="text-3xl font-bold mt-1">{{ $totalReports }}</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <i class="fa-solid fa-chart-line text-2xl opacity-80 mb-2"></i>
            <p class="text-white/80 text-sm font-medium">Average Mastery</p>
            <p class="text-3xl font-bold mt-1">{{ $averageMastery ? number_format($averageMastery, 1) . '%' : 'N/A' }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
            <i class="fa-solid fa-clock text-2xl opacity-80 mb-2"></i>
            <p class="text-white/80 text-sm font-medium">Recent (30 days)</p>
            <p class="text-3xl font-bold mt-1">{{ $recentReports }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-3xl shadow-lg p-6 mb-6 border border-gray-100">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Student -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                    <select name="student_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Teacher -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                    <select name="teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Course -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                    <select name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mastery Score Min -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Mastery Score</label>
                    <input type="number" name="mastery_min" value="{{ request('mastery_min') }}" min="0" max="100" 
                           placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Mastery Score Max -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Mastery Score</label>
                    <input type="number" name="mastery_max" value="{{ request('mastery_max') }}" min="0" max="100" 
                           placeholder="100" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.reports.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    <i class="fa-solid fa-times mr-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    @if($reports->count() > 0)
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-purple-50 to-pink-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Teacher</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mastery Score</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($reports as $report)
                            <tr class="hover:bg-purple-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $report->report_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $report->student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $report->student->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $report->teacher->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $report->course->title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $report->level ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->mastery_score !== null)
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-900">{{ $report->mastery_score }}%</span>
                                            <span class="px-2 py-1 rounded-lg text-xs font-bold
                                                @if($report->mastery_score >= 90) bg-green-100 text-green-700
                                                @elseif($report->mastery_score >= 80) bg-blue-100 text-blue-700
                                                @elseif($report->mastery_score >= 70) bg-yellow-100 text-yellow-700
                                                @elseif($report->mastery_score >= 60) bg-orange-100 text-orange-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ $report->getMasteryLevel() }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.reports.show', $report) }}" 
                                       class="text-purple-600 hover:text-purple-800 font-medium">
                                        <i class="fa-solid fa-eye mr-1"></i>View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @else
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-16 rounded-3xl shadow-md text-center border border-purple-200">
            <div class="bg-gradient-to-br from-purple-100 to-pink-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-file-alt text-purple-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Reports Found</h3>
            <p class="text-gray-600">Try adjusting your filters to see more results.</p>
        </div>
    @endif
</x-dashboard-layout>
