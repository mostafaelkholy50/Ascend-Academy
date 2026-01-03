<x-dashboard-layout title="Progress Report">
    <!-- Header with Gradient -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('student.reports.index') }}" 
                   class="group bg-white hover:bg-gray-50 p-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200">
                    <i class="fa-solid fa-arrow-left text-gray-600 group-hover:-translate-x-1 transition-transform duration-200"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-vibrant-green to-deep-blue bg-clip-text text-transparent">
                        Progress Report
                    </h1>
                    <div class="flex items-center gap-2 mt-1">
                        <i class="fa-solid fa-calendar text-gray-500 text-sm"></i>
                        <p class="text-gray-600 text-sm font-medium">{{ $report->report_date->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-vibrant-green text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-vibrant-green mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Report Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Teacher & Course Info Card -->
            <div class="bg-gradient-to-br from-white to-blue-50 rounded-3xl shadow-lg p-8 border border-blue-100">
                <div class="flex items-center space-x-6 mb-6">
                    <div class="relative">
                        <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-green-400 via-emerald-500 to-blue-500 flex items-center justify-center text-white font-bold text-3xl shadow-xl">
                            {{ strtoupper(substr($report->teacher->name, 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-vibrant-green rounded-full p-2 shadow-lg">
                            <i class="fa-solid fa-user-tie text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Report Created By</p>
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ $report->teacher->name }}</h2>
                        <p class="text-gray-600 text-sm">{{ $report->teacher->email }}</p>
                        @if($report->course)
                            <div class="mt-3 inline-flex items-center bg-gradient-to-r from-purple-100 to-pink-100 px-4 py-2 rounded-xl">
                                <i class="fa-solid fa-book text-purple-600 mr-2"></i>
                                <span class="font-semibold text-purple-700">{{ $report->course->title }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($report->level)
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-5 border border-amber-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-amber-700 mb-1">Current Level</p>
                                <p class="text-2xl font-bold text-amber-800">{{ $report->level }}</p>
                            </div>
                            <div class="bg-white/50 p-4 rounded-xl">
                                <i class="fa-solid fa-layer-group text-amber-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Mastery Score Card -->
            @if($report->mastery_score)
                <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <div class="bg-gradient-to-br from-vibrant-green to-emerald-600 p-3 rounded-xl mr-3">
                                <i class="fa-solid fa-chart-line text-white"></i>
                            </div>
                            Mastery Score
                        </h3>
                        <span class="px-4 py-2 bg-gradient-to-r {{ $report->mastery_score >= 80 ? 'from-green-100 to-emerald-100 text-green-700' : ($report->mastery_score >= 60 ? 'from-yellow-100 to-orange-100 text-yellow-700' : 'from-red-100 to-pink-100 text-red-700') }} rounded-xl font-bold">
                            {{ $report->getMasteryLevel() }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-6xl font-bold {{ $report->mastery_score >= 80 ? 'bg-gradient-to-r from-green-500 to-emerald-600' : ($report->mastery_score >= 60 ? 'bg-gradient-to-r from-yellow-500 to-orange-500' : 'bg-gradient-to-r from-red-500 to-pink-600') }} bg-clip-text text-transparent">
                                {{ $report->mastery_score }}%
                            </p>
                            <p class="text-sm text-gray-600 mt-2 font-medium">Overall Performance</p>
                        </div>
                        <div class="w-40 h-40 relative">
                            <svg class="transform -rotate-90 w-full h-full" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                                <circle cx="60" cy="60" r="54" fill="none"
                                    stroke="url(#gradient{{ $report->id }})"
                                    stroke-width="8"
                                    stroke-dasharray="{{ (339.292 * $report->mastery_score) / 100 }} 339.292"
                                    stroke-linecap="round"
                                    class="transition-all duration-1000"/>
                                <defs>
                                    <linearGradient id="gradient{{ $report->id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                        @if($report->mastery_score >= 80)
                                            <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                        @elseif($report->mastery_score >= 60)
                                            <stop offset="0%" style="stop-color:#F59E0B;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#D97706;stop-opacity:1" />
                                        @else
                                            <stop offset="0%" style="stop-color:#EF4444;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#DC2626;stop-opacity:1" />
                                        @endif
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fa-solid fa-trophy {{ $report->mastery_score >= 80 ? 'text-yellow-500' : 'text-gray-400' }} text-3xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="h-4 rounded-full {{ $report->mastery_score >= 80 ? 'bg-gradient-to-r from-green-400 to-emerald-500' : ($report->mastery_score >= 60 ? 'bg-gradient-to-r from-yellow-400 to-orange-500' : 'bg-gradient-to-r from-red-400 to-pink-500') }} transition-all duration-1000"
                             style="width: {{ $report->mastery_score }}%"></div>
                    </div>
                </div>
            @endif

            <!-- Strengths Card -->
            @if($report->strengths)
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-3xl shadow-lg p-8 border border-yellow-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-br from-yellow-400 to-amber-500 p-3 rounded-xl mr-3">
                            <i class="fa-solid fa-star text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Strengths & Achievements</h3>
                    </div>
                    <div class="bg-white/70 rounded-2xl p-6">
                        <p class="text-gray-700 leading-relaxed">{{ $report->strengths }}</p>
                    </div>
                </div>
            @endif

            <!-- Areas for Improvement Card -->
            @if($report->weaknesses)
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl shadow-lg p-8 border border-blue-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-xl mr-3">
                            <i class="fa-solid fa-arrow-up text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Areas for Growth</h3>
                    </div>
                    <div class="bg-white/70 rounded-2xl p-6">
                        <p class="text-gray-700 leading-relaxed">{{ $report->weaknesses }}</p>
                    </div>
                </div>
            @endif

            <!-- Behavior Card -->
            @if($report->behavior)
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl shadow-lg p-8 border border-green-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-xl mr-3">
                            <i class="fa-solid fa-user-check text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Behavior & Attitude</h3>
                    </div>
                    <div class="bg-white/70 rounded-2xl p-6">
                        <p class="text-gray-700 leading-relaxed">{{ $report->behavior }}</p>
                    </div>
                </div>
            @endif

            <!-- Additional Notes Card -->
            @if($report->notes)
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-3xl shadow-lg p-8 border border-purple-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-3 rounded-xl mr-3">
                            <i class="fa-solid fa-sticky-note text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Additional Notes</h3>
                    </div>
                    <div class="bg-white/70 rounded-2xl p-6">
                        <p class="text-gray-700 leading-relaxed">{{ $report->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Report Details Card -->
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fa-solid fa-info-circle text-vibrant-green mr-2"></i>
                    Report Details
                </h3>
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Report Date</p>
                        <p class="text-sm font-bold text-gray-800">{{ $report->report_date->format('F d, Y') }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Created By</p>
                        <p class="text-sm font-bold text-gray-800">{{ $report->teacher->name }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Created On</p>
                        <p class="text-sm font-bold text-gray-800">{{ $report->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($report->created_at != $report->updated_at)
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Last Updated</p>
                            <p class="text-sm font-bold text-gray-800">{{ $report->updated_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-gradient-to-br from-vibrant-green to-emerald-600 rounded-3xl shadow-lg p-6 text-white">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <i class="fa-solid fa-bolt mr-2"></i>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('student.reports.index') }}" class="block w-full bg-white/20 backdrop-blur-sm hover:bg-white/30 px-4 py-3 rounded-xl transition-all duration-200 text-center font-semibold border border-white/30">
                        <i class="fa-solid fa-list mr-2"></i>View All Reports
                    </a>
                    <a href="{{ route('student.dashboard') }}" class="block w-full bg-white text-vibrant-green hover:bg-gray-50 px-4 py-3 rounded-xl transition-all duration-200 text-center font-semibold shadow-md">
                        <i class="fa-solid fa-home mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Recent Reports Card -->
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600 mr-2"></i>
                    Recent Reports
                </h3>
                @php
                    $recentReports = \App\Models\Report::where('student_id', $report->student_id)
                        ->where('id', '!=', $report->id)
                        ->latest('report_date')
                        ->take(3)
                        ->get();
                @endphp

                @if($recentReports->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentReports as $recentReport)
                            <a href="{{ route('student.reports.show', $recentReport->id) }}" 
                               class="block p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 border border-gray-200 hover:border-blue-300 hover:shadow-md">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-gray-800">{{ $recentReport->report_date->format('M d, Y') }}</p>
                                    @if($recentReport->mastery_score)
                                        <span class="px-2 py-1 bg-white rounded-lg text-xs font-bold {{ $recentReport->mastery_score >= 80 ? 'text-green-600' : ($recentReport->mastery_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $recentReport->mastery_score }}%
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600">By {{ $recentReport->teacher->name }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">No other reports available</p>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
