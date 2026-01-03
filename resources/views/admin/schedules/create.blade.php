<x-dashboard-layout title="Create Schedule">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.schedules.index') }}" class="hover:text-vibrant-green">Schedules</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Create Recurring Schedule</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Create Recurring Schedule</h1>
        <p class="text-gray-600 text-sm">Set up recurring class sessions for an enrollment</p>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 whitespace-pre-line">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <p class="font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.schedules.store') }}" class="max-w-4xl" id="scheduleForm">
        @csrf

        <!-- Step 1: Select Enrollment -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                Select Enrollment
            </h2>

            <div>
                <label for="enrollment_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Enrollment <span class="text-red-500">*</span>
                </label>
                <select name="enrollment_id" id="enrollment_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    <option value="">Select an enrollment</option>
                    @foreach($enrollments as $enrollment)
                        <option value="{{ $enrollment->id }}" {{ old('enrollment_id') == $enrollment->id ? 'selected' : '' }}
                            data-student="{{ $enrollment->student->name }}"
                            data-course="{{ $enrollment->course->title }}"
                            data-start="{{ $enrollment->start_date?->format('M d, Y') }}"
                            data-end="{{ $enrollment->end_date?->format('M d, Y') }}">
                            {{ $enrollment->student->name }} - {{ $enrollment->course->title }}
                            ({{ $enrollment->start_date?->format('M d') }} to {{ $enrollment->end_date?->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
                @error('enrollment_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                
                <div id="enrollmentInfo" class="mt-3 p-3 bg-blue-50 rounded-lg hidden">
                    <p class="text-sm text-blue-800"><strong>Student:</strong> <span id="studentName"></span></p>
                    <p class="text-sm text-blue-800"><strong>Course:</strong> <span id="courseName"></span></p>
                    <p class="text-sm text-blue-800"><strong>Duration:</strong> <span id="enrollmentDuration"></span></p>
                </div>
            </div>
        </div>

        <!-- Step 2: Select Teacher -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                Select Teacher
            </h2>

            <div>
                <label for="teacher_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Teacher <span class="text-red-500">*</span>
                </label>
                <select name="teacher_id" id="teacher_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    <option value="">Select a teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }} ({{ $teacher->email }})
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Step 3: Select Days & Times -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                Select Days & Times
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                Select the days of the week and set a specific time for each day. Each day can have a different time.
            </p>

            <div class="space-y-3">
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    <div class="flex items-center gap-4 p-3 border-2 border-gray-200 rounded-lg hover:border-vibrant-green transition day-time-row">
                        <label class="flex items-center cursor-pointer flex-1">
                            <input type="checkbox" name="days[]" value="{{ $day }}" 
                                {{ is_array(old('days')) && in_array($day, old('days')) ? 'checked' : '' }}
                                class="w-5 h-5 text-vibrant-green focus:ring-vibrant-green rounded day-checkbox"
                                data-day="{{ $day }}"
                                onchange="toggleTimeInput('{{ $day }}')">
                            <span class="ml-3 text-sm font-medium text-gray-700 w-24">{{ $day }}</span>
                        </label>
                        
                        <div class="flex items-center gap-2 time-input-container" id="time-container-{{ $day }}" style="display: none;">
                            <i class="fa-solid fa-clock text-vibrant-green"></i>
                            <input type="time" 
                                name="schedule_times[{{ $day }}]" 
                                id="time-{{ $day }}"
                                value="{{ old('schedule_times.' . $day, '17:00') }}"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent text-sm">
                        </div>
                    </div>
                @endforeach
            </div>
            
            @error('days')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
            @error('schedule_times')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror

            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    <strong>Tip:</strong> You can set different times for each day. For example, Monday at 4:00 PM, Wednesday at 6:00 PM, Friday at 5:00 PM.
                </p>
            </div>
        </div>

        <!-- Step 4: Set Duration -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">4</span>
                Set Duration
            </h2>

            <div>
                <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                    Session Duration <span class="text-red-500">*</span>
                </label>
                <select name="duration_minutes" id="duration_minutes" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    <option value="30" {{ old('duration_minutes') == 30 ? 'selected' : '' }}>30 minutes</option>
                    <option value="45" {{ old('duration_minutes') == 45 ? 'selected' : '' }}>45 minutes</option>
                    <option value="60" {{ old('duration_minutes', 60) == 60 ? 'selected' : '' }}>1 hour</option>
                    <option value="90" {{ old('duration_minutes') == 90 ? 'selected' : '' }}>1.5 hours</option>
                    <option value="120" {{ old('duration_minutes') == 120 ? 'selected' : '' }}>2 hours</option>
                </select>
                @error('duration_minutes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Step 5: Optional Fields -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-gray-300 text-white rounded-full flex items-center justify-center mr-3 text-sm">5</span>
                Optional Details
            </h2>

            <div class="space-y-4">
                <div>
                    <label for="zoom_link" class="block text-sm font-semibold text-gray-700 mb-2">
                        Zoom Link
                    </label>
                    <input type="url" name="zoom_link" id="zoom_link" value="{{ old('zoom_link') }}"
                        placeholder="https://zoom.us/j/..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    @error('zoom_link')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        placeholder="Add any notes about this recurring schedule..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-calendar-plus mr-2"></i>Create Recurring Schedule
            </button>
            <a href="{{ route('admin.schedules.index') }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fa-solid fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>

    <script>
        // Handle enrollment selection display
        document.getElementById('enrollment_id').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const infoDiv = document.getElementById('enrollmentInfo');
            
            if (this.value) {
                document.getElementById('studentName').textContent = option.dataset.student;
                document.getElementById('courseName').textContent = option.dataset.course;
                document.getElementById('enrollmentDuration').textContent = option.dataset.start + ' to ' + option.dataset.end;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        });

        // Toggle time input visibility based on day checkbox
        function toggleTimeInput(day) {
            const checkbox = document.querySelector(`input[data-day="${day}"]`);
            const timeContainer = document.getElementById(`time-container-${day}`);
            const timeInput = document.getElementById(`time-${day}`);
            
            if (checkbox.checked) {
                timeContainer.style.display = 'flex';
                timeInput.required = true;
            } else {
                timeContainer.style.display = 'none';
                timeInput.required = false;
            }
        }

        // Initialize time inputs on page load (for old input values)
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.day-checkbox');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const day = checkbox.dataset.day;
                    toggleTimeInput(day);
                }
            });
        });
    </script>
</x-dashboard-layout>
