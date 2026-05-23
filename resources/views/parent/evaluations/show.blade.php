<x-dashboard-layout title="Detailed Evaluations">
    @php $parent = auth()->user(); @endphp

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('parent.children.show', $child->id) }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $child->name }}'s Profile
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
                        {{ strtoupper(substr($child->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="text-white/80 text-sm font-medium uppercase tracking-wider">Detailed Evaluations</span>
                        <h1 class="text-4xl font-bold mt-1">{{ $child->name }}</h1>
                        <p class="text-white/90 mt-1 flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-sm"></i>
                            Track performance across months
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aggregate Report (Fixed) -->
    <div class="mb-8 bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-indigo-600"></i>
                Aggregate Evaluation (Summary)
            </h2>
            <p class="text-sm text-gray-500 mt-1">Average scores across all evaluations</p>
        </div>
        
        <div class="p-6">
            @if(!empty($aggregates))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($questions as $key => $label)
                        @php $score = $aggregates[$key . '_score'] ?? 0; @endphp
                        <div class="bg-white p-3 rounded-xl border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= 10; $i++)
                                        <span class="w-2.5 h-2.5 rounded-full {{ $i <= round($score) ? ($score >= 8 ? 'bg-emerald-500' : ($score >= 6 ? 'bg-amber-500' : 'bg-red-500')) : 'bg-gray-200' }}"></span>
                                    @endfor
                                </div>
                                <span class="text-xs font-bold w-10 text-center {{ $score >= 8 ? 'text-emerald-600' : ($score >= 6 ? 'text-amber-600' : 'text-red-600') }}">{{ $score }}/10</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 bg-gradient-to-r from-indigo-50 to-purple-50 p-4 rounded-xl flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Total Evaluations Count</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $aggregates['count'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 font-medium">Overall Average Score</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $aggregates['total_score'] }}%</p>
                    </div>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                    <p>No evaluations available to aggregate.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Monthly Report (Navigable) -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Month Navigation -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white flex justify-between items-center">
            <a href="{{ route('parent.children.evaluations', ['child' => $child->id, 'month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition shadow-md">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            
            <div class="text-center">
                <h2 class="text-2xl font-bold">{{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</h2>
                <p class="text-white/80 text-sm">Monthly Evaluation</p>
            </div>
            
            <a href="{{ route('parent.children.evaluations', ['child' => $child->id, 'month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition shadow-md">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
        
        <div class="p-6">
            @if($evaluation)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @foreach($questions as $key => $label)
                        @php $score = $evaluation->{$key . '_score'} ?? 0; @endphp
                        <div class="bg-white p-3 rounded-xl border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Score Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-100 flex flex-col justify-between">
                        <p class="text-sm text-gray-600 font-medium">Total Score</p>
                        <div class="flex items-baseline gap-1 mt-2">
                            <span class="text-4xl font-bold text-purple-600">{{ $evaluation->total_score }}</span>
                            <span class="text-lg font-bold text-purple-400">/100</span>
                        </div>
                    </div>

                    <!-- Teacher Card -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-4 rounded-xl border border-indigo-100 flex flex-col justify-between">
                        <p class="text-sm text-gray-600 font-medium">Evaluated By</p>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($evaluation->teacher->name, 0, 1)) }}
                            </div>
                            <span class="font-bold text-gray-800 text-sm">{{ $evaluation->teacher->name }}</span>
                        </div>
                    </div>

                    <!-- Date Card -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-4 rounded-xl border border-emerald-100 flex flex-col justify-between">
                        <p class="text-sm text-gray-600 font-medium">Date</p>
                        <div class="flex items-center gap-2 mt-2">
                            <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                            <span class="font-bold text-gray-800 text-sm">{{ $evaluation->evaluation_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Teacher Comments -->
                @if($evaluation->notes)
                    <div class="mt-6 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-comment-dots text-purple-600"></i>
                            Teacher's Comments
                        </h3>
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $evaluation->notes }}</p>
                    </div>
                @endif
            @else
                <div class="text-center py-16 text-gray-500">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-calendar-xmark text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-1">No Evaluation Found</h3>
                    <p class="text-gray-500">There is no evaluation recorded for {{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}.</p>
                </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>
