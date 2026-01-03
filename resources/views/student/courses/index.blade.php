<x-dashboard-layout title="My Courses">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent">
                    My Courses
                </h1>
                <p class="text-gray-600 text-sm mt-1">Track your learning journey and progress</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-purple-100 to-pink-100 px-4 py-2 rounded-xl border border-purple-200">
                    <span class="text-sm font-semibold text-purple-700">{{ $enrollments->count() }} {{ $enrollments->count() === 1 ? 'Course' : 'Courses' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($enrollments->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($enrollments as $enrollment)
                <div class="group bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-purple-300">
                    <!-- Course Image Header -->
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-purple-400 via-pink-500 to-red-500">
                        @if($enrollment->course && $enrollment->course->photo)
                            <img src="{{ $enrollment->course->getPhotoUrl() }}" 
                                 alt="{{ $enrollment->course->title ?? 'Course' }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fa-solid fa-book text-white/30 text-6xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            @if($enrollment->status === 'active')
                                <span class="px-4 py-2 bg-gradient-to-r from-green-400 to-emerald-500 text-white rounded-xl font-bold text-sm shadow-lg backdrop-blur-sm">
                                    <i class="fa-solid fa-circle-check mr-1"></i>Active
                                </span>
                            @elseif($enrollment->status === 'completed')
                                <span class="px-4 py-2 bg-gradient-to-r from-blue-400 to-indigo-500 text-white rounded-xl font-bold text-sm shadow-lg backdrop-blur-sm">
                                    <i class="fa-solid fa-trophy mr-1"></i>Completed
                                </span>
                            @else
                                <span class="px-4 py-2 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-xl font-bold text-sm shadow-lg backdrop-blur-sm">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            @endif
                        </div>

                        <!-- Course Level Badge -->
                        @if($enrollment->course && $enrollment->course->level)
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-purple-700 rounded-lg font-bold text-xs">
                                    {{ $enrollment->course->level }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Course Content -->
                    <div class="p-6">
                        <!-- Course Title -->
                        <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-purple-600 transition-colors duration-200">
                            {{ $enrollment->course->title ?? 'N/A' }}
                        </h3>

                        <!-- Course Description -->
                        @if($enrollment->course && $enrollment->course->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                {{ $enrollment->course->description }}
                            </p>
                        @endif

                        <!-- Progress Section -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Overall Progress</span>
                                <span class="text-lg font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                    {{ $enrollment->progress }}%
                                </span>
                            </div>
                            <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 rounded-full transition-all duration-1000"
                                     style="width: {{ $enrollment->progress }}%"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent"></div>
                            </div>
                        </div>

                        <!-- Statistics Grid -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <!-- Total Sessions -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-3 border border-blue-200">
                                <div class="text-center">
                                    <i class="fa-solid fa-calendar-days text-blue-600 mb-1"></i>
                                    <p class="text-2xl font-bold text-blue-700">{{ $enrollment->total_sessions }}</p>
                                    <p class="text-xs text-blue-600 font-medium">Total</p>
                                </div>
                            </div>

                            <!-- Completed Sessions -->
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-3 border border-green-200">
                                <div class="text-center">
                                    <i class="fa-solid fa-check-circle text-green-600 mb-1"></i>
                                    <p class="text-2xl font-bold text-green-700">{{ $enrollment->completed_sessions }}</p>
                                    <p class="text-xs text-green-600 font-medium">Done</p>
                                </div>
                            </div>

                            <!-- Upcoming Sessions -->
                            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-3 border border-amber-200">
                                <div class="text-center">
                                    <i class="fa-solid fa-clock text-amber-600 mb-1"></i>
                                    <p class="text-2xl font-bold text-amber-700">{{ $enrollment->upcoming_sessions }}</p>
                                    <p class="text-xs text-amber-600 font-medium">Upcoming</p>
                                </div>
                            </div>
                        </div>

                        <!-- Average Mastery Score -->
                        @if($enrollment->average_mastery)
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 mb-4 border border-purple-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-2 rounded-lg">
                                            <i class="fa-solid fa-star text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 font-medium">Average Mastery</p>
                                            <p class="text-lg font-bold text-purple-700">{{ $enrollment->average_mastery }}%</p>
                                        </div>
                                    </div>
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-purple-400 to-pink-500 h-2 rounded-full" 
                                             style="width: {{ $enrollment->average_mastery }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Next Session Info -->
                        @if($enrollment->next_session)
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-xl">
                                        <i class="fa-solid fa-calendar-check text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Next Session</p>
                                        <p class="text-sm font-bold text-gray-800">
                                            {{ $enrollment->next_session->starts_at->format('M d, Y') }} at {{ $enrollment->next_session->starts_at->format('g:i A') }}
                                        </p>
                                        @if($enrollment->next_session->teacher)
                                            <p class="text-xs text-gray-600 mt-1">
                                                <i class="fa-solid fa-user-tie mr-1"></i>{{ $enrollment->next_session->teacher->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-center">
                                <i class="fa-solid fa-calendar-xmark text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500 font-medium">No upcoming sessions scheduled</p>
                            </div>
                        @endif

                        <!-- Course Duration -->
                        <!-- <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    <span>Duration: <strong>{{ $enrollment->course->duration_weeks ?? 'N/A' }} weeks</strong></span>
                                </div>
                                @if($enrollment->start_date && $enrollment->end_date)
                                    <div class="text-gray-600">
                                        <i class="fa-solid fa-calendar-range mr-1"></i>
                                        {{ $enrollment->start_date->format('M d') }} - {{ $enrollment->end_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                        </div> -->
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-red-50 rounded-3xl shadow-lg p-16 text-center border border-purple-200">
            <div class="max-w-md mx-auto">
                <div class="bg-gradient-to-br from-purple-100 to-pink-100 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fa-solid fa-book-open text-purple-600 text-5xl"></i>
                </div>
                <h3 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                    No Courses Yet
                </h3>
                <p class="text-gray-600 mb-6 text-lg">You haven't enrolled in any courses yet. Start your learning journey today!</p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('student.dashboard') }}" 
                       class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 text-white px-8 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-200 font-bold">
                        <i class="fa-solid fa-home mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-dashboard-layout>
