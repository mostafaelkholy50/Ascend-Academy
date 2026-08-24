<x-dashboard-layout title="Absent Students Tracker">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">Absent Students Tracker</h1>
                <p class="text-sm text-gray-500 mt-1">Monitor students who have missed more than 2 consecutive classes</p>
            </div>
        </div>
    </div>


    <!-- Table -->
    @if($absentStudents->count() > 0)
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-red-50 to-orange-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact Info</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Consecutive Absences</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($absentStudents as $student)
                            <tr class="hover:bg-red-50 transition">
                                <td class="px-4 py-4 min-w-[150px]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-100 to-orange-100 flex items-center justify-center text-red-600 font-bold text-xs">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 leading-tight">{{ $student->name }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-[12px] text-gray-600 font-medium">
                                        <i class="fa-solid fa-envelope mr-1 text-gray-400"></i> {{ $student->email }}
                                    </div>
                                    @if($student->phone)
                                    <div class="text-[12px] text-gray-600 font-medium mt-1">
                                        <i class="fa-solid fa-phone mr-1 text-gray-400"></i> {{ $student->phone }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-tight bg-red-100 text-red-700">
                                        {{ $student->consecutive_absences }} Classes
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.students.show', $student->id) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-200 transition-all duration-300 text-xs font-bold"
                                       title="View Student Profile">
                                        <i class="fa-solid fa-user mr-1.5"></i> View Profile
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-16 rounded-3xl shadow-md text-center border border-green-200">
            <div class="bg-gradient-to-br from-green-100 to-emerald-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Students At Risk</h3>
            <p class="text-gray-600">Great news! No student has missed more than 2 consecutive classes recently.</p>
        </div>
    @endif
</x-dashboard-layout>
