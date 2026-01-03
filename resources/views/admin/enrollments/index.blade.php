<x-dashboard-layout title="Enrollments Management">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Enrollments Management</h1>
            <p class="text-gray-600 text-sm">Manage student enrollments and payments</p>
        </div>
        <a href="{{ route('admin.enrollments.create') }}" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
            <i class="fa-solid fa-plus mr-2"></i>Enroll Student
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Completed</p>
                    <p class="text-2xl font-bold text-gray-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-flag-checkered text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>
        

    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by student or course name"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
            
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            

            
            <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                <i class="fa-solid fa-search mr-2"></i>Search
            </button>
            <a href="{{ route('admin.enrollments.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fa-solid fa-redo"></i>
            </a>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Enrollments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($enrollments as $enrollment)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white text-lg font-bold">
                                {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $enrollment->student->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $enrollment->student->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-book text-vibrant-green"></i>
                            <span class="text-sm font-semibold text-gray-800">{{ $enrollment->course->title }}</span>
                        </div>
                    </div>

                    <!-- Status Badges -->
                    <div class="flex gap-2 mb-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                            {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $enrollment->status === 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                            <i class="fa-solid fa-circle-dot mr-1"></i>{{ ucfirst($enrollment->status) }}
                        </span>

                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-4 border-t border-gray-100 pt-3">
                        @if($enrollment->start_date)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Start Date:</span>
                                <span class="font-semibold text-gray-800">{{ $enrollment->start_date->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.enrollments.show', $enrollment->id) }}"
                            class="flex-1 text-center bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                            <i class="fa-solid fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}"
                            class="flex-1 text-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-sm font-semibold">
                            <i class="fa-solid fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <i class="fa-solid fa-graduation-cap text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">No enrollments found</p>
                <a href="{{ route('admin.enrollments.create') }}" class="inline-block bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                    <i class="fa-solid fa-plus mr-2"></i>Create First Enrollment
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $enrollments->links() }}
    </div>
</x-dashboard-layout>
