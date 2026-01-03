@props([
    'message' => '',
    'time' => '',
    'borderColor' => 'border-vibrant-green'
])

<div class="border-l-4 {{ $borderColor }} pl-3">
    <p class="text-xs text-gray-600 mb-1">{{ $message }}</p>
    <span class="text-xs text-gray-400">{{ $time }}</span>
</div>
