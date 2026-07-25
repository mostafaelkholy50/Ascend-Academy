<x-dashboard-layout title="{{ $teacher->name }}'s Hours">
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('accountant.teacher-hours.index', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="w-10 h-10 rounded-full bg-white text-gray-500 flex items-center justify-center hover:bg-gray-100 transition shadow-sm border border-gray-200">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $teacher->name }}'s Hours & Earnings</h1>
                <p class="text-gray-600 text-sm">Detailed breakdown of logged hours for this teacher</p>
            </div>
        </div>
    </div>

    <!-- Month Selector -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('accountant.teacher-hours.show', $teacher->id) }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                <select name="month" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    @foreach($months as $value => $label)
                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <select name="year" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-filter mr-2"></i>View
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Hours -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Total Hours Worked</h3>
                <i class="fa-solid fa-clock text-2xl opacity-80"></i>
            </div>
            <p class="text-3xl font-bold">{{ number_format($totalHours, 2) }}</p>
            @if(isset($bonusHours) && $bonusHours > 0)
                <div class="flex items-center text-xs opacity-90 mt-1">
                    <i class="fa-solid fa-gift mr-1"></i>
                    <span>Includes {{ number_format($bonusHours, 1) }} hrs Evaluation Bonus</span>
                </div>
            @endif
            <p class="text-xs opacity-80 mt-1">{{ $date->format('F Y') }}</p>
        </div>

        <!-- Hourly Rate -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Hourly Rate</h3>
                <i class="fa-solid fa-dollar-sign text-2xl opacity-80"></i>
            </div>
            <p class="text-3xl font-bold">${{ number_format($hourlyRate, 2) }}</p>
            <p class="text-xs opacity-80 mt-1">Per hour</p>
        </div>

        <!-- Total Earnings -->
        <div class="bg-gradient-to-br from-vibrant-green to-green-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Total Earnings</h3>
                <i class="fa-solid fa-money-bill-wave text-2xl opacity-80"></i>
            </div>
            <p class="text-3xl font-bold">${{ number_format($totalEarnings, 2) }}</p>
            <p class="text-xs opacity-80 mt-1">{{ $date->format('F Y') }}</p>
        </div>
    </div>

    <!-- Attended Schedules List -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Attended Sessions</h2>
            <p class="text-sm text-gray-600">Sessions where both you and the student were present</p>
        </div>

        @if($attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Earnings</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendances as $attendance)
                            @php
                                $schedule = $attendance->schedule;
                                $isHalfTime = (!$attendance->student_present && $attendance->remark === 'Waited Half Time');
                                $hours = $schedule->getDurationInHours();
                                if ($isHalfTime) {
                                    $hours = $hours / 2;
                                }
                                $earnings = $hours * $hourlyRate;
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $isHalfTime ? 'bg-orange-50/30' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->starts_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-xs mr-3">
                                            {{ strtoupper(substr($schedule->student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $schedule->student->name }}</div>
                                            @if($isHalfTime)
                                                <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-medium bg-orange-100 text-orange-800">
                                                    <i class="fa-solid fa-clock mr-1"></i> Waited Half Time
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->course->title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->starts_at->format('g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->ends_at->format('g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                        {{ number_format($hours, 2) }} hrs
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-vibrant-green">
                                    ${{ number_format($earnings, 2) }}
                                </td>
                            </tr>
                        @endforeach

                        @if(isset($bonusHours) && $bonusHours > 0)
                            <tr class="bg-amber-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    -
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-xs mr-3">
                                            <i class="fa-solid fa-gift"></i>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">Evaluation Bonus</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Bonus for completing evaluations
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    -
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    -
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">
                                        {{ number_format($bonusHours, 2) }} hrs
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-vibrant-green">
                                    ${{ number_format($bonusHours * $hourlyRate, 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attendances->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg font-medium">No attended sessions found</p>
                <p class="text-gray-400 text-sm mb-4">
                    @if($hourlyRate == 0)
                        This teacher's hourly rate has not been set yet.
                    @else
                        No sessions where both the teacher and the student were present in {{ $date->format('F Y') }}
                    @endif
                </p>
            </div>
        @endif
    </div>
</x-dashboard-layout>
