@props([
    'title' => 'Section Title',
    'linkText' => 'See all',
    'linkHref' => '#'
])

<div class="flex justify-between items-center mb-4">
    <h2 class="text-lg font-bold text-gray-800">{{ $title }}</h2>
    @if($linkHref && $linkText)
        <a href="{{ $linkHref }}" class="text-sm text-vibrant-green hover:underline font-semibold">{{ $linkText }}</a>
    @endif
</div>
