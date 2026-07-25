<x-dashboard-layout title="Teacher Payroll">
    <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Header & Month Navigator -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Teacher Payroll</h1>
                <p class="text-slate-500 mt-2 text-lg">Track and manage compensation for all staff.</p>
            </div>

            @php
                $currentDate = \Carbon\Carbon::create($year, $month, 1);
                $prevDate = $currentDate->copy()->subMonth();
                $nextDate = $currentDate->copy()->addMonth();
            @endphp

            <div class="flex items-center bg-white p-2 rounded-3xl shadow-sm border border-slate-100">
                <a href="{{ route('accountant.teacher-hours.index', ['month' => $prevDate->month, 'year' => $prevDate->year, 'search' => request('search'), 'country' => request('country')]) }}" 
                    class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <div class="px-8 text-center min-w-[180px]">
                    <h3 class="text-xl font-black text-slate-900 leading-none">{{ $currentDate->format('F') }}</h3>
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mt-1">{{ $year }}</p>
                </div>
                <a href="{{ route('accountant.teacher-hours.index', ['month' => $nextDate->month, 'year' => $nextDate->year, 'search' => request('search'), 'country' => request('country')]) }}" 
                    class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
        </div>

        @php
            $totalHours = $payrollRecords->sum('total_hours');
            $totalPayroll = $payrollRecords->sum('total_salary');
            $paidCount = $payrollRecords->where('is_paid', true)->count();
            $totalTeachersInList = $teachers->count();
        @endphp

        <div id="payroll-stats" class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hours Logged</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($totalHours, 1) }}</p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Payroll</p>
                    <p class="text-xl font-black text-slate-900">${{ number_format($totalPayroll, 2) }}</p>
                </div>
            </div>
            <div class="bg-indigo-600 p-5 rounded-[2rem] shadow-lg shadow-indigo-100 flex items-center gap-4 col-span-2 md:col-span-1">
                <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center">
                    <i class="fa-solid fa-check-double text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-white/70 uppercase tracking-widest">Payment Progress</p>
                    <p class="text-xl font-black text-white">{{ $paidCount }} / {{ $totalTeachersInList }} Paid</p>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <form method="GET" action="{{ route('accountant.teacher-hours.index') }}" id="payroll-filters" class="flex flex-wrap items-center gap-4">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <div class="flex-1 min-w-[250px] relative group">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search teachers..." 
                        class="w-full pl-12 pr-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-2xl font-bold text-slate-900 transition-all">
                </div>

                @if(count($allowedCountries) > 1)
                    <div class="w-full md:w-48">
                        <select name="country" class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-2xl font-bold text-slate-900 transition-all appearance-none cursor-pointer">
                            <option value="">All Countries</option>
                            @foreach($allowedCountries as $c)
                                <option value="{{ $c }}" {{ request('country') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="flex items-center justify-center w-14 h-14 bg-slate-900 text-white rounded-2xl hover:bg-indigo-600 transition-all active:scale-95 shadow-lg shadow-slate-200">
                    <i class="fa-solid fa-filter text-xs"></i>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-3xl flex items-center gap-4 animate-in slide-in-from-top duration-500">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-100">
                    <i class="fa-solid fa-check"></i>
                </div>
                <p class="text-emerald-800 font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Main Table Card -->
        <div id="payroll-table-container" class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-slate-50">
                            <th class="px-8 py-6 text-left">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Teacher</span>
                            </th>
                            <th class="px-6 py-6 text-center">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Logged Hours</span>
                            </th>
                            <th class="px-6 py-6 text-center">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Hourly Rate</span>
                            </th>
                            <th class="px-6 py-6 text-center">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Amount</span>
                            </th>
                            <th class="px-6 py-6 text-center">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</span>
                            </th>
                            <th class="px-8 py-6 text-right">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($teachers as $teacher)
                            @php $record = $payrollRecords->get($teacher->id); @endphp
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group">
                                <td class="px-8 py-6">
                                    <a href="{{ route('accountant.teacher-hours.show', $teacher->id) }}?month={{ $month }}&year={{ $year }}" class="flex items-center gap-4 group/link hover:bg-slate-50 p-2 rounded-2xl transition-all">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500 flex items-center justify-center text-lg font-black group-hover/link:from-indigo-500 group-hover/link:to-purple-600 group-hover/link:text-white transition-all duration-500">
                                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-black text-slate-900 group-hover/link:text-indigo-600 transition-colors">{{ $teacher->name }}</h4>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $teacher->email }}</p>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl">
                                        <span class="text-sm font-black text-slate-900">{{ number_format($record->total_hours ?? 0, 1) }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">hrs</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @can('edit-teacher-rate')
                                        <form action="{{ route('accountant.teacher-hours.update-rate', $teacher->id) }}" method="POST" class="inline-flex items-center gap-2 justify-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="hourly_rate" value="{{ $teacher->hourly_rate }}" step="0.01" class="w-20 px-3 py-1.5 bg-slate-50 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-xl font-bold text-slate-900 text-sm transition-all text-center">
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                                                <i class="fa-solid fa-save text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-sm font-black text-slate-900">${{ number_format($teacher->hourly_rate ?? 0, 2) }}</span>
                                    @endcan
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="text-lg font-black text-slate-900">${{ number_format($record->total_salary ?? 0, 2) }}</span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($record && $record->is_paid)
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-full">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Paid</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-600 rounded-full">
                                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Unpaid</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-4">
                                        @if(!$record || !$record->is_paid)
                                            <form action="{{ route('accountant.teacher-hours.mark-paid') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                                <input type="hidden" name="month" value="{{ $month }}">
                                                <input type="hidden" name="year" value="{{ $year }}">
                                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-2xl text-xs font-black shadow-lg shadow-indigo-100 hover:bg-slate-900 transition-all active:scale-95 uppercase tracking-widest">
                                                    Mark Paid
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('accountant.teacher-hours.mark-unpaid') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                                <input type="hidden" name="month" value="{{ $month }}">
                                                <input type="hidden" name="year" value="{{ $year }}">
                                                <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors text-[10px] font-black uppercase tracking-widest italic flex items-center gap-2">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                    <span>Revert</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-32 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                                        <i class="fa-solid fa-user-slash text-slate-200 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900">No teachers found</h3>
                                    <p class="text-slate-400 mt-2 font-bold uppercase tracking-widest text-[10px]">Try adjusting your search filters</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($teachers->hasPages())
                <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .animate-in { animation-duration: 0.5s; animation-fill-mode: both; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation-name: fadeIn; }
        @keyframes zoomIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .zoom-in { animation-name: zoomIn; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('payroll-filters');
            const inputs = form.querySelectorAll('input:not([type="hidden"]), select');
            const tableContainer = document.getElementById('payroll-table-container');
            const statsContainer = document.getElementById('payroll-stats');
            const mainContent = document.querySelector('.space-y-8');

            let timeout = null;

            function updateUI() {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const url = `${form.action}?${params.toString()}`;

                mainContent.style.opacity = '0.6';

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    if (statsContainer) {
                        const newStats = doc.getElementById('payroll-stats');
                        if (newStats) statsContainer.innerHTML = newStats.innerHTML;
                    }

                    if (tableContainer) {
                        const newTable = doc.getElementById('payroll-table-container');
                        if (newTable) tableContainer.innerHTML = newTable.innerHTML;
                    }

                    window.history.pushState({}, '', url);
                })
                .finally(() => {
                    mainContent.style.opacity = '1';
                });
            }

            inputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.addEventListener('change', updateUI);
                } else {
                    input.addEventListener('input', function() {
                        clearTimeout(timeout);
                        timeout = setTimeout(updateUI, 500);
                    });
                }
            });

            form.addEventListener('submit', e => e.preventDefault());
        });
    </script>
</x-dashboard-layout>
