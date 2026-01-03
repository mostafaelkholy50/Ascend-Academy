@props([
    'name' => 'Chat Room',
    'initials' => 'CR',
    'lastMessage' => '',
    'time' => '',
    'href' => '#'
])

<a href="{{ $href }}" class="bg-white p-4 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between block">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
            <span class="font-bold text-yellow-600">{{ $initials }}</span>
        </div>
        <div>
            <h4 class="font-semibold text-gray-800 text-sm">{{ $name }}</h4>
            <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ $lastMessage }}</p>
        </div>
    </div>
    <span class="text-xs text-gray-400">{{ $time }}</span>
</a>
