<x-dashboard-layout title="Teacher Attendance Profile - {{ $teacher->name }}">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('admin.attendances.index') }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium mb-2 inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to Attendances
                </a>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">{{ $teacher->name }}</h1>
                <p class="text-sm text-gray-500 mt-1"><i class="fa-solid fa-chalkboard-teacher mr-1"></i> Teacher Attendance Profile</p>
            </div>
        </div>
    </div>

    <!-- Students Summary -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Students Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($students as $student)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">{{ $student->name }}</h3>
                            <p class="text-xs text-gray-500">Student</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Total</p>
                            <p class="text-lg font-black text-gray-800">{{ $student->total_sessions }}</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-green-600 uppercase font-bold">Attended</p>
                            <p class="text-lg font-black text-green-700">{{ $student->attended_count }}</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-red-600 uppercase font-bold">Absent</p>
                            <p class="text-lg font-black text-red-700">{{ $student->absent_count }}</p>
                        </div>
                        <div class="bg-orange-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-orange-600 uppercase font-bold">Std Abs</p>
                            <p class="text-lg font-black text-orange-700">{{ $student->student_absent_count }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-gray-50 rounded-3xl p-8 text-center border border-gray-100">
                    <p class="text-gray-500">No students found for this teacher.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Detailed Log -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-gray-800 font-bold text-lg"><i class="fa-solid fa-list mr-2"></i> Session History Log</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Report / Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $attendance->schedule->starts_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $attendance->schedule->starts_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-700">{{ $attendance->schedule->course->title ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $attendance->schedule->student->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->teacher_present)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Present</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Absent</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                @if(!$attendance->student_present || !$attendance->teacher_present || $attendance->remark || $attendance->teacher_report)
                                    <div class="text-xs text-gray-600 line-clamp-2" title="{{ $attendance->remark ?? $attendance->teacher_report ?? 'No reason provided' }}">
                                        {{ $attendance->remark ?? $attendance->teacher_report ?? 'No reason provided' }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">None</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($attendances as $attendance)
                <div class="p-5 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $attendance->schedule->starts_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $attendance->schedule->starts_at->format('g:i A') }}</div>
                        </div>
                        <div>
                            @if($attendance->teacher_present)
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-green-100 text-green-700 uppercase">Present</span>
                            @else
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-red-100 text-red-700 uppercase">Absent</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3 text-sm">
                        <span class="text-gray-500">Student:</span> <span class="font-bold text-gray-800">{{ $attendance->schedule->student->name }}</span>
                    </div>

                    @if(!$attendance->student_present || !$attendance->teacher_present || $attendance->remark || $attendance->teacher_report)
                        <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3 mt-3">
                            <p class="text-[10px] font-bold text-yellow-800 uppercase mb-1">Reason / Note</p>
                            <p class="text-xs text-yellow-900">{{ $attendance->remark ?? $attendance->teacher_report ?? 'No reason provided' }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">No attendance records found.</div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $attendances->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
