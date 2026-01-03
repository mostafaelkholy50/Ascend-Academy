<x-dashboard-layout title="Resource Details">
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.resources.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Resource Details</h1>
                <p class="text-gray-600 text-sm">View and manage this resource</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Resource Info Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center
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
                                @endif text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $resource->title }}</h2>
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium uppercase">
                                {{ $resource->type }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($resource->description)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $resource->description }}</p>
                    </div>
                @endif

                <!-- Resource Preview/Access -->
                <div class="border-t border-gray-200 pt-6">
                    @if($resource->isLink())
                        <a href="{{ $resource->external_url }}" target="_blank"
                            class="inline-flex items-center bg-vibrant-green text-white px-6 py-3 rounded-xl hover:bg-deep-blue transition font-semibold">
                            <i class="fa-solid fa-external-link-alt mr-2"></i>Open Link
                        </a>
                    @elseif($resource->isFile())
                        <div class="flex gap-3">
                            @if(in_array($resource->type, ['image']))
                                <div class="mb-4">
                                    <img src="{{ $resource->getUrl() }}" alt="{{ $resource->title }}" class="max-w-full h-auto rounded-lg shadow-md">
                                </div>
                            @endif
                            <a href="{{ route('teacher.resources.download', $resource->id) }}"
                                class="inline-flex items-center bg-vibrant-green text-white px-6 py-3 rounded-xl hover:bg-deep-blue transition font-semibold">
                                <i class="fa-solid fa-download mr-2"></i>Download File
                            </a>
                            @if($resource->getFileSize())
                                <span class="inline-flex items-center text-gray-600 px-4 py-3">
                                    <i class="fa-solid fa-file mr-2"></i>
                                    {{ number_format($resource->getFileSize() / 1024 / 1024, 2) }} MB
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>
                <div class="flex gap-3">
                    <a href="{{ route('teacher.resources.edit', $resource->id) }}"
                        class="bg-yellow-500 text-white px-6 py-3 rounded-xl hover:bg-yellow-600 transition font-semibold">
                        <i class="fa-solid fa-edit mr-2"></i>Edit Resource
                    </a>
                    <form action="{{ route('teacher.resources.destroy', $resource->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this resource? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition font-semibold">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Resource
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Resource Details -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Details</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Uploaded By</p>
                        <p class="text-sm font-medium text-gray-800">{{ $resource->teacher->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Upload Date</p>
                        <p class="text-sm font-medium text-gray-800">{{ $resource->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($resource->student)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Student</p>
                            <p class="text-sm font-medium text-gray-800">{{ $resource->student->name }}</p>
                        </div>
                    @endif
                    @if($resource->course)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Course</p>
                            <p class="text-sm font-medium text-gray-800">{{ $resource->course->title }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- File Information -->
            @if($resource->isFile())
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">File Information</h3>
                    <div class="space-y-3">
                        @if($resource->mime_type)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">File Type</p>
                                <p class="text-sm font-medium text-gray-800">{{ $resource->mime_type }}</p>
                            </div>
                        @endif
                        @if($resource->getFileSize())
                            <div>
                                <p class="text-xs text-gray-500 mb-1">File Size</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ number_format($resource->getFileSize() / 1024 / 1024, 2) }} MB
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Link Information -->
            @if($resource->isLink())
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Link Information</h3>
                    <div class="break-all">
                        <p class="text-xs text-gray-500 mb-1">URL</p>
                        <a href="{{ $resource->external_url }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                            {{ $resource->external_url }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>
