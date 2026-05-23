<x-dashboard-layout title="Monthly Evaluation Details">
    <!-- Print Styles for a Premium Academic Report Card -->
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
                background: white !important;
                page-break-inside: avoid;
            }
            .print-gradient {
                background: #f3f4f6 !important;
                color: black !important;
            }
            .print-progress-bg {
                background-color: #e5e7eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-progress-fill {
                background-color: #4F46E5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <div class="mb-8 no-print">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('parent.reports.index') }}" 
                   class="group bg-white hover:bg-gray-50 p-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200">
                    <i class="fa-solid fa-arrow-left text-gray-600 group-hover:-translate-x-1 transition-transform duration-200"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-700 bg-clip-text text-transparent">
                        Monthly Evaluation Result
                    </h1>
                    <div class="flex items-center gap-2 mt-1">
                        <i class="fa-solid fa-calendar text-gray-500 text-sm"></i>
                        <p class="text-gray-600 text-sm font-semibold">
                            {{ \Carbon\Carbon::createFromDate($evaluation->evaluation_year, $evaluation->evaluation_month, 1)->format('F Y') }}
                        </p>
                    </div>
                </div>
            </div>
            <button onclick="window.print()" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Print / Save PDF</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Report Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Summary Card -->
            <div class="bg-gradient-to-br from-white to-indigo-50/30 rounded-3xl shadow-lg p-6 md:p-8 border border-indigo-100 print-card">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-3xl shadow-xl">
                                {{ strtoupper(substr($evaluation->teacher->name, 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-indigo-600 rounded-full p-2 shadow-lg">
                                <i class="fa-solid fa-user-tie text-white text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="mb-2 inline-flex items-center gap-2 px-3 py-1 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100">
                                <i class="fa-solid fa-user-graduate text-purple-600 text-xs"></i>
                                <span class="text-xs font-bold text-purple-800">Student: {{ $evaluation->student->name }}</span>
                            </div>
                            <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1">Assessed By Teacher</p>
                            <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ $evaluation->teacher->name }}</h2>
                            <p class="text-gray-500 text-sm">{{ $evaluation->teacher->email }}</p>
                            @if($evaluation->course)
                                <div class="mt-3 inline-flex items-center bg-gradient-to-r from-indigo-100/50 to-purple-100/50 px-3 py-1.5 rounded-lg border border-indigo-100">
                                    <i class="fa-solid fa-book text-indigo-600 mr-2"></i>
                                    <span class="font-bold text-indigo-800 text-xs">{{ $evaluation->course->title }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Level Badge -->
                    <div class="text-center bg-white rounded-2xl p-4 border border-indigo-100/80 shadow-sm print-gradient min-w-[120px]">
                        <p class="text-xs font-semibold text-gray-400 mb-1">Date</p>
                        <p class="text-sm font-bold text-indigo-700">
                            {{ $evaluation->evaluation_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Overall Performance Gauge -->
            <div class="bg-white rounded-3xl shadow-lg p-6 md:p-8 border border-gray-100 print-card">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8 mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 flex items-center mb-2">
                            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 p-2.5 rounded-xl mr-3 shadow-md">
                                <i class="fa-solid fa-gauge-high text-white"></i>
                            </div>
                            Overall Performance
                        </h3>
                        <p class="text-gray-500 text-sm max-w-sm">This score represents the combined average of all 10 academic and behavioral criteria assessed this month.</p>
                        
                        <div class="mt-6">
                            <span class="px-4 py-2 bg-gradient-to-r {{ $evaluation->total_score >= 90 ? 'from-green-50 to-emerald-100 text-green-700 border-green-200' : ($evaluation->total_score >= 80 ? 'from-indigo-50 to-purple-100 text-indigo-700 border-indigo-200' : ($evaluation->total_score >= 70 ? 'from-blue-50 to-indigo-100 text-blue-700 border-blue-200' : ($evaluation->total_score >= 60 ? 'from-yellow-50 to-amber-100 text-yellow-700 border-yellow-200' : 'from-red-50 to-rose-100 text-red-700 border-rose-200'))) }} rounded-xl font-bold border text-sm shadow-sm">
                                Rating: 
                                @if($evaluation->total_score >= 90) Excellent
                                @elseif($evaluation->total_score >= 80) Very Good
                                @elseif($evaluation->total_score >= 70) Good
                                @elseif($evaluation->total_score >= 60) Satisfactory
                                @else Needs Improvement
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Progress Circle -->
                    <div class="relative w-44 h-44 flex items-center justify-center">
                        <svg class="transform -rotate-90 w-full h-full" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="8" class="print-progress-bg"/>
                            <!-- Indicator Progress Circle -->
                            <circle cx="60" cy="60" r="52" fill="none"
                                stroke="url(#totalScoreGrad)"
                                stroke-width="9"
                                stroke-dasharray="{{ (326.72 * $evaluation->total_score) / 100 }} 326.72"
                                stroke-linecap="round"
                                class="transition-all duration-1000 print-progress-fill"/>
                            <defs>
                                <linearGradient id="totalScoreGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    @if($evaluation->total_score >= 90)
                                        <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                    @elseif($evaluation->total_score >= 80)
                                        <stop offset="0%" style="stop-color:#4F46E5;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#7C3AED;stop-opacity:1" />
                                    @elseif($evaluation->total_score >= 70)
                                        <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#2563EB;stop-opacity:1" />
                                    @elseif($evaluation->total_score >= 60)
                                        <stop offset="0%" style="stop-color:#F59E0B;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#D97706;stop-opacity:1" />
                                    @else
                                        <stop offset="0%" style="stop-color:#EF4444;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#DC2626;stop-opacity:1" />
                                    @endif
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-extrabold text-gray-800 leading-none">{{ $evaluation->total_score }}%</span>
                            <span class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-wider">Score</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Breakdown (10 Questions) -->
            <div>
                <h3 class="text-xl font-bold text-gray-800 flex items-center mb-6 no-print">
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 p-2.5 rounded-xl mr-3 shadow-md">
                        <i class="fa-solid fa-list-check text-white"></i>
                    </div>
                    Monthly Performance Breakdown
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $questions = [
                            ['q1_score', 'Attendance & Punctuality', 'fa-clock', 'text-blue-500', 'from-blue-400 to-blue-600'],
                            ['q2_score', 'Participation & Engagement', 'fa-comments', 'text-purple-500', 'from-purple-400 to-purple-600'],
                            ['q3_score', 'Homework Completion', 'fa-book-open', 'text-amber-500', 'from-amber-400 to-amber-600'],
                            ['q4_score', 'Understanding & Comprehension', 'fa-brain', 'text-emerald-500', 'from-emerald-400 to-emerald-600'],
                            ['q5_score', 'Behavior & Discipline', 'fa-child-reaching', 'text-indigo-500', 'from-indigo-400 to-indigo-600'],
                            ['q6_score', 'Focus & Attention', 'fa-eye', 'text-cyan-500', 'from-cyan-400 to-cyan-600'],
                            ['q7_score', 'Interaction with Teacher', 'fa-hands-holding', 'text-rose-500', 'from-rose-400 to-rose-600'],
                            ['q8_score', 'Progress & Improvement', 'fa-chart-line', 'text-teal-500', 'from-teal-400 to-teal-600'],
                            ['q9_score', 'Effort & Motivation', 'fa-fire', 'text-orange-500', 'from-orange-400 to-orange-600'],
                            ['q10_score', 'Retention of Previous Lessons', 'fa-folder-open', 'text-sky-500', 'from-sky-400 to-sky-600'],
                        ];
                    @endphp

                    @foreach($questions as [$field, $title, $icon, $textClass, $gradient])
                        @php
                            $score = $evaluation->$field ?? 0;
                            // Choose colors based on score out of 10
                            $badgeColor = $score >= 9 ? 'bg-green-50 text-green-700 border-green-200' : ($score >= 8 ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : ($score >= 7 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($score >= 5 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-50 text-red-700 border-red-200')));
                            $barGrad = $score >= 9 ? 'from-green-400 to-emerald-500' : ($score >= 8 ? 'from-indigo-400 to-purple-500' : ($score >= 7 ? 'from-blue-400 to-indigo-500' : ($score >= 5 ? 'from-yellow-400 to-amber-500' : 'from-red-400 to-pink-500')));
                        @endphp
                        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-200 flex flex-col justify-between print-card">
                            <div class="flex justify-between items-start gap-2 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-gray-50 flex items-center justify-center shadow-sm">
                                        <i class="fa-solid {{ $icon }} {{ $textClass }} text-lg"></i>
                                    </div>
                                    <span class="font-bold text-gray-700 text-sm leading-tight">{{ $title }}</span>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg border {{ $badgeColor }}">
                                    {{ $score }}/10
                                </span>
                            </div>
                            
                            <!-- Custom Progress bar -->
                            <div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden print-progress-bg">
                                    <div class="h-2.5 rounded-full bg-gradient-to-r {{ $barGrad }} print-progress-fill" 
                                         style="width: {{ $score * 10 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Teacher Notes -->
            @if($evaluation->notes)
                <div class="bg-gradient-to-br from-indigo-50/30 to-purple-50/30 rounded-3xl shadow-lg p-6 md:p-8 border border-indigo-100 print-card">
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2.5 rounded-xl mr-3 shadow-md">
                            <i class="fa-solid fa-comment-dots text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Teacher's Remarks</h3>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-indigo-100/50 shadow-inner">
                        <i class="fa-solid fa-quote-left text-indigo-400/30 text-4xl block mb-2 -ml-2"></i>
                        <p class="text-gray-700 leading-relaxed font-medium italic whitespace-pre-line">{{ $evaluation->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Actions & Summary -->
        <div class="space-y-6 no-print">
            <!-- Summary Details -->
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fa-solid fa-clipboard-check text-indigo-600 mr-2"></i>
                    Evaluation Meta
                </h3>
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-purple-50/50 to-pink-50/50 rounded-xl p-4 border border-purple-50">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Student</p>
                        <p class="text-sm font-bold text-gray-800">{{ $evaluation->student->name }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-indigo-50/50 to-purple-50/50 rounded-xl p-4 border border-indigo-50">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Assessment Month</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ \Carbon\Carbon::createFromDate($evaluation->evaluation_year, $evaluation->evaluation_month, 1)->format('F Y') }}
                        </p>
                    </div>
                    <div class="bg-gradient-to-r from-emerald-50/50 to-green-50/50 rounded-xl p-4 border border-emerald-50">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Instructor</p>
                        <p class="text-sm font-bold text-gray-800">{{ $evaluation->teacher->name }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-blue-50/50 to-indigo-50/50 rounded-xl p-4 border border-blue-50">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Registered Course</p>
                        <p class="text-sm font-bold text-gray-800">{{ $evaluation->course->title ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-amber-50/50 to-orange-50/50 rounded-xl p-4 border border-amber-50">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Submitted On</p>
                        <p class="text-sm font-bold text-gray-800">{{ $evaluation->evaluation_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-3xl shadow-lg p-6 text-white">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <i class="fa-solid fa-arrow-turn-up mr-2"></i>
                    Quick Navigation
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('parent.reports.index') }}" class="block w-full bg-white/20 hover:bg-white/30 px-4 py-3 rounded-xl transition-all duration-200 text-center font-semibold border border-white/20">
                        <i class="fa-solid fa-list mr-2"></i>All Children Evaluations
                    </a>
                    <a href="{{ route('parent.dashboard') }}" class="block w-full bg-white text-indigo-700 hover:bg-indigo-50 px-4 py-3 rounded-xl transition-all duration-200 text-center font-semibold shadow-md">
                        <i class="fa-solid fa-home mr-2"></i>Dashboard Home
                    </a>
                </div>
            </div>

            <!-- Recent Evaluations Card -->
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-600 mr-2"></i>
                    Recent Evaluations
                </h3>
                @php
                    $recentEvals = \App\Models\StudentEvaluation::where('student_id', $evaluation->student_id)
                        ->where('id', '!=', $evaluation->id)
                        ->latest('evaluation_date')
                        ->take(3)
                        ->get();
                @endphp

                @if($recentEvals->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentEvals as $recentEval)
                            <a href="{{ route('parent.reports.show', $recentEval->id) }}" 
                               class="block p-4 bg-gradient-to-r from-gray-50 to-indigo-50/30 rounded-xl hover:from-indigo-50 hover:to-purple-50 transition-all duration-200 border border-gray-200 hover:border-indigo-300 hover:shadow-md">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ \Carbon\Carbon::createFromDate($recentEval->evaluation_year, $recentEval->evaluation_month, 1)->format('F Y') }}
                                    </p>
                                    <span class="px-2 py-1 bg-white rounded-lg text-xs font-bold text-indigo-600">
                                        {{ $recentEval->total_score }}%
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 font-medium">By {{ $recentEval->teacher->name }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">No other evaluations available</p>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
