    <x-app-layout>
        <!-- Hero Section with Image -->
        <section class="relative h-[50vh] md:h-[60vh] overflow-hidden">
            @if($newsItem->image)
                <img src="{{ asset('storage/' . $newsItem->image) }}" alt="{{ $newsItem->title }}"
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-teal-600 to-blue-600"></div>
            @endif

            <div class="absolute bottom-0 left-0 right-0 pb-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center text-sm text-gray-200 mb-4">
                        <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                        <i class="fa-solid fa-chevron-right mx-2 text-xs"></i>
                        <a href="{{ route('news') }}" class="hover:text-white transition">News</a>
                        <i class="fa-solid fa-chevron-right mx-2 text-xs"></i>
                        <span class="text-white">Article</span>
                    </div>
                    <h1
                        class="text-3xl md:text-5xl lg:text-6xl font-serif font-bold text-white tracking-tight drop-shadow-2xl mb-4">
                        {{ $newsItem->title }}
                    </h1>
                    <div class="flex items-center text-gray-200">
                        <i class="fa-solid fa-calendar mr-2"></i>
                        <span>{{ $newsItem->published_at->format('F d, Y') }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section class="py-12 md:py-20 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <article class="prose prose-lg max-w-none ql-editor !overflow-visible !h-auto">
                        {!! $newsItem->description !!}
                </article>

                <!-- Share Section -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Share this article</h3>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('news.show', $newsItem->slug)) }}"
                            target="_blank"
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('news.show', $newsItem->slug)) }}&text={{ urlencode($newsItem->title) }}"
                            target="_blank"
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-sky-500 text-white hover:bg-sky-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($newsItem->title . ' ' . route('news.show', $newsItem->slug)) }}"
                            target="_blank"
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button onclick="copyToClipboard()"
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-600 text-white hover:bg-gray-700 transition">
                            <i class="fa-solid fa-link"></i>
                        </button>
                    </div>
                </div>

                <!-- Back to News -->
                <div class="mt-12">
                    <a href="{{ route('news') }}"
                        class="inline-flex items-center text-[#1E90A0] font-semibold hover:text-teal-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to All News
                    </a>
                </div>
            </div>
        </section>

        <!-- Related News -->
        @if($relatedNews->count() > 0)
            <section class="py-16 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
                        Related News
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                        @foreach($relatedNews as $related)
                            <article
                                class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                                <a href="{{ route('news.show', $related->slug) }}" class="block">
                                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-teal-400 to-blue-500">
                                        @if($related->image)
                                            <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->title }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fa-solid fa-newspaper text-5xl text-white opacity-50"></i>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <div class="p-6">
                                    <div class="flex items-center text-xs text-gray-500 mb-2">
                                        <i class="fa-solid fa-calendar mr-2"></i>
                                        {{ $related->published_at->format('F d, Y') }}
                                    </div>
                                    <a href="{{ route('news.show', $related->slug) }}">
                                        <h3
                                            class="text-lg font-bold text-gray-900 mb-2 group-hover:text-[#1E90A0] transition-colors line-clamp-2">
                                            {{ $related->title }}
                                        </h3>
                                    </a>
                                    <p class="text-gray-600 text-sm line-clamp-2">
                                        {!! $related->getExcerpt(100) !!}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <script>
            function copyToClipboard() {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(function () {
                    alert('Link copied to clipboard!');
                }, function () {
                    alert('Failed to copy link');
                });
            }
        </script>

        <style>
            /* Enhanced prose styling for rich text content */
            .prose {
                color: #374151;
                line-height: 1.75;
            }

            .prose h1,
            .prose h2,
            .prose h3,
            .prose h4,
            .prose h5,
            .prose h6 {
                font-weight: 700;
                color: #1f2937;
                line-height: 1.3;
            }

            .prose h1 {
                font-size: 2.25em;
            }

            .prose h2 {
                font-size: 1.875em;
            }

            .prose h3 {
                font-size: 1.5em;
            }

            .prose h4 {
                font-size: 1.25em;
            }

            .prose p {
                margin-bottom: 1.25em;
            }

            .prose ul,
            .prose ol {
                padding-left: 1.75em;
            }

            .prose ul {
                list-style-type: disc;
            }

            .prose ol {
                list-style-type: decimal;
            }

            .prose li {
                margin-bottom: 0.75em;
            }

            .prose img {
                max-width: 100%;
                height: auto;
                border-radius: 0.75rem;
                margin: 2em 0;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }

            .prose table {
                width: 100%;
                border-collapse: collapse;
                margin: 2em 0;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
                border-radius: 0.5rem;
                overflow: hidden;
            }

            .prose table th,
            .prose table td {
                border: 1px solid #e5e7eb;
                padding: 0.75em 1em;
            }

            .prose table th {
                background-color: #f9fafb;
                font-weight: 600;
                text-align: left;
            }

            .prose table tr:nth-child(even) {
                background-color: #f9fafb;
            }

            .prose a {
                color: #1E90A0;
                text-decoration: underline;
                font-weight: 500;
            }

            .prose a:hover {
                color: #157a8a;
            }

            .prose strong {
                font-weight: 700;
                color: #1f2937;
            }

            .prose em {
                font-style: italic;
            }

            .prose blockquote {
                border-left: 4px solid #1E90A0;
                padding-left: 1.5em;
                margin: 1.5em 0;
                font-style: italic;
                color: #4b5563;
            }

            .prose code {
                background-color: #f3f4f6;
                padding: 0.2em 0.4em;
                border-radius: 0.25rem;
                font-size: 0.875em;
                font-family: monospace;
            }

            .prose pre {
                background-color: #1f2937;
                color: #f3f4f6;
                padding: 1.5em;
                border-radius: 0.5rem;
                overflow-x: auto;
                margin: 1.5em 0;
            }

            .prose pre code {
                background: none;
                padding: 0;
                color: inherit;
            }
        </style>
    </x-app-layout>