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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

    <!-- Data Tables Grid -->
    <div class="grid grid-cols-1 gap-8">
        
        <!-- Teachers List Table -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden flex flex-col">
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-emerald-100">
                <h3 class="text-emerald-800 font-bold text-lg"><i class="fa-solid fa-chalkboard-teacher mr-2"></i> Teachers Attendance</h3>
            </div>
            <div class="hidden md:block overflow-x-auto flex-1">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Teacher Name</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sessions</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Attended</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Absent</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Student Abs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($teachersList as $teacher)
                            <tr class="hover:bg-emerald-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $teacher->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $teacher->email }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">{{ $teacher->total_sessions }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">{{ $teacher->attended_count }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600">{{ $teacher->absent_count }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-orange-500">{{ $teacher->student_absent_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No teachers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View (Cards) -->
            <div class="md:hidden divide-y divide-gray-100 flex-1">
                @forelse($teachersList as $teacher)
                    <div class="p-4 hover:bg-emerald-50/50 transition">
                        <div class="mb-3">
                            <div class="text-sm font-bold text-gray-900">{{ $teacher->name }}</div>
                            <div class="text-xs text-gray-500">{{ $teacher->email }}</div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                <p class="text-[9px] text-gray-500 uppercase font-bold mb-1">Total</p>
                                <p class="text-sm font-bold text-gray-700">{{ $teacher->total_sessions }}</p>
                            </div>
                            <div class="bg-green-50 p-2 rounded-lg border border-green-100">
                                <p class="text-[9px] text-green-600 uppercase font-bold mb-1">Attended</p>
                                <p class="text-sm font-bold text-green-700">{{ $teacher->attended_count }}</p>
                            </div>
                            <div class="bg-red-50 p-2 rounded-lg border border-red-100">
                                <p class="text-[9px] text-red-600 uppercase font-bold mb-1">Absent</p>
                                <p class="text-sm font-bold text-red-700">{{ $teacher->absent_count }}</p>
                            </div>
                            <div class="bg-orange-50 p-2 rounded-lg border border-orange-100">
                                <p class="text-[9px] text-orange-600 uppercase font-bold mb-1">Std Abs</p>
                                <p class="text-sm font-bold text-orange-700">{{ $teacher->student_absent_count }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">No teachers found.</div>
                @endforelse
            </div>
            <!-- Pagination -->
            @if($teachersList->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $teachersList->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>

        <!-- Students List Table -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden flex flex-col">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-100">
                <h3 class="text-blue-800 font-bold text-lg"><i class="fa-solid fa-user-graduate mr-2"></i> Students Attendance</h3>
            </div>
            <div class="hidden md:block overflow-x-auto flex-1">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student Name</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sessions</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Attended</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Absent</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Teacher Abs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($studentsList as $student)
                            <tr class="hover:bg-blue-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $student->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->email }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">{{ $student->total_sessions }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">{{ $student->attended_count }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600">{{ $student->absent_count }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-orange-500">{{ $student->teacher_absent_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View (Cards) -->
            <div class="md:hidden divide-y divide-gray-100 flex-1">
                @forelse($studentsList as $student)
                    <div class="p-4 hover:bg-blue-50/50 transition">
                        <div class="mb-3">
                            <div class="text-sm font-bold text-gray-900">{{ $student->name }}</div>
                            <div class="text-xs text-gray-500">{{ $student->email }}</div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                <p class="text-[9px] text-gray-500 uppercase font-bold mb-1">Total</p>
                                <p class="text-sm font-bold text-gray-700">{{ $student->total_sessions }}</p>
                            </div>
                            <div class="bg-green-50 p-2 rounded-lg border border-green-100">
                                <p class="text-[9px] text-green-600 uppercase font-bold mb-1">Attended</p>
                                <p class="text-sm font-bold text-green-700">{{ $student->attended_count }}</p>
                            </div>
                            <div class="bg-red-50 p-2 rounded-lg border border-red-100">
                                <p class="text-[9px] text-red-600 uppercase font-bold mb-1">Absent</p>
                                <p class="text-sm font-bold text-red-700">{{ $student->absent_count }}</p>
                            </div>
                            <div class="bg-orange-50 p-2 rounded-lg border border-orange-100">
                                <p class="text-[9px] text-orange-600 uppercase font-bold mb-1">Tch Abs</p>
                                <p class="text-sm font-bold text-orange-700">{{ $student->teacher_absent_count }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">No students found.</div>
                @endforelse
            </div>
            <!-- Pagination -->
            @if($studentsList->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $studentsList->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>

    </div>
</x-dashboard-layout>
