<x-dashboard-layout title="Create Report">
    <div class="mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('teacher.reports.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Create New Report</h1>
                <p class="text-gray-600 text-sm">Fill in the details to create a student progress report</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('teacher.reports.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Student Selection -->
            <div class="md:col-span-2">
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student <span class="text-red-500">*</span></label>
                <select id="student_id" name="student_id" required class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">Select a student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id', $selectedStudent) == $student->id ? 'selected' : '' }}>
                            {{ $student->name }} 
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Course Selection -->
            <div class="md:col-span-2">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                <select id="course_id" name="course_id" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">Select a course (optional)</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Report Date -->
            <div>
                <label for="report_date" class="block text-sm font-medium text-gray-700 mb-2">Report Date <span class="text-red-500">*</span></label>
                <input type="date" id="report_date" name="report_date" value="{{ old('report_date', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
            </div>

            <!-- Level -->
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Current Level</label>
                <select id="level" name="level" class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">
                    <option value="">Select Level</option>
                    <option value="Qaida Nooraniya" {{ old('level') == 'Qaida Nooraniya' ? 'selected' : '' }}>Qaida Nooraniya</option>
                    <option value="Nazira (Reading)" {{ old('level') == 'Nazira (Reading)' ? 'selected' : '' }}>Nazira (Reading)</option>
                    <option value="Hifz (Memorization)" {{ old('level') == 'Hifz (Memorization)' ? 'selected' : '' }}>Hifz (Memorization)</option>
                    <option value="Tajweed Rules" {{ old('level') == 'Tajweed Rules' ? 'selected' : '' }}>Tajweed Rules</option>
                    <option value="Foundation" {{ old('level') == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                    <option value="Beginner" {{ old('level') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="Intermediate" {{ old('level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                    <option value="Ijazah" {{ old('level') == 'Ijazah' ? 'selected' : '' }}>Ijazah</option>
                </select>
            </div>

            <!-- Mastery Score -->
            <div class="md:col-span-2">
                <label for="mastery_score" class="block text-sm font-medium text-gray-700 mb-2">Mastery Score (0-100%)</label>
                <div class="flex items-center space-x-4">
                    <input type="range" id="mastery_score_slider" name="mastery_score" min="0" max="100" value="{{ old('mastery_score', 50) }}" class="flex-grow h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    <span id="mastery_score_display" class="text-2xl font-bold text-vibrant-green w-16 text-center">{{ old('mastery_score', 50) }}%</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Needs Improvement</span>
                    <span>Excellent</span>
                </div>
            </div>

            <!-- Strengths -->
            <div class="md:col-span-2">
                <label for="strengths" class="block text-sm font-medium text-gray-700 mb-2">Strengths</label>
                <textarea id="strengths" name="strengths" rows="3" maxlength="1000" placeholder="Describe the student's strengths and areas they excel in..." class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">{{ old('strengths') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 1000 characters</p>
            </div>

            <!-- Areas for Improvement -->
            <div class="md:col-span-2">
                <label for="weaknesses" class="block text-sm font-medium text-gray-700 mb-2">Areas for Improvement</label>
                <textarea id="weaknesses" name="weaknesses" rows="3" maxlength="1000" placeholder="Identify areas where the student can improve..." class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">{{ old('weaknesses') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 1000 characters</p>
            </div>

            <!-- Behavior & Attitude -->
            <div class="md:col-span-2">
                <label for="behavior" class="block text-sm font-medium text-gray-700 mb-2">Behavior & Attitude</label>
                <textarea id="behavior" name="behavior" rows="2" maxlength="1000" placeholder="Comment on the student's behavior, participation, and attitude..." class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">{{ old('behavior') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 1000 characters</p>
            </div>

            <!-- Additional Notes -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea id="notes" name="notes" rows="4" maxlength="2000" placeholder="Any additional comments, recommendations, or observations..." class="w-full rounded-lg border-gray-300 focus:border-vibrant-green focus:ring focus:ring-vibrant-green focus:ring-opacity-50">{{ old('notes') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 2000 characters</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('teacher.reports.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-semibold">
                Cancel
            </a>
            <button type="submit" class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold shadow-sm">
                <i class="fa-solid fa-save mr-2"></i>Create Report
            </button>
        </div>
    </form>

    <script>
        // Update mastery score display
        const slider = document.getElementById('mastery_score_slider');
        const display = document.getElementById('mastery_score_display');

        slider.addEventListener('input', function() {
            display.textContent = this.value + '%';

            // Change color based on score
            if (this.value < 60) {
                display.className = 'text-2xl font-bold text-red-500 w-16 text-center';
            } else if (this.value < 80) {
                display.className = 'text-2xl font-bold text-yellow-500 w-16 text-center';
            } else {
                display.className = 'text-2xl font-bold text-vibrant-green w-16 text-center';
            }
        });

        // Load courses when student is selected
        const studentSelect = document.getElementById('student_id');
        const courseSelect = document.getElementById('course_id');

        studentSelect.addEventListener('change', function() {
            const studentId = this.value;

            if (!studentId) {
                courseSelect.innerHTML = '<option value="">Select a course (optional)</option>';
                return;
            }

            // Fetch courses for this student
            fetch(`/teacher/reports/student/${studentId}/courses`)
                .then(response => response.json())
                .then(courses => {
                    courseSelect.innerHTML = '<option value="">Select a course (optional)</option>';
                    courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.title;
                        courseSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading courses:', error));
        });
    </script>
</x-dashboard-layout>
