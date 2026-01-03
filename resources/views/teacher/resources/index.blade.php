<x-dashboard-layout title="My Resources">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">My Resources</h1>
                <p class="text-gray-600 text-sm">Upload and manage educational resources for your students</p>
            </div>
            <a href="{{ route('teacher.resources.create') }}" class="bg-vibrant-green text-white px-6 py-3 rounded-xl hover:bg-deep-blue transition font-semibold shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i>Upload Resource
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('teacher.resources.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..." class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
            </div>

            <!-- Student Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                <select name="student_id" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">Filter by Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Course Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                <select name="course_id" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">All Types</option>
                    <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Image</option>
                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                    <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                    <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>Link</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition font-semibold">
                    <i class="fa-solid fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('teacher.resources.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-semibold">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Resources Grid -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        @if($resources->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($resources as $resource)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition">
                        <!-- Resource Type Icon -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center
                                @if($resource->type == 'pdf') bg-red-100
                                @elseif($resource->type == 'image') bg-purple-100
                                @elseif($resource->type == 'video') bg-blue-100
                                @elseif($resource->type == 'audio') bg-green-100
                                @elseif($resource->type == 'link') bg-yellow-100
                                @else bg-gray-100
                                @endif">
                                <i class="fa-solid
                                    @if($resource->type == 'pdf') fa-file-pdf text-red-600
                                    @elseif($resource->type == 'image') fa-image text-purple-600
                                    @elseif($resource->type == 'video') fa-video text-blue-600
                                    @elseif($resource->type == 'audio') fa-music text-green-600
                                    @elseif($resource->type == 'link') fa-link text-yellow-600
                                    @else fa-file text-gray-600
                                    @endif text-2xl"></i>
                            </div>
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium uppercase">
                                {{ $resource->type }}
                            </span>
                        </div>

                        <!-- Resource Info -->
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $resource->title }}</h3>
                        @if($resource->description)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $resource->description }}</p>
                        @endif

                        <!-- Student/Course Info -->
                        <div class="space-y-1 mb-4">
                            @if($resource->student)
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fa-solid fa-user mr-2"></i>
                                    <span>{{ $resource->student->name }}</span>
                                </div>
                            @endif
                            @if($resource->course)
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fa-solid fa-book mr-2"></i>
                                    <span>{{ $resource->course->title }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('teacher.resources.show', $resource->id) }}" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center text-sm font-semibold">
                                <i class="fa-solid fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('teacher.resources.edit', $resource->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm font-semibold">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <form action="{{ route('teacher.resources.destroy', $resource->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this resource?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-semibold">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $resources->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-folder-open text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg font-medium">No resources found</p>
                <p class="text-gray-400 text-sm mb-4">Upload your first resource to get started</p>
                <a href="{{ route('teacher.resources.create') }}" class="inline-block bg-vibrant-green text-white px-6 py-3 rounded-xl hover:bg-deep-blue transition font-semibold">
                    <i class="fa-solid fa-plus mr-2"></i>Upload Resource
                </a>
            </div>
        @endif
    </div>
</x-dashboard-layout>
