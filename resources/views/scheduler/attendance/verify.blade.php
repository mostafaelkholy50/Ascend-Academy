<x-dashboard-layout title="Verify Session">
    <div class="h-[calc(100vh-120px)] flex flex-col">
        <!-- Header & Breadcrumbs -->
        <div class="flex-shrink-0 mb-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">
                    <li><a href="{{ route('scheduler.dashboard') }}" class="hover:text-vibrant-green transition">Dashboard</a></li>
                    <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                    <li><a href="{{ route('scheduler.attendance.create', ['view' => 'weekly']) }}" class="hover:text-vibrant-green transition">Weekly Roadmap</a></li>
                    <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                    <li class="text-vibrant-green">Session Verification</li>
                </ol>
            </nav>

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight leading-tight">Session Verification</h2>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Reviewing Academic Performance</p>
                </div>
                <div class="px-5 py-2.5 bg-vibrant-green/10 text-vibrant-green rounded-2xl text-[10px] font-black uppercase tracking-widest border border-vibrant-green/20">
                    {{ $schedule->starts_at->format('M d, Y | h:i A') }}
                </div>
            </div>
        </div>

        <!-- Main Form Area (Scrollable if needed) -->
        <form action="{{ route('scheduler.attendance.store') }}" method="POST" class="flex-1 flex flex-col min-h-0">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
            <input type="hidden" name="redirect_url" value="{{ route('scheduler.attendance.create', ['view' => 'weekly', 'date' => $schedule->starts_at->format('Y-m-d')]) }}">

            <div class="flex-1 overflow-y-auto overflow-x-hidden pr-4 -mr-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Card -->
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-xl border border-blue-50 relative overflow-hidden group h-full">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg font-black shadow-inner">S</div>
                                <div>
                                    <div class="text-[9px] font-black text-blue-400 uppercase tracking-[0.2em] mb-0.5">Student</div>
                                    <div class="text-lg font-black text-gray-900 leading-tight">{{ $schedule->student->name }}</div>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <div class="flex bg-gray-50 p-1 rounded-xl border border-gray-100">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="student_present" value="1" {{ ($attendance && $attendance->student_present) || !$attendance ? 'checked' : '' }} class="hidden peer" onchange="toggleReportRequired('student', true)">
                                            <div class="py-2.5 rounded-lg text-center text-[9px] font-black uppercase tracking-widest text-gray-400 peer-checked:bg-vibrant-green peer-checked:text-white transition shadow-sm">Present</div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="student_present" value="0" {{ $attendance && !$attendance->student_present ? 'checked' : '' }} class="hidden peer" onchange="toggleReportRequired('student', false)">
                                            <div class="py-2.5 rounded-lg text-center text-[9px] font-black uppercase tracking-widest text-gray-400 peer-checked:bg-red-500 peer-checked:text-white transition shadow-sm">Absent</div>
                                        </label>
                                    </div>
                                </div>

                                <div class="relative">
                                    <textarea name="student_report" id="student_report" rows="3" 
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-vibrant-green focus:border-transparent transition placeholder:text-gray-300"
                                        placeholder="Describe the student's progress...">{{ $attendance->student_report ?? '' }}</textarea>
                                    <span id="student_report_star" class="absolute top-2 right-4 text-red-500 hidden">*</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teacher Card -->
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-xl border border-purple-50 relative overflow-hidden group h-full">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg font-black shadow-inner">T</div>
                                <div>
                                    <div class="text-[9px] font-black text-purple-400 uppercase tracking-[0.2em] mb-0.5">Teacher</div>
                                    <div class="text-lg font-black text-gray-900 leading-tight">{{ $schedule->teacher->name }}</div>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <div class="flex bg-gray-50 p-1 rounded-xl border border-gray-100">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="teacher_present" value="1" {{ ($attendance && $attendance->teacher_present) || !$attendance ? 'checked' : '' }} class="hidden peer" onchange="toggleReportRequired('teacher', true)">
                                            <div class="py-2.5 rounded-lg text-center text-[9px] font-black uppercase tracking-widest text-gray-400 peer-checked:bg-vibrant-green peer-checked:text-white transition shadow-sm">Present</div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="teacher_present" value="0" {{ $attendance && !$attendance->teacher_present ? 'checked' : '' }} class="hidden peer" onchange="toggleReportRequired('teacher', false)">
                                            <div class="py-2.5 rounded-lg text-center text-[9px] font-black uppercase tracking-widest text-gray-400 peer-checked:bg-red-500 peer-checked:text-white transition shadow-sm">Absent</div>
                                        </label>
                                    </div>
                                </div>

                                <div class="relative">
                                    <textarea name="teacher_report" id="teacher_report" rows="3" 
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-vibrant-green focus:border-transparent transition placeholder:text-gray-300"
                                        placeholder="Describe teacher performance...">{{ $attendance->teacher_report ?? '' }}</textarea>
                                    <span id="teacher_report_star" class="absolute top-2 right-4 text-red-500 hidden">*</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                            <i class="fa-solid fa-comment-dots text-[10px]"></i>
                        </div>
                        <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Internal Remarks (Optional)</h4>
                    </div>
                    <textarea name="remark" rows="1" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-medium focus:ring-2 focus:ring-vibrant-green focus:border-transparent transition placeholder:text-gray-300" placeholder="Any additional internal notes...">{{ $attendance->remark ?? '' }}</textarea>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex-shrink-0 pt-6 flex gap-4 mt-auto">
                <button type="submit" class="flex-1 py-5 bg-vibrant-green text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-deep-blue transition shadow-xl shadow-vibrant-green/20 active:scale-[0.98]">
                    Save & Submit Verification
                </button>
                <a href="{{ route('scheduler.attendance.create', ['view' => 'weekly']) }}" class="px-8 py-5 bg-white text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 border border-gray-100 transition">
                    Discard
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleReportRequired(type, isPresent) {
            const textarea = document.getElementById(type + '_report');
            const star = document.getElementById(type + '_report_star');
            if (isPresent) {
                textarea.required = false;
                star.classList.add('hidden');
            } else {
                textarea.required = true;
                star.classList.remove('hidden');
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            toggleReportRequired('student', document.querySelector('input[name="student_present"]:checked').value == "1");
            toggleReportRequired('teacher', document.querySelector('input[name="teacher_present"]:checked').value == "1");
        });
    </script>
</x-dashboard-layout>
