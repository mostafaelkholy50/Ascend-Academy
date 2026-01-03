<x-dashboard-layout title="Edit Resource">
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.resources.show', $resource->id) }}" class="text-gray-600 hover:text-gray-800">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Resource</h1>
                <p class="text-gray-600 text-sm">Update resource information and file</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm p-8">
        <form action="{{ route('teacher.resources.update', $resource->id) }}" method="POST" enctype="multipart/form-data" id="resourceForm">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Resource Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $resource->title) }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50"
                        placeholder="e.g., Tajweed Rules PDF, Quran Recitation Video">
                </div>

                <!-- Resource Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Resource Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="resourceType" required
                        class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                        <option value="">Select Type</option>
                        <option value="pdf" {{ old('type', $resource->type) == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                        <option value="image" {{ old('type', $resource->type) == 'image' ? 'selected' : '' }}>Image</option>
                        <option value="video" {{ old('type', $resource->type) == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="audio" {{ old('type', $resource->type) == 'audio' ? 'selected' : '' }}>Audio</option>
                        <option value="link" {{ old('type', $resource->type) == 'link' ? 'selected' : '' }}>External Link</option>
                        <option value="other" {{ old('type', $resource->type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Student (Required) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $resource->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Course (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Course (Optional)
                    </label>
                    <select name="course_id"
                        class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                        <option value="">No Specific Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $resource->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50"
                        placeholder="Brief description of this resource...">{{ old('description', $resource->description) }}</textarea>
                </div>

                <!-- Current File Info -->
                @if($resource->isFile())
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current File</label>
                        <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fa-solid fa-file text-gray-600 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ basename($resource->file_path) }}</p>
                                    @if($resource->getFileSize())
                                        <p class="text-xs text-gray-500">{{ number_format($resource->getFileSize() / 1024 / 1024, 2) }} MB</p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('teacher.resources.download', $resource->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- File Upload (Replace existing file) -->
                <div class="md:col-span-2" id="fileUploadSection" style="{{ $resource->type == 'link' ? 'display:none;' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Replace File (Optional)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-vibrant-green transition"
                        id="dropZone">
                        <input type="file" name="file" id="fileInput" class="hidden" accept="*/*">
                        <i class="fa-solid fa-cloud-upload text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-600 mb-2">Drag and drop a new file here, or click to browse</p>
                        <button type="button" onclick="document.getElementById('fileInput').click()"
                            class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition font-semibold">
                            Choose New File
                        </button>
                        <p class="text-xs text-gray-500 mt-3">Leave empty to keep the current file. Maximum size: 50MB</p>
                        <div id="filePreview" class="mt-4 hidden">
                            <div class="bg-gray-100 rounded-lg p-4 inline-block">
                                <i class="fa-solid fa-file text-gray-600 mr-2"></i>
                                <span id="fileName" class="text-gray-800 font-medium"></span>
                                <button type="button" onclick="clearFile()" class="ml-3 text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- External URL -->
                <div class="md:col-span-2" id="urlSection" style="{{ $resource->type != 'link' ? 'display:none;' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        External URL <span class="text-red-500">*</span>
                    </label>
                    <input type="url" name="external_url" value="{{ old('external_url', $resource->external_url) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50"
                        placeholder="https://example.com/resource">
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 mt-8">
                <button type="submit"
                    class="bg-vibrant-green text-white px-8 py-3 rounded-xl hover:bg-deep-blue transition font-semibold shadow-sm">
                    <i class="fa-solid fa-save mr-2"></i>Update Resource
                </button>
                <a href="{{ route('teacher.resources.show', $resource->id) }}"
                    class="bg-gray-200 text-gray-700 px-8 py-3 rounded-xl hover:bg-gray-300 transition font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        // Toggle between file upload and URL input based on resource type
        const resourceType = document.getElementById('resourceType');
        const fileUploadSection = document.getElementById('fileUploadSection');
        const urlSection = document.getElementById('urlSection');

        resourceType.addEventListener('change', function() {
            if (this.value === 'link') {
                fileUploadSection.style.display = 'none';
                urlSection.style.display = 'block';
            } else {
                fileUploadSection.style.display = 'block';
                urlSection.style.display = 'none';
            }
        });

        // File input handling
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const dropZone = document.getElementById('dropZone');

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
                filePreview.classList.remove('hidden');
            }
        });

        // Drag and drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-vibrant-green', 'bg-green-50');
        });

        dropZone.addEventListener('dragleave', function() {
            this.classList.remove('border-vibrant-green', 'bg-green-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-vibrant-green', 'bg-green-50');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = e.dataTransfer.files[0].name;
                filePreview.classList.remove('hidden');
            }
        });

        function clearFile() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
        }
    </script>
</x-dashboard-layout>
