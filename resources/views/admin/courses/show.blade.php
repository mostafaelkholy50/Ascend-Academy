<x-dashboard-layout title="Course Details">
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Courses
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Course Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Course Information</h2>
                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="text-vibrant-green hover:text-deep-blue">
                        <i class="fa-solid fa-edit mr-1"></i>Edit
                    </a>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Title</label>
                        <p class="text-gray-800 text-lg font-semibold">{{ $course->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Description</label>
                        <p class="text-gray-800 whitespace-pre-line">{{ $course->description }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Duration</label>
                            <p class="text-gray-800">{{ $course->duration_weeks }} weeks</p>
                        </div>

                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-semibold">Created:</span> {{ $course->created_at->format('M d, Y') }}
                        </div>
                        <div>
                            <span class="font-semibold">Last Updated:</span> {{ $course->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Students -->
            @if($course->enrollments && $course->enrollments->count() > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Enrolled Students ({{ $course->enrollments->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($course->enrollments->take(10) as $enrollment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($enrollment->student->name ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $enrollment->student->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $enrollment->student->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs px-2 py-1 rounded-full
                                        {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $enrollment->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($enrollment->status ?? 'N/A') }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Started: {{ $enrollment->start_date ? $enrollment->start_date->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        @if($course->enrollments->count() > 10)
                            <p class="text-sm text-gray-500 text-center">+{{ $course->enrollments->count() - 10 }} more students</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Schedules -->
            @if($course->schedules && $course->schedules->count() > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Class Schedules ({{ $course->schedules->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($course->schedules->take(10) as $schedule)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $schedule->day_of_week ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $schedule->start_time ?? 'N/A' }} - {{ $schedule->end_time ?? 'N/A' }}
                                    </p>
                                </div>
                                @if(isset($schedule->status))
                                    <span class="text-xs px-2 py-1 rounded-full
                                        {{ $schedule->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $schedule->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $schedule->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Enrollments:</span>
                        <span class="text-lg font-bold text-vibrant-green">{{ $totalStudents ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Active Enrollments:</span>
                        <span class="text-lg font-bold text-green-600">{{ $activeEnrollments ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed:</span>
                        <span class="text-lg font-bold text-blue-600">{{ $completedEnrollments ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Schedules:</span>
                        <span class="text-lg font-bold text-purple-600">{{ $course->schedules ? $course->schedules->count() : 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.courses.edit', $course->id) }}"
                        class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-edit mr-2"></i>Edit Course
                    </a>

                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                            onclick="return confirm('Are you sure? This will delete the course and all related data.')">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
