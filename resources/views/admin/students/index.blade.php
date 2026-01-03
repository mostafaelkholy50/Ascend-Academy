<x-dashboard-layout title="Students Management">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Students (Children) Management</h1>
        <p class="text-gray-600 text-sm">View and manage all student accounts</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col md:flex-row gap-4">
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
            <a href="{{ route('admin.students.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
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

    <!-- Students Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($students as $student)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden flex flex-col h-full">
                <div class="p-6 flex flex-col flex-1">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white text-lg font-bold">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $student->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $student->email }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $student->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $student->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-4">
                        @if($student->phone)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-phone w-5"></i>
                                <span>{{ $student->phone }}</span>
                            </div>
                        @endif
                        @if($student->gender)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-{{ $student->gender == 'male' ? 'mars' : 'venus' }} w-5"></i>
                                <span>{{ ucfirst($student->gender) }}</span>
                            </div>
                        @endif
                        @if($student->birth_date)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-birthday-cake w-5"></i>
                                <span>{{ \Carbon\Carbon::parse($student->birth_date)->age }} years old</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-calendar w-5"></i>
                            <span>Joined {{ $student->created_at->format('M Y') }}</span>
                        </div>
                    </div>

                    <!-- Parents Preview -->
                    @if($student->parents && $student->parents->count() > 0)
                        <div class="border-t border-gray-100 pt-3 mb-4">
                            <p class="text-xs font-semibold text-gray-600 mb-2">Parents:</p>
                            <div class="space-y-1">
                                @foreach($student->parents->take(2) as $parent)
                                    <div class="text-xs text-gray-700 flex items-center">
                                        <i class="fa-solid fa-user text-gray-400 w-4"></i>
                                        {{ $parent->name }}
                                    </div>
                                @endforeach
                                @if($student->parents->count() > 2)
                                    <p class="text-xs text-gray-500 italic">+{{ $student->parents->count() - 2 }} more</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Enrollments Preview -->
                    @if($student->enrollments && $student->enrollments->count() > 0)
                        <div class="border-t border-gray-100 pt-3 mb-4">
                            <p class="text-xs font-semibold text-gray-600 mb-2">Enrolled Courses:</p>
                            <div class="space-y-1">
                                @foreach($student->enrollments->take(2) as $enrollment)
                                    <div class="text-xs text-gray-700 flex items-center">
                                        <i class="fa-solid fa-book text-gray-400 w-4"></i>
                                        {{ $enrollment->course->title ?? 'N/A' }}
                                    </div>
                                @endforeach
                                @if($student->enrollments->count() > 2)
                                    <p class="text-xs text-gray-500 italic">+{{ $student->enrollments->count() - 2 }} more</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <a href="{{ route('admin.students.show', $student->id) }}"
                        class="mt-auto block w-full text-center bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                        <i class="fa-solid fa-eye mr-2"></i>View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <i class="fa-solid fa-user-graduate text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No students found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $students->links() }}
    </div>
</x-dashboard-layout>
