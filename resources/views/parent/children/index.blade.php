<x-dashboard-layout title="My Children">
    @php $parent = auth()->user(); @endphp

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">My Children</h1>
        <p class="text-gray-600">Manage and track your children's progress</p>
    </div>

    @if($children->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($children as $child)
                <a href="{{ route('parent.children.show', $child->id) }}" class="group block">
                    <div class="bg-white p-6 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-indigo-300">
                        <!-- Child Header -->
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center text-white text-3xl font-bold shadow-xl group-hover:scale-110 transition-transform">
                                {{ strtoupper(substr($child->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow">
                                <h3 class="font-bold text-gray-800 text-xl">{{ $child->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fa-solid fa-envelope mr-1"></i>{{ $child->email }}
                                </p>
                                @if($child->phone)
                                    <p class="text-sm text-gray-500">
                                        <i class="fa-solid fa-phone mr-1"></i>{{ $child->phone }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl text-center border border-blue-100">
                                <p class="text-3xl font-bold text-indigo-600">{{ $child->stats['active_courses'] }}</p>
                                <p class="text-xs text-gray-600 mt-1">Active Courses</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl text-center border border-green-100">
                                <p class="text-3xl font-bold text-green-600">{{ $child->stats['completed_sessions'] }}</p>
                                <p class="text-xs text-gray-600 mt-1">Completed</p>
                            </div>
                        </div>

                        <!-- Attendance Rate -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Attendance Rate</span>
                                <span class="text-lg font-bold text-purple-600">{{ $child->stats['attendance_rate'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 h-2.5 rounded-full transition-all duration-500" style="width:{{ $child->stats['attendance_rate'] }}%"></div>
                            </div>
                        </div>

                        <!-- Latest Report -->
                        @if($child->stats['latest_report_date'])
                            <div class="mt-4 p-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-file-lines text-amber-600"></i>
                                        <span class="text-xs font-medium text-gray-700">Latest Report</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($child->stats['latest_report_date'])->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- View Details Button -->
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center gap-2 text-indigo-600 font-semibold group-hover:gap-3 transition-all">
                                View Details <i class="fa-solid fa-arrow-right text-sm"></i>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-16 rounded-3xl shadow-md text-center border border-indigo-200">
            <div class="bg-gradient-to-br from-indigo-100 to-purple-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-child text-indigo-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Children Registered</h3>
            <p class="text-gray-600 mb-4">Contact the admin to add your children to the system</p>
        </div>
    @endif
</x-dashboard-layout>
