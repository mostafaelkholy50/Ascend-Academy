@props(['user', 'message' => null])

<section class="bg-gradient-to-r from-vibrant-green to-teal-500 text-white rounded-3xl p-4 sm:p-5 md:p-8 shadow-lg mb-4 sm:mb-6 relative overflow-hidden">
    <p class="text-[10px] sm:text-sm opacity-90 mb-1">{{ now()->format('F j, Y') }}</p>
    <h2 class="text-lg sm:text-2xl md:text-3xl font-bold mb-2 leading-tight">Welcome back, {{ $user->name }}!</h2>
    @if($message)
        <p class="opacity-95 text-xs sm:text-sm leading-relaxed max-w-2xl">{{ $message }}</p>
    @else
        <p class="opacity-95 text-xs sm:text-sm leading-relaxed max-w-2xl">Ready to continue your learning journey today.</p>
    @endif

    <div class="hidden md:flex absolute right-8 top-1/2 -translate-y-1/2 items-center gap-4">
        <div class="w-32 h-24 bg-gray-800 rounded-lg shadow-2xl transform -rotate-6"></div>
        <div class="w-20 h-24 bg-yellow-400 rounded-lg shadow-2xl transform rotate-12"></div>
        <div class="w-24 h-28 bg-purple-400 rounded-2xl shadow-2xl"></div>
    </div>
</section>
