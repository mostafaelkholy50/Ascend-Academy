<x-dashboard-layout>
    <x-slot name="title">
        Add New Book
    </x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-deep-blue">Add New Book to Library</h1>
            <p class="text-sm text-gray-500 mt-1">Fill in the details and upload the book file for students and teachers to view</p>
        </div>

        <a href="{{ route('books.index') }}" class="flex items-center gap-1.5 text-gray-600 hover:text-deep-blue bg-white border border-gray-200 px-4 py-2 rounded-xl transition text-sm font-semibold shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Library
        </a>
    </div>

    <!-- Error Messages -->
    <div id="ajax-error-container" class="hidden bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl mb-6 shadow-sm">
        <h4 class="font-bold mb-1">Please correct the following errors:</h4>
        <ul id="ajax-error-list" class="list-disc pl-5 text-sm"></ul>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl mb-6 shadow-sm">
            <h4 class="font-bold mb-1">Please correct the following errors:</h4>
            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 max-w-3xl mx-auto">
        <form id="book-form" action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="chunk_upload_id" id="chunk_upload_id">

            <!-- Title -->
            <div class="space-y-2">
                <label for="title" class="block text-sm font-semibold text-gray-700">Book Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Enter textbook title..." 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-vibrant-green focus:border-transparent transition-all">
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="description" class="block text-sm font-semibold text-gray-700">Book Description</label>
                <textarea name="description" id="description" rows="4" placeholder="Write a short description of the textbook contents..." 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-vibrant-green focus:border-transparent transition-all">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- PDF File -->
                <div class="space-y-2">
                    <label for="pdf_file" class="block text-sm font-semibold text-gray-700">Book File (PDF) <span class="text-red-500">*</span></label>
                    <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-vibrant-green transition cursor-pointer bg-gray-50 flex flex-col items-center justify-center text-center">
                        <input type="file" name="pdf_file" id="pdf_file" required accept="application/pdf" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                        <i class="fa-solid fa-file-pdf text-3xl text-gray-400 mb-2"></i>
                        <span class="text-xs font-semibold text-gray-600 block">Choose PDF File</span>
                        <span class="text-[10px] text-gray-400 mt-1 block">Max file size: 250MB</span>
                    </div>
                    <div id="pdf-file-name" class="text-xs text-vibrant-green font-semibold mt-1 hidden"></div>
                </div>

                <!-- Cover Image -->
                <div class="space-y-2">
                    <label for="cover_image" class="block text-sm font-semibold text-gray-700">Book Cover Image (Optional)</label>
                    <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-vibrant-green transition cursor-pointer bg-gray-50 flex flex-col items-center justify-center text-center">
                        <input type="file" name="cover_image" id="cover_image" accept="image/*" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                        <i class="fa-solid fa-image text-3xl text-gray-400 mb-2"></i>
                        <span class="text-xs font-semibold text-gray-600 block">Choose Cover Image</span>
                        <span class="text-[10px] text-gray-400 mt-1 block">Image formats only (PNG, JPG, WebP)</span>
                    </div>
                    <div id="cover-file-name" class="text-xs text-vibrant-green font-semibold mt-1 hidden"></div>
                </div>
            </div>

            <!-- Is Active Toggle -->
            <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked 
                    class="w-5 h-5 text-vibrant-green focus:ring-vibrant-green border-gray-300 rounded transition-all cursor-pointer">
                <label for="is_active" class="text-sm font-bold text-gray-700 cursor-pointer">Make this book active and available immediately</label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('books.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 transition font-medium text-sm">
                    Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-vibrant-green to-deep-blue text-white px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition font-medium text-sm">
                    Save Book
                </button>
            </div>
        </form>
    </div>

    <!-- Progress Overlay Modal -->
    <div id="upload-progress-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-100 transform scale-95 transition-transform duration-300">
            <div class="flex flex-col items-center text-center">
                <!-- Icon & Glow -->
                <div class="relative w-20 h-20 flex items-center justify-center bg-green-50 rounded-full mb-6">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-vibrant-green animate-bounce"></i>
                    <div class="absolute inset-0 rounded-full border-4 border-vibrant-green border-t-transparent animate-spin"></div>
                </div>
                
                <h3 class="text-xl font-bold text-deep-blue mb-1" id="upload-status-title">Uploading Book...</h3>
                <p class="text-xs text-gray-400 mb-6 font-medium">Please do not close this tab or refresh the page.</p>
                
                <!-- Progress Bar Container -->
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden mb-3 relative shadow-inner">
                    <div id="progress-bar-fill" class="bg-gradient-to-r from-vibrant-green to-deep-blue h-full w-0 transition-all duration-100 ease-out rounded-full relative">
                        <!-- Shimmer effect -->
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                </div>
                
                <!-- Statistics -->
                <div class="w-full flex justify-between items-center text-xs font-semibold">
                    <span class="text-vibrant-green bg-green-50 px-2.5 py-1 rounded-md" id="progress-percent">0%</span>
                    <span class="text-gray-500 font-mono" id="progress-bytes">0 MB / 0 MB</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Display filename when PDF is selected
            document.getElementById('pdf_file').addEventListener('change', function(e) {
                const fileNameDiv = document.getElementById('pdf-file-name');
                if (e.target.files.length > 0) {
                    fileNameDiv.textContent = 'Selected file: ' + e.target.files[0].name;
                    fileNameDiv.classList.remove('hidden');
                } else {
                    fileNameDiv.classList.add('hidden');
                }
            });

            // Display filename when Cover is selected
            document.getElementById('cover_image').addEventListener('change', function(e) {
                const fileNameDiv = document.getElementById('cover-file-name');
                if (e.target.files.length > 0) {
                    fileNameDiv.textContent = 'Selected image: ' + e.target.files[0].name;
                    fileNameDiv.classList.remove('hidden');
                } else {
                    fileNameDiv.classList.add('hidden');
                }
            });

            // Handle AJAX form submission with upload progress tracking
            document.getElementById('book-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const errorContainer = document.getElementById('ajax-error-container');
                const errorList = document.getElementById('ajax-error-list');
                
                // Clear and hide previous error messages
                errorContainer.classList.add('hidden');
                errorList.innerHTML = '';
                
                const formData = new FormData(form);
                const pdfInput = document.getElementById('pdf_file');
                const pdfFile = pdfInput.files[0];
                
                // Show progress modal
                const modal = document.getElementById('upload-progress-modal');
                const statusTitle = document.getElementById('upload-status-title');
                statusTitle.textContent = 'Uploading Book...';
                modal.classList.remove('hidden');
                
                // Reset progress values
                const barFill = document.getElementById('progress-bar-fill');
                const percentText = document.getElementById('progress-percent');
                const bytesText = document.getElementById('progress-bytes');
                
                barFill.style.width = '0%';
                percentText.textContent = '0%';
                bytesText.textContent = '0 MB / 0 MB';
                
                // Helper to format bytes cleanly
                function formatBytes(bytes, decimals = 1) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const dm = decimals < 0 ? 0 : decimals;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                }
                
                async function uploadInChunks(file) {
                    const chunkSize = 5 * 1024 * 1024;
                    const totalChunks = Math.ceil(file.size / chunkSize);
                    const uploadId = (crypto.randomUUID ? crypto.randomUUID() : (Date.now() + '-' + Math.random().toString(36).slice(2)));
                    document.getElementById('chunk_upload_id').value = uploadId;

                    for (let i = 0; i < totalChunks; i++) {
                        const start = i * chunkSize;
                        const end = Math.min(start + chunkSize, file.size);
                        const chunk = file.slice(start, end);
                        const chunkData = new FormData();
                        chunkData.append('_token', '{{ csrf_token() }}');
                        chunkData.append('upload_id', uploadId);
                        chunkData.append('chunk_index', i);
                        chunkData.append('total_chunks', totalChunks);
                        chunkData.append('file_name', file.name);
                        chunkData.append('chunk', chunk, file.name + '.part');

                        const res = await fetch('{{ route('books.upload-pdf-chunk') }}', {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: chunkData
                        });

                        if (!res.ok) {
                            throw new Error('Chunk upload failed at part ' + (i + 1));
                        }

                        const percent = Math.round(((i + 1) / totalChunks) * 100);
                        barFill.style.width = percent + '%';
                        percentText.textContent = percent + '%';
                        bytesText.textContent = formatBytes(end) + ' / ' + formatBytes(file.size);
                    }
                    return uploadId;
                }

                const sendFinalForm = () => {
                // Set up XHR request
                const xhr = new XMLHttpRequest();
                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                // Track upload progress
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        barFill.style.width = percent + '%';
                        percentText.textContent = percent + '%';
                        bytesText.textContent = formatBytes(e.loaded) + ' / ' + formatBytes(e.total);
                    }
                });
                
                // Handle completion
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success && response.redirect) {
                                barFill.style.width = '100%';
                                percentText.textContent = '100%';
                                statusTitle.textContent = 'Upload Complete! Redirecting...';
                                
                                setTimeout(function() {
                                    window.location.href = response.redirect;
                                }, 500);
                            } else {
                                modal.classList.add('hidden');
                                showErrorBanner(['An unexpected response was received from the server.']);
                            }
                        } catch (e) {
                            modal.classList.add('hidden');
                            showErrorBanner(['Failed to parse server response.']);
                        }
                    } else if (xhr.status === 413) {
                        modal.classList.add('hidden');
                        showErrorBanner([
                            'The file you uploaded is too large for the server\'s configuration (HTTP 413: Content Too Large).',
                            'To resolve this: Open your php.ini (located in C:\\xampp\\php\\php.ini), increase "upload_max_filesize" and "post_max_size" to "256M" (or higher), restart Apache in XAMPP, and try again.'
                        ]);
                    } else if (xhr.status === 422) {
                        modal.classList.add('hidden');
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.errors) {
                                const errorMsgs = [];
                                Object.keys(response.errors).forEach(function(field) {
                                    response.errors[field].forEach(function(msg) {
                                        errorMsgs.push(msg);
                                    });
                                });
                                showErrorBanner(errorMsgs);
                            } else if (response.message) {
                                showErrorBanner([response.message]);
                            } else {
                                showErrorBanner(['An error occurred while uploading. Please check inputs and try again.']);
                            }
                        } catch (e) {
                            showErrorBanner(['Validation failed, but error details could not be displayed.']);
                        }
                    } else {
                        modal.classList.add('hidden');
                        showErrorBanner(['Server returned error code ' + xhr.status + ': ' + xhr.statusText]);
                    }
                };
                
                xhr.onerror = function() {
                    modal.classList.add('hidden');
                    showErrorBanner(['A network error occurred. Please check your connection and try again.']);
                };
                
                // Send form data
                xhr.send(formData);
                };

                (async () => {
                    try {
                        if (pdfFile) {
                            statusTitle.textContent = 'Uploading Book in Parts...';
                            const uploadId = await uploadInChunks(pdfFile);
                            formData.delete('pdf_file');
                            formData.set('chunk_upload_id', uploadId);
                        }
                        statusTitle.textContent = 'Saving Book Record...';
                        sendFinalForm();
                    } catch (err) {
                        modal.classList.add('hidden');
                        showErrorBanner([err.message || 'Chunk upload failed.']);
                    }
                })();
                
                function showErrorBanner(messages) {
                    errorList.innerHTML = '';
                    messages.forEach(function(msg) {
                        const li = document.createElement('li');
                        li.textContent = msg;
                        errorList.appendChild(li);
                    });
                    errorContainer.classList.remove('hidden');
                    errorContainer.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            });
        </script>
    @endpush
</x-dashboard-layout>
