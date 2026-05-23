<x-dashboard-layout title="Manage Attendance">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Attendance Records</h2>
        <a href="{{ route('scheduler.attendance.create') }}" class="bg-vibrant-green text-white px-6 py-2.5 rounded-xl hover:bg-deep-blue transition shadow-md flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Mark Attendance
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left">Date</th>
                        <th class="px-6 py-4 text-left">Student</th>
                        <th class="px-6 py-4 text-left">Teacher</th>
                        <th class="px-6 py-4 text-left">Attendance</th>
                        <th class="px-6 py-4 text-left">Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $attendance->schedule->starts_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $attendance->schedule->starts_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $attendance->student->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $attendance->teacher->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <span class="flex items-center text-[10px] font-bold">
                                        <i class="fa-solid {{ $attendance->student_present ? 'fa-circle-check text-green-500' : 'fa-circle-xmark text-red-500' }} mr-1"></i>
                                        STUDENT: {{ $attendance->student_present ? 'PRESENT' : 'ABSENT' }}
                                    </span>
                                    <span class="flex items-center text-[10px] font-bold">
                                        <i class="fa-solid {{ $attendance->teacher_present ? 'fa-circle-check text-green-500' : 'fa-circle-xmark text-red-500' }} mr-1"></i>
                                        TEACHER: {{ $attendance->teacher_present ? 'PRESENT' : 'ABSENT' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 italic text-xs">
                                {{ $attendance->remark ?? 'No remarks' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
    </div>
</x-dashboard-layout>
