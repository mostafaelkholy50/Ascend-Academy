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

        <!-- Step 1: Select Student & Course -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                Select Student & Course
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="student_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" id="student_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select a student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $selectedStudent) == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Course <span class="text-red-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select a course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $selectedCourse) == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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

        <!-- Enrollment / Pricing Details -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">2.5</span>
                Enrollment & Pricing
            </h2>

            @if($selectedEnrollment)
                <div class="mb-4 p-4 rounded-lg border border-green-200 bg-green-50 text-sm text-green-900">
                    <p class="font-semibold mb-1">Existing enrollment found</p>
                    <p>Student: {{ $selectedEnrollment->student?->name }} | Course: {{ $selectedEnrollment->course?->title }}</p>
                    <p>Current price: {{ $selectedEnrollment->getFormattedPrice() }}</p>
                </div>
            @else
                <div class="mb-4 p-4 rounded-lg border border-blue-200 bg-blue-50 text-sm text-blue-900">
                    No active enrollment was found for the selected student and course. A new enrollment will be created when you submit.
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="admin_price" class="block text-sm font-semibold text-gray-700 mb-2">
                        Price
                    </label>
                    <input type="number" name="admin_price" id="admin_price" step="0.01" min="0"
                        value="{{ old('admin_price', $selectedEnrollment?->admin_price) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    @error('admin_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="currency" class="block text-sm font-semibold text-gray-700 mb-2">
                        Currency
                    </label>
                    <select name="currency" id="currency"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="CAD" {{ old('currency', $selectedEnrollment?->currency ?? 'CAD') == 'CAD' ? 'selected' : '' }}>CAD</option>
                        <option value="USD" {{ old('currency', $selectedEnrollment?->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="GBP" {{ old('currency', $selectedEnrollment?->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                        <option value="EUR" {{ old('currency', $selectedEnrollment?->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                    @error('currency')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 3: Select Days & Times -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                Select Days & Times
            </h2>

            <div class="mb-6">
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    Schedule Start Month <span class="text-red-500">*</span>
                </label>
                <input type="month" name="start_date" id="start_date" required
                    value="{{ is_string(old('start_date')) ? old('start_date') : now()->format('Y-m') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                <p class="text-[10px] text-gray-500 mt-1 italic">The system will automatically generate sessions for this full month starting from the 1st.</p>
                @error('start_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Select the days of the week and set one or more times for each day.
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
                        
                        <div class="flex flex-col gap-3 time-input-container flex-1" id="time-container-{{ $day }}" style="display: none;">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-500">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-vibrant-green/10 text-vibrant-green">
                                    <i class="fa-solid fa-clock"></i>
                                </span>
                                <span>Time Slots</span>
                            </div>
                            <div class="time-input-list flex flex-col gap-2" data-day="{{ $day }}">
                                @php
                                    $oldTimes = old('schedule_times.' . $day, ['17:00']);
                                    if (!is_array($oldTimes)) {
                                        $oldTimes = is_string($oldTimes) ? [$oldTimes] : ['17:00'];
                                    }
                                    $oldDurations = old('durations.' . $day, [60]);
                                    if (!is_array($oldDurations)) {
                                        $oldDurations = is_numeric($oldDurations) ? [$oldDurations] : [60];
                                    }
                                @endphp
                                @foreach($oldTimes as $index => $time)
                                <div class="time-input-wrap flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <input type="time"
                                            name="schedule_times[{{ $day }}][]"
                                            id="time-{{ $day }}-{{ $index }}"
                                            value="{{ $time }}"
                                            class="time-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-vibrant-green focus:border-transparent text-sm">
                                        <i class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                    </div>
                                    <div class="relative flex-1">
                                        <select name="durations[{{ $day }}][]"
                                            class="time-input w-full pl-3 pr-8 py-2 border border-gray-300 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-vibrant-green focus:border-transparent text-sm appearance-none">
                                            <option value="15" {{ ($oldDurations[$index] ?? 60) == 15 ? 'selected' : '' }}>15m</option>
                                            <option value="30" {{ ($oldDurations[$index] ?? 60) == 30 ? 'selected' : '' }}>30m</option>
                                            <option value="45" {{ ($oldDurations[$index] ?? 60) == 45 ? 'selected' : '' }}>45m</option>
                                            <option value="60" {{ ($oldDurations[$index] ?? 60) == 60 ? 'selected' : '' }}>1h</option>
                                            <option value="90" {{ ($oldDurations[$index] ?? 60) == 90 ? 'selected' : '' }}>1.5h</option>
                                            <option value="120" {{ ($oldDurations[$index] ?? 60) == 120 ? 'selected' : '' }}>2h</option>
                                        </select>
                                        <i class="fa-solid fa-hourglass-half absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                                    </div>
                                    @if($index > 0)
                                    <button type="button" class="text-red-500 hover:text-red-700 p-2" onclick="this.parentElement.remove()">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="self-start inline-flex items-center gap-2 text-xs font-bold text-vibrant-green bg-vibrant-green/10 hover:bg-vibrant-green/15 px-3 py-2 rounded-full transition mt-1" onclick="addTimeInput('{{ $day }}')">
                                <i class="fa-solid fa-circle-plus"></i>
                                Add another time
                            </button>
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
                    <strong>Tip:</strong> You can set multiple times for the same day. For example, Monday at 4:00 PM and 6:00 PM.
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

    <!-- Tom Select for Searchable Dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Tom Select for Student
            const studentSelect = new TomSelect('#student_id', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Search for student name or email...',
            });

            // Initialize Tom Select for Course
            const courseSelect = new TomSelect('#course_id', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Search for course title...',
            });

            // Initialize Tom Select for Teacher
            const teacherSelect = new TomSelect('#teacher_id', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Search for teacher name or email...',
            });

            // Set teacher default selection from query param if available
            @if($selectedTeacher)
                teacherSelect.setValue('{{ $selectedTeacher }}');
            @endif

            // Handle checkboxes for days
            const checkboxes = document.querySelectorAll('.day-checkbox');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const day = checkbox.dataset.day;
                    toggleTimeInput(day);
                }
            });
        });

        // Toggle time input visibility based on day checkbox
        function toggleTimeInput(day) {
            const checkbox = document.querySelector(`input[data-day="${day}"]`);
            const timeContainer = document.getElementById(`time-container-${day}`);
            const timeInputs = timeContainer.querySelectorAll('.time-input');
            
            if (checkbox.checked) {
                timeContainer.style.display = 'flex';
                timeInputs.forEach(input => input.required = true);
            } else {
                timeContainer.style.display = 'none';
                timeInputs.forEach(input => input.required = false);
            }
        }

        function addTimeInput(day) {
            const list = document.querySelector(`.time-input-list[data-day="${day}"]`);
            const index = list.querySelectorAll('.time-input-wrap').length;
            const wrapper = document.createElement('div');
            wrapper.className = 'time-input-wrap flex items-center gap-2';

            const timeDiv = document.createElement('div');
            timeDiv.className = 'relative flex-1';

            const input = document.createElement('input');
            input.type = 'time';
            input.name = `schedule_times[${day}][]`;
            input.id = `time-${day}-${index}`;
            input.required = true;
            input.value = '17:00';
            input.className = 'time-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-vibrant-green focus:border-transparent text-sm';

            const icon = document.createElement('i');
            icon.className = 'fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none';

            timeDiv.appendChild(input);
            timeDiv.appendChild(icon);

            const durDiv = document.createElement('div');
            durDiv.className = 'relative flex-1';
            
            const durSelect = document.createElement('select');
            durSelect.name = `durations[${day}][]`;
            durSelect.className = 'time-input w-full pl-3 pr-8 py-2 border border-gray-300 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-vibrant-green focus:border-transparent text-sm appearance-none';
            durSelect.innerHTML = `
                <option value="15">15m</option>
                <option value="30">30m</option>
                <option value="45">45m</option>
                <option value="60" selected>1h</option>
                <option value="90">1.5h</option>
                <option value="120">2h</option>
            `;
            const durIcon = document.createElement('i');
            durIcon.className = 'fa-solid fa-hourglass-half absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs';
            
            durDiv.appendChild(durSelect);
            durDiv.appendChild(durIcon);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-red-500 hover:text-red-700 p-2';
            removeBtn.innerHTML = '<i class="fa-solid fa-times"></i>';
            removeBtn.onclick = function() { wrapper.remove(); };

            wrapper.appendChild(timeDiv);
            wrapper.appendChild(durDiv);
            wrapper.appendChild(removeBtn);
            
            list.appendChild(wrapper);
        }
    </script>
</x-dashboard-layout>
