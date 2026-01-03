@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
        @if($notifications->where('read_at', null)->count() > 0)
            <form method="POST" action="{{ route(strtolower(auth()->user()->role) . '.notifications.read-all') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-vibrant-green text-white rounded-lg hover:bg-vibrant-green/90 transition">
                    <i class="fa-solid fa-check-double mr-2"></i>Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        @forelse($notifications as $notification)
            <div class="border-b border-gray-100 last:border-0">
                <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-4">
                        @if(!$notification->read_at)
                            <div class="w-3 h-3 bg-vibrant-green rounded-full mt-1 flex-shrink-0"></div>
                        @else
                            <div class="w-3 h-3 bg-gray-300 rounded-full mt-1 flex-shrink-0"></div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-gray-800 {{ !$notification->read_at ? 'font-bold' : '' }}">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        <i class="fa-regular fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                
                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route(strtolower(auth()->user()->role) . '.notifications.read', $notification->id) }}" class="flex-shrink-0">
                                        @csrf
                                        <button type="submit" class="text-xs text-vibrant-green hover:underline">
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="p-12 text-center text-gray-500">
                <i class="fa-regular fa-bell-slash text-5xl mb-4"></i>
                <p class="text-lg">No notifications yet</p>
                <p class="text-sm mt-2">You'll see notifications here when you receive them</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
