<div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[800px]">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-6 py-4 text-left">Teacher</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Today's Sessions</th>
                <th class="px-6 py-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($teachers as $teacher)
                @php
                    $todayCount = $teacher->teacherSchedules()->whereDate('starts_at', now())->count();
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-3">
                                <i class="fa-solid fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">{{ $teacher->name }}</div>
                                <div class="text-xs text-gray-500">{{ $teacher->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs {{ $teacher->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $teacher->active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-700 mr-2">{{ $todayCount }}</span>
                            <div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-purple-500 h-full" style="width: {{ min($todayCount * 10, 100) }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('scheduler.teachers.show', $teacher->id) }}" class="text-blue-500 hover:text-deep-blue transition" title="View Profile">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <a href="{{ route('scheduler.availability', $teacher->id) }}" class="text-orange-500 hover:text-deep-blue transition" title="Check Availability">
                                <i class="fa-solid fa-clock"></i>
                            </a>
                            <a href="{{ route('scheduler.schedules.index', ['teacher_id' => $teacher->id]) }}" class="text-purple-600 hover:text-deep-blue transition" title="View Schedule">
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
    {{ $teachers->links() }}
</div>
