<x-dashboard-layout title="Teacher Hours">
    <div class="mb-6 px-4 md:px-0">
        <div class="mb-4">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Teacher Hours & Payments</h1>
            <p class="text-xs md:text-sm text-gray-600 mt-1">Track worked hours and manage payments</p>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm mb-6 border border-gray-100">
            <form method="GET" action="{{ route('admin.teacher-hours.index') }}" class="grid grid-cols-2 md:flex md:items-end gap-3">
                <div class="col-span-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Month</label>
                    <select name="month" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-vibrant-green outline-none">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('M') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Year</label>
                    <select name="year" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-vibrant-green outline-none">
                        @foreach(range(now()->year - 1, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <button type="submit" class="w-full bg-vibrant-green text-white px-6 py-2 rounded-xl hover:bg-deep-blue transition font-bold text-sm h-[42px]">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        @php
            $totalTeachers = count($teacherData);
            $totalHours = array_sum(array_column($teacherData, 'workedHours'));
            $totalPayments = array_sum(array_map(fn($data) => $data['teacherHour']->total_salary, $teacherData));
            $paidCount = count(array_filter($teacherData, fn($data) => $data['teacherHour']->is_paid));
        @endphp
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
            <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border-b-4 border-blue-500">
                <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase">Teachers</p>
                <p class="text-lg md:text-2xl font-black text-gray-800">{{ $totalTeachers }}</p>
            </div>
            <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border-b-4 border-green-500">
                <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase">Total Hours</p>
                <p class="text-lg md:text-2xl font-black text-gray-800">{{ number_format($totalHours, 1) }}</p>
            </div>
            <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border-b-4 border-purple-500">
                <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase">Payments</p>
                <p class="text-lg md:text-2xl font-black text-gray-800">${{ number_format($totalPayments, 0) }}</p>
            </div>
            <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border-b-4 border-orange-500">
                <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase">Paid</p>
                <p class="text-lg md:text-2xl font-black text-gray-800">{{ $paidCount }}/{{ $totalTeachers }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mx-4 md:mx-0 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="block md:hidden space-y-4 px-4 mb-10">
        @forelse($teacherData as $data)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 flex items-center justify-between border-b border-gray-50 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-vibrant-green flex items-center justify-center text-white font-bold shadow-sm">
                            {{ strtoupper(substr($data['teacher']->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 leading-tight">{{ $data['teacher']->name }}</h3>
                            <p class="text-[10px] text-gray-500">{{ $data['teacher']->email }}</p>
                        </div>
                    </div>
                    <button onclick="openRateModal({{ $data['teacher']->id }}, '{{ $data['teacher']->name }}', {{ $data['teacher']->hourly_rate }})" 
                            class="p-2 text-blue-500 bg-blue-50 rounded-lg">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </div>
                
                <div class="p-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Hours Worked</p>
                        <p class="font-bold text-gray-700">{{ number_format($data['workedHours'], 2) }} <span class="text-[10px] font-normal">hrs</span></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Total Salary</p>
                        <p class="font-bold text-deep-blue text-lg">${{ number_format($data['teacherHour']->total_salary, 2) }}</p>
                    </div>
                </div>

                <div class="px-4 pb-4 flex items-center justify-between gap-3">
                    @if($data['teacherHour']->is_paid)
                        <div class="flex-1 text-center py-2 rounded-xl bg-green-50 text-green-600 text-xs font-bold border border-green-100">
                            <i class="fa-solid fa-check-circle mr-1"></i> Paid
                        </div>
                        <form action="{{ route('admin.teacher-hours.mark-unpaid', $data['teacherHour']->id) }}" method="POST" class="shrink-0">
                            @csrf @method('PATCH')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition">
                                <i class="fa-solid fa-undo"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.teacher-hours.mark-paid', $data['teacherHour']->id) }}" method="POST" class="w-full">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-full py-2.5 bg-vibrant-green text-white rounded-xl text-xs font-bold shadow-lg shadow-green-100">
                                <i class="fa-solid fa-money-bill-wave mr-1"></i> Mark as Paid
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-gray-500 bg-white rounded-2xl border border-dashed border-gray-300">
                No data available for this month.
            </div>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Teacher</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Rate</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Hours</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Total Payment</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($teacherData as $data)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-vibrant-green flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr($data['teacher']->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $data['teacher']->name }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $data['teacher']->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="font-bold text-gray-700">${{ number_format($data['teacher']->hourly_rate, 2) }}</span>
                                    <button onclick="openRateModal({{ $data['teacher']->id }}, '{{ $data['teacher']->name }}', {{ $data['teacher']->hourly_rate }})" 
                                            class="text-blue-400 hover:text-blue-600 transition">
                                        <i class="fa-solid fa-edit text-xs"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-green-600">{{ number_format($data['workedHours'], 1) }}h</td>
                            <td class="px-6 py-4 text-center font-black text-deep-blue text-base">
                                ${{ number_format($data['teacherHour']->total_salary, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $data['teacherHour']->is_paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $data['teacherHour']->is_paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    @if(!$data['teacherHour']->is_paid)
                                        <form action="{{ route('admin.teacher-hours.mark-paid', $data['teacherHour']->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="bg-vibrant-green text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:shadow-md transition">
                                                Mark Paid
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.teacher-hours.mark-unpaid', $data['teacherHour']->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-gray-400 hover:text-gray-600 text-xs font-bold italic">
                                                Undo
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="rateModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl">
            <h3 class="text-lg font-black text-gray-800 mb-2">Set Hourly Rate</h3>
            <p class="text-xs text-gray-500 mb-6">Updating rate for <span id="teacherName" class="text-vibrant-green font-bold"></span></p>
            
            <form id="rateForm" method="POST">
                @csrf @method('PATCH')
                <div class="mb-6">
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                        <input type="number" name="hourly_rate" id="hourly_rate_input" 
                            step="0.01" min="0" required
                            class="w-full pl-8 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-vibrant-green outline-none font-bold text-gray-700">
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit" class="w-full bg-vibrant-green text-white py-3 rounded-2xl font-bold shadow-lg shadow-green-100 transition hover:scale-[1.02]">
                        Update Rate
                    </button>
                    <button type="button" onclick="closeRateModal()" class="w-full bg-gray-100 text-gray-500 py-3 rounded-2xl font-bold text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRateModal(teacherId, teacherName, currentRate) {
            document.getElementById('teacherName').textContent = teacherName;
            document.getElementById('hourly_rate_input').value = currentRate;
            document.getElementById('rateForm').action = `/admin/teacher-hours/${teacherId}/update-rate`;
            document.getElementById('rateModal').classList.remove('hidden');
        }

        function closeRateModal() {
            document.getElementById('rateModal').classList.add('hidden');
        }

        document.getElementById('rateModal').addEventListener('click', function(e) {
            if (e.target === this) closeRateModal();
        });
    </script>
</x-dashboard-layout>