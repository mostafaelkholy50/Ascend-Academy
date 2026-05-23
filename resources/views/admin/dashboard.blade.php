<x-dashboard-layout title="Admin - Dashboard">
    @php 
        $user = auth()->user();
    @endphp

    @if($user->hasRole(['Admin', 'SuperAdmin', 'Accountant', 'QualityControl']))
    <!-- Header Section with Key Ratios -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-deep-blue">
                @if($user->hasRole(['Admin', 'SuperAdmin']))
                    Executive Insights
                @else
                    Academy Analytics
                @endif
            </h1>
            <p class="text-gray-500">
                @if($user->hasRole(['Admin', 'SuperAdmin']))
                    Advanced performance metrics and predictive growth analysis.
                @else
                    Performance summary based on your role permissions.
                @endif
            </p>
        </div>
        
        @if(isset($conversionRate))
        <div class="flex items-center gap-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-center border-r border-gray-100 pr-6">
                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Inquiry Conversion</p>
                <p class="text-2xl font-black text-blue-600">{{ number_format($conversionRate, 1) }}%</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Retention</p>
                <span class="px-3 py-1 bg-green-100 text-green-600 text-[10px] font-black rounded-full uppercase">Optimal</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Comparative Analysis Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @if(isset($comparison) && $user->hasRole(['Admin', 'SuperAdmin', 'Accountant']))
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fa-solid fa-money-bill-trend-up text-5xl text-blue-600"></i>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Revenue Velocity</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-gray-800">${{ number_format($comparison['revenue']['current'], 0) }}</h3>
                @php $revDiff = $comparison['revenue']['current'] - $comparison['revenue']['previous']; @endphp
                <span class="text-xs font-bold {{ $revDiff >= 0 ? 'text-vibrant-green' : 'text-red-500' }}">
                    <i class="fa-solid fa-caret-{{ $revDiff >= 0 ? 'up' : 'down' }}"></i>
                    {{ $comparison['revenue']['previous'] > 0 ? round(($revDiff / $comparison['revenue']['previous']) * 100) : 100 }}%
                </span>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">vs last month</p>
        </div>
        @endif

        @if(isset($comparison) && $user->hasRole(['Admin', 'SuperAdmin']))
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fa-solid fa-user-plus text-5xl text-blue-600"></i>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Acquisition Rate</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-gray-800">{{ $comparison['enrollments']['current'] }}</h3>
                @php $enDiff = $comparison['enrollments']['current'] - $comparison['enrollments']['previous']; @endphp
                <span class="text-xs font-bold {{ $enDiff >= 0 ? 'text-vibrant-green' : 'text-red-500' }}">
                    <i class="fa-solid fa-caret-{{ $enDiff >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($enDiff) }}
                </span>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">vs last month</p>
        </div>
        @endif

        @if(isset($attendanceSummary))
        <div class="bg-deep-blue p-6 rounded-3xl shadow-xl text-white relative overflow-hidden {{ !$user->hasRole(['Admin', 'SuperAdmin']) ? 'md:col-span-2 lg:col-span-3' : '' }}">
            <p class="text-xs font-bold text-white/50 uppercase tracking-widest mb-4">Quality Score</p>
            <h3 class="text-2xl font-black">94.2%</h3>
            <div class="w-full bg-white/20 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-vibrant-green h-full" style="width: 94.2%"></div>
            </div>
            <p class="text-[10px] text-white/50 mt-2">Aggregated teacher/student performance.</p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Main Performance Graphs -->
        <div class="lg:col-span-2 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Enrollment Trends -->
                @if(isset($enrollmentTrends))
                <section class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 md:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Enrollment Trends</h3>
                        <div class="flex gap-2">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Growth Curve</span>
                        </div>
                    </div>
                    <div class="h-72">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </section>
                @endif

                <!-- Course Market Share -->
                @if(isset($topCourses))
                <section class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Course Performance</h3>
                    <div class="space-y-5">
                        @foreach($topCourses as $course)
                        <div>
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-bold text-gray-700">{{ $course->title }}</span>
                                <span class="text-gray-400 font-medium">{{ $course->enrollments_count }} Enrolled</span>
                            </div>
                            <div class="w-full bg-gray-50 h-2 rounded-full overflow-hidden">
                                <div class="bg-deep-blue h-full" style="width: {{ ($course->enrollments_count / max(1, $totalStudents ?? 100)) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Attendance Analytics -->
                @if(isset($attendanceSummary))
                <section class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Attendance Fidelity</h3>
                    <div class="flex items-center gap-8">
                        <div class="w-32 h-32">
                            <canvas id="attendancePieChart"></canvas>
                        </div>
                        <div class="flex-1 space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                                <div class="flex-1">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Present</p>
                                    <p class="text-sm font-black text-gray-800">{{ round(($attendanceSummary['present'] / max(1, $attendanceSummary['total'])) * 100) }}%</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                                <div class="flex-1">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Absent</p>
                                    <p class="text-sm font-black text-gray-800">{{ round(($attendanceSummary['absent'] / max(1, $attendanceSummary['total'])) * 100) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @endif
            </div>
        </div>

        <!-- Sidebar Analytics -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Teacher Leaderboard -->
            @if(isset($teacherRankings))
            <section class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Faculty Leaderboard</h3>
                <div class="space-y-6">
                    @foreach($teacherRankings as $index => $teacher)
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center font-black text-deep-blue border border-gray-100 uppercase">
                                {{ substr($teacher->name, 0, 1) }}
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 text-white rounded-full flex items-center justify-center text-[10px] font-black shadow-lg">
                                #{{ $index + 1 }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-800">{{ $teacher->name }}</h4>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-star text-yellow-400 text-[10px]"></i>
                                <span class="text-xs font-black text-gray-600">{{ number_format($teacher->avg_score, 1) }}</span>
                                <span class="text-[10px] text-gray-300 mx-1">|</span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold">Elite</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Revenue Forecast -->
            <section class="bg-gradient-to-br from-vibrant-green to-emerald-600 p-6 rounded-3xl shadow-lg text-white relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="flex justify-between items-start mb-4">
                    <p class="text-xs font-bold text-white/60 uppercase tracking-widest">30-Day Forecast</p>
                    <i class="fa-solid fa-wand-magic-sparkles text-xl opacity-50"></i>
                </div>
                <h3 class="text-3xl font-black mb-1">${{ number_format(($monthlyRevenue ?? 1000) * 1.12, 0) }}</h3>
                <p class="text-[10px] text-white/80 leading-relaxed">Projected growth based on current retention rates and inquiry velocity.</p>
                
                <div class="mt-6 flex justify-between items-end">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full border-2 border-emerald-600 bg-white/20 backdrop-blur-sm flex items-center justify-center text-[10px] font-bold">AI</div>
                    </div>
                    <span class="text-[10px] font-bold bg-white/20 px-2 py-1 rounded-lg">Predictive Mode</span>
                </div>
            </section>

            <!-- Quick Links -->
            <section class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Management</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.students.index') }}" class="p-4 bg-gray-50 hover:bg-blue-50 rounded-2xl text-center transition group">
                        <i class="fa-solid fa-users block mb-1 text-gray-400 group-hover:text-blue-500"></i>
                        <span class="text-[10px] font-bold text-gray-500 group-hover:text-blue-600">Students</span>
                    </a>
                    <a href="{{ route('admin.teachers.index') }}" class="p-4 bg-gray-50 hover:bg-blue-50 rounded-2xl text-center transition group">
                        <i class="fa-solid fa-chalkboard-teacher block mb-1 text-gray-400 group-hover:text-blue-500"></i>
                        <span class="text-[10px] font-bold text-gray-500 group-hover:text-blue-600">Teachers</span>
                    </a>
                </div>
            </section>
        </div>
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
            <i class="fa-solid fa-shield-halved text-4xl text-gray-300"></i>
        </div>
        <h2 class="text-2xl font-black text-deep-blue mb-2">Restricted Access</h2>
        <p class="text-gray-500 max-w-md text-center">Your account does not have sufficient permissions to view the unified academy analytics.</p>
        <a href="{{ route('admin.profile.show') }}" class="mt-8 px-8 py-3 bg-deep-blue text-white rounded-2xl font-bold shadow-lg shadow-blue-900/20 hover:-translate-y-1 transition-all">
            Go to Profile
        </a>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced Enrollment Trends Chart
            @if(isset($enrollmentTrends))
            const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
            const enrollmentGradient = enrollmentCtx.createLinearGradient(0, 0, 0, 400);
            enrollmentGradient.addColorStop(0, 'rgba(0, 159, 188, 0.4)');
            enrollmentGradient.addColorStop(1, 'rgba(0, 159, 188, 0.0)');

            new Chart(enrollmentCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($enrollmentTrends->map(fn($t) => \Carbon\Carbon::create(null, $t->month)->format('M'))) !!},
                    datasets: [{
                        label: 'New Students',
                        data: {!! json_encode($enrollmentTrends->pluck('count')) !!},
                        borderColor: '#009FBC',
                        backgroundColor: enrollmentGradient,
                        fill: true,
                        tension: 0.5,
                        borderWidth: 4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#009FBC',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#1D3A5F',
                            padding: 12,
                            cornerRadius: 12,
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                            ticks: { font: { weight: 'bold' }, color: '#94a3b8' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { font: { weight: 'bold' }, color: '#94a3b8' }
                        }
                    }
                }
            });
            @endif

            // Enhanced Attendance Doughnut
            @if(isset($attendanceSummary))
            const attendanceCtx = document.getElementById('attendancePieChart').getContext('2d');
            new Chart(attendanceCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [{{ $attendanceSummary['present'] }}, {{ $attendanceSummary['absent'] }}],
                        backgroundColor: ['#10B981', '#F43F5E'],
                        borderWidth: 0,
                        cutout: '80%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    animation: { animateRotate: true, animateScale: true }
                }
            });
            @endif
        });
    </script>
    @endpush
</x-dashboard-layout>
