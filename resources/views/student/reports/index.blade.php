<x-dashboard-layout title="My Monthly Evaluations">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-teal-600 to-cyan-700 bg-clip-text text-transparent">
                    My Monthly Evaluations
                </h1>
                <p class="text-gray-600 text-sm mt-1">Track your monthly academic evaluation and teacher feedback</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-4 py-2 rounded-xl border border-teal-100">
                    <span class="text-sm font-semibold text-teal-700">{{ $evaluations->count() }} Total Evaluations</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-teal-600 text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-teal-600 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex items-center mb-4">
            <i class="fa-solid fa-filter text-teal-600 mr-2"></i>
            <h3 class="text-lg font-bold text-gray-800">Filter Evaluations</h3>
        </div>
        <form method="GET" action="{{ route('student.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Course Filter -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-book text-xs mr-1 text-teal-600"></i>Course
                </label>
                <select name="course_id" class="w-full rounded-xl border-gray-300 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Teacher Filter -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-user-tie text-xs mr-1 text-teal-600"></i>Teacher
                </label>
                <select name="teacher_id" class="w-full rounded-xl border-gray-300 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-calendar-day text-xs mr-1 text-teal-600"></i>From Date
                </label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border-gray-300 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200">
            </div>

            <!-- Date To -->
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-calendar-check text-xs mr-1 text-teal-600"></i>To Date
                </label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border-gray-300 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200">
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="bg-gradient-to-r from-teal-600 to-cyan-600 text-white px-8 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold">
                    <i class="fa-solid fa-search mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('student.reports.index') }}" class="bg-gray-100 text-gray-700 px-8 py-3 rounded-xl hover:bg-gray-200 hover:shadow-md transition-all duration-200 font-semibold">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Monthly Evaluations Grid -->
    <div class="space-y-6">
        @if($evaluations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($evaluations as $eval)
                    @php
                        $score = $eval->total_score;
                        $badgeColor = $score >= 90 ? 'bg-green-50 text-green-700 border-green-200' : ($score >= 80 ? 'bg-teal-50 text-teal-700 border-teal-200' : ($score >= 70 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($score >= 60 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-50 text-red-700 border-red-200')));
                        $barGrad = $score >= 90 ? 'from-green-400 to-emerald-500' : ($score >= 80 ? 'from-teal-400 to-cyan-500' : ($score >= 70 ? 'from-blue-400 to-indigo-500' : ($score >= 60 ? 'from-yellow-400 to-amber-500' : 'from-red-400 to-pink-500')));
                        $ratingLabel = $score >= 90 ? 'Excellent' : ($score >= 80 ? 'Very Good' : ($score >= 70 ? 'Good' : ($score >= 60 ? 'Satisfactory' : 'Needs Improvement')));
                    @endphp
                    <div class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-md hover:shadow-xl hover:border-teal-200 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Header Info -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-4 py-2 rounded-xl border border-teal-100">
                                    <span class="text-sm font-bold text-teal-800">
                                        {{ \Carbon\Carbon::createFromDate($eval->evaluation_year, $eval->evaluation_month, 1)->format('F Y') }}
                                    </span>
                                </div>
                                <span class="px-3 py-1.5 text-xs font-bold rounded-xl border {{ $badgeColor }}">
                                    {{ $ratingLabel }}
                                </span>
                            </div>

                            <!-- Teacher & Course Info -->
                            <div class="flex items-center gap-4 mb-6 mt-4">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center text-white font-bold text-lg shadow">
                                    {{ strtoupper(substr($eval->teacher->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $eval->teacher->name }}</h4>
                                    <p class="text-xs text-gray-400">{{ $eval->course->title ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Score details -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Overall Score</span>
                                    <span class="text-xl font-black text-teal-600">{{ $score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                    <div class="h-3 rounded-full bg-gradient-to-r {{ $barGrad }} transition-all duration-500" 
                                         style="width: {{ $score }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div>
                            <a href="{{ route('student.reports.show', $eval->id) }}?type=evaluation" 
                               class="block w-full py-3 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white font-semibold rounded-xl text-center shadow hover:shadow-lg transition transform hover:-translate-y-0.5">
                                <i class="fa-solid fa-eye mr-2"></i>View Evaluation Results
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty state for evaluations -->
            <div class="bg-gradient-to-br from-gray-50 to-teal-50/20 rounded-3xl shadow-lg p-16 text-center border border-gray-100">
                <div class="max-w-md mx-auto">
                    <div class="bg-teal-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-square-poll-vertical text-teal-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Evaluations Yet</h3>
                    <p class="text-gray-500 text-sm mb-6">Your teachers haven't submitted any new monthly evaluations for you yet. Check back soon!</p>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
