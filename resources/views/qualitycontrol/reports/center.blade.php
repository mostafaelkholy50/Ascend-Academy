<x-dashboard-layout title="Teacher Evaluation Center">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Evaluation Control Center</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2 text-sm">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                Monitoring performance and weekly tasks.
            </p>
        </div>

        <div class="bg-gray-100 p-1 rounded-xl flex flex-wrap shadow-inner overflow-hidden">
            <a href="{{ route('qualitycontrol.reports.center', ['view' => 'weekly']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $view == 'weekly' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-calendar-week mr-1.5"></i> Weekly Tasks
            </a>
            <a href="{{ route('qualitycontrol.reports.center', ['view' => 'monthly']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $view == 'monthly' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-calendar-check mr-1.5"></i> Monthly Report
            </a>
            <a href="{{ route('qualitycontrol.reports.center', ['view' => 'yearly']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $view == 'yearly' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-chart-line mr-1.5"></i> Yearly Report
            </a>
            <a href="{{ route('qualitycontrol.reports.center', ['view' => 'log']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $view == 'log' ? 'bg-white text-gray-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-history mr-1.5"></i> Evaluation Log
            </a>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center"><i class="fa-solid fa-clock"></i></div>
            <div>
                <p class="text-[10px] uppercase font-black text-gray-400">Pending This Week</p>
                <p class="text-xl font-black text-gray-800">{{ $pendingTeachers->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center"><i class="fa-solid fa-check"></i></div>
            <div>
                <p class="text-[10px] uppercase font-black text-gray-400">Monthly Avg</p>
                <p class="text-xl font-black text-gray-800">{{ round($monthlyRankings->avg('monthly_avg'), 1) }}%</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-star"></i></div>
            <div>
                <p class="text-[10px] uppercase font-black text-gray-400">Yearly Avg</p>
                <p class="text-xl font-black text-gray-800">{{ round($yearlyRankings->avg('yearly_avg'), 1) }}%</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
            <div>
                <p class="text-[10px] uppercase font-black text-gray-400">Total Teachers</p>
                <p class="text-xl font-black text-gray-800">{{ $teachers->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Dynamic Content Section -->
    <div class="animate-fade-in">
        @if($view == 'weekly')
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Weekly Task List <span class="text-xs font-normal text-gray-500 ml-2">(Evaluated teachers disappear automatically)</span></h2>
            </div>
            <x-evaluations.performance-table :performanceData="$pendingTeachers" :showMonthly="false" :showYearly="false" />
        @elseif($view == 'monthly')
            <div class="mb-4">
                <h2 class="font-bold text-gray-800">Monthly Performance Report <span class="text-xs font-normal text-gray-500 ml-2">({{ now()->format('F Y') }})</span></h2>
            </div>
            <x-evaluations.performance-table :performanceData="$monthlyRankings" :showStatus="false" :showYearly="false" />
        @elseif($view == 'yearly')
            <div class="mb-4">
                <h2 class="font-bold text-gray-800">Yearly Performance Report <span class="text-xs font-normal text-gray-500 ml-2">({{ now()->format('Y') }})</span></h2>
            </div>
            <x-evaluations.performance-table :performanceData="$yearlyRankings" :showStatus="false" :showMonthly="false" />
        @else
            <x-evaluations.weekly-table :evaluations="$evaluations" :teachers="$teachers" />
        @endif
    </div>
</x-dashboard-layout>
