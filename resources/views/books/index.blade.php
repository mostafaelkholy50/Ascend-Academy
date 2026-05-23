<x-dashboard-layout>
    <x-slot name="title">
        Books - Library
    </x-slot>

    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-deep-blue flex items-center gap-3">
                <i class="fa-solid fa-book-bookmark text-vibrant-green"></i>
                Library & Textbooks
            </h1>
            <p class="text-sm text-gray-500 mt-1">Browse and read educational materials directly inside the platform</p>
        </div>

        @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('manage books'))
            <a href="{{ route('books.create') }}" class="flex items-center gap-2 bg-gradient-to-r from-vibrant-green to-deep-blue text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg hover:scale-[1.02] transition font-medium">
                <i class="fa-solid fa-plus"></i>
                Add New Book
            </a>
        @endif
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl mb-6 shadow-sm flex items-center gap-3 animate-fade-in">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl mb-6 shadow-sm flex items-center gap-3 animate-fade-in">
            <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search Section -->
    <div class="bg-white rounded-2xl p-5 shadow-sm mb-8 border border-gray-100">
        <form method="GET" action="{{ route('books.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-3 flex items-center pr-2 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search books by title or description..." 
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-vibrant-green focus:border-transparent transition-all">
            </div>
            <button type="submit" class="bg-vibrant-green hover:bg-vibrant-green/90 text-white px-8 py-3 rounded-xl transition font-medium shadow-sm">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('books.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl transition text-center flex items-center justify-center font-medium">
                    Clear Filter
                </a>
            @endif
        </form>
    </div>

    <!-- Books Shelf Grid -->
    @if($books->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100 flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 text-4xl mb-4">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <h3 class="text-xl font-bold text-deep-blue">No Books Available</h3>
            <p class="text-gray-500 mt-2 max-w-md">No books were found in the library. If you are an administrator, you can start by adding new books.</p>
        </div>
    @else
        @php
            // List of beautiful gradients for cover generation
            $gradients = [
                'from-emerald-600 to-teal-800',
                'from-indigo-600 to-purple-800',
                'from-blue-600 to-cyan-800',
                'from-amber-600 to-orange-800',
                'from-rose-600 to-pink-800',
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col group hover:-translate-y-1">
                    
                    <!-- Dynamic Cover Container -->
                    <div class="relative pt-[135%] w-full bg-gray-100 overflow-hidden flex-shrink-0 cursor-pointer" onclick="window.location='{{ route('books.show', $book) }}'">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <!-- Dynamic generated book cover -->
                            @php
                                $grad = $gradients[$book->id % count($gradients)];
                            @endphp
                            <div class="absolute inset-0 bg-gradient-to-br {{ $grad }} flex flex-col justify-between p-6 text-white border-l-[12px] border-black/20 select-none">
                                <div class="flex justify-between items-center opacity-80">
                                    <span class="text-[10px] uppercase font-bold tracking-widest">Ascend Book</span>
                                    <i class="fa-solid fa-bookmark text-sm"></i>
                                </div>

                                <div class="flex flex-col items-center justify-center flex-grow text-center py-4">
                                    <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center mb-4 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                                        <i class="fa-solid fa-book-open text-2xl text-white"></i>
                                    </div>
                                    <h2 class="font-bold text-lg leading-snug px-2 line-clamp-3 text-white/95">{{ $book->title }}</h2>
                                </div>

                                <div class="text-center border-t border-white/10 pt-3">
                                    <span class="text-[9px] opacity-70 tracking-wider uppercase font-semibold">Ascend Academy</span>
                                </div>
                            </div>
                        @endif

                        <!-- Inactive Label -->
                        @if(!$book->is_active)
                            <div class="absolute top-3 right-3 bg-red-600 text-white text-[11px] font-bold px-2.5 py-1 rounded-lg shadow-md z-10">
                                Inactive
                            </div>
                        @endif
                    </div>

                    <!-- Book info & Actions -->
                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div class="mb-4">
                            <h3 class="font-bold text-deep-blue text-base leading-snug line-clamp-1 mb-1.5" title="{{ $book->title }}">
                                {{ $book->title }}
                            </h3>
                            <p class="text-xs text-gray-500 line-clamp-2 min-h-[2rem]">
                                {{ $book->description ?? 'No description available for this book.' }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <!-- Main action -->
                            <a href="{{ route('books.show', $book) }}" class="w-full flex items-center justify-center gap-2 bg-vibrant-green hover:bg-vibrant-green/90 text-white py-2 px-4 rounded-xl transition text-sm font-medium shadow-sm">
                                <i class="fa-solid fa-book-open-reader"></i>
                                Read Book
                            </a>

                            <!-- Extra actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('books.download', $book) }}" class="flex-1 flex items-center justify-center gap-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 py-1.5 px-3 rounded-lg border border-gray-200 transition text-xs font-semibold">
                                    <i class="fa-solid fa-download text-gray-400"></i>
                                    Download
                                </a>

                                @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('manage books'))
                                    <a href="{{ route('books.edit', $book) }}" class="bg-gray-50 hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 text-gray-600 p-1.5 rounded-lg border border-gray-200 transition flex items-center justify-center" title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this book?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-gray-50 hover:bg-red-50 hover:text-red-700 hover:border-red-200 text-gray-600 p-1.5 rounded-lg border border-gray-200 transition flex items-center justify-center h-full" title="Delete">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->appends(request()->query())->links() }}
        </div>
    @endif
</x-dashboard-layout>
