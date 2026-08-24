<x-dashboard-layout title="Attendance Records">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Attendance Records</h1>
                <p class="text-sm text-gray-500 mt-1">View and manage all attendance records</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards: Students & Teachers (Current Month or Filtered Period) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        
        <!-- Students Section -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-white text-lg font-bold"><i class="fa-solid fa-user-graduate mr-2"></i> Student Statistics</h2>
                    <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full font-medium">Total Sessions: {{ $studentStats['total'] }}</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4">
                    <!-- Attended -->
                    <div class="bg-green-50 rounded-2xl p-4 border border-green-100 text-center flex flex-col justify-center transition hover:shadow-md">
                        <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Attended</p>
                        <p class="text-2xl font-black text-green-700">{{ $studentStats['attended'] }}</p>
                    </div>
                    <!-- Student Absent -->
                    <div class="bg-red-50 rounded-2xl p-4 border border-red-100 text-center flex flex-col justify-center transition hover:shadow-md">
                        <i class="fa-solid fa-user-xmark text-red-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Absent (Student)</p>
                        <p class="text-2xl font-black text-red-700">{{ $studentStats['absent'] }}</p>
                    </div>
                    <!-- Teacher Absent -->
                    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100 text-center flex flex-col justify-center transition hover:shadow-md">
                        <i class="fa-solid fa-chalkboard-user text-orange-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Absent (Teacher)</p>
                        <p class="text-2xl font-black text-orange-700">{{ $studentStats['teacher_absent'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers Section -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-white text-lg font-bold"><i class="fa-solid fa-chalkboard-teacher mr-2"></i> Teacher Statistics</h2>
                    <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full font-medium">Total Sessions: {{ $teacherStats['total'] }}</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4">
                    <!-- Attended -->
                    <div class="bg-green-50 rounded-2xl p-4 border border-green-100 text-center flex flex-col justify-center transition hover:shadow-md">
                        <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Attended</p>
                        <p class="text-2xl font-black text-green-700">{{ $teacherStats['attended'] }}</p>
                    </div>
                    <!-- Teacher Absent -->
                    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100 text-center flex flex-col justify-center transition hover:shadow-md">
                        <i class="fa-solid fa-user-xmark text-orange-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Absent (Teacher)</p>
                        <p class="text-2xl font-black text-orange-700">{{ $teacherStats['absent'] }}</p>
                    </div>
                    <!-- Student Absent -->
                    <div class="bg-red-50 rounded-2xl p-4 border border-red-100 text-center flex flex-col justify-center transition hover:shadow-md">
                        <i class="fa-solid fa-user-graduate text-red-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Absent (Student)</p>
                        <p class="text-2xl font-black text-red-700">{{ $teacherStats['student_absent'] }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Filters -->
    <div class="bg-white rounded-3xl shadow-lg p-6 mb-6 border border-gray-100">
        <form method="GET" action="{{ route('admin.attendances.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="both_present" {{ request('status') == 'both_present' ? 'selected' : '' }}>Both Present</option>
                        <option value="student_absent" {{ request('status') == 'student_absent' ? 'selected' : '' }}>Student Absent</option>
                        <option value="teacher_absent" {{ request('status') == 'teacher_absent' ? 'selected' : '' }}>Teacher Absent</option>
                        <option value="both_absent" {{ request('status') == 'both_absent' ? 'selected' : '' }}>Both Absent</option>
                    </select>
                </div>

                <!-- Student -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                    <select name="student_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                    <select name="teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                    <select name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.attendances.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    <i class="fa-solid fa-times mr-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    @if($attendances->count() > 0)
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Teacher</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Student Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Teacher Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($attendances as $attendance)
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">
                                        {{ $attendance->schedule->starts_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 font-medium">
                                        {{ $attendance->schedule->starts_at->format('g:i A') }} - {{ $attendance->schedule->ends_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 min-w-[150px]">
                                    <div class="text-sm font-bold text-gray-900 leading-tight">{{ $attendance->student->name }}</div>
                                    <div class="text-[10px] text-gray-400 break-all">{{ $attendance->student->email }}</div>
                                </td>
                                <td class="px-4 py-4 min-w-[150px]">
                                    <div class="text-sm font-bold text-gray-900 leading-tight">{{ $attendance->teacher->name }}</div>
                                    <div class="text-[10px] text-gray-400 break-all">{{ $attendance->teacher->email }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-700 leading-tight">{{ $attendance->schedule->course->title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-tight {{ $attendance->student_present ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $attendance->student_present ? 'Present' : 'Absent' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-tight {{ $attendance->teacher_present ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $attendance->teacher_present ? 'Present' : 'Absent' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.attendances.show', $attendance) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300"
                                       title="View Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $attendances->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-16 rounded-3xl shadow-md text-center border border-blue-200">
            <div class="bg-gradient-to-br from-blue-100 to-indigo-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-clipboard-list text-blue-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Attendance Records Found</h3>
            <p class="text-gray-600">Try adjusting your filters to see more results.</p>
        </div>
    @endif
</x-dashboard-layout>
