<x-dashboard-layout title="My Reports">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">My Reports</h1>
                <p class="text-gray-600 text-sm">Manage student progress reports</p>
            </div>
            <a href="{{ route('teacher.reports.create') }}" class="bg-vibrant-green text-white px-6 py-3 rounded-xl hover:bg-deep-blue transition font-semibold shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i>Create New Report
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('teacher.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Student Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                <select name="student_id" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Course Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                <select name="course_id" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition font-semibold">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('teacher.reports.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-semibold">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        @if($reports->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mastery</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($report->student->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $report->student->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $report->student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $report->course->title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $report->level ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->mastery_score)
                                        <div class="flex items-center">
                                            <span class="text-sm font-semibold text-gray-900 mr-2">{{ $report->mastery_score }}%</span>
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-vibrant-green h-2 rounded-full" style="width: {{ $report->mastery_score }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $report->report_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('teacher.reports.show', $report->id) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('teacher.reports.edit', $report->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form action="{{ route('teacher.reports.destroy', $report->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this report?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reports->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-file-alt text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg font-medium">No reports found</p>
                <p class="text-gray-400 text-sm mb-4">Create your first report to get started</p>
                <a href="{{ route('teacher.reports.create') }}" class="inline-block bg-vibrant-green text-white px-6 py-3 rounded-xl hover:bg-deep-blue transition font-semibold">
                    <i class="fa-solid fa-plus mr-2"></i>Create Report
                </a>
            </div>
        @endif
    </div>
</x-dashboard-layout>
