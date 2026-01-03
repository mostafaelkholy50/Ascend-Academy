@props([
    'label' => 'Class',
    'percentage' => 0,
    'count' => null,
    'color' => 'bg-vibrant-green'
])

<div>
    <div class="flex justify-between text-xs mb-2">
        <span class="font-semibold text-gray-700">{{ $label }}</span>
        <span class="font-bold text-vibrant-green">{{ $percentage }}%</span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-2">
        <div class="{{ $color }} h-2 rounded-full" style="width:{{ $percentage }}%"></div>
    </div>
    @if($count)
        <p class="text-xs text-gray-400 mt-1">{{ $count }} Registered</p>
    @endif
</div>
