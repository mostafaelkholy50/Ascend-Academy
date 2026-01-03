<x-dashboard-layout title="Create Parent">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.parents.index') }}" class="bg-white hover:bg-gray-50 p-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200">
                <i class="fa-solid fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Create New Parent
                </h1>
                <p class="text-gray-600 text-sm mt-1">Add a new parent account to the system</p>
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

    @if(session('error'))
        <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-3xl">
        <form action="{{ route('admin.parents.store') }}" method="POST" class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
            @csrf

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-user text-blue-600 mr-1"></i>Full Name *
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-lg">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-envelope text-purple-600 mr-1"></i>Email Address *
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
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
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-lg">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-lock text-red-600 mr-1"></i>Password *
                    </label>
                    <input type="password" name="password" required
                           class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all text-lg">
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-lock text-red-600 mr-1"></i>Confirm Password *
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all text-lg">
                    @error('password_confirmation')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Select Children -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-child text-orange-500 mr-1"></i>Select Children
                        <span class="text-gray-500 font-normal">(Optional)</span>
                    </label>
                    <div class="border border-gray-300 rounded-xl p-4 max-h-48 overflow-y-auto">
                        @if(isset($students) && $students->count() > 0)
                            <div class="space-y-2">
                                @foreach($students as $student)
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition">
                                        <input type="checkbox" name="children[]" value="{{ $student->id }}" 
                                            {{ (is_array(old('children')) && in_array($student->id, old('children'))) ? 'checked' : '' }}
                                            class="w-5 h-5 text-vibrant-green border-gray-300 rounded focus:ring-vibrant-green">
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $student->email }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No students available to select.</p>
                        @endif
                    </div>
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input type="checkbox" name="active" id="active" value="1" checked
                        class="w-5 h-5 text-vibrant-green border-gray-300 rounded focus:ring-2 focus:ring-vibrant-green">
                    <label for="active" class="ml-3 text-sm font-medium text-gray-700">
                        <i class="fa-solid fa-check-circle text-vibrant-green mr-1"></i>Active Account
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white px-6 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-200 font-bold text-lg">
                    <i class="fa-solid fa-save mr-2"></i>Create Parent
                </button>
                <a href="{{ route('admin.parents.index') }}" class="px-6 py-4 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition font-bold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-dashboard-layout>
