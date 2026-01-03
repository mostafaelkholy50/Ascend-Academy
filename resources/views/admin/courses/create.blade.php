<x-dashboard-layout title="Create New Course">
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Courses
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Course</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Course Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Description *</label>
                <textarea name="description" rows="5" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Course Type</label>
                <div class="flex items-center space-x-4 mt-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="is_free" value="0" {{ old('is_free', '0') == '0' ? 'checked' : '' }}
                            class="mr-2 text-vibrant-green focus:ring-vibrant-green">
                        <span class="text-sm text-gray-700">Paid</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="is_free" value="1" {{ old('is_free') == '1' ? 'checked' : '' }}
                            class="mr-2 text-vibrant-green focus:ring-vibrant-green">
                        <span class="text-sm text-gray-700">Free</span>
                    </label>
                </div>
                @error('is_free')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Course Photo</label>
                <input type="file" name="photo" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('photo') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Recommended size: 800x600px. Max 2MB.</p>
                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                    <i class="fa-solid fa-save mr-2"></i>Create Course
                </button>
                <a href="{{ route('admin.courses.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-dashboard-layout>
