<x-dashboard-layout title="Teachers Management">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Teachers Management</h1>
        <p class="text-gray-600 text-sm">View and manage all teacher accounts</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
        <div class="flex flex-col md:flex-row justify-between gap-4">
            <form method="GET" action="{{ route('admin.teachers.index') }}" class="flex flex-col md:flex-row gap-4 flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, email, or phone"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                
                <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                    <i class="fa-solid fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('admin.teachers.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center">
                    <i class="fa-solid fa-redo"></i>
                </a>
            </form>
            <a href="{{ route('admin.teachers.create') }}" class="bg-deep-blue text-white px-6 py-2 rounded-lg hover:bg-vibrant-green transition flex items-center justify-center whitespace-nowrap">
                <i class="fa-solid fa-plus mr-2"></i>Add New Teacher
            </a>
        </div>
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

    <!-- Teachers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($teachers as $teacher)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <!-- Teacher Photo/Avatar -->
                            @if($teacher->avatar)
                                <img src="{{ asset('storage/' . $teacher->avatar) }}" 
                                     alt="{{ $teacher->name }}" 
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center text-white text-lg font-bold">
                                    {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $teacher->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $teacher->email }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $teacher->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $teacher->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-4">
                        @if($teacher->phone)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-phone w-5"></i>
                                <span>{{ $teacher->phone }}</span>
                            </div>
                        @endif
                        @if($teacher->gender)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-{{ $teacher->gender == 'male' ? 'mars' : 'venus' }} w-5"></i>
                                <span>{{ ucfirst($teacher->gender) }}</span>
                            </div>
                        @endif
                        @if($teacher->birth_date)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-birthday-cake w-5"></i>
                                <span>{{ \Carbon\Carbon::parse($teacher->birth_date)->age }} years old</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-calendar w-5"></i>
                            <span>Joined {{ $teacher->created_at->format('M Y') }}</span>
                        </div>
                    </div>

                    <!-- Stats Preview -->
                    <div class="border-t border-gray-100 pt-3 mb-4">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Schedules</p>
                                <p class="text-lg font-bold text-vibrant-green">{{ $teacher->teacher_schedules_count ?? 0 }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Reports</p>
                                <p class="text-lg font-bold text-deep-blue">{{ $teacher->teacher_reports_count ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.teachers.show', $teacher->id) }}"
                            class="flex-1 text-center bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                            <i class="fa-solid fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.teachers.edit', $teacher->id) }}"
                            class="flex-1 text-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-sm font-semibold">
                            <i class="fa-solid fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <i class="fa-solid fa-chalkboard-teacher text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No teachers found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $teachers->links() }}
    </div>
</x-dashboard-layout>
