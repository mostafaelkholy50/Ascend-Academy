<x-app-layout>
    <!-- Hero Section -->
    <section class="relative h-[40vh] md:h-[50vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-teal-600 to-blue-600">
        <div class="absolute inset-0 bg-black/30"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-white tracking-tight drop-shadow-2xl mb-4">
                Latest <span class="text-yellow-400">News</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto">
                Stay updated with our latest announcements, events, and educational insights
            </p>
        </div>
    </section>

    <!-- News Grid Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($news->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($news as $newsItem)
                        <article class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                            <!-- News Image -->
                            <a href="{{ route('news.show', $newsItem->slug) }}" class="block">
                                <div class="relative h-52 overflow-hidden bg-gradient-to-br from-teal-400 to-blue-500">
                                    @if($newsItem->image)
                                        <img src="{{ asset('storage/' . $newsItem->image) }}" 
                                             alt="{{ $newsItem->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fa-solid fa-newspaper text-6xl text-white opacity-50"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
                                </div>
                            </a>

                            <!-- News Content -->
                            <div class="p-6">
                                <div class="flex items-center text-xs text-gray-500 mb-3">
                                    <i class="fa-solid fa-calendar mr-2"></i>
                                    {{ $newsItem->published_at->format('F d, Y') }}
                                </div>
                                <a href="{{ route('news.show', $newsItem->slug) }}">
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#1E90A0] transition-colors">
                                        {{ $newsItem->title }}
                                    </h3>
                                </a>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                    {{ $newsItem->getExcerpt(150) }}
                                </p>
                                <a href="{{ route('news.show', $newsItem->slug) }}" 
                                   class="inline-flex items-center text-[#1E90A0] font-semibold hover:text-teal-700 transition-colors">
                                    Read More
                                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $news->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <i class="fa-solid fa-newspaper text-8xl text-gray-300 mb-6"></i>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">No News Available</h2>
                    <p class="text-gray-600">Please check back later for updates.</p>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
