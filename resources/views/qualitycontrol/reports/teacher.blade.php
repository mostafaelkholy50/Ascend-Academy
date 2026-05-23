<x-dashboard-layout title="Teacher Evaluation History">
<div class="p-0">
    <div class="flex items-center mb-6">
        <a href="{{ route('qualitycontrol.reports.center') }}" class="mr-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-gray-500 hover:text-blue-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Teacher Evaluation History</h1>
            <p class="text-gray-500 mt-1">Viewing history for <span class="font-bold text-blue-600">{{ $teacher->name }}</span></p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total Evaluations</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $evaluations->count() }}</p>
            <p class="text-xs text-gray-400 mt-2">All-time total</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Monthly Average</h3>
            @php
                $mAvg = $currentMonthAvg ? round($currentMonthAvg, 1) : 0;
            @endphp
            <p class="text-3xl font-bold {{ $mAvg >= 85 ? 'text-green-600' : ($mAvg >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $mAvg }} <span class="text-sm text-gray-400">/100</span>
            </p>
            <p class="text-xs text-gray-400 mt-2">Current Month: {{ now()->format('F') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Annual Average</h3>
            @php
                $yAvg = $currentYearAvg ? round($currentYearAvg, 1) : 0;
            @endphp
            <p class="text-3xl font-bold {{ $yAvg >= 85 ? 'text-green-600' : ($yAvg >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $yAvg }} <span class="text-sm text-gray-400">/100</span>
            </p>
            <p class="text-xs text-gray-400 mt-2">Current Year: {{ now()->format('Y') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Overall Average</h3>
            @php
                $avgScore = $evaluations->count() > 0 ? round($evaluations->avg('total_score'), 1) : 0;
            @endphp
            <p class="text-3xl font-bold {{ $avgScore >= 85 ? 'text-green-600' : ($avgScore >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $avgScore }} <span class="text-sm text-gray-400">/100</span>
            </p>
            <p class="text-xs text-gray-400 mt-2">Lifetime performance</p>
        </div>
    </div>

    <!-- Performance Trends -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <h2 class="font-bold text-gray-800 flex items-center">
                    <i class="fa-solid fa-calendar-days mr-2 text-blue-500"></i>
                    Monthly Performance
                </h2>
            </div>
            <div class="p-0 max-h-[300px] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-gray-50">
                            <th class="px-6 py-3 font-medium">Month</th>
                            <th class="px-6 py-3 font-medium">Average</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <body class="divide-y divide-gray-50">
                        @forelse($monthlyAverages as $m)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    {{ DateTime::createFromFormat('!m', $m->month)->format('F') }} {{ $m->year }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ round($m->average, 1) }}%
                                </td>
                                <td class="px-6 py-4">
                                    @php $avg = $m->average; @endphp
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold {{ $avg >= 85 ? 'bg-green-100 text-green-700' : ($avg >= 70 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $avg >= 85 ? 'EXCELLENT' : ($avg >= 70 ? 'GOOD' : 'POOR') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">No monthly data</td>
                            </tr>
                        @endforelse
                    </body>
                </table>
            </div>
        </div>

        <!-- Annual Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <h2 class="font-bold text-gray-800 flex items-center">
                    <i class="fa-solid fa-chart-line mr-2 text-indigo-500"></i>
                    Annual Performance
                </h2>
            </div>
            <div class="p-0 max-h-[300px] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-gray-50">
                            <th class="px-6 py-3 font-medium">Year</th>
                            <th class="px-6 py-3 font-medium">Average</th>
                            <th class="px-6 py-3 font-medium">Trend</th>
                        </tr>
                    </thead>
                    <body class="divide-y divide-gray-50">
                        @forelse($yearlyAverages as $y)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    {{ $y->year }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ round($y->average, 1) }}%
                                </td>
                                <td class="px-6 py-4">
                                    @php $avg = $y->average; @endphp
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 max-w-[100px]">
                                        <div class="h-1.5 rounded-full {{ $avg >= 85 ? 'bg-green-500' : ($avg >= 70 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $avg }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">No yearly data</td>
                            </tr>
                        @endforelse
                    </body>
                </table>
            </div>
        </div>
    </div>

    <!-- History Timeline/List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Evaluation Records</h2>
        </div>
        <div class="p-0">
            @if($evaluations->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <p>No evaluation history found for this teacher.</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($evaluations as $eval)
                        <li class="p-6 hover:bg-gray-50 transition">
                            <div class="flex flex-col md:flex-row justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-lg">Week of {{ $eval->week_start_date->format('M d, Y') }}</h4>
                                    <p class="text-sm text-gray-500">Evaluated on {{ $eval->evaluation_date->format('M d, Y') }} by {{ $eval->evaluator->name }}</p>
                                </div>
                                <div class="mt-2 md:mt-0">
                                    <span class="px-4 py-2 rounded-xl text-lg font-bold shadow-sm {{ $eval->total_score >= 85 ? 'bg-green-100 text-green-700' : ($eval->total_score >= 70 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $eval->total_score }} / 100
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Detailed Breakdown -->
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 bg-white p-4 rounded-xl border border-gray-100">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Punctuality</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q1_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Appearance</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q2_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Environment</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q3_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Internet</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q4_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Silence</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q5_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Engagement</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q6_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Clarity</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q7_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Knowledge</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q8_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Time Mgmt</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q9_score }}/10</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">Tools</span>
                                    <span class="font-semibold text-gray-800">{{ $eval->q10_score }}/10</span>
                                </div>
                            </div>

                            @if($eval->notes)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <p class="text-sm font-bold text-gray-700 mb-1">Notes:</p>
                                    <p class="text-sm text-gray-600 italic bg-gray-50 p-3 rounded-lg">{{ $eval->notes }}</p>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
</x-dashboard-layout>
