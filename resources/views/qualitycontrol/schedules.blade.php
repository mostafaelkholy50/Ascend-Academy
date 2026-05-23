<x-dashboard-layout title="Weekly Schedule View">
<div class="p-0">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Weekly Schedule</h1>
            <p class="text-gray-500 mt-1">Viewing sessions from <span class="font-bold text-blue-600">{{ $startOfWeek->format('d M') }}</span> to <span class="font-bold text-blue-600">{{ $endOfWeek->format('d M, Y') }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('qualitycontrol.schedules', ['date' => $prevWeek]) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-chevron-left mr-1"></i> Prev Week
            </a>
            <a href="{{ route('qualitycontrol.schedules', ['date' => now()->format('Y-m-d')]) }}" class="px-4 py-2 bg-blue-50 text-blue-700 rounded-xl font-bold hover:bg-blue-100 transition shadow-sm">
                Today
            </a>
            <a href="{{ route('qualitycontrol.schedules', ['date' => $nextWeek]) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                Next Week <i class="fa-solid fa-chevron-right ml-1"></i>
            </a>
        </div>
    </div>

    @php
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $startOfWeek->copy()->addDays($i);
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
        @foreach($days as $day)
            @php
                $dayStr = $day->format('Y-m-d');
                $daySchedules = $schedules->get($dayStr, collect());
                $isToday = $day->isToday();
            @endphp
            <div class="flex flex-col gap-3">
                <!-- Day Header -->
                <div class="bg-white rounded-2xl p-3 shadow-sm border {{ $isToday ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-100' }} text-center">
                    <p class="text-xs font-bold {{ $isToday ? 'text-blue-600' : 'text-gray-500' }} uppercase">{{ $day->format('D') }}</p>
                    <p class="text-lg font-black text-gray-800">{{ $day->format('d') }}</p>
                </div>

                <!-- Sessions for this day -->
                <div class="flex flex-col gap-3 min-h-[200px]">
                    @forelse($daySchedules as $session)
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition group relative">
                            <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl {{ $session->status === 'completed' ? 'bg-green-500' : ($session->status === 'cancelled' ? 'bg-red-500' : 'bg-blue-500') }}"></div>
                            
                            <p class="text-[10px] font-bold text-gray-400 mb-1">
                                {{ \Carbon\Carbon::parse($session->starts_at)->format('h:i A') }}
                            </p>
                            
                            <h4 class="text-sm font-bold text-gray-800 leading-tight mb-1 truncate">{{ $session->course->name }}</h4>
                            
                            <div class="flex items-center text-[11px] text-gray-600 mb-1">
                                <i class="fa-solid fa-chalkboard-teacher mr-1 text-blue-400"></i>
                                <span class="truncate">{{ $session->teacher->name ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex items-center text-[11px] text-gray-600">
                                <i class="fa-solid fa-user-graduate mr-1 text-orange-400"></i>
                                <span class="truncate">{{ $session->student->name ?? 'N/A' }}</span>
                            </div>

                            @if($session->status !== 'scheduled')
                                <div class="mt-2 pt-2 border-t border-gray-50 flex justify-end">
                                    <span class="text-[9px] font-bold uppercase {{ $session->status === 'completed' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $session->status }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex-grow flex items-center justify-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 p-4">
                            <p class="text-[10px] text-gray-400 text-center italic">No sessions</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
</x-dashboard-layout>
