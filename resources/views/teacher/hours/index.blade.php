<x-dashboard-layout title="My Hours">
    <div class="mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 leading-tight">My Hours & Earnings</h1>
                <p class="text-gray-600 text-xs sm:text-sm">Track your worked hours and monthly earnings</p>
            </div>
        </div>
    </div>

    <!-- Month Selector -->
    <div class="bg-white rounded-2xl shadow-sm p-3 sm:p-6 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('teacher.hours.index') }}" class="grid grid-cols-3 gap-2 sm:flex sm:flex-wrap sm:gap-4 sm:items-end">
            <div class="min-w-0 sm:flex-1 sm:min-w-[200px]">
                <label class="block text-[10px] sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Month</label>
                <select name="month" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm px-2 py-2 sm:px-3 sm:py-2 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    @foreach($months as $value => $label)
                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 sm:flex-1 sm:min-w-[200px]">
                <label class="block text-[10px] sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Year</label>
                <select name="year" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm px-2 py-2 sm:px-3 sm:py-2 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-vibrant-green text-white px-2 sm:px-6 py-2 rounded-lg hover:bg-deep-blue transition font-semibold text-xs sm:text-sm whitespace-nowrap self-end">
                <i class="fa-solid fa-filter sm:mr-2"></i><span class="hidden sm:inline">View</span>
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-2 sm:gap-4 md:gap-6 mb-4 sm:mb-6">
        <!-- Total Hours -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-3 sm:p-6 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h3 class="text-[10px] sm:text-sm font-medium opacity-90 leading-tight">Total Hours Worked</h3>
                <i class="fa-solid fa-clock text-sm sm:text-2xl opacity-80 shrink-0"></i>
            </div>
            <p class="text-lg sm:text-3xl font-bold leading-none">{{ number_format($totalHours, 2) }}</p>
            @if(isset($bonusHours) && $bonusHours > 0)
                <div class="hidden sm:flex items-center text-xs opacity-90 mt-1">
                    <i class="fa-solid fa-gift mr-1"></i>
                    <span>Includes {{ number_format($bonusHours, 1) }} hrs Evaluation Bonus</span>
                </div>
            @endif
            <p class="text-[10px] sm:text-xs opacity-80 mt-1">{{ $date->format('F Y') }}</p>
        </div>

        <!-- Hourly Rate -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl shadow-lg p-3 sm:p-6 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h3 class="text-[10px] sm:text-sm font-medium opacity-90 leading-tight">Hourly Rate</h3>
                <i class="fa-solid fa-dollar-sign text-sm sm:text-2xl opacity-80 shrink-0"></i>
            </div>
            <p class="text-lg sm:text-3xl font-bold leading-none">${{ number_format($hourlyRate, 2) }}</p>
            <p class="text-[10px] sm:text-xs opacity-80 mt-1">Per hour</p>
        </div>

        <!-- Total Earnings -->
        <div class="bg-gradient-to-br from-vibrant-green to-green-600 text-white rounded-2xl shadow-lg p-3 sm:p-6 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h3 class="text-[10px] sm:text-sm font-medium opacity-90 leading-tight">Total Earnings</h3>
                <i class="fa-solid fa-money-bill-wave text-sm sm:text-2xl opacity-80 shrink-0"></i>
            </div>
            <p class="text-lg sm:text-3xl font-bold leading-none">${{ number_format($totalEarnings, 2) }}</p>
            <p class="text-[10px] sm:text-xs opacity-80 mt-1">{{ $date->format('F Y') }}</p>
        </div>
    </div>

    <!-- Attended Schedules List -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h2 class="text-base sm:text-lg font-bold text-gray-800">Attended Sessions</h2>
            <p class="text-xs sm:text-sm text-gray-600">Sessions where both you and the student were present</p>
        </div>

        @if($attendances->count() > 0)
            <div class="hidden md:block overflow-x-auto">
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
            <div class="md:hidden divide-y divide-gray-200">
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
                    <div class="p-4 {{ $isHalfTime ? 'bg-orange-50/30' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $schedule->student->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $schedule->course->title ?? 'N/A' }}</p>
                            </div>
                            @if($isHalfTime)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium bg-orange-100 text-orange-800 shrink-0">
                                    <i class="fa-solid fa-clock mr-1"></i>Half Time
                                </span>
                            @endif
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div class="rounded-xl bg-gray-50 p-2">
                                <span class="block text-[10px] uppercase tracking-wide text-gray-500">Date</span>
                                <span class="block mt-1 font-medium text-gray-900">{{ $schedule->starts_at->format('M d, Y') }}</span>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-2">
                                <span class="block text-[10px] uppercase tracking-wide text-gray-500">Time</span>
                                <span class="block mt-1 font-medium text-gray-900">{{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}</span>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-2">
                                <span class="block text-[10px] uppercase tracking-wide text-gray-500">Duration</span>
                                <span class="block mt-1 font-medium text-blue-700">{{ number_format($hours, 2) }} hrs</span>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-2">
                                <span class="block text-[10px] uppercase tracking-wide text-gray-500">Earnings</span>
                                <span class="block mt-1 font-medium text-vibrant-green">${{ number_format($earnings, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if(isset($bonusHours) && $bonusHours > 0)
                    <div class="p-4 bg-amber-50">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">Evaluation Bonus</p>
                                <p class="text-xs text-gray-600">{{ number_format($bonusHours, 2) }} hrs added for completing evaluations</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                {{ $attendances->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg font-medium">No attended sessions found</p>
                <p class="text-gray-400 text-sm mb-4">
                    @if($hourlyRate == 0)
                        Your hourly rate has not been set yet. Please contact the admin.
                    @else
                        No sessions where both you and the student were present in {{ $date->format('F Y') }}
                    @endif
                </p>
            </div>
        @endif
    </div>
</x-dashboard-layout>
