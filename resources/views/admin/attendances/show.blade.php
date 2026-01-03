<x-dashboard-layout title="Attendance Details">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.attendances.index') }}" 
               class="w-10 h-10 bg-white rounded-xl shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Attendance Details</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $attendance->schedule->starts_at->format('l, F d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Schedule Information -->
        <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Schedule Information</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-clock text-blue-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Time</p>
                        <p class="font-semibold text-gray-800">
                            {{ $attendance->schedule->starts_at->format('g:i A') }} - {{ $attendance->schedule->ends_at->format('g:i A') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-hourglass-half text-blue-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-semibold text-gray-800">{{ $attendance->schedule->getDurationInMinutes() }} minutes</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-info-circle text-blue-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="px-3 py-1 rounded-lg text-sm font-bold inline-block
                            @if($attendance->schedule->status == 'completed') bg-green-100 text-green-700
                            @elseif($attendance->schedule->status == 'scheduled') bg-blue-100 text-blue-700
                            @elseif($attendance->schedule->status == 'cancelled') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($attendance->schedule->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Information -->
        <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-book text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Course Information</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-book-quran text-green-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Course Title</p>
                        <p class="font-semibold text-gray-800">{{ $attendance->schedule->course->title ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($attendance->schedule->course && $attendance->schedule->course->description)
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-align-left text-green-600 mt-1"></i>
                        <div>
                            <p class="text-sm text-gray-500">Description</p>
                            <p class="text-gray-700">{{ Str::limit($attendance->schedule->course->description, 150) }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Student Information</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-user text-purple-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-semibold text-gray-800">{{ $attendance->student->name }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-envelope text-purple-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-700">{{ $attendance->student->email }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-clipboard-check text-purple-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Attendance Status</p>
                        <span class="px-4 py-2 rounded-lg text-sm font-bold inline-block {{ $attendance->student_present ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <i class="fa-solid {{ $attendance->student_present ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            {{ $attendance->student_present ? 'Present' : 'Absent' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Information -->
        <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chalkboard-teacher text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Teacher Information</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-user-tie text-amber-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-semibold text-gray-800">{{ $attendance->teacher->name }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-envelope text-amber-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-700">{{ $attendance->teacher->email }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-clipboard-check text-amber-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Attendance Status</p>
                        <span class="px-4 py-2 rounded-lg text-sm font-bold inline-block {{ $attendance->teacher_present ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <i class="fa-solid {{ $attendance->teacher_present ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            {{ $attendance->teacher_present ? 'Present' : 'Absent' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remarks Section -->
    @if($attendance->remark)
        <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100 mt-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-comment text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Remarks</h2>
            </div>
            <p class="text-gray-700 leading-relaxed">{{ $attendance->remark }}</p>
        </div>
    @endif

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('admin.attendances.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Attendance List
        </a>
    </div>
</x-dashboard-layout>
