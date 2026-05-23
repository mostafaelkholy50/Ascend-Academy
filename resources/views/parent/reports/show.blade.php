<x-dashboard-layout title="Report Details">
    @php $parent = auth()->user(); @endphp

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('parent.reports.index') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Reports
        </a>
    </div>

    <!-- Hero Section -->
    <div class="mb-8 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-32 -mb-32"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white/30 shadow-xl text-3xl font-bold">
                        {{ strtoupper(substr($report->student->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="text-white/80 text-sm font-medium uppercase tracking-wider">Progress Report</span>
                        <h1 class="text-4xl font-bold mt-1">{{ $report->student->name }}</h1>
                        <p class="text-white/90 mt-1 flex items-center gap-2">
                            <i class="fa-solid fa-book-open text-sm"></i>
                            {{ $report->course->title ?? 'General Course' }}
                        </p>
                    </div>
                </div>
                
                @if($report->mastery_score)
                    <div class="bg-white/20 backdrop-blur-sm px-6 py-4 rounded-2xl border border-white/30 text-center md:text-left">
                        <p class="text-sm font-medium text-white/80 uppercase tracking-wider">Mastery Score</p>
                        <div class="flex items-baseline justify-center md:justify-start gap-1 mt-1">
                            <span class="text-5xl font-bold">{{ $report->mastery_score }}</span>
                            <span class="text-2xl font-bold">%</span>
                        </div>
                        <p class="text-xs font-semibold mt-1 px-2 py-0.5 bg-white/30 rounded-full inline-block">
                            {{ $report->getMasteryLevel() }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Mastery Score Visualization (Linear) -->
            @if($report->mastery_score)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                            <i class="fa-solid fa-chart-bar text-indigo-600"></i>
                            Performance Progress
                        </h3>
                        <span class="text-sm font-bold text-indigo-600">{{ $report->mastery_score }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden shadow-inner">
                        <div class="h-4 rounded-full bg-gradient-to-r {{ $report->mastery_score >= 80 ? 'from-green-400 to-emerald-500' : ($report->mastery_score >= 60 ? 'from-yellow-400 to-orange-500' : 'from-red-400 to-pink-500') }} transition-all duration-1000" style="width: {{ $report->mastery_score }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Needs Improvement</span>
                        <span>Excellent</span>
                    </div>
                </div>
            @endif

            <!-- Detailed Evaluation (Questions Breakdown) -->
            @if($evaluation)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-purple-600"></i>
                        Detailed Evaluation
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $questions = [
                                'q1' => 'Attendance & Punctuality',
                                'q2' => 'Participation & Engagement',
                                'q3' => 'Homework Completion',
                                'q4' => 'Understanding & Comprehension',
                                'q5' => 'Behavior & Discipline',
                                'q6' => 'Focus & Attention',
                                'q7' => 'Interaction with Teacher',
                                'q8' => 'Progress & Improvement',
                                'q9' => 'Effort & Motivation',
                                'q10' => 'Retention of Previous Lessons',
                            ];
                        @endphp

                        @foreach($questions as $key => $label)
                            @php $score = $evaluation->{$key . '_score'} ?? 0; @endphp
                            <div class="bg-gradient-to-r from-gray-50 to-white p-3 rounded-xl border border-gray-100 hover:shadow-md transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="flex gap-1">
                                        @for($i = 1; $i <= 10; $i++)
                                            <span class="w-2.5 h-2.5 rounded-full {{ $i <= $score ? ($score >= 8 ? 'bg-emerald-500' : ($score >= 6 ? 'bg-amber-500' : 'bg-red-500')) : 'bg-gray-200' }}"></span>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold w-7 text-center {{ $score >= 8 ? 'text-emerald-600' : ($score >= 6 ? 'text-amber-600' : 'text-red-600') }}">{{ $score }}/10</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Strengths -->
            @if($report->strengths)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:border-emerald-200 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-star text-amber-500"></i>
                        Strengths & Achievements
                    </h3>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-4 rounded-xl border border-emerald-100">
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $report->strengths }}</p>
                    </div>
                </div>
            @endif

            <!-- Areas for Improvement -->
            @if($report->weaknesses)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:border-orange-200 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-up-right-dots text-orange-500"></i>
                        Areas for Improvement
                    </h3>
                    <div class="bg-gradient-to-br from-orange-50 to-yellow-50 p-4 rounded-xl border border-orange-100">
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $report->weaknesses }}</p>
                    </div>
                </div>
            @endif

            <!-- Behavior -->
            @if($report->behavior)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:border-blue-200 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-blue-500"></i>
                        Behavior & Attitude
                    </h3>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $report->behavior }}</p>
                    </div>
                </div>
            @endif

            <!-- Additional Notes -->
            @if($report->notes)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:border-purple-200 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-sticky-note text-purple-500"></i>
                        Teacher's Notes
                    </h3>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-100">
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $report->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Report Meta Details -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg mb-4">Report Details</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Date of Report</p>
                            <p class="font-bold text-gray-800 mt-0.5">{{ $report->report_date->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Teacher</p>
                            <p class="font-bold text-gray-800 mt-0.5">{{ $report->teacher->name }}</p>
                        </div>
                    </div>

                    @if($report->level)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center text-pink-600 flex-shrink-0">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Current Level</p>
                                <p class="font-bold text-gray-800 mt-0.5">{{ $report->level }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Created At</p>
                            <p class="font-bold text-gray-800 mt-0.5">{{ $report->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Progress History (Recent Reports) -->
            @php
                $recentReports = \App\Models\Report::where('student_id', $report->student_id)
                    ->where('id', '!=', $report->id)
                    ->latest('report_date')
                    ->take(3)
                    ->get();
            @endphp

            @if($recentReports->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-history text-indigo-600"></i>
                        History
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentReports as $recentReport)
                            <a href="{{ route('parent.reports.show', $recentReport->id) }}" class="block p-3 bg-gradient-to-r from-gray-50 to-white border border-gray-100 rounded-xl hover:shadow-md hover:border-indigo-100 transition">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $recentReport->report_date->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $recentReport->course->title ?? 'General Course' }}</p>
                                    </div>
                                    @if($recentReport->mastery_score)
                                        <span class="text-xs font-bold px-2 py-1 {{ $recentReport->mastery_score >= 80 ? 'bg-emerald-100 text-emerald-700' : ($recentReport->mastery_score >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }} rounded-lg">
                                            {{ $recentReport->mastery_score }}%
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-gray-900 to-indigo-900 text-white p-6 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
                <div class="relative z-10">
                    <h3 class="font-bold text-lg mb-3">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('parent.children.show', $report->student_id) }}" class="flex items-center gap-3 p-2 bg-white/10 rounded-lg hover:bg-white/20 transition text-sm">
                            <i class="fa-solid fa-user"></i>
                            View Student Profile
                        </a>
                        <a href="{{ route('parent.reports.index') }}" class="flex items-center gap-3 p-2 bg-white/10 rounded-lg hover:bg-white/20 transition text-sm">
                            <i class="fa-solid fa-list-check"></i>
                            All Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
