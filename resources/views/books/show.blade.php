<x-dashboard-layout>
    <x-slot name="title">
        {{ $book->title }} - Read
    </x-slot>

    <!-- Navigation Breadcrumbs & Back -->
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-vibrant-green transition">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('books.index') }}" class="hover:text-vibrant-green transition">Library</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-deep-blue font-medium line-clamp-1 max-w-[200px]">{{ $book->title }}</span>
        </div>

        <a href="{{ route('books.index') }}" class="flex items-center gap-1.5 text-gray-600 hover:text-deep-blue bg-white border border-gray-200 px-4 py-2 rounded-xl transition text-sm font-semibold shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Library
        </a>
    </div>

    <!-- Reader Interface Wrapper -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        
        <!-- Main Reader Panel (takes 3 cols on large screens) -->
        <div class="lg:col-span-3 flex flex-col gap-4">
            
            <!-- Reader Container with Toolbar and Iframe -->
            <div id="reader-container" class="bg-white rounded-3xl overflow-hidden shadow-md border border-gray-100 flex flex-col p-4">
                
                <!-- Toolbar -->
                <div class="flex flex-wrap items-center justify-between border-b border-gray-100 pb-4 mb-4 gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-vibrant-green/10 text-vibrant-green rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-book-open text-lg"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-deep-blue text-sm md:text-base leading-snug line-clamp-1 max-w-[200px] md:max-w-xs">{{ $book->title }}</h2>
                            <p class="text-[10px] text-gray-400 mt-0.5">Native PDF Reader</p>
                        </div>
                    </div>

                    <!-- Toolbar Reader Controls -->
                    <div class="flex items-center gap-3">
                        <button onclick="toggleFullScreen()" class="w-10 h-10 bg-white hover:bg-gray-100 text-gray-600 rounded-xl border border-gray-200 flex items-center justify-center transition" title="Fullscreen">
                            <i id="fullscreen-icon" class="fa-solid fa-expand"></i>
                        </button>
                        <a href="{{ route('books.download', $book) }}" class="flex items-center gap-1.5 bg-deep-blue hover:bg-deep-blue/90 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm" title="Download Book">
                            <i class="fa-solid fa-download"></i>
                            <span class="hidden sm:inline">Download</span>
                        </a>
                    </div>
                </div>

                <!-- Iframe Viewer -->
                <div class="relative h-[70vh] w-full rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
                    <!-- Loading Spinner Overlay -->
                    <div id="pdf-loader" class="absolute inset-0 bg-white flex flex-col items-center justify-center z-30 transition-all duration-300">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 border-4 border-vibrant-green border-t-transparent rounded-full animate-spin mb-4"></div>
                            <h3 class="font-bold text-deep-blue text-base">Loading book...</h3>
                            <p class="text-xs text-gray-400 mt-1">Please wait while the PDF is loaded</p>
                        </div>
                    </div>
                    <iframe id="pdf-iframe" src="{{ route('books.stream', $book) }}" class="w-full h-full" style="border: none;"></iframe>
                </div>

            </div>
        </div>

        <!-- Right/Sidebar Panel: Other Books (takes 1 col) -->
        <div class="lg:col-span-1 flex flex-col gap-4">
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
                <h3 class="font-bold text-deep-blue text-base mb-4 border-b border-gray-50 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-list-ul text-vibrant-green"></i>
                    Other Books
                </h3>

                @if($otherBooks->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">No other books available currently.</p>
                @else
                    <div class="flex flex-col gap-3 max-h-[65vh] overflow-y-auto pr-1">
                        @foreach($otherBooks as $other)
                            <a href="{{ route('books.show', $other) }}" class="flex gap-3 p-2.5 rounded-2xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition group">
                                <!-- Mini cover placeholder -->
                                <div class="w-12 h-16 rounded-lg bg-gradient-to-br from-deep-blue to-vibrant-green flex-shrink-0 flex items-center justify-center text-white border-l-[3px] border-black/20 text-xs shadow-sm font-black group-hover:scale-95 transition-transform duration-200">
                                    <i class="fa-solid fa-book text-[10px]"></i>
                                </div>
                                <div class="flex flex-col justify-center min-w-0">
                                    <h4 class="font-bold text-gray-800 text-xs line-clamp-1 group-hover:text-vibrant-green transition mb-1">{{ $other->title }}</h4>
                                    <p class="text-[10px] text-gray-400 line-clamp-1">Read now</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            const iframe = document.getElementById('pdf-iframe');
            if (iframe) {
                iframe.onload = function() {
                    const loader = document.getElementById('pdf-loader');
                    if (loader) {
                        loader.style.opacity = '0';
                        setTimeout(() => {
                            loader.style.display = 'none';
                        }, 300);
                    }
                };
            }

            function toggleFullScreen() {
                const container = document.getElementById("reader-container");
                const icon = document.getElementById("fullscreen-icon");
                
                if (!document.fullscreenElement) {
                    container.requestFullscreen().then(() => {
                        container.classList.add('p-8');
                        icon.classList.remove('fa-expand');
                        icon.classList.add('fa-compress');
                    }).catch(err => {
                        console.error("Error enabling full-screen mode:", err.message);
                    });
                } else {
                    document.exitFullscreen().then(() => {
                        container.classList.remove('p-8');
                        icon.classList.remove('fa-compress');
                        icon.classList.add('fa-expand');
                    });
                }
            }

            // Keep padding clean when exiting full screen using Esc key
            document.addEventListener('fullscreenchange', () => {
                const container = document.getElementById("reader-container");
                const icon = document.getElementById("fullscreen-icon");
                if (!document.fullscreenElement) {
                    container.classList.remove('p-8');
                    icon.classList.remove('fa-compress');
                    icon.classList.add('fa-expand');
                }
            });
        </script>
    @endpush
</x-dashboard-layout>
