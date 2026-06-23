<x-dashboard-layout title="Edit Schedule Pattern">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.schedules.index') }}" class="hover:text-vibrant-green">Schedules</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Edit Schedule Pattern</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Schedule Pattern</h1>
        <p class="text-gray-600 text-sm">Update the recurring schedule for {{ $enrollment->student->name }} - {{ $enrollment->course->title }}</p>
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

    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 px-6 py-4 rounded-xl mb-6">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-xl mt-0.5"></i>
            <div>
                <p class="font-semibold">Important Information</p>
                <p class="text-sm mt-1">
                    Changing the schedule pattern here will <strong>delete all upcoming sessions</strong> for this enrollment and generate new ones according to your new selections. Past or completed sessions will remain unchanged.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.schedules.update-pattern', $enrollment->id) }}" class="max-w-4xl" id="scheduleForm">
        @csrf
        @method('PUT')

        <!-- Step 1: Select Teacher -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">1</span>
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
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $currentTeacherId) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }} ({{ $teacher->email }})
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Step 2: Select Days & Times -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                Select Days & Times
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                Select the days of the week and set a specific time for each day. Each day can have a different time.
            </p>

            @php
                $pattern = $enrollment->getSchedulePattern() ?? [];
            @endphp

            <div class="space-y-3">
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    @php
                        $isChecked = in_array($day, old('days', array_keys($pattern)));
                        $timeValue = old('schedule_times.' . $day, $pattern[$day] ?? '17:00');
                    @endphp
                    <div class="flex items-center gap-4 p-3 border-2 border-gray-200 rounded-lg hover:border-vibrant-green transition day-time-row">
                        <label class="flex items-center cursor-pointer flex-1">
                            <input type="checkbox" name="days[]" value="{{ $day }}" 
                                {{ $isChecked ? 'checked' : '' }}
                                class="w-5 h-5 text-vibrant-green focus:ring-vibrant-green rounded day-checkbox"
                                data-day="{{ $day }}"
                                onchange="toggleTimeInput('{{ $day }}')">
                            <span class="ml-3 text-sm font-medium text-gray-700 w-24">{{ $day }}</span>
                        </label>
                        
                        <div class="flex items-center gap-2 time-input-container" id="time-container-{{ $day }}" style="display: {{ $isChecked ? 'flex' : 'none' }};">
                            <i class="fa-solid fa-clock text-vibrant-green"></i>
                            <input type="time" 
                                name="schedule_times[{{ $day }}]" 
                                id="time-{{ $day }}"
                                value="{{ $timeValue }}"
                                {{ $isChecked ? 'required' : '' }}
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
        </div>

        <!-- Step 3: Set Duration -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-vibrant-green text-white rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                Set Duration
            </h2>

            <div>
                <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                    Session Duration <span class="text-red-500">*</span>
                </label>
                <select name="duration_minutes" id="duration_minutes" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    <option value="30" {{ old('duration_minutes', $enrollment->session_duration) == 30 ? 'selected' : '' }}>30 minutes</option>
                    <option value="45" {{ old('duration_minutes', $enrollment->session_duration) == 45 ? 'selected' : '' }}>45 minutes</option>
                    <option value="60" {{ old('duration_minutes', $enrollment->session_duration) == 60 ? 'selected' : '' }}>1 hour</option>
                    <option value="90" {{ old('duration_minutes', $enrollment->session_duration) == 90 ? 'selected' : '' }}>1.5 hours</option>
                    <option value="120" {{ old('duration_minutes', $enrollment->session_duration) == 120 ? 'selected' : '' }}>2 hours</option>
                </select>
                @error('duration_minutes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-save mr-2"></i>Update Pattern
            </button>
            <a href="{{ route('admin.schedules.index', ['view' => 'list']) }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fa-solid fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>

    <!-- Tom Select for Searchable Dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Tom Select for Teacher
            const teacherSelect = new TomSelect('#teacher_id', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Search for teacher name or email...',
            });
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
    </script>
</x-dashboard-layout>
