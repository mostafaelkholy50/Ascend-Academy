<x-dashboard-layout title="Daily Attendance Sheet">
    <!-- Premium Header -->
    <div class="relative mb-10">
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 relative z-10">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                        <li><a href="{{ route('scheduler.dashboard') }}" class="hover:text-vibrant-green transition">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-vibrant-green">Attendance Sheet</li>
                    </ol>
                </nav>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Daily Session Sheet</h2>
                <p class="text-gray-500 font-medium max-w-md leading-relaxed">Efficiently manage and verify attendance for all academic sessions scheduled for today.</p>
            </div>
            
            <div class="flex flex-col items-end gap-4">
                <div class="flex bg-gray-100 p-1.5 rounded-2xl shadow-inner border border-gray-200/50">
                    <a href="#" class="px-6 py-2.5 rounded-xl text-xs font-black bg-white text-gray-900 shadow-sm border border-gray-100 transition active:scale-95">Daily</a>
                    <a href="{{ route('scheduler.attendance.create', ['view' => 'weekly', 'date' => $date]) }}" class="px-6 py-2.5 rounded-xl text-xs font-black text-gray-400 hover:text-gray-600 transition">Weekly</a>
                </div>
                
                <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-xl border border-gray-100/50">
                    <a href="{{ route('scheduler.attendance.create', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-vibrant-green hover:text-white text-gray-400 transition-all shadow-sm">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <div class="px-4 text-center">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : \Carbon\Carbon::parse($date)->format('l') }}</div>
                        <div class="text-sm font-black text-gray-800">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</div>
                    </div>
                    <a href="{{ route('scheduler.attendance.create', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-vibrant-green hover:text-white text-gray-400 transition-all shadow-sm">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Insights -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 group hover:border-vibrant-green/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-layer-group text-xl"></i>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</div>
            </div>
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Sessions</div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 group hover:border-vibrant-green/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-check-double text-xl"></i>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $stats['completed'] }}</div>
            </div>
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Completed</div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 group hover:border-vibrant-green/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $stats['total'] - $stats['completed'] }}</div>
            </div>
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending Verification</div>
        </div>
        <div class="bg-gradient-to-br from-vibrant-green to-deep-blue p-6 rounded-[2.5rem] shadow-xl text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white">
                    <i class="fa-solid fa-percentage text-xl"></i>
                </div>
                <div class="text-2xl font-black text-white">{{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}%</div>
            </div>
            <div class="text-[10px] font-black text-white/70 uppercase tracking-widest">Progress Rate</div>
        </div>
    </div>

    <!-- Main List -->
    @if($schedules->count() > 0)
        <div class="bg-white rounded-[3rem] shadow-xl border border-gray-100 overflow-hidden mb-10">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Session Info</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Participants</th>
                            <th class="px-8 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Verification Status</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($schedules as $s)
                            <tr class="group hover:bg-gray-50/80 transition-colors cursor-pointer" onclick="window.location='{{ route('scheduler.attendance.verify', $s->id) }}'">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="px-3 py-1 bg-vibrant-green/10 text-vibrant-green rounded-lg text-xs font-black">
                                            {{ $s->starts_at->format('h:i A') }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900">{{ $s->course->title ?? 'Academic Session' }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Status: {{ ucfirst($s->status) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-black border border-blue-200">S</div>
                                            <span class="text-xs font-bold text-gray-700">{{ $s->student->name }}</span>
                                        </div>
                                        <div class="w-4 h-[1px] bg-gray-200"></div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-[10px] font-black border border-purple-200">T</div>
                                            <span class="text-xs font-bold text-gray-700">{{ $s->teacher->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($s->attendance)
                                        <div class="flex items-center justify-center gap-4">
                                            <div class="flex items-center gap-1.5 {{ $s->attendance->student_present ? 'text-green-600' : 'text-red-500' }}">
                                                <i class="fa-solid {{ $s->attendance->student_present ? 'fa-check-circle' : 'fa-times-circle' }} text-xs"></i>
                                                <span class="text-[9px] font-black uppercase tracking-widest">Stu</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 {{ $s->attendance->teacher_present ? 'text-green-600' : 'text-red-500' }}">
                                                <i class="fa-solid {{ $s->attendance->teacher_present ? 'fa-check-circle' : 'fa-times-circle' }} text-xs"></i>
                                                <span class="text-[9px] font-black uppercase tracking-widest">Tea</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-400 text-[8px] font-black uppercase tracking-widest rounded-full">Not Marked</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-vibrant-green text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-deep-blue transition shadow-lg shadow-vibrant-green/20">
                                        <i class="fa-solid fa-file-signature"></i>
                                        <span>{{ $s->attendance ? 'Edit' : 'Verify' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-[3rem] p-32 text-center border border-gray-100 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 mx-auto mb-8 border border-gray-100">
                <i class="fa-solid fa-calendar-xmark text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">No Sessions Found</h3>
            <p class="text-gray-400 font-medium max-w-xs mx-auto leading-relaxed">Relax! There are no academic sessions scheduled for this specific date.</p>
        </div>
    @endif

</x-dashboard-layout>
