@props([
    'performanceData', 
    'showStatus' => true, 
    'showMonthly' => true, 
    'showYearly' => true
])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Search Bar -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
        <div class="relative max-w-md">
            <input type="text" id="teacherPerformanceSearch" placeholder="Filter teachers by name..." 
                class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm"
            >
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="performanceTable">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="p-4 font-semibold text-gray-600 text-sm">Teacher</th>
                    @if($showStatus)
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Status</th>
                    @endif
                    @if($showMonthly)
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Monthly Avg</th>
                    @endif
                    @if($showYearly)
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Yearly Avg</th>
                    @endif
                    <th class="p-4 font-semibold text-gray-600 text-sm text-center">Total Avg</th>
                    <th class="p-4 font-semibold text-gray-600 text-sm text-right">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($performanceData as $data)
                    <tr class="hover:bg-gray-50 transition performance-row">
                        <td class="p-4">
                            <div class="font-bold text-gray-800 teacher-name">{{ $data->name }}</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $data->total_evals }} total evals</div>
                        </td>
                        @if($showStatus)
                        <td class="p-4 text-center">
                            @if($data->has_eval_this_week)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-green-200">
                                    <i class="fa-solid fa-check-double mr-1"></i> Evaluated
                                </span>
                            @else
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-orange-200">
                                    <i class="fa-solid fa-hourglass-half mr-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        @endif

                        @if($showMonthly)
                        <td class="p-4 text-center">
                            <span class="font-bold text-xs {{ $data->monthly_avg >= 85 ? 'text-green-600' : ($data->monthly_avg >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $data->monthly_avg > 0 ? round($data->monthly_avg, 1) . '%' : '0%' }}
                            </span>
                        </td>
                        @endif

                        @if($showYearly)
                        <td class="p-4 text-center">
                            <span class="font-bold text-xs {{ $data->yearly_avg >= 85 ? 'text-green-600' : ($data->yearly_avg >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $data->yearly_avg > 0 ? round($data->yearly_avg, 1) . '%' : '0%' }}
                            </span>
                        </td>
                        @endif

                        <td class="p-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-lg font-black {{ $data->avg_score >= 85 ? 'text-green-600' : ($data->avg_score >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ round($data->avg_score, 1) }}%
                                </span>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                @can('view evaluations')
                                <a href="{{ route('qualitycontrol.reports.teacher', $data->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm" title="History">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </a>
                                @endcan

                                @if(!$data->has_eval_this_week)
                                    @can('add evaluations')
                                    <a href="{{ route('qualitycontrol.evaluations.create', $data->id) }}" class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-600 hover:text-white transition shadow-sm" title="Evaluate Now">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                    @endcan
                                @else
                                    @can('edit evaluations')
                                    <a href="{{ route('qualitycontrol.evaluations.create', $data->id) }}" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-900 hover:text-white transition shadow-sm" title="Re-evaluate">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </a>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="p-12 text-center text-gray-400 italic">
                            No teacher data found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('teacherPerformanceSearch')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('.performance-row');
    
    rows.forEach(row => {
        let name = row.querySelector('.teacher-name').textContent.toLowerCase();
        if (name.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
