<x-dashboard-layout title="Weekly Schedule Overview">
    <!-- Premium Header -->
    <div class="relative mb-10">
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 relative z-10">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                        <li><a href="{{ route('scheduler.dashboard') }}" class="hover:text-vibrant-green transition">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-vibrant-green">Weekly Overview</li>
                    </ol>
                </nav>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Weekly Roadmap</h2>
                <p class="text-gray-500 font-medium max-w-md leading-relaxed">A holistic view of the academic week. Track session distribution across the next 7 days.</p>
            </div>
            
            <div class="flex flex-col items-end gap-4">
                <div class="flex bg-gray-100 p-1.5 rounded-2xl shadow-inner border border-gray-200/50">
                    <a href="{{ route('scheduler.attendance.create', ['view' => 'daily', 'date' => $date]) }}" class="px-6 py-2.5 rounded-xl text-xs font-black text-gray-400 hover:text-gray-600 transition">Daily</a>
                    <a href="#" class="px-6 py-2.5 rounded-xl text-xs font-black bg-white text-gray-900 shadow-sm border border-gray-100 transition active:scale-95">Weekly</a>
                </div>
                
                <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-xl border border-gray-100/50">
                    <a href="{{ route('scheduler.attendance.create', ['view' => 'weekly', 'date' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-vibrant-green hover:text-white text-gray-400 transition-all shadow-sm">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <div class="px-6 text-center">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Week Range</div>
                        <div class="text-sm font-black text-gray-800">{{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d') }}</div>
                    </div>
                    <a href="{{ route('scheduler.attendance.create', ['view' => 'weekly', 'date' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-vibrant-green hover:text-white text-gray-400 transition-all shadow-sm">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Vertical Weekly Roadmap -->
    <div class="space-y-12 mb-20">
        @for($i = 0; $i < 7; $i++)
            @php 
                $day = $weekStart->copy()->addDays($i); 
                $dayKey = $day->format('Y-m-d');
                $daySchedules = $schedules->get($dayKey, collect());
                $isToday = $day->isToday();
            @endphp
            
            <div class="relative">
                @if($i < 6)
                    <div class="absolute left-6 top-16 bottom-0 w-[2px] bg-gray-100 hidden md:block"></div>
                @endif
                
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Date Sidebar -->
                    <div class="flex md:flex-col items-center md:items-end gap-4 md:w-32 flex-shrink-0">
                        <div class="text-right hidden md:block">
                            <div class="text-[10px] font-black uppercase tracking-widest {{ $isToday ? 'text-vibrant-green' : 'text-gray-400' }}">{{ $day->format('l') }}</div>
                            <div class="text-xs font-bold text-gray-400">{{ $day->format('M d') }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-lg {{ $isToday ? 'bg-vibrant-green text-white ring-4 ring-vibrant-green/20' : 'bg-white text-gray-800 border border-gray-100' }}">
                            <span class="text-lg font-black">{{ $day->format('d') }}</span>
                        </div>
                        <div class="md:hidden">
                            <div class="text-sm font-black text-gray-800">{{ $day->format('l') }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $day->format('M d') }}</div>
                        </div>
                    </div>

                    <!-- Session Cards -->
                    <div class="flex-1">
                        @if($daySchedules->count() > 0)
                            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($daySchedules as $s)
                                    <a href="{{ route('scheduler.attendance.verify', $s->id) }}" class="block bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group relative overflow-hidden active:scale-95">
                                        @if($s->attendance)
                                            <div class="absolute top-4 right-4 text-green-500">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="px-2.5 py-1 bg-gray-50 rounded-lg text-[10px] font-black text-gray-500 border border-gray-100">
                                                {{ $s->starts_at->format('h:i A') }}
                                            </div>
                                            <span class="text-[9px] font-black uppercase tracking-[0.1em] {{ $s->status === 'scheduled' ? 'text-blue-500' : 'text-green-500' }}">{{ $s->status }}</span>
                                        </div>

                                        <div class="space-y-3 mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-black border border-blue-100">S</div>
                                                <div class="text-xs font-black text-gray-800 truncate">{{ $s->student->name }}</div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-[10px] font-black border border-purple-100">T</div>
                                                <div class="text-xs font-bold text-gray-500 truncate">{{ $s->teacher->name }}</div>
                                            </div>
                                        </div>

                                        <div class="pt-4 border-t border-gray-100 flex justify-between items-center mt-2">
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest leading-none mb-1">Course</span>
                                                <span class="text-[11px] font-black text-gray-700 truncate max-w-[100px]">{{ $s->course->title ?? 'General' }}</span>
                                            </div>
                                            <div class="w-8 h-8 rounded-full bg-vibrant-green text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="py-12 px-8 bg-gray-50/30 rounded-[3rem] border border-dashed border-gray-200 text-center group hover:bg-white transition-colors">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-gray-200 mx-auto mb-4 border border-gray-100 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-calendar-day text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Rest Day - No Sessions</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endfor
    </div>
</x-dashboard-layout>
