@props(['href', 'icon', 'title', 'active' => false])

<a href="{{ $href }}" 
   class="flex items-center px-4 py-2.5 rounded-xl {{ $active ? 'bg-white/20 backdrop-blur-sm font-medium' : 'hover:bg-white/10 opacity-80 hover:opacity-100' }} transition">
    <i class="fa-solid {{ $icon }} mr-3 text-base"></i> {{ $title }}
    {{ $slot }}
</a>
