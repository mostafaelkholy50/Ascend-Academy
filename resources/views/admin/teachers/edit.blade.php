<x-dashboard-layout title="Edit Teacher">
    <div class="mb-6">
        <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Teacher Details
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Teacher</h1>

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

        <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Gender</label>
                    <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Date of Birth</label>
                    <input type="date" name="birth_date" value="{{ $teacher->birth_date ? \Carbon\Carbon::parse($teacher->birth_date)->format('Y-m-d') : '' }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('birth_date') border-red-500 @enderror">
                    @error('birth_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
                    <select name="active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        <option value="1" {{ old('active', $teacher->active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('active', $teacher->active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('active')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Location / Country</label>
                    <select name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ old('country', $teacher->country) == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Profile Photo</label>
                @if($teacher->avatar)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $teacher->avatar) }}" alt="Current Avatar" class="h-24 w-24 rounded-full object-cover border border-gray-200">
                        <p class="text-xs text-gray-500 mt-1">Current Photo</p>
                    </div>
                @endif
                <input type="file" name="avatar" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green @error('avatar') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Recommended size: 400x400px. Max 2MB. Leave empty to keep current photo.</p>
                @error('avatar')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                    <i class="fa-solid fa-save mr-2"></i>Update Teacher
                </button>
                <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-dashboard-layout>
