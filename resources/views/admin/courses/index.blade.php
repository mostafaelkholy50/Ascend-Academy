<x-dashboard-layout title="Courses Management">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Courses Management</h1>
            <p class="text-gray-600 text-sm">View and manage all courses</p>
        </div>
        <a href="{{ route('admin.courses.create') }}" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
            <i class="fa-solid fa-plus mr-2"></i>Add New Course
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by title or description"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
            
            <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                <i class="fa-solid fa-search mr-2"></i>Search
            </button>
            <a href="{{ route('admin.courses.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
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

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
                <!-- Course Photo -->
                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-yellow-400 to-orange-500">
                    @if($course->photo)
                        <img src="{{ asset('storage/' . $course->photo) }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fa-solid fa-book text-6xl text-white opacity-50"></i>
                        </div>
                    @endif
                    <!-- Free Badge -->
                    @if($course->is_free)
                        <span class="absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                            Free
                        </span>
                    @endif
                </div>

                <div class="p-6">
                    <!-- Header -->
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-lg mb-2">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-3">{{ $course->description }}</p>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-4 border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                <i class="fa-solid fa-clock w-5"></i>Duration:
                            </span>
                            <span class="font-semibold text-gray-800">{{ $course->duration_weeks }} weeks</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                <i class="fa-solid fa-users w-5"></i>Enrollments:
                            </span>
                            <span class="font-semibold text-gray-800">{{ $course->enrollments_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                <i class="fa-solid fa-calendar w-5"></i>Schedules:
                            </span>
                            <span class="font-semibold text-gray-800">{{ $course->schedules_count ?? 0 }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.courses.show', $course->id) }}"
                            class="flex-1 text-center bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                            <i class="fa-solid fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.courses.edit', $course->id) }}"
                            class="flex-1 text-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-sm font-semibold">
                            <i class="fa-solid fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <i class="fa-solid fa-book text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">No courses found</p>
                <a href="{{ route('admin.courses.create') }}" class="inline-block bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                    <i class="fa-solid fa-plus mr-2"></i>Create Your First Course
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</x-dashboard-layout>
