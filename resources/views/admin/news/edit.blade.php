<x-dashboard-layout title="Edit News Article">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('admin.news.index') }}" class="hover:text-vibrant-green">News</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">Edit Article</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit News Article</h1>
        <p class="text-gray-600 text-sm">Update the article details</p>
    </div>

    <div class="bg-white p-4 md:p-8 rounded-2xl shadow-sm">
        <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $news->title) }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div class="mb-6">
                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                    Featured Image
                </label>

                @if($news->image)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
                            class="max-w-full h-auto rounded-lg shadow-md max-h-64 object-cover">
                    </div>
                @endif

                <div class="flex items-center gap-4">
                    <label class="flex-1 cursor-pointer">
                        <div
                            class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-vibrant-green transition text-center">
                            <i class="fa-solid fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-600">Click to upload new image</p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, or GIF (max 2MB)</p>
                        </div>
                        <input type="file" name="image" id="image" accept="image/*" class="hidden"
                            onchange="previewImage(this)">
                    </label>
                </div>
                <div id="imagePreview" class="mt-4 hidden">
                    <p class="text-sm text-gray-600 mb-2">New Image Preview:</p>
                    <img id="preview" class="max-w-full h-auto rounded-lg shadow-md max-h-64 object-cover">
                </div>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description with Quill Editor -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <input type="hidden" name="description" id="description"
                    value="{{ old('description', $news->description) }}">
                <div id="editor" style="height: 400px;"
                    class="bg-white border border-gray-300 rounded-lg @error('description') border-red-500 @enderror">
                </div>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Published Checkbox -->
            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $news->is_published) ? 'checked' : '' }}
                        class="w-5 h-5 text-vibrant-green border-gray-300 rounded focus:ring-vibrant-green">
                    <span class="ml-2 text-sm font-semibold text-gray-700">Publish this article</span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-7">Uncheck to save as draft</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 border-t border-gray-200 pt-6">
                <button type="submit"
                    class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                    <i class="fa-solid fa-check mr-2"></i>Update Article
                </button>
                <a href="{{ route('admin.news.index') }}"
                    class="text-center px-8 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-semibold">
                    <i class="fa-solid fa-times mr-2"></i>Cancel
                </a>
                <button type="button" onclick="confirmDelete()"
                    class="sm:ml-auto text-center px-8 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold">
                    <i class="fa-solid fa-trash mr-2"></i>Delete
                </button>
            </div>
        </form>

        <!-- Delete Form -->
        <form id="deleteForm" action="{{ route('admin.news.destroy', $news->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <!-- Quill Editor CSS and JS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        // Initialize Quill editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'font': [] }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub' }, { 'script': 'super' }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'indent': '-1' }, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
            placeholder: 'Write your news content here...'
        });

        // Set initial content
        var initialContent = document.getElementById('description').value;
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        // Update hidden input whenever content changes
        quill.on('text-change', function () {
            document.getElementById('description').value = quill.root.innerHTML;
        });

        // Ensure content is transferred on form submit
        document.querySelector('form').addEventListener('submit', function (e) {
            // Transfer Quill content to hidden field
            var content = quill.root.innerHTML;
            document.getElementById('description').value = content;

            // Check if content is empty (only contains whitespace or empty tags)
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            var textContent = tempDiv.textContent || tempDiv.innerText || '';

            if (textContent.trim() === '') {
                e.preventDefault();
                alert('Please enter some content for the news description.');
                return false;
            }
        });

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</x-dashboard-layout>