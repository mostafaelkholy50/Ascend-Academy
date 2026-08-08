<x-dashboard-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pending Reschedule Requests</h1>
                <p class="mt-2 text-sm text-gray-600">Review and approve or reject session reschedule requests.</p>
            </div>
            <div>
                <a href="{{ route(auth()->user()->role === 'SchedulerManager' ? 'scheduler.schedules.index' : 'admin.schedules.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back to Schedules
                </a>
            </div>
        </div>

        <!-- Success/Error Alerts -->
        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-times-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($requests->isEmpty())
                <div class="p-8 text-center">
                    <i class="fa-solid fa-calendar-check text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 font-medium">No pending reschedule requests.</p>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teacher</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Original Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Requested Time</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $request->teacher->profile_photo_url }}" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->teacher->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $request->student->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->schedule->starts_at->format('M j, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->schedule->starts_at->format('g:i A') }} - {{ $request->schedule->ends_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-blue-600">{{ $request->new_starts_at->format('M j, Y') }}</div>
                                        <div class="text-sm text-blue-500">{{ $request->new_starts_at->format('g:i A') }} - {{ $request->new_ends_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route(auth()->user()->role === 'SchedulerManager' ? 'scheduler.schedules.requests.approve' : 'admin.schedules.requests.approve', $request->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-bold py-1 px-3 rounded shadow-sm mr-2 transition-colors">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route(auth()->user()->role === 'SchedulerManager' ? 'scheduler.schedules.requests.reject' : 'admin.schedules.requests.reject', $request->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-white bg-red-600 hover:bg-red-700 font-bold py-1 px-3 rounded shadow-sm transition-colors">
                                                Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="block md:hidden divide-y divide-gray-200">
                    @foreach($requests as $request)
                        <div class="p-4 hover:bg-gray-50 flex flex-col gap-3">
                            <!-- Header: Teacher -->
                            <div class="flex items-center gap-3">
                                <img class="h-12 w-12 rounded-full object-cover shadow-sm border-2 border-white" src="{{ $request->teacher->profile_photo_url }}" alt="">
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $request->teacher->name }}</div>
                                    <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Teacher</div>
                                </div>
                            </div>
                            
                            <!-- Student Info -->
                            <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100 flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user-graduate text-indigo-500 text-[10px]"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ $request->student->name }}</span>
                            </div>

                            <!-- Schedule Times -->
                            <div class="grid grid-cols-2 gap-3 mt-1">
                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <div class="text-[9px] uppercase font-bold text-gray-500 mb-1 tracking-wider">Original Time</div>
                                    <div class="text-xs text-gray-900 font-semibold">{{ $request->schedule->starts_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $request->schedule->starts_at->format('g:i A') }} - {{ $request->schedule->ends_at->format('g:i A') }}</div>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 relative overflow-hidden">
                                    <div class="absolute -right-2 -top-2 opacity-10">
                                        <i class="fa-solid fa-clock text-4xl text-blue-500"></i>
                                    </div>
                                    <div class="text-[9px] uppercase font-bold text-blue-500 mb-1 tracking-wider relative z-10">Requested Time</div>
                                    <div class="text-xs text-blue-800 font-bold relative z-10">{{ $request->new_starts_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-blue-600 mt-0.5 relative z-10">{{ $request->new_starts_at->format('g:i A') }} - {{ $request->new_ends_at->format('g:i A') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 mt-2 pt-3 border-t border-gray-100">
                                <form action="{{ route(auth()->user()->role === 'SchedulerManager' ? 'scheduler.schedules.requests.approve' : 'admin.schedules.requests.approve', $request->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full flex justify-center items-center gap-2 text-white bg-green-600 hover:bg-green-700 font-bold py-2.5 px-3 rounded-xl shadow-sm transition-all active:scale-95 text-sm">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route(auth()->user()->role === 'SchedulerManager' ? 'scheduler.schedules.requests.reject' : 'admin.schedules.requests.reject', $request->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full flex justify-center items-center gap-2 text-white bg-red-600 hover:bg-red-700 font-bold py-2.5 px-3 rounded-xl shadow-sm transition-all active:scale-95 text-sm">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $requests->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-dashboard-layout>
