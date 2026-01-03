<x-dashboard-layout title="Attendance Records">
    @php $parent = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">Attendance Records</h1>
        <p class="text-gray-600">Track your children's attendance history</p>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-md mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Child Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Child</label>
                <select name="child_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="all" {{ request('child_id') == 'all' ? 'selected' : '' }}>All Children</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ request('child_id') == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="md:col-span-3 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('parent.attendance.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    @if(count($stats) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($stats), 4) }} gap-4 mb-8">
            @foreach($stats as $childId => $stat)
                <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-child text-indigo-600 mr-2"></i>
                        {{ $stat['name'] }}
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Present</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg font-bold">{{ $stat['present'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Absent</span>
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg font-bold">{{ $stat['absent'] }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Attendance Rate</span>
                                <span class="text-lg font-bold text-green-600">{{ $stat['rate'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width:{{ $stat['rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Attendance Records List -->
    @if($attendances->count() > 0)
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
            <div class="divide-y divide-gray-100">
                @foreach($attendances as $attendance)
                    <div class="p-5 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-grow">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center text-white shadow-md">
                                    <i class="fa-solid fa-clipboard-check text-xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-bold text-gray-800">{{ $attendance->schedule->course->title ?? 'Session' }}</h4>
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold">
                                            {{ $attendance->student->name }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                        <span><i class="fa-solid fa-user-tie mr-1 text-green-600"></i>{{ $attendance->schedule->teacher->name }}</span>
                                        <span><i class="fa-solid fa-calendar mr-1 text-green-600"></i>{{ $attendance->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($attendance->remark)
                                        <p class="text-sm text-gray-600 mt-2">
                                            <i class="fa-solid fa-comment mr-1"></i>{{ $attendance->remark }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <span class="px-4 py-2 rounded-xl text-sm font-bold {{ $attendance->student_present ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    <i class="fa-solid {{ $attendance->student_present ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                    {{ $attendance->student_present ? 'Present' : 'Absent' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $attendances->links() }}
        </div>
    @else
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-16 rounded-3xl shadow-md text-center border border-green-200">
            <div class="bg-gradient-to-br from-green-100 to-emerald-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-clipboard-check text-green-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Attendance Records</h3>
            <p class="text-gray-600">No attendance records found for the selected filters.</p>
        </div>
    @endif
</x-dashboard-layout>
