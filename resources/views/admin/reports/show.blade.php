<x-dashboard-layout title="Report Details">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.reports.index') }}" 
               class="w-10 h-10 bg-white rounded-xl shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Report Details</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $report->report_date->format('l, F d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                        <p class="font-semibold text-gray-800">{{ $report->student->name }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-envelope text-purple-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-700">{{ $report->student->email }}</p>
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
                        <p class="font-semibold text-gray-800">{{ $report->teacher->name }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-envelope text-amber-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-700">{{ $report->teacher->email }}</p>
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
                        <p class="font-semibold text-gray-800">{{ $report->course->title ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Summary -->
        <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Performance Summary</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-layer-group text-blue-600 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500">Level</p>
                        <p class="font-semibold text-gray-800">{{ $report->level ?? 'Not specified' }}</p>
                    </div>
                </div>
                @if($report->mastery_score !== null)
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-star text-blue-600 mt-1"></i>
                        <div class="flex-grow">
                            <p class="text-sm text-gray-500 mb-2">Mastery Score</p>
                            <div class="flex items-center gap-3">
                                <div class="flex-grow bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all
                                        @if($report->mastery_score >= 90) bg-gradient-to-r from-green-500 to-emerald-600
                                        @elseif($report->mastery_score >= 80) bg-gradient-to-r from-blue-500 to-indigo-600
                                        @elseif($report->mastery_score >= 70) bg-gradient-to-r from-yellow-500 to-amber-600
                                        @elseif($report->mastery_score >= 60) bg-gradient-to-r from-orange-500 to-red-500
                                        @else bg-gradient-to-r from-red-500 to-pink-600
                                        @endif" 
                                        style="width: {{ $report->mastery_score }}%"></div>
                                </div>
                                <span class="text-lg font-bold text-gray-800">{{ $report->mastery_score }}%</span>
                            </div>
                            <p class="text-sm font-semibold mt-1
                                @if($report->mastery_score >= 90) text-green-600
                                @elseif($report->mastery_score >= 80) text-blue-600
                                @elseif($report->mastery_score >= 70) text-yellow-600
                                @elseif($report->mastery_score >= 60) text-orange-600
                                @else text-red-600
                                @endif">
                                {{ $report->getMasteryLevel() }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="mt-6 space-y-6">
        <!-- Strengths -->
        @if($report->strengths)
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-thumbs-up text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Strengths</h2>
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $report->strengths }}</p>
            </div>
        @endif

        <!-- Weaknesses -->
        @if($report->weaknesses)
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Areas for Improvement</h2>
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $report->weaknesses }}</p>
            </div>
        @endif

        <!-- Behavior -->
        @if($report->behavior)
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-smile text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Behavior Notes</h2>
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $report->behavior }}</p>
            </div>
        @endif

        <!-- Additional Notes -->
        @if($report->notes)
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-sticky-note text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Additional Notes</h2>
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $report->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('admin.reports.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Reports List
        </a>
    </div>
</x-dashboard-layout>
