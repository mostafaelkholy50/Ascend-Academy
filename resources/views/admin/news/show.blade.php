<x-dashboard-layout title="View News Article">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('admin.news.index') }}" class="hover:text-vibrant-green">News</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">Article Details</span>
        </div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $news->title }}</h1>
                <p class="text-gray-600 text-sm mt-1">
                    <i class="fa-solid fa-calendar mr-1"></i>
                    Created: {{ $news->created_at->format('F d, Y') }}
                    @if($news->published_at)
                        <span class="mx-2">•</span>
                        Published: {{ $news->published_at->format('F d, Y') }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.news.edit', $news->id) }}"
                    class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                    <i class="fa-solid fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.news.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- Status Banner -->
        <div class="px-4 md:px-8 py-4 border-b border-gray-200 
            {{ $news->is_published ? 'bg-green-50' : 'bg-gray-50' }}">
            <div class="flex items-center justify-between">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        {{ $news->is_published ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                        <i class="fa-solid {{ $news->is_published ? 'fa-check-circle' : 'fa-clock' }} mr-2"></i>
                        {{ $news->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
                <div class="text-sm text-gray-600">
                    Slug: <code class="bg-gray-100 px-2 py-1 rounded">{{ $news->slug }}</code>
                </div>
            </div>
        </div>

        <!-- Image -->
        @if($news->image)
            <div class="px-4 md:px-8 py-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Featured Image</h3>
                <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
                    class="w-full h-auto rounded-lg shadow-md max-h-96 object-cover">
            </div>
        @endif

        <!-- Content -->
        <div class="px-4 md:px-8 py-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Article Content</h3>
            <div class="prose max-w-none text-gray-700 leading-relaxed">
                {!! $news->description !!}
            </div>
        </div>

        <!-- Metadata -->
        <div class="px-4 md:px-8 py-6 bg-gray-50 border-t border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Metadata</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600 mb-1">Created At</p>
                    <p class="font-semibold text-gray-800">{{ $news->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600 mb-1">Last Updated</p>
                    <p class="font-semibold text-gray-800">{{ $news->updated_at->format('F d, Y h:i A') }}</p>
                </div>
                @if($news->published_at)
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 mb-1">Published At</p>
                        <p class="font-semibold text-gray-800">{{ $news->published_at->format('F d, Y h:i A') }}</p>
                    </div>
                @endif
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600 mb-1">Public URL</p>
                    <a href="{{ route('news.show', $news->slug) }}" target="_blank"
                        class="font-semibold text-vibrant-green hover:underline">
                        View on Website <i class="fa-solid fa-external-link-alt ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Prose styling for rich text content */
        .prose {
            line-height: 1.75;
        }

        .prose h1,
        .prose h2,
        .prose h3,
        .prose h4,
        .prose h5,
        .prose h6 {
            font-weight: 700;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            color: #1f2937;
        }

        .prose h1 {
            font-size: 2em;
        }

        .prose h2 {
            font-size: 1.5em;
        }

        .prose h3 {
            font-size: 1.25em;
        }

        .prose p {
            margin-bottom: 1em;
        }

        .prose ul,
        .prose ol {
            margin-bottom: 1em;
            padding-left: 1.5em;
        }

        .prose li {
            margin-bottom: 0.5em;
        }

        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1em 0;
        }

        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1em 0;
        }

        .prose table th,
        .prose table td {
            border: 1px solid #e5e7eb;
            padding: 0.5em;
        }

        .prose table th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .prose a {
            color: #1E90A0;
            text-decoration: underline;
        }
    </style>
</x-dashboard-layout>