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
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: white; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 10mm; size: landscape; }
            .calendar-container { width: 100%; }
            .print-table { page-break-inside: auto; }
            .print-table-wrap { overflow: visible !important; }
        }
        .day-cell { min-height: 100px; }
    </style>
</head>
<body class="p-8 max-w-7xl mx-auto">
    
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
    <div class="flex justify-between items-center mb-8 pb-6 border-b-2 border-indigo-100">
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
    <div class="calendar-container bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm p-4">
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
            <div class="print-table-wrap print-table rounded-2xl border border-gray-200">
                <table class="w-full text-left border-collapse border border-gray-200 text-[10px] table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-200 p-2 font-bold text-gray-700 w-36 bg-gray-100">Date</th>
                            @foreach($hours as $time)
                                <th class="border border-gray-200 p-2 font-bold text-gray-700 text-center bg-gray-100 whitespace-nowrap w-[78px]">
                                    {{ \Carbon\Carbon::parse($time . ':00')->format('g A') }}
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
                                <td class="border border-gray-200 p-2 font-semibold {{ $day['isToday'] ? 'text-indigo-600' : 'text-gray-700' }} align-top w-36">
                                    <span class="block text-[10px] uppercase text-gray-500">{{ $day['date']->format('D') }}</span>
                                    <span class="text-sm">{{ $day['date']->format('d M') }}</span>
                                </td>
                                @foreach($hours as $time)
                                    @php $hourSchedules = $schedulesByHour->get($time, collect()); @endphp
                                    <td class="border border-gray-200 p-1 align-top h-20 w-[78px]">
                                        @foreach($hourSchedules as $s)
                                            @php
                                                $start = $s->getStartsAtInTimezone($timezone);
                                                $end = $s->getEndsAtInTimezone($timezone);
                                            @endphp
                                            <div class="mb-1 last:mb-0 p-1 bg-indigo-50 border border-indigo-100 rounded-md text-indigo-800 text-[9px] text-left shadow-sm overflow-hidden break-words">
                                                <div class="font-bold leading-tight break-words" title="{{ $s->student->name }}">
                                                    {{ $s->student->name }}
                                                </div>
                                                <div class="text-gray-500 leading-tight mt-0.5 break-words">
                                                    {{ $s->course->title }}
                                                </div>
                                                <div class="text-gray-500 mt-0.5">
                                                    {{ $start->format('g:i A') }} - {{ $end->format('g:i A') }}
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
    <div class="mt-8 pt-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500">
        <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        <p class="font-medium text-indigo-600">Ascend Quran Academy - Excellence in Education</p>
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
