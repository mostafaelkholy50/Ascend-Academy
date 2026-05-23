<x-dashboard-layout title="Manage Schedules">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Master Schedule</h2>
        <a href="{{ route('scheduler.schedules.create') }}" class="bg-vibrant-green text-white px-6 py-2.5 rounded-xl hover:bg-deep-blue transition shadow-md flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Create New Session
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex flex-wrap gap-4">
            <!-- Filter placeholder -->
            <form action="{{ route('scheduler.schedules.index') }}" method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" placeholder="Search student or teacher..." class="text-sm border-gray-200 rounded-lg focus:ring-vibrant-green focus:border-vibrant-green w-64" value="{{ request('search') }}">
                <select name="status" class="text-sm border-gray-200 rounded-lg focus:ring-vibrant-green focus:border-vibrant-green">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left">Date & Time</th>
                        <th class="px-6 py-4 text-left">Student</th>
                        <th class="px-6 py-4 text-left">Teacher</th>
                        <th class="px-6 py-4 text-left">Course</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($schedules as $schedule)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-vibrant-green">{{ $schedule->starts_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $schedule->starts_at->format('h:i A') }} - {{ $schedule->ends_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $schedule->student->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-gray-400">Timezone: {{ $schedule->student->timezone ?? 'Cairo' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $schedule->teacher->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-600">{{ $schedule->course->title ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ $schedule->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $schedule->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $schedule->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $schedule->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('scheduler.schedules.edit', $schedule->id) }}" class="text-blue-500 hover:text-blue-700 transition">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <form action="{{ route('scheduler.schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $schedules->links() }}
        </div>
    </div>
</x-dashboard-layout>
