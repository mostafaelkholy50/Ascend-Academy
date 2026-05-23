<div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[800px]">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-6 py-4 text-left">Student</th>
                <th class="px-6 py-4 text-left">Location / Timezone</th>
                <th class="px-6 py-4 text-left">Current Local Time</th>
                <th class="px-6 py-4 text-left">Time Difference</th>
                <th class="px-6 py-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($students as $student)
                @php
                    $timezone = $student->getUserTimezone();
                    $studentTime = now()->setTimezone($timezone);
                    $cairoTime = now()->setTimezone('Africa/Cairo');
                    
                    $cairoOffset = $cairoTime->offsetHours;
                    $studentOffset = $studentTime->offsetHours;
                    $diffFromCairo = $studentOffset - $cairoOffset;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-vibrant-green/10 flex items-center justify-center text-vibrant-green mr-3">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">{{ $student->name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                            {{ $timezone }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $studentTime->format('h:i A') }}</div>
                        <div class="text-xs text-gray-400">{{ $studentTime->format('D, M d') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold
                            {{ $diffFromCairo == 0 ? 'bg-gray-100 text-gray-600' : ($diffFromCairo > 0 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ $diffFromCairo > 0 ? '+' : '' }}{{ $diffFromCairo }} hours from Cairo
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('scheduler.students.show', $student->id) }}" class="text-blue-500 hover:text-deep-blue transition" title="View Profile">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <a href="{{ route('scheduler.schedules.index', ['student_id' => $student->id]) }}" class="text-vibrant-green hover:text-deep-blue transition" title="View Schedule">
                                <i class="fa-solid fa-calendar-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="px-6 py-4 border-t border-gray-100">
    {{ $students->links() }}
</div>
