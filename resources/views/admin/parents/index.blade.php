<x-dashboard-layout title="Manage Parents">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Parents Management</h1>
            <p class="text-gray-600 text-sm">View and manage all parent accounts</p>
        </div>
        <a href="{{ route('admin.parents.create') }}" class="bg-vibrant-green text-white px-6 py-3 rounded-lg hover:bg-deep-blue transition font-semibold shadow-md hover:shadow-lg">
            <i class="fa-solid fa-plus mr-2"></i>Add New Parent
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.parents.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by name, email, or phone"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
            <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                <i class="fa-solid fa-search mr-2"></i>Search
            </button>
            <a href="{{ route('admin.parents.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fa-solid fa-redo"></i>
            </a>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Parents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($parents as $parent)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold">
                                {{ strtoupper(substr($parent->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $parent->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $parent->email }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $parent->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $parent->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-4">
                        @if($parent->phone)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fa-solid fa-phone w-5"></i>
                                <span>{{ $parent->phone }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-users w-5"></i>
                            <span>{{ $parent->children->count() }} {{ $parent->children->count() == 1 ? 'Child' : 'Children' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fa-solid fa-calendar w-5"></i>
                            <span>Joined {{ $parent->created_at->format('M Y') }}</span>
                        </div>
                    </div>

                    <!-- Children Preview -->
                    @if($parent->children->count() > 0)
                        <div class="border-t border-gray-100 pt-3 mb-4">
                            <p class="text-xs font-semibold text-gray-600 mb-2">Children:</p>
                            <div class="space-y-1">
                                @foreach($parent->children->take(3) as $child)
                                    <div class="text-xs text-gray-700 flex items-center">
                                        <i class="fa-solid fa-child text-gray-400 w-4"></i>
                                        {{ $child->name }}
                                    </div>
                                @endforeach
                                @if($parent->children->count() > 3)
                                    <p class="text-xs text-gray-500 italic">+{{ $parent->children->count() - 3 }} more</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <a href="{{ route('admin.parents.show', $parent->id) }}"
                        class="block w-full text-center bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                        <i class="fa-solid fa-eye mr-2"></i>View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <i class="fa-solid fa-users text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No parents found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $parents->links() }}
    </div>
</x-dashboard-layout>
