<x-dashboard-layout title="Accountant Dashboard">
    <div class="space-y-8 animate-fade-in">
        <!-- Welcome Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-deep-blue to-black p-8 rounded-[2.5rem] shadow-2xl text-white">
            <div class="relative z-10">
                <h2 class="text-3xl font-black tracking-tight">Financial Overview</h2>
                <p class="text-white/60 text-sm mt-2 max-w-xl leading-relaxed">
                    Welcome back, {{ auth()->user()->name }}. You are currently monitoring sessions and personnel for 
                    @if(empty($allowedCountries))
                        <span class="text-vibrant-green font-bold">Global Regions (All)</span>.
                    @else
                        <span class="text-vibrant-green font-bold">{{ implode(', ', $allowedCountries) }}</span>.
                    @endif
                </p>
            </div>
            <!-- Decorative background element -->
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-vibrant-green/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 group hover:border-vibrant-green/30 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user-graduate text-xl"></i>
                    </div>
                    <div class="text-2xl font-black text-gray-900">{{ number_format($stats['total_students']) }}</div>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Target Students</div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 group hover:border-vibrant-green/30 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chalkboard-teacher text-xl"></i>
                    </div>
                    <div class="text-2xl font-black text-gray-900">{{ number_format($stats['total_teachers']) }}</div>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Target Teachers</div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 group hover:border-vibrant-green/30 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-vibrant-green/5 flex items-center justify-center text-vibrant-green group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calendar-check text-xl"></i>
                    </div>
                    <div class="text-2xl font-black text-gray-900">{{ number_format($stats['today_sessions']) }}</div>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sessions Today</div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 group hover:border-vibrant-green/30 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <div class="text-2xl font-black text-gray-900">{{ number_format($stats['pending_payments']) }}</div>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Awaiting Billing</div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-xl font-black text-gray-800 mb-1">Revenue Analytics</h3>
            <p class="text-xs text-gray-400 mb-6 uppercase tracking-widest font-bold">Monthly revenue for the last 6 months</p>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-gray-800">Regional Session Activity</h3>
                    <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold">Latest session logs in your regions</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase tracking-widest font-black">
                            <th class="px-10 py-5 text-left">Session Identity</th>
                            <th class="px-10 py-5 text-left">Student (Location)</th>
                            <th class="px-10 py-5 text-left">Teacher</th>
                            <th class="px-10 py-5 text-left">Schedule</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentSchedules as $schedule)
                            <tr class="group hover:bg-gray-50/40 transition-colors">
                                <td class="px-10 py-6">
                                    <div class="font-bold text-gray-800">{{ $schedule->course->title ?? 'General Session' }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $schedule->status }}</div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800">{{ $schedule->student->name }}</span>
                                        <span class="text-[10px] text-vibrant-green font-black uppercase tracking-widest">
                                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $schedule->student->country ?? 'Unknown' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="text-sm font-medium text-gray-600">{{ $schedule->teacher->name }}</div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="text-xs font-bold text-gray-800">{{ $schedule->getStartsAtInTimezone(auth()->user()->getUserTimezone())->format('M d, Y') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $schedule->getStartsAtInTimezone(auth()->user()->getUserTimezone())->format('h:i A') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 mb-4">
                                            <i class="fa-solid fa-calendar-xmark text-2xl"></i>
                                        </div>
                                        <h4 class="text-gray-400 font-bold">No recent activity found in your assigned regions.</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const chartData = @json($monthlyRevenue);
            
            const labels = chartData.map(data => data.month);
            const values = chartData.map(data => data.revenue);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue',
                        data: values,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-dashboard-layout>
