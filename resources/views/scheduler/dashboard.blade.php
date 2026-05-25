<x-dashboard-layout title="Scheduler Dashboard">
    @php $user = auth()->user(); @endphp

    <!-- Hero Welcome -->
    <x-dashboard.hero-welcome :user="$user" message="Welcome to the Scheduling Management System. You have {{ $todaySchedules->count() }} sessions today." />

    <div class="flex flex-col lg:flex-row gap-4 md:gap-6">
        <div class="flex-grow space-y-4 md:space-y-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                 <a href="{{route('scheduler.students.index')}}">
                    <x-dashboard.stat-card icon="fa-user-graduate" title="Students" :value="number_format($totalStudents)" color="green" />
                </a>        
                <a href="{{route('scheduler.teachers.index')}}">
                    <x-dashboard.stat-card icon="fa-chalkboard-teacher" title="Teachers" :value="number_format($totalTeachers)" color="purple" />
                </a>
                <a href="{{route('scheduler.schedules.index')}}">
                    <x-dashboard.stat-card icon="fa-calendar-alt" title="Today's Sessions" :value="number_format($todaySchedules->count())" color="blue" />
                </a>
                <a href="{{route('scheduler.attendance.index')}}">
                    <x-dashboard.stat-card icon="fa-user-clock" title="Pending Attendance" :value="number_format($pendingAttendance)" color="red" />
                </a>
                @can('manage accounting')
                <a href="{{route('accountant.payments.index')}}">
                    <x-dashboard.stat-card icon="fa-money-bill-wave" title="Monthly Revenue" :value="'$'.number_format($monthlyRevenue)" color="teal" />
                </a>
                @endcan
                @can('manage quality')
                <a href="{{route('admin.reports.index')}}">
                    <x-dashboard.stat-card icon="fa-chart-line" title="Total Reports" :value="number_format($totalReports)" color="orange" />
                </a>
                @endcan
            </div>

            <!-- User/Teacher Quick Search -->
            <section class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Quick Search</h3>
                    <div class="relative w-full max-w-md">
                        <form action="{{ route('scheduler.dashboard') }}" method="GET">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name, email or phone..." 
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-vibrant-green focus:border-vibrant-green text-sm transition shadow-sm">
                            <div class="absolute left-3 top-3 text-gray-400">
                                <i class="fa-solid fa-search text-sm"></i>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="search-results-container">
                    @include('scheduler.partials.search-results', ['searchResults' => $searchResults, 'search' => $search])
                </div>
            </section>

            <!-- Today's Schedule -->
            <section>
                <x-dashboard.section-header title="Today's Sessions" linkText="View All" linkHref="{{ route('scheduler.schedules.index') }}" />
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @if($todaySchedules->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm min-w-[600px]">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-3 md:px-4 py-3 text-left">Time</th>
                                        <th class="px-3 md:px-4 py-3 text-left">Student</th>
                                        <th class="px-3 md:px-4 py-3 text-left">Teacher</th>
                                        <th class="px-3 md:px-4 py-3 text-left">Course</th>
                                        <th class="px-3 md:px-4 py-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($todaySchedules as $schedule)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 md:px-4 py-3 font-medium text-vibrant-green">
                                                {{ $schedule->starts_at->format('h:i A') }}
                                            </td>
                                            <td class="px-3 md:px-4 py-3">{{ $schedule->student->name ?? 'N/A' }}</td>
                                            <td class="px-3 md:px-4 py-3">{{ $schedule->teacher->name ?? 'N/A' }}</td>
                                            <td class="px-3 md:px-4 py-3 text-gray-500">{{ $schedule->course->title ?? 'N/A' }}</td>
                                            <td class="px-3 md:px-4 py-3">
                                                <span class="px-2 py-1 rounded-full text-xs
                                                    {{ $schedule->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : '' }}
                                                    {{ $schedule->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                                    {{ $schedule->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center text-gray-500">
                            <i class="fa-solid fa-calendar-day text-4xl mb-3 text-gray-300"></i>
                            <p>No sessions scheduled for today</p>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Upcoming Schedules -->
            <section>
                <x-dashboard.section-header title="Upcoming Sessions" linkText="Manage" linkHref="{{ route('scheduler.schedules.index') }}" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    @foreach($upcomingSchedules as $schedule)
                        <div class="bg-white p-4 rounded-2xl shadow-sm border-l-4 border-vibrant-green">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-sm">{{ $schedule->student->name ?? 'N/A' }}</h4>
                                    <p class="text-xs text-gray-500">{{ $schedule->course->title ?? 'N/A' }}</p>
                                </div>
                                <span class="text-xs font-bold text-deep-blue bg-blue-50 px-2 py-1 rounded-lg">
                                    {{ $schedule->starts_at->format('M d, h:i A') }}
                                </span>
                            </div>
                            <div class="flex items-center text-xs text-gray-600 mt-2">
                                <i class="fa-solid fa-chalkboard-teacher mr-2 text-vibrant-green"></i>
                                <span>Teacher: {{ $schedule->teacher->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Right Sidebar -->
        <div class="w-full lg:w-72 space-y-4 md:space-y-6">
            <!-- Quick Actions -->
            <section class="bg-white p-4 md:p-5 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Scheduling Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('scheduler.schedules.create') }}" class="block w-full bg-vibrant-green text-white px-4 py-2.5 rounded-lg hover:bg-deep-blue transition text-center text-sm">
                        <i class="fa-solid fa-plus mr-2"></i>New Session
                    </a>
                    <a href="{{ route('scheduler.attendance.create') }}" class="block w-full bg-blue-500 text-white px-4 py-2.5 rounded-lg hover:bg-blue-600 transition text-center text-sm font-bold">
                        <i class="fa-solid fa-check-double mr-2"></i>Daily Attendance Sheet
                    </a>
                    <a href="{{ route('scheduler.students.index') }}" class="block w-full bg-purple-500 text-white px-4 py-2.5 rounded-lg hover:bg-purple-600 transition text-center text-sm">
                        <i class="fa-solid fa-clock mr-2"></i>Check Timezones
                    </a>
                </div>
            </section>

            <!-- User Counts -->
            <section class="bg-white p-4 md:p-5 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Total Managed</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Students</span>
                        <span class="text-lg font-bold text-green-600">{{ $totalStudents }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Teachers</span>
                        <span class="text-lg font-bold text-purple-600">{{ $totalTeachers }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const resultsContainer = document.getElementById('search-results-container');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                const query = this.value;
                clearTimeout(timeout);
                
                timeout = setTimeout(() => {
                    resultsContainer.style.opacity = '0.5';
                    fetch(`{{ route('scheduler.dashboard.search') }}?search=${encodeURIComponent(query)}`)
                        .then(response => response.text())
                        .then(html => {
                            resultsContainer.innerHTML = html;
                            resultsContainer.style.opacity = '1';
                        })
                        .catch(err => {
                            console.error('Search error:', err);
                            resultsContainer.style.opacity = '1';
                        });
                }, 300);
            });

            // Prevent form submission
            searchInput.closest('form').addEventListener('submit', (e) => e.preventDefault());
        });
    </script>
    @endpush
</x-dashboard-layout>
