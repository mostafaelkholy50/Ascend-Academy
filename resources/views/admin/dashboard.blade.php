<x-dashboard-layout title="Admin Dashboard">
    @php $user = auth()->user(); @endphp

    <!-- Hero Welcome -->
    <x-dashboard.hero-welcome :user="$user" :message="'You have ' . $newInquiries . ' pending registrations and ' . $recentEnrollments->count() . ' new enrollments.'" />

    <div class="flex flex-col lg:flex-row gap-4 md:gap-6">
        <div class="flex-grow space-y-4 md:space-y-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                 <a href="{{route('admin.students.index')}}">
                <x-dashboard.stat-card icon="fa-users" title="Total Users" :value="number_format($totalUsers)" color="blue" />
                </a>        
                <a href="{{route('admin.students.index')}}">
                <x-dashboard.stat-card icon="fa-user-graduate" title="Students" :value="number_format($totalStudents)" color="green" />
                </a>
                <a href="{{route('admin.teachers.index')}}">
                <x-dashboard.stat-card icon="fa-chalkboard-teacher" title="Teachers" :value="number_format($totalTeachers)" color="purple" />
                </a>
                <a href="{{route('admin.inquiries.index')}}">
                <x-dashboard.stat-card icon="fa-envelope" title="New Registrations" :value="number_format($newInquiries)" color="red" />
                </a>
            </div>

            <!-- Recent Enrollments -->
            <section>
                <x-dashboard.section-header title="Recent Enrollments" linkText="View All" linkHref="{{ route('admin.enrollments.index') }}" />
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @if($recentEnrollments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm min-w-[600px]">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-3 md:px-4 py-3 text-left">Student</th>
                                        <th class="px-3 md:px-4 py-3 text-left">Course</th>
                                        <th class="px-3 md:px-4 py-3 text-left">Status</th>
                                        <th class="px-3 md:px-4 py-3 text-left">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($recentEnrollments as $enrollment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 md:px-4 py-3">{{ $enrollment->student->name ?? 'N/A' }}</td>
                                            <td class="px-3 md:px-4 py-3">{{ $enrollment->course->title ?? 'N/A' }}</td>
                                            <td class="px-3 md:px-4 py-3">
                                                <span class="px-2 py-1 rounded-full text-xs
                                                    {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                                    {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                                    {{ $enrollment->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-3 md:px-4 py-3 text-gray-500">{{ $enrollment->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center text-gray-500">
                            <i class="fa-solid fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>No recent enrollments</p>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Recent Inquiries -->
            <section>
                <x-dashboard.section-header title="Recent Registrations" linkText="View All" linkHref="{{ route('admin.inquiries.index') }}" />
                @if($recentInquiries->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        @foreach($recentInquiries->take(2) as $inquiry)
                            <div class="bg-white p-4 rounded-2xl shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 text-sm md:text-base">{{ $inquiry->full_name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $inquiry->email }}</p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs
                                        {{ $inquiry->type === 'trial' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $inquiry->type === 'contact' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $inquiry->type === 'registration' ? 'bg-purple-100 text-purple-700' : '' }}">
                                        {{ ucfirst($inquiry->type) }}
                                    </span>
                                </div>
                                @if($inquiry->message)
                                    <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $inquiry->message }}</p>
                                @endif
                                <span class="text-xs text-gray-400">{{ $inquiry->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white p-8 rounded-2xl shadow-sm text-center text-gray-500">
                        <i class="fa-solid fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p>No recent registrations</p>
                    </div>
                @endif
            </section>
        </div>

        <!-- Right Sidebar -->
        <div class="w-full lg:w-72 space-y-4 md:space-y-6">
            <!-- Revenue Overview -->
            <section class="bg-white p-4 md:p-5 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Revenue This Month</h3>
                <p class="text-2xl md:text-3xl font-bold text-vibrant-green mb-2">${{ number_format($monthlyRevenue, 2) }}</p>
                <p class="text-xs text-gray-500">
                    @if($revenueGrowth > 0)
                        <span class="text-green-600">+{{ $revenueGrowth }}%</span> from last month
                    @elseif($revenueGrowth < 0)
                        <span class="text-red-600">{{ $revenueGrowth }}%</span> from last month
                    @else
                        No change from last month
                    @endif
                </p>
            </section>

            <!-- Users Breakdown -->
            <section class="bg-white p-4 md:p-5 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Users Breakdown</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Parents</span>
                        <span class="text-lg font-bold text-blue-600">{{ $totalParents }}</span>
                    </div>
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

            <!-- Quick Actions -->
            <section class="bg-white p-4 md:p-5 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.enrollments.index') }}" class="block w-full bg-vibrant-green text-white px-4 py-2.5 rounded-lg hover:bg-deep-blue transition text-center text-sm">
                        <i class="fa-solid fa-graduation-cap mr-2"></i>Manage Enrollments
                    </a>
                    <a href="{{ route('admin.inquiries.index') }}" class="block w-full bg-blue-500 text-white px-4 py-2.5 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-inbox mr-2"></i>View Registrations
                    </a>
                    <a href="{{ route('admin.parents.index') }}" class="block w-full bg-purple-500 text-white px-4 py-2.5 rounded-lg hover:bg-purple-600 transition text-center text-sm">
                        <i class="fa-solid fa-users mr-2"></i>Manage Parents
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="block w-full bg-teal-500 text-white px-4 py-2.5 rounded-lg hover:bg-teal-600 transition text-center text-sm">
                        <i class="fa-solid fa-user-graduate mr-2"></i>Manage Students
                    </a>
                    <a href="{{ route('admin.teacher-applications.index') }}" class="block w-full bg-orange-500 text-white px-4 py-2.5 rounded-lg hover:bg-orange-600 transition text-center text-sm">
                        <i class="fa-solid fa-chalkboard-teacher mr-2"></i>Teacher Applications
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-dashboard-layout>
