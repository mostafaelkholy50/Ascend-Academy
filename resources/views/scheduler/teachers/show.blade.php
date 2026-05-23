<x-dashboard-layout title="Teacher Details">
    <div class="mb-6">
        <a href="{{ route('scheduler.teachers.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Teachers
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Teacher Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Teacher Information</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $teacher->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $teacher->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Full Name</p>
                        <p class="font-semibold text-gray-800">{{ $teacher->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="font-semibold text-gray-800">{{ $teacher->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Phone</p>
                        <p class="font-semibold text-gray-800">{{ $teacher->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Gender</p>
                        <p class="font-semibold text-gray-800">{{ ucfirst($teacher->gender) ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Timezone</p>
                        <p class="font-semibold text-gray-800">{{ $teacher->getUserTimezone() }}</p>
                    </div>
                </div>
            </div>

            <!-- Class Schedule -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Assigned Classes</h2>
                    <a href="{{ route('scheduler.schedules.index', ['teacher_id' => $teacher->id]) }}" class="text-vibrant-green hover:underline text-sm font-semibold">
                        View Full Schedule
                    </a>
                </div>
                @if($teacher->teacherSchedules && $teacher->teacherSchedules->count() > 0)
                    <div class="space-y-3">
                        @foreach($teacher->teacherSchedules->take(10) as $schedule)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $schedule->day_of_week }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->starts_at->format('h:i A') }} - {{ $schedule->ends_at->format('h:i A') }}</p>
                                </div>
                                @if($schedule->student)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Student:</p>
                                        <p class="font-semibold text-gray-800">{{ $schedule->student->name }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-4">No classes assigned</p>
                @endif
            </div>

            <!-- Recent Reports -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Recent Student Reports</h2>
                @if($teacher->teacherReports && $teacher->teacherReports->count() > 0)
                    <div class="space-y-4">
                        @foreach($teacher->teacherReports->take(5) as $report)
                            <div class="border-l-4 border-vibrant-green pl-4 py-2 bg-gray-50 rounded-r-xl p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $report->title ?? 'Report' }}</p>
                                        <p class="text-sm text-gray-600 mt-1">Student: {{ $report->student->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 mt-2">{{ $report->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-4">No reports submitted yet</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('scheduler.schedules.create', ['teacher_id' => $teacher->id]) }}" class="block w-full bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-center text-sm">
                        <i class="fa-solid fa-calendar-plus mr-2"></i>Assign Schedule
                    </a>
                    <a href="{{ route('scheduler.availability', $teacher->id) }}" class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-clock mr-2"></i>Manage Availability
                    </a>
                    @if($teacher->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $teacher->phone) }}" target="_blank"
                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm font-bold">
                            <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp Message
                        </a>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Teaching Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Classes Conducted:</span>
                        <span class="text-lg font-bold text-vibrant-green">{{ $teacher->teacherAttendances->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Active Students:</span>
                        @php 
                            $studentCount = \App\Models\Schedule::where('teacher_id', $teacher->id)->distinct('student_id')->count();
                        @endphp
                        <span class="text-lg font-bold text-blue-600">{{ $studentCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
