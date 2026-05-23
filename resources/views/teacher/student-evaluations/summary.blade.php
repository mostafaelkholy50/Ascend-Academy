<x-dashboard-layout title="Evaluations Summary">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Evaluations Summary</h1>
                <p class="text-gray-600 text-sm">Summary of student performance based on your evaluations</p>
            </div>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        @if($summaryData->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Evaluations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Highest Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($summaryData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $data['student']->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data['total_evaluations'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-semibold text-gray-900 mr-2">{{ $data['average_score'] }}/100</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-vibrant-green h-2 rounded-full" style="width: {{ $data['average_score'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data['highest_score'] }}/100
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($data['total_evaluations'] == 0)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No Data</span>
                                    @elseif($data['average_score'] >= 80)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Excellent</span>
                                    @elseif($data['average_score'] >= 60)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Good</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Needs Improvement</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-chart-bar text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg font-medium">No evaluation data available</p>
                <p class="text-gray-400 text-sm">Start evaluating your students to see the summary here.</p>
            </div>
        @endif
    </div>
</x-dashboard-layout>
