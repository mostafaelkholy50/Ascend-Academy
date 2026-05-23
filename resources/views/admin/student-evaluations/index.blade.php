<x-dashboard-layout title="Student Evaluations Explorer">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-700 bg-clip-text text-transparent">
                    Student Evaluations Explorer
                </h1>
                <p class="text-gray-600 text-sm mt-1">Review, monitor, and print all monthly evaluations submitted by instructors</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-4 py-2 rounded-xl border border-indigo-100">
                    <span class="text-sm font-semibold text-indigo-700">{{ $evaluations->total() }} Total Submissions</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-indigo-600 text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-indigo-600 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-600 text-red-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-times-circle text-red-600 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex items-center mb-4">
            <i class="fa-solid fa-filter text-indigo-600 mr-2"></i>
            <h3 class="text-lg font-bold text-gray-800">Filter Submissions</h3>
        </div>
        <form method="GET" action="{{ route('admin.student-evaluations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Student Filter -->
            <div class="group">
                <label for="student_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-user-graduate text-xs mr-1 text-indigo-600"></i>Student
                </label>
                <select name="student_id" id="student_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Teacher Filter -->
            <div class="group">
                <label for="teacher_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-user-tie text-xs mr-1 text-indigo-600"></i>Teacher
                </label>
                <select name="teacher_id" id="teacher_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Year Filter -->
            <div class="group">
                <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-calendar-days text-xs mr-1 text-indigo-600"></i>Year
                </label>
                <select name="year" id="year" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Month Filter -->
            <div class="group">
                <label for="month" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-calendar text-xs mr-1 text-indigo-600"></i>Month
                </label>
                <select name="month" id="month" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end gap-3 md:col-span-1">
                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-3 rounded-xl hover:shadow-lg hover:scale-102 transition-all duration-200 font-semibold text-sm">
                    <i class="fa-solid fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.student-evaluations.index') }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-3 rounded-xl hover:bg-gray-200 hover:shadow-md transition-all duration-200 font-semibold text-sm text-center">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Monthly Stats Dashboard -->
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-4 mb-8">
        @for($m = 1; $m <= 12; $m++)
            @php 
                $count = $monthlyCounts[$m] ?? 0;
                $isActive = request('month') == $m;
            @endphp
            <a href="{{ route('admin.student-evaluations.index', array_merge(request()->except('month'), ['month' => $m])) }}" 
               class="bg-white rounded-2xl p-4 border transition-all duration-200 shadow-sm hover:shadow-md hover:border-indigo-300 text-center flex flex-col justify-between {{ $isActive ? 'border-indigo-600 ring-2 ring-indigo-500/10' : 'border-gray-100' }}">
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ DateTime::createFromFormat('!m', $m)->format('M') }}</div>
                <div class="my-2">
                    <div class="text-xl font-black {{ $count > 0 ? 'text-indigo-600' : 'text-gray-300' }}">{{ $count }}</div>
                </div>
                <div class="text-[8px] font-bold {{ $count > 0 ? 'text-indigo-700' : 'text-gray-400' }}">Submissions</div>
            </a>
        @endfor
    </div>

    <!-- Past Evaluations List -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Evaluations Register</h2>
            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full">
                {{ $evaluations->total() }} Records
            </span>
        </div>
        @if($evaluations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Instructor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Assessment Month</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Overall Score</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($evaluations as $evaluation)
                            @php
                                $score = $evaluation->total_score;
                                $badgeColor = $score >= 90 ? 'bg-green-50 text-green-700 border-green-200' : ($score >= 80 ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : ($score >= 70 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($score >= 60 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-50 text-red-700 border-red-200')));
                                $barGrad = $score >= 90 ? 'from-green-400 to-emerald-500' : ($score >= 80 ? 'from-indigo-400 to-purple-500' : ($score >= 70 ? 'from-blue-400 to-indigo-500' : ($score >= 60 ? 'from-yellow-400 to-amber-500' : 'from-red-400 to-pink-500')));
                                $ratingLabel = $score >= 90 ? 'Excellent' : ($score >= 80 ? 'Very Good' : ($score >= 70 ? 'Good' : ($score >= 60 ? 'Satisfactory' : 'Needs Improvement')));
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <!-- Student Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ strtoupper(substr($evaluation->student->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-800">{{ $evaluation->student->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $evaluation->student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Teacher Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs border border-gray-200">
                                            {{ strtoupper(substr($evaluation->teacher->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-xs font-bold text-gray-700">{{ $evaluation->teacher->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $evaluation->teacher->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Month Info -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-600">
                                    {{ \Carbon\Carbon::createFromDate($evaluation->evaluation_year, $evaluation->evaluation_month, 1)->format('F Y') }}
                                </td>
                                <!-- Score info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-black text-gray-800 mr-3 w-16">{{ $score }}/100</span>
                                        <div class="w-24 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-2.5 rounded-full bg-gradient-to-r {{ $barGrad }}" style="width: {{ $score }}%"></div>
                                        </div>
                                        <span class="ml-3 px-2 py-0.5 text-3xs font-bold rounded-lg border {{ $badgeColor }}">
                                            {{ $ratingLabel }}
                                        </span>
                                    </div>
                                </td>
                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('admin.student-evaluations.show', $evaluation->id) }}" 
                                       class="inline-flex items-center gap-1.5 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 text-indigo-700 hover:text-indigo-800 px-4 py-2 rounded-lg border border-indigo-100 hover:border-indigo-200 transition-all duration-200 font-bold text-xs shadow-sm">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>View Details</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($evaluations->hasPages())
                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    {{ $evaluations->links() }}
                </div>
            @endif
        @else
            <!-- Empty state -->
            <div class="bg-gradient-to-br from-gray-50 to-indigo-50/20 rounded-3xl p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="bg-indigo-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-square-poll-vertical text-indigo-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Evaluations Found</h3>
                    <p class="text-gray-500 text-sm mb-6">There are no student evaluations logged in the database matching your criteria.</p>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
