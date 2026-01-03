<x-dashboard-layout title="Edit Profile">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.profile.show') }}" class="bg-white hover:bg-gray-50 p-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200">
                <i class="fa-solid fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Edit Profile
                </h1>
                <p class="text-gray-600 text-sm mt-1">Update your account information</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-vibrant-green text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-vibrant-green mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-3xl space-y-6">
        <!-- Profile Picture Section (Separate Form) -->
        <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-6">
                <i class="fa-solid fa-image text-blue-600 mr-2"></i>Profile Picture
            </h2>
            
            <div class="flex items-center gap-6">
                <!-- Current Avatar -->
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-orange-400 to-pink-500 border-4 border-gray-200 shadow-lg flex items-center justify-center text-white font-bold text-3xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <!-- Upload/Remove Buttons -->
                <div class="flex-1">
                    <form action="{{ route('student.profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Choose new profile picture
                            </label>
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, or GIF (max 2MB)</p>
                            @error('avatar')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                            <i class="fa-solid fa-upload mr-2"></i>Upload Picture
                        </button>
                    </form>

                    @if($user->avatar)
                        <form action="{{ route('student.profile.avatar.delete') }}" method="POST" class="mt-2" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                <i class="fa-solid fa-trash mr-1"></i>Remove Picture
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Information Form -->
        <form action="{{ route('student.profile.update') }}" method="POST" class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-user text-blue-600 mr-1"></i>Full Name
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-lg">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-envelope text-purple-600 mr-1"></i>Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all text-lg">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-phone text-green-600 mr-1"></i>Phone Number
                        <span class="text-gray-500 font-normal">(Optional)</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-lg">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white px-6 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-200 font-bold text-lg">
                    <i class="fa-solid fa-save mr-2"></i>Save Changes
                </button>
                <a href="{{ route('student.profile.show') }}" class="px-6 py-4 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition font-bold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-dashboard-layout>
