<x-dashboard-layout title="My Profile">
    <div class="mb-8">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
            My Profile
        </h1>
        <p class="text-gray-600 text-sm mt-1">Manage your account information and settings</p>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-vibrant-green text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-vibrant-green mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Profile Card -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Profile Information</h2>
                    <a href="{{ route('parent.profile.edit') }}" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold">
                        <i class="fa-solid fa-edit mr-2"></i>Edit Profile
                    </a>
                </div>

                <div class="flex items-start gap-6 mb-8">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-blue-500 via-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-5xl shadow-xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-vibrant-green rounded-full p-3 shadow-lg">
                            <i class="fa-solid fa-user text-white"></i>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $user->name }}</h3>
                        <p class="text-gray-600 mb-4">
                            <i class="fa-solid fa-envelope mr-2 text-blue-600"></i>{{ $user->email }}
                        </p>
                        @if($user->phone)
                            <p class="text-gray-600 mb-4">
                                <i class="fa-solid fa-phone mr-2 text-green-600"></i>{{ $user->phone }}
                            </p>
                        @endif
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 rounded-xl font-bold text-sm">
                                <i class="fa-solid fa-user-graduate mr-1"></i>Parent
                            </span>
                            <span class="px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 rounded-xl font-semibold text-sm">
                                <i class="fa-solid fa-calendar mr-1"></i>Member since {{ $stats['member_since'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Account Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                        <p class="text-sm text-gray-600 mb-1">Full Name</p>
                        <p class="text-lg font-bold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200">
                        <p class="text-sm text-gray-600 mb-1">Email Address</p>
                        <p class="text-lg font-bold text-gray-800">{{ $user->email }}</p>
                    </div>
                    @if($user->phone)
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
                            <p class="text-sm text-gray-600 mb-1">Phone Number</p>
                            <p class="text-lg font-bold text-gray-800">{{ $user->phone }}</p>
                        </div>
                    @endif
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-200">
                        <p class="text-sm text-gray-600 mb-1">Account Status</p>
                        <p class="text-lg font-bold text-green-700">
                            <i class="fa-solid fa-circle-check mr-1"></i>Active
                        </p>
                    </div>

                </div>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fa-solid fa-lock text-purple-600 mr-2"></i>
                    Change Password
                </h2>

                <form action="{{ route('parent.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                        @error('current_password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <input type="password" name="password" required
                               class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                        <i class="fa-solid fa-key mr-2"></i>Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Statistics -->
            <div class="bg-gradient-to-br from-blue-500 via-purple-600 to-pink-600 text-white rounded-3xl shadow-xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative z-10">
                    <h3 class="font-bold text-lg mb-6 flex items-center">
                        <i class="fa-solid fa-chart-bar mr-2"></i>
                        Account Statistics
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-white/80 text-sm mb-1">My Children</p>
                            <p class="text-3xl font-bold">{{ $stats['total_children'] }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-white/80 text-sm mb-1">Total Sessions</p>
                            <p class="text-3xl font-bold">{{ $stats['total_sessions'] }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-white/80 text-sm mb-1">Progress Reports</p>
                            <p class="text-3xl font-bold">{{ $stats['total_reports'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-bolt text-amber-600 mr-2"></i>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('parent.profile.edit') }}" class="block w-full bg-gradient-to-r from-blue-100 to-purple-100 hover:from-blue-200 hover:to-purple-200 text-blue-700 px-4 py-3 rounded-xl transition-all font-semibold text-center">
                        <i class="fa-solid fa-user-edit mr-2"></i>Edit Profile
                    </a>
                    <a href="{{ route('parent.dashboard') }}" class="block w-full bg-gradient-to-r from-green-100 to-emerald-100 hover:from-green-200 hover:to-emerald-200 text-green-700 px-4 py-3 rounded-xl transition-all font-semibold text-center">
                        <i class="fa-solid fa-home mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
