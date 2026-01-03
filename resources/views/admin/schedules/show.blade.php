<x-dashboard-layout title="Schedule Details">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.schedules.index') }}" class="hover:text-vibrant-green">Schedules</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Schedule Details</span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Schedule Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Schedule Details</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ $schedule->status === 'scheduled' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $schedule->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $schedule->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($schedule->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Date</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->starts_at->format('l, M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Time</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Duration</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->getDurationInMinutes() }} minutes</p>
                    </div>
                    @if($schedule->zoom_link)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600 mb-1">Zoom Link</p>
                            <a href="{{ $schedule->zoom_link }}" target="_blank" class="text-blue-600 hover:underline break-all">
                                {{ $schedule->zoom_link }}
                            </a>
                        </div>
                    @endif
                    @if($schedule->notes)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600 mb-1">Notes</p>
                            <p class="text-gray-800">{{ $schedule->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Student</h3>
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($schedule->student->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $schedule->student->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $schedule->student->email }}</p>
                    </div>
                    <a href="{{ route('admin.students.show', $schedule->student->id) }}" 
                        class="text-vibrant-green hover:text-deep-blue transition">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Teacher Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Teacher</h3>
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-400 to-teal-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($schedule->teacher->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $schedule->teacher->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $schedule->teacher->email }}</p>
                    </div>
                    <a href="{{ route('admin.teachers.show', $schedule->teacher->id) }}" 
                        class="text-vibrant-green hover:text-deep-blue transition">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Course Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Course</h3>
                <h4 class="font-bold text-gray-800">{{ $schedule->course->title }}</h4>
                <p class="text-sm text-gray-600 mt-1">{{ $schedule->course->description }}</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}" 
                        class="block w-full bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-center text-sm">
                        <i class="fa-solid fa-edit mr-2"></i>Edit Schedule
                    </a>

                    @if($schedule->zoom_link)
                        <a href="{{ $schedule->zoom_link }}" target="_blank"
                            class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                            <i class="fa-solid fa-video mr-2"></i>Join Zoom
                        </a>
                    @endif

                    <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                            onclick="return confirm('Are you sure you want to delete this schedule?')">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Schedule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
