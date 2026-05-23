<x-dashboard-layout title="Student Details">
    <div class="mb-6">
        <a href="{{ route('scheduler.students.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Students
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Student Information</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $student->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $student->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Full Name</p>
                        <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="font-semibold text-gray-800">{{ $student->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Phone</p>
                        <p class="font-semibold text-gray-800">{{ $student->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Gender</p>
                        <p class="font-semibold text-gray-800">{{ ucfirst($student->gender) ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Date of Birth</p>
                        <p class="font-semibold text-gray-800">{{ $student->birth_date ? $student->birth_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Timezone</p>
                        <p class="font-semibold text-gray-800">{{ $student->getUserTimezone() }}</p>
                    </div>
                </div>
            </div>

            <!-- Parents List -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Parents ({{ $student->parents ? $student->parents->count() : 0 }})</h2>

                @if($student->parents && $student->parents->count() > 0)
                    <div class="space-y-4">
                        @foreach($student->parents as $parent)
                            <div class="border border-gray-100 rounded-xl p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-full bg-vibrant-green/10 flex items-center justify-center text-vibrant-green text-lg font-bold">
                                        {{ strtoupper(substr($parent->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $parent->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $parent->email }}</p>
                                        @if($parent->phone)
                                            <p class="text-xs text-gray-500">{{ $parent->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-4">No parents linked</p>
                @endif
            </div>

            <!-- Enrolled Courses -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Enrolled Courses ({{ $student->enrollments ? $student->enrollments->count() : 0 }})</h2>

                @if($student->enrollments && $student->enrollments->count() > 0)
                    <div class="space-y-4">
                        @foreach($student->enrollments as $enrollment)
                            <div class="border border-gray-100 rounded-xl p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $enrollment->course->title }}</h3>
                                        <div class="flex items-center gap-4 mt-2">
                                            <span class="text-xs px-2 py-1 rounded-full
                                                {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $enrollment->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Started: {{ $enrollment->start_date ? $enrollment->start_date->format('M d, Y') : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-4">No courses enrolled</p>
                @endif
            </div>

            <!-- Class Schedule -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Class Schedule</h2>
                    <a href="{{ route('scheduler.schedules.index', ['student_id' => $student->id]) }}" class="text-vibrant-green hover:underline text-sm font-semibold">
                        View Full Schedule
                    </a>
                </div>
                @if($student->schedules && $student->schedules->count() > 0)
                    <div class="space-y-3">
                        @foreach($student->schedules->take(10) as $schedule)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $schedule->day_of_week }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->starts_at->format('h:i A') }} - {{ $schedule->ends_at->format('h:i A') }}</p>
                                </div>
                                @if($schedule->teacher)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Teacher:</p>
                                        <p class="font-semibold text-gray-800">{{ $schedule->teacher->name }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-4">No classes scheduled</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('scheduler.schedules.create', ['student_id' => $student->id]) }}" class="block w-full bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-center text-sm">
                        <i class="fa-solid fa-calendar-plus mr-2"></i>Add Schedule
                    </a>
                    @if($student->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone) }}" target="_blank"
                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm font-bold">
                            <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp Message
                        </a>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Student Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Classes:</span>
                        <span class="text-lg font-bold text-vibrant-green">{{ $student->attendances->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Present Rate:</span>
                        @php 
                            $total = $student->attendances->count();
                            $present = $student->attendances->where('status', 'present')->count();
                            $rate = $total > 0 ? round(($present / $total) * 100) : 0;
                        @endphp
                        <span class="text-lg font-bold text-blue-600">{{ $rate }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
