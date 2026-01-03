<x-dashboard-layout title="Teacher Applications">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Teacher Applications</h1>
        <p class="text-gray-600 text-sm">Review and manage teacher applications</p>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.teacher-applications.index') }}" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by name, email, or phone"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
            
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
            </select>
            
            <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                <i class="fa-solid fa-search mr-2"></i>Search
            </button>
            <a href="{{ route('admin.teacher-applications.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
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

    <!-- Applications Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($applications as $application)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center text-white text-lg font-bold">
                                {{ strtoupper(substr($application->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $application->full_name }}</h3>
                                <p class="text-xs text-gray-500">{{ $application->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $application->status === 'reviewed' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $application->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $application->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $application->status === 'converted' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ $application->getStatusLabel() }}
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-4">
                        @if($application->phone)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-phone w-5"></i>
                                <span>{{ $application->phone }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-globe w-5"></i>
                            <span>{{ $application->country }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-graduation-cap w-5"></i>
                            <span>{{ $application->education_level }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-clock w-5"></i>
                            <span>{{ $application->years_of_experience }} years exp.</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-calendar w-5"></i>
                            <span>Applied {{ $application->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Subjects Preview -->
                    @if($application->subjects && count($application->subjects) > 0)
                        <div class="border-t border-gray-100 pt-3 mb-4">
                            <p class="text-xs font-semibold text-gray-600 mb-2">Subjects:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($application->subjects, 0, 3) as $subject)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                        {{ $subject }}
                                    </span>
                                @endforeach
                                @if(count($application->subjects) > 3)
                                    <span class="px-2 py-0.5 text-gray-500 text-xs">
                                        +{{ count($application->subjects) - 3 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <a href="{{ route('admin.teacher-applications.show', $application->id) }}"
                        class="block w-full text-center bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                        <i class="fa-solid fa-eye mr-2"></i>View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <i class="fa-solid fa-chalkboard-teacher text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No teacher applications found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $applications->links() }}
    </div>
</x-dashboard-layout>
