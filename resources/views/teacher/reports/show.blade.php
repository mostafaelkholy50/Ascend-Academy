<x-dashboard-layout title="View Report">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('teacher.reports.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Progress Report</h1>
                    <p class="text-gray-600 text-sm">{{ $report->report_date->format('F d, Y') }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('teacher.reports.edit', $report->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition font-semibold">
                    <i class="fa-solid fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('teacher.reports.destroy', $report->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this report?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition font-semibold">
                        <i class="fa-solid fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Report Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student & Course Info -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($report->student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $report->student->name }}</h2>
                        <p class="text-gray-600">{{ $report->student->email }}</p>
                        @if($report->course)
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fa-solid fa-book mr-1"></i>{{ $report->course->title }}
                            </p>
                        @endif
                    </div>
                </div>

                @if($report->level)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Current Level</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $report->level }}</p>
                    </div>
                @endif
            </div>

            <!-- Mastery Score -->
            @if($report->mastery_score)
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fa-solid fa-chart-line text-vibrant-green mr-2"></i>Mastery Score
                    </h3>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-4xl font-bold {{ $report->mastery_score >= 80 ? 'text-vibrant-green' : ($report->mastery_score >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
                                {{ $report->mastery_score }}%
                            </p>
                            <p class="text-sm text-gray-600 mt-1">{{ $report->getMasteryLevel() }}</p>
                        </div>
                        <div class="w-32 h-32">
                            <svg class="transform -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" stroke-width="12"/>
                                <circle cx="60" cy="60" r="54" fill="none"
                                    stroke="{{ $report->mastery_score >= 80 ? '#10B981' : ($report->mastery_score >= 60 ? '#F59E0B' : '#EF4444') }}"
                                    stroke-width="12"
                                    stroke-dasharray="{{ (339.292 * $report->mastery_score) / 100 }} 339.292"
                                    stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full {{ $report->mastery_score >= 80 ? 'bg-vibrant-green' : ($report->mastery_score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $report->mastery_score }}%"></div>
                    </div>
                </div>
            @endif

            <!-- Strengths -->
            @if($report->strengths)
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        <i class="fa-solid fa-star text-yellow-500 mr-2"></i>Strengths
                    </h3>
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $report->strengths }}</p>
                    </div>
                </div>
            @endif

            <!-- Areas for Improvement -->
            @if($report->weaknesses)
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        <i class="fa-solid fa-arrow-up text-blue-500 mr-2"></i>Areas for Improvement
                    </h3>
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $report->weaknesses }}</p>
                    </div>
                </div>
            @endif

            <!-- Behavior -->
            @if($report->behavior)
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        <i class="fa-solid fa-user-check text-green-500 mr-2"></i>Behavior & Attitude
                    </h3>
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $report->behavior }}</p>
                    </div>
                </div>
            @endif

            <!-- Additional Notes -->
            @if($report->notes)
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        <i class="fa-solid fa-sticky-note text-purple-500 mr-2"></i>Additional Notes
                    </h3>
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $report->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Report Details -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Report Details</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Report Date</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $report->report_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Created By</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $report->teacher->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Created On</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $report->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($report->created_at != $report->updated_at)
                        <div>
                            <p class="text-xs text-gray-500">Last Updated</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $report->updated_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('teacher.reports.create', ['student_id' => $report->student_id]) }}" class="block w-full bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-center font-semibold">
                        <i class="fa-solid fa-plus mr-2"></i>New Report for This Student
                    </a>
                    <a href="{{ route('teacher.my-students') }}" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-center font-semibold">
                        <i class="fa-solid fa-users mr-2"></i>View All Students
                    </a>
                </div>
            </div>

            <!-- Student Progress History -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Reports</h3>
                @php
                    $recentReports = \App\Models\Report::where('student_id', $report->student_id)
                        ->where('teacher_id', auth()->id())
                        ->where('id', '!=', $report->id)
                        ->latest('report_date')
                        ->take(3)
                        ->get();
                @endphp

                @if($recentReports->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentReports as $recentReport)
                            <a href="{{ route('teacher.reports.show', $recentReport->id) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <p class="text-sm font-semibold text-gray-800">{{ $recentReport->report_date->format('M d, Y') }}</p>
                                @if($recentReport->mastery_score)
                                    <p class="text-xs text-gray-600">Score: {{ $recentReport->mastery_score }}%</p>
                                @endif
                            </a>
                        @endforeach>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No previous reports</p>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
