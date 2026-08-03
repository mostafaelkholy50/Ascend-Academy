@props([
    'icon' => 'fa-bookmark',
    'title' => 'Stat Title',
    'value' => '0',
    'subtitle' => '',
    'color' => 'blue'
])

@php
    $colorMap = [
        'blue' => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'border' => 'border-blue-100'],
        'green' => ['bg' => 'bg-green-50', 'icon' => 'text-green-600', 'border' => 'border-green-100'],
        'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'border' => 'border-purple-100'],
        'red' => ['bg' => 'bg-red-50', 'icon' => 'text-red-600', 'border' => 'border-red-100'],
        'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'text-yellow-600', 'border' => 'border-yellow-100'],
        'teal' => ['bg' => 'bg-teal-50', 'icon' => 'text-teal-600', 'border' => 'border-teal-100'],
    ];
    $style = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<div class="bg-white p-3 sm:p-5 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
    <div class="flex items-start justify-between mb-3 sm:mb-4 gap-2">
        <div class="w-9 h-9 sm:w-12 sm:h-12 {{ $style['bg'] }} rounded-xl sm:rounded-2xl flex items-center justify-center border {{ $style['border'] }} shrink-0">
            <i class="fa-solid {{ $icon }} text-sm sm:text-xl {{ $style['icon'] }}"></i>
        </div>
        @if($subtitle)
            <span class="hidden sm:inline-flex text-[10px] font-bold px-2 py-1 bg-gray-50 text-gray-400 rounded-lg uppercase tracking-wider">
                {{ $subtitle }}
            </span>
        @endif
    </div>
    <div>
        <h3 class="text-lg sm:text-2xl font-black text-gray-800 mb-0.5 leading-none">{{ $value }}</h3>
        <p class="text-[10px] sm:text-xs font-medium text-gray-400 uppercase tracking-wide leading-tight">{{ $title }}</p>
    </div>
</div>
