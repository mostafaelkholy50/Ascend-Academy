@props([
    'title' => 'Course Title',
    'subtitle' => '',
    'progress' => 0,
    'icon' => 'fa-graduation-cap',
    'iconBg' => 'bg-teal-50',
    'iconColor' => 'text-teal-600',
    'href' => '#'
])

<div class="bg-white p-5 rounded-2xl shadow-sm hover:shadow-md transition">
    <div class="flex justify-between items-start mb-3">
        <div>
            <h4 class="font-bold text-gray-800 text-sm">{{ $title }}</h4>
            @if($subtitle)
                <p class="text-xs text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="w-12 h-12 {{ $iconBg }} rounded-xl flex items-center justify-center">
            <i class="fa-solid {{ $icon }} text-2xl {{ $iconColor }}"></i>
        </div>
    </div>

    @if($progress > 0)
        <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
            <div class="bg-vibrant-green h-2 rounded-full" style="width:{{ $progress }}%"></div>
        </div>
        <p class="text-xs text-gray-600 font-semibold mb-3">{{ $progress }}%</p>
    @endif

    <a href="{{ $href }}" class="block w-full px-4 py-2 text-xs bg-vibrant-green text-white rounded-xl font-semibold hover:bg-deep-blue transition text-center">
        View Details
    </a>
</div>
