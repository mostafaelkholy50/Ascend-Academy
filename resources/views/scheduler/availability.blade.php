<x-dashboard-layout title="Teacher Availability - {{ $user->name }}">
    @if(session('success'))
    <div id="success-toast" class="fixed top-6 right-6 z-[200] animate-in slide-in-from-right-10 duration-500">
        <div class="bg-white border-l-4 border-vibrant-green rounded-2xl shadow-2xl p-4 pr-12 flex items-center gap-4 relative overflow-hidden group">
            <div class="absolute inset-0 bg-vibrant-green/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-10 h-10 rounded-xl bg-vibrant-green/10 flex items-center justify-center text-vibrant-green shrink-0 shadow-inner">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
            <div>
                <h4 class="text-sm font-black text-gray-900 tracking-tight">System Updated</h4>
                <p class="text-xs text-gray-500 font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('success-toast').remove()" class="absolute top-4 right-4 text-gray-300 hover:text-gray-500 transition-colors">
                <i class="fa-solid fa-times text-xs"></i>
            </button>
            <div class="absolute bottom-0 left-0 h-1 bg-vibrant-green/20 w-full">
                <div class="h-full bg-vibrant-green animate-progress-shrink origin-left"></div>
            </div>
        </div>
    </div>
    <style>
        @keyframes progress-shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
        .animate-progress-shrink {
            animation: progress-shrink 4s linear forwards;
        }
    </style>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('success-toast');
            if (toast) {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 500);
            }
        }, 4000);
    </script>
    @endif

    <div class="mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Teacher Availability</h2>
            <p class="text-gray-500 font-medium">Setting weekly availability for <span class="text-vibrant-green font-bold">{{ $user->name }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-blue-50 text-blue-700 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest border border-blue-100">
                <i class="fa-solid fa-globe mr-2"></i> {{ $user->getUserTimezone() }}
            </span>
        </div>
    </div>

    <form action="{{ route('scheduler.availability', $user->id) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Week View -->
            <div class="lg:col-span-3 space-y-4">
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden" id="day-card-{{ $day }}">
                        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                            <span class="font-black text-gray-800 uppercase tracking-widest text-sm">{{ $day }}</span>
                            <button type="button" onclick="addSlot('{{ $day }}')" class="text-xs font-bold text-vibrant-green hover:text-deep-blue transition flex items-center gap-2">
                                <i class="fa-solid fa-plus-circle"></i> Add Time Slot
                            </button>
                        </div>
                        
                        <div class="p-6">
                            <div id="slots-container-{{ $day }}" class="space-y-4">
                                @php 
                                    $dayAvailabilities = $user->availabilities->where('day_of_week', $day); 
                                @endphp
                                
                                @forelse($dayAvailabilities as $index => $avail)
                                    <div class="flex items-center gap-4 slot-row" data-day="{{ $day }}">
                                        <div class="flex-1">
                                            <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Start Time</label>
                                            <input type="time" name="availabilities[{{ $day }}][{{ $index }}][start_time]" value="{{ \Carbon\Carbon::parse($avail->start_time)->format('H:i') }}" class="w-full rounded-xl border-gray-100 focus:ring-vibrant-green text-sm">
                                        </div>
                                        <div class="text-gray-300 mt-4">to</div>
                                        <div class="flex-1">
                                            <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">End Time</label>
                                            <input type="time" name="availabilities[{{ $day }}][{{ $index }}][end_time]" value="{{ \Carbon\Carbon::parse($avail->end_time)->format('H:i') }}" class="w-full rounded-xl border-gray-100 focus:ring-vibrant-green text-sm">
                                        </div>
                                        <div class="pt-4">
                                            <button type="button" onclick="this.closest('.slot-row').remove()" class="p-2 text-gray-300 hover:text-red-500 transition">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-400 italic no-slots-msg">No slots added for this day</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-10 py-4 rounded-2xl bg-vibrant-green text-white font-black shadow-lg shadow-vibrant-green/20 hover:bg-deep-blue transition active:scale-95 uppercase tracking-widest text-sm">
                        <i class="fa-solid fa-save mr-2"></i> Save All Availabilities
                    </button>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <div class="bg-deep-blue rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
                    <h3 class="font-black text-lg mb-4 relative z-10">Timezone Context</h3>
                    <div class="space-y-4 relative z-10">
                        <div class="bg-white/10 rounded-2xl p-4">
                            <div class="text-[10px] font-bold text-white/50 uppercase tracking-widest mb-1">Teacher's Local Time</div>
                            <div class="text-xl font-black">{{ now()->setTimezone($user->getUserTimezone())->format('h:i A') }}</div>
                        </div>
                        <div class="bg-white/10 rounded-2xl p-4">
                            <div class="text-[10px] font-bold text-white/50 uppercase tracking-widest mb-1">Difference from Cairo</div>
                            @php
                                $teacherTime = now()->setTimezone($user->getUserTimezone());
                                $cairoTime = now()->setTimezone('Africa/Cairo');
                                $diffFromCairo = $teacherTime->offsetHours - $cairoTime->offsetHours;
                            @endphp
                            <div class="text-xl font-black">{{ $diffFromCairo > 0 ? '+' : '' }}{{ $diffFromCairo }} Hours</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="font-black text-gray-800 mb-4 uppercase text-[10px] tracking-widest">Instructions</h3>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-lg bg-green-50 text-green-600 flex-shrink-0 flex items-center justify-center text-[10px] font-bold">1</div>
                            <p class="text-xs text-gray-500 leading-relaxed">You can add <b>multiple slots</b> per day (e.g., Morning and Evening sessions).</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-lg bg-green-50 text-green-600 flex-shrink-0 flex items-center justify-center text-[10px] font-bold">2</div>
                            <p class="text-xs text-gray-500 leading-relaxed">All times must be in the <b>teacher's local timezone</b>.</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-lg bg-green-50 text-green-600 flex-shrink-0 flex items-center justify-center text-[10px] font-bold">3</div>
                            <p class="text-xs text-gray-500 leading-relaxed">Click "Save" at the bottom after making all changes.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

    <script>
        let slotIndex = 100; // Start high to avoid conflicts with existing indices

        function addSlot(day) {
            const container = document.getElementById('slots-container-' + day);
            const noSlotsMsg = container.querySelector('.no-slots-msg');
            if (noSlotsMsg) noSlotsMsg.remove();

            const row = document.createElement('div');
            row.className = 'flex items-center gap-4 slot-row animate-in fade-in slide-in-from-top-1 duration-200';
            row.innerHTML = `
                <div class="flex-1">
                    <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Start Time</label>
                    <input type="time" name="availabilities[${day}][${slotIndex}][start_time]" required class="w-full rounded-xl border-gray-100 focus:ring-vibrant-green text-sm">
                </div>
                <div class="text-gray-300 mt-4">to</div>
                <div class="flex-1">
                    <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">End Time</label>
                    <input type="time" name="availabilities[${day}][${slotIndex}][end_time]" required class="w-full rounded-xl border-gray-100 focus:ring-vibrant-green text-sm">
                </div>
                <div class="pt-4">
                    <button type="button" onclick="this.closest('.slot-row').remove()" class="p-2 text-gray-300 hover:text-red-500 transition">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
            slotIndex++;
        }
    </script>
</x-dashboard-layout>
