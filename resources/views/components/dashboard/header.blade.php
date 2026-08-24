@php
    $user = auth()->user();
    $unreadCount = $user->unreadNotifications()->count();
    $recentNotifications = $user->unreadNotifications()->take(5)->get();
    
    // Unify role prefix for routes
    $roleName = $user->getRoleNames()->first() ?? 'student';
    $rolePrefix = strtolower($roleName);
    if ($rolePrefix === 'superadmin' || $rolePrefix === 'schedulermanager' || $rolePrefix === 'accountant' || $rolePrefix === 'qualitycontrol') {
        $rolePrefix = 'admin';
    }
@endphp

<header class="flex justify-between items-center mb-4 md:mb-6 flex-wrap gap-3 md:gap-4">
    <!-- Right Side Actions -->
    <div class="flex items-center gap-2 md:gap-4 ml-auto">
        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center hover:shadow-md transition relative" style="pointer-events: auto; touch-action: manipulation;">
                <i class="fa-regular fa-bell text-gray-600 pointer-events-none"></i>
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-white text-xs flex items-center justify-center font-semibold pointer-events-none">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </button>

            <!-- Notifications Dropdown -->
            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-12 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-[100] max-h-96 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Notifications</h3>
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route($rolePrefix . '.notifications.read-all') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-vibrant-green hover:underline">Mark all as read</button>
                        </form>
                    @endif
                </div>
                
                <div class="max-h-80 overflow-y-auto">
                    @forelse($recentNotifications as $notification)
                        <a href="{{ isset($notification->data['url']) ? url($notification->data['url']) : '#' }}" class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 bg-vibrant-green rounded-full mt-2 flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-gray-500 text-sm">
                            <i class="fa-regular fa-bell-slash text-3xl mb-2"></i>
                            <p>No new notifications</p>
                        </div>
                    @endforelse
                </div>

                @if($recentNotifications->count() > 0)
                    <div class="p-3 border-t border-gray-100 text-center">
                        <a href="{{ route($rolePrefix . '.notifications.index') }}" class="text-sm text-vibrant-green hover:underline font-medium">View all notifications</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div class="flex items-center space-x-2" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2 hover:bg-gray-100 rounded-lg px-2 py-1 transition cursor-pointer" style="pointer-events: auto; touch-action: manipulation;">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full border-2 border-white shadow-md object-cover pointer-events-none">
                @else
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-pink-500 border-2 border-white shadow-md flex items-center justify-center text-white font-bold pointer-events-none">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div class="text-left hidden md:block pointer-events-none">
                    <span class="text-gray-800 font-semibold text-sm block">{{ $user->name }}</span>
                    <span class="text-gray-500 text-xs">{{ $user->email }}</span>
                </div>
                <i class="fa-solid fa-chevron-down text-gray-400 text-xs hidden md:block pointer-events-none"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition class="absolute right-2 md:right-6 top-16 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-[100] border border-gray-100">
                
                <a href="{{ route($rolePrefix . '.profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-regular fa-user mr-3 text-gray-400"></i> My Profile
                </a>
                <a href="{{ route($rolePrefix . '.profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-cog mr-3 text-gray-400"></i> Settings
                </a>
                <hr class="my-2 border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                        <i class="fa-solid fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
