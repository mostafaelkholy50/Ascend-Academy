@php
    $user = auth()->user();
    $role = strtolower($user->role ?? 'student');
@endphp

<aside id="sidebar" class="hidden lg:flex w-64 bg-gradient-to-b from-vibrant-green to-deep-blue text-white flex-col rounded-3xl shadow-xl m-4 sticky top-4 h-[calc(100vh-2rem)] overflow-hidden z-[70]">
    <!-- Mobile close btn -->
    <button id="closeSidebarBtn" class="absolute top-4 right-4 z-70 lg:hidden bg-white/20 backdrop-blur-sm p-2.5 rounded-xl hover:bg-white/30 transition-all active:scale-95" aria-label="Close menu">
        <i class="fa-solid fa-xmark text-xl"></i>
    </button>

    <!-- Logo -->
    <div class="flex flex-col items-center py-6 px-4 border-b border-white/10">
        <img src="{{ asset('assets/images/Gemini_Generated_Image_9401xh9401xh9401.png') }}" class="h-16 w-16 rounded-2xl shadow-lg mb-3 object-cover" alt="Logo">
        <h1 class="text-lg font-bold tracking-wide">Ascend Academy</h1>
        <p class="text-xs text-white/70 mt-1">{{ ucfirst($role) }} Dashboard</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1.5 text-sm overflow-y-auto min-h-0 hide-scrollbar" style="-webkit-overflow-scrolling: touch;">
        @if($role === 'admin')
            @include('components.dashboard.sidebar-admin')
        @elseif($role === 'teacher')
            @include('components.dashboard.sidebar-teacher')
        @elseif($role === 'parent')
            @include('components.dashboard.sidebar-parent')
        @else
            @include('components.dashboard.sidebar-student')
        @endif
    </nav>

    <!-- Logout -->
    <div class="flex-shrink-0 border-t border-white/10 px-4 py-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-4 py-3 rounded-xl hover:bg-white/10 transition opacity-80 hover:opacity-100 active:scale-95" style="pointer-events: auto; touch-action: manipulation;">
                <i class="fa-solid fa-sign-out-alt mr-3 text-base"></i> 
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>
