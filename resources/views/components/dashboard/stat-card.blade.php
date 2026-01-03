@props([
    'icon' => 'fa-bookmark',
    'title' => 'Stat Title',
    'value' => '',
    'subtitle' => '',
    'color' => 'yellow'
])

@php
    $bgColors = [
        'yellow' => 'bg-yellow-50',
        'green' => 'bg-green-50',
        'blue' => 'bg-blue-50',
        'red' => 'bg-red-50',
        'purple' => 'bg-purple-50',
        'teal' => 'bg-teal-50',
    ];
    $textColors = [
        'yellow' => 'text-yellow-500',
        'green' => 'text-green-500',
        'blue' => 'text-blue-500',
        'red' => 'text-red-500',
        'purple' => 'text-purple-500',
        'teal' => 'text-teal-500',
    ];
@endphp

<div class="bg-white p-6 rounded-2xl shadow-sm text-center hover:shadow-md transition">
    <div class="w-12 h-12 mx-auto mb-3 {{ $bgColors[$color] ?? 'bg-yellow-50' }} rounded-xl flex items-center justify-center">
        <i class="fa-solid {{ $icon }} text-2xl {{ $textColors[$color] ?? 'text-yellow-500' }}"></i>
    </div>
    <p class="text-sm font-bold text-gray-800 mb-1">{{ $title }}</p>
    @if($value)
        <p class="text-xs text-gray-500">{{ $value }}</p>
    @endif
    @if($subtitle)
        <p class="text-xs text-gray-500">{{ $subtitle }}</p>
    @endif
</div>
