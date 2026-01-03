<x-dashboard-layout title="Report Details">
    @php $parent = auth()->user(); @endphp

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('parent.reports.index') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Reports
        </a>
    </div>

    <!-- Report Card -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-8 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $report->course->title ?? 'General Report' }}</h1>
                    <p class="text-white/90">
                        <i class="fa-solid fa-child mr-2"></i>{{ $report->student->name }}
                    </p>
                </div>
                @if($report->mastery_score)
                    <div class="bg-white/20 backdrop-blur-sm px-6 py-3 rounded-2xl">
                        <p class="text-sm font-medium opacity-90">Mastery Score</p>
                        <p class="text-4xl font-bold">{{ $report->mastery_score }}%</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Report Details -->
        <div class="p-8">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
                    <p class="text-sm text-gray-600 mb-1">Teacher</p>
                    <p class="font-bold text-gray-800">{{ $report->teacher->name }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-100">
                    <p class="text-sm text-gray-600 mb-1">Report Date</p>
                    <p class="font-bold text-gray-800">{{ $report->report_date->format('F d, Y') }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-100">
                    <p class="text-sm text-gray-600 mb-1">Report Type</p>
                    <p class="font-bold text-gray-800">{{ ucfirst($report->report_type ?? 'General') }}</p>
                </div>
            </div>

            <!-- Performance Metrics -->
            @if($report->mastery_score || $report->proficiency_level)
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Performance Metrics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($report->mastery_score)
                            <div class="bg-gray-50 p-6 rounded-2xl">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-medium text-gray-700">Mastery Score</span>
                                    <span class="text-2xl font-bold text-purple-600">{{ $report->mastery_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 h-3 rounded-full transition-all duration-500" style="width:{{ $report->mastery_score }}%"></div>
                                </div>
                            </div>
                        @endif

                        @if($report->proficiency_level)
                            <div class="bg-gray-50 p-6 rounded-2xl">
                                <span class="text-sm font-medium text-gray-700 block mb-2">Proficiency Level</span>
                                <span class="inline-block px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold">
                                    {{ ucfirst($report->proficiency_level) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Strengths & Weaknesses -->
            @if($report->strengths || $report->weaknesses)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @if($report->strengths)
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fa-solid fa-star text-yellow-500 mr-2"></i>
                                Strengths
                            </h3>
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200">
                                <p class="text-gray-700">{{ $report->strengths }}</p>
                            </div>
                        </div>
                    @endif

                    @if($report->weaknesses)
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fa-solid fa-exclamation-triangle text-orange-500 mr-2"></i>
                                Areas for Improvement
                            </h3>
                            <div class="bg-gradient-to-br from-orange-50 to-amber-50 p-4 rounded-xl border border-orange-200">
                                <p class="text-gray-700">{{ $report->weaknesses }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Behavior -->
            @if($report->behavior)
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fa-solid fa-user-check text-blue-600 mr-2"></i>
                        Behavior
                    </h3>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
                        <p class="text-gray-700">{{ $report->behavior }}</p>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($report->notes)
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fa-solid fa-comment-dots text-purple-600 mr-2"></i>
                        Teacher's Notes
                    </h3>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-200">
                        <p class="text-gray-700 whitespace-pre-line">{{ $report->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Recommendations -->
            @if($report->recommendations)
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fa-solid fa-lightbulb text-amber-500 mr-2"></i>
                        Recommendations
                    </h3>
                    <div class="bg-gradient-to-br from-amber-50 to-yellow-50 p-4 rounded-xl border border-amber-200">
                        <p class="text-gray-700">{{ $report->recommendations }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <p class="text-sm text-gray-500 text-center">
                Report created on {{ $report->created_at->format('F d, Y \a\t g:i A') }}
            </p>
        </div>
    </div>
</x-dashboard-layout>
