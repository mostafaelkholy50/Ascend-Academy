<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $teacher->name }} - Schedule {{ $targetMonth->format('F Y') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background: white; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: white; margin: 0; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            .no-print { display: none !important; }
            @page { margin: 5mm; size: landscape; }
            .calendar-container { width: 100% !important; max-width: 100% !important; border: none !important; box-shadow: none !important; padding: 0 !important; margin: 0 !important; }
            .print-table { page-break-inside: auto; width: 100% !important; }
            .print-table-wrap { overflow: visible !important; width: 100% !important; border: none !important; }
            table { width: 100% !important; table-layout: fixed !important; }
            .main-header { margin-bottom: 10px !important; padding-bottom: 5px !important; }
        }
        .day-cell { min-height: 100px; }
    </style>
</head>
<body class="p-4 md:p-8 w-full print:p-0 mx-auto">
    
    <!-- Controls (Hidden on Print) -->
    <div class="no-print flex justify-end mb-6 gap-4 border-b pb-4">
        <button onclick="window.close()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
            Close
        </button>
        <button onclick="window.print()" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg font-medium flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Print PDF
        </button>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-8 pb-6 border-b-2 border-indigo-100 main-header">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Ascend Academy</h1>
                <p class="text-indigo-600 font-medium tracking-wide uppercase text-sm mt-1">Monthly Schedule Report</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-bold text-gray-800">{{ $teacher->name }}</h2>
            <p class="text-gray-500 font-medium text-lg">{{ $targetMonth->format('F Y') }}</p>
        </div>
    </div>

    <!-- Timetable Grid -->
    <div class="calendar-container w-full bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm p-4 print:p-0 print:rounded-none">
        @php
            $timezone = auth()->user()->getUserTimezone();
            $dayEntries = collect($monthDays)->map(function ($dayData, $dateString) use ($timezone) {
                return [
                    'dateString' => $dateString,
                    'date' => $dayData['date'],
                    'isToday' => $dateString === now()->format('Y-m-d'),
                    'isWeekend' => $dayData['date']->isWeekend(),
                    'schedules' => $dayData['schedules']
                        ->sortBy(function ($schedule) use ($timezone) {
                            return $schedule->getStartsAtInTimezone($timezone)->format('H:i');
                        })
                        ->values(),
                ];
            })->values();

            $hasSchedules = $dayEntries->contains(fn ($day) => $day['schedules']->isNotEmpty());
            $hours = $dayEntries
                ->flatMap(fn ($day) => $day['schedules']->map(fn ($schedule) => $schedule->getStartsAtInTimezone($timezone)->format('H')))
                ->unique()
                ->sort()
                ->values();
        @endphp

        @if(! $hasSchedules)
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-calendar-xmark text-4xl mb-3 text-gray-300"></i>
                <p class="text-lg">No schedules found for this month.</p>
            </div>
        @else
            <div class="print-table-wrap print-table rounded-2xl border border-gray-200 print:rounded-none">
                <table class="w-full text-left border-collapse border border-gray-200 text-[10px] table-fixed print:w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-200 p-1 font-bold text-gray-700 w-[50px] bg-gray-100 text-center">Date</th>
                            @foreach($hours as $time)
                                <th class="border border-gray-200 p-1 font-bold text-gray-700 text-center bg-gray-100 whitespace-nowrap text-[10px]">
                                    {{ \Carbon\Carbon::parse($time . ':00')->format('ga') }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dayEntries as $day)
                            @php
                                $schedulesByHour = $day['schedules']->groupBy(fn ($schedule) => $schedule->getStartsAtInTimezone($timezone)->format('H'));
                            @endphp
                            <tr class="{{ $day['isToday'] ? 'bg-indigo-50/50' : ($day['isWeekend'] ? 'bg-gray-50/50' : 'bg-white') }}">
                                <td class="border border-gray-200 p-1 font-semibold {{ $day['isToday'] ? 'text-indigo-600' : 'text-gray-700' }} align-middle text-center w-[50px]">
                                    <span class="block text-[9px] uppercase text-gray-500 leading-tight">{{ $day['date']->format('D') }}</span>
                                    <span class="text-xs leading-tight">{{ $day['date']->format('d/m') }}</span>
                                </td>
                                @foreach($hours as $time)
                                    @php $hourSchedules = $schedulesByHour->get($time, collect()); @endphp
                                    <td class="border border-gray-200 p-0.5 align-top min-h-[40px]">
                                        @foreach($hourSchedules as $s)
                                            @php
                                                $start = $s->getStartsAtInTimezone($timezone);
                                                $end = $s->getEndsAtInTimezone($timezone);
                                                $now = now();
                                                $isPast = $now->greaterThan($s->ends_at);
                                                $isInProgress = $now->between($s->starts_at, $s->ends_at);
                                                $statusClass = 'bg-blue-50 border-blue-200 text-blue-800';
                                                $statusText = 'Not Yet';
                                                $statusIcon = 'fa-calendar';

                                                if ($s->status === 'completed') {
                                                    $statusClass = 'bg-green-50 border-green-200 text-green-800';
                                                    $statusText = 'Attended';
                                                    $statusIcon = 'fa-check-circle';
                                                } elseif ($s->attendance) {
                                                    if ($s->attendance->student_present && $s->attendance->teacher_present) {
                                                        $statusClass = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                                                        $statusText = 'Attended';
                                                        $statusIcon = 'fa-check-double';
                                                    } else {
                                                        $statusClass = 'bg-red-50 border-red-200 text-red-800';
                                                        $statusText = 'Absent';
                                                        $statusIcon = 'fa-times-circle';
                                                    }
                                                } elseif ($isPast) {
                                                    $statusClass = 'bg-gray-100 border-gray-200 text-gray-600';
                                                    $statusText = 'Past';
                                                    $statusIcon = 'fa-history';
                                                } elseif ($isInProgress) {
                                                    $statusClass = 'bg-yellow-50 border-yellow-300 text-yellow-900';
                                                    $statusText = 'In Progress';
                                                    $statusIcon = 'fa-spinner fa-spin';
                                                }
                                            @endphp
                                            <div class="mb-0.5 last:mb-0 p-0.5 bg-indigo-50 border border-indigo-100 rounded text-indigo-800 text-[8px] sm:text-[9px] text-left shadow-sm overflow-hidden break-words">
                                                <div class="font-bold leading-none break-words" title="{{ $s->student->name }}">
                                                    {{ $s->student->name }}
                                                </div>
                                                <div class="text-gray-500 leading-none mt-[2px] break-words text-[8px]">
                                                    {{ $s->course->title }}
                                                </div>
                                                <div class="text-gray-500 mt-[2px] leading-none text-[8px]">
                                                    {{ $start->format('g:ia') }}-{{ $end->format('g:ia') }}
                                                </div>
                                                <div class="mt-[3px] inline-flex items-center gap-1 px-1 py-0.5 rounded border text-[8px] font-bold {{ $statusClass }}">
                                                    <i class="fa-solid {{ $statusIcon }}"></i>
                                                    <span>{{ $statusText }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Summary / Footer -->
    <div class="mt-4 pt-2 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500 main-footer print:mt-2">
        <p>Generated on {{ now()->format('M d, Y g:i A') }}</p>
        <p class="font-medium text-indigo-600">Ascend Quran Academy</p>
    </div>

    <script>
        // Auto-trigger print dialog when loaded
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
