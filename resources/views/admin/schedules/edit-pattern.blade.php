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
                    Changing the schedule pattern here will <strong>delete ALL sessions</strong> (both past and future) for this enrollment and generate new ones according to your new selections. Please make sure this is intended, as data attached to past sessions may be affected.
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
                Select the days of the week and set one or more times for each day.
            </p>

            <div class="space-y-3">
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    @php
                        $dayData = $pattern[$day] ?? null;
                        if (!$dayData) {
                            continue;
                        }

                        $isActive = old('day_active.' . $day, $dayData['active'] ?? true);
                        $timeValues = old('schedule_times.' . $day, $dayData['slots'] ?? []);
                        $durValues = old('durations.' . $day, []);
                        
                        // Normalizing $timeValues and $durValues for the view
                        $slots = [];
                        if (!empty(old('schedule_times.' . $day))) {
                            // If coming from old input
                            foreach ($timeValues as $idx => $t) {
                                $slots[] = ['time' => $t, 'duration' => $durValues[$idx] ?? $enrollment->session_duration];
                            }
                        } else {
                            foreach ($timeValues as $t) {
                                if (is_array($t)) {
                                    $slots[] = ['time' => $t['time'], 'duration' => $t['duration'] ?? $enrollment->session_duration];
                                } else {
                                    $slots[] = ['time' => $t, 'duration' => $enrollment->session_duration];
                                }
                            }
                        }

                        if (empty($slots)) {
                            continue;
                        }
                    @endphp
                    <div class="flex items-center gap-4 p-3 border-2 border-gray-200 rounded-lg hover:border-vibrant-green transition day-time-row">
                        <label class="flex items-center cursor-pointer flex-1">
                            <input type="hidden" name="day_active[{{ $day }}]" value="0">
                            <input type="checkbox" name="day_active[{{ $day }}]" value="1" 
                                {{ $isActive ? 'checked' : '' }}
                                class="w-5 h-5 text-vibrant-green focus:ring-vibrant-green rounded day-checkbox"
                                data-day="{{ $day }}"
                                onchange="toggleTimeInput('{{ $day }}')">
                            <span class="ml-3 text-sm font-medium text-gray-700 w-24">{{ $day }}</span>
                        </label>
                        
                        <div class="flex flex-col gap-3 time-input-container flex-1" id="time-container-{{ $day }}" style="display: {{ $isActive ? 'flex' : 'none' }};">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-500">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-vibrant-green/10 text-vibrant-green">
                                    <i class="fa-solid fa-clock"></i>
                                </span>
                                <span>Time Slots</span>
                            </div>
                            <div class="time-input-list flex flex-col gap-2" data-day="{{ $day }}">
                                @foreach($slots as $index => $slot)
                                    <div class="time-input-wrap flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <input type="time"
                                                name="schedule_times[{{ $day }}][]"
                                                id="time-{{ $day }}-{{ $index }}"
                                                value="{{ $slot['time'] }}"
                                                {{ $isActive ? 'required' : '' }}
                                                class="time-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-vibrant-green focus:border-transparent text-sm">
                                            <i class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                        </div>
                                        <div class="relative flex-1">
                                            <select name="durations[{{ $day }}][]"
                                                class="time-input w-full pl-3 pr-8 py-2 border border-gray-300 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-vibrant-green focus:border-transparent text-sm appearance-none">
                                                <option value="15" {{ $slot['duration'] == 15 ? 'selected' : '' }}>15m</option>
                                                <option value="30" {{ $slot['duration'] == 30 ? 'selected' : '' }}>30m</option>
                                                <option value="45" {{ $slot['duration'] == 45 ? 'selected' : '' }}>45m</option>
                                                <option value="60" {{ $slot['duration'] == 60 ? 'selected' : '' }}>1h</option>
                                                <option value="90" {{ $slot['duration'] == 90 ? 'selected' : '' }}>1.5h</option>
                                                <option value="120" {{ $slot['duration'] == 120 ? 'selected' : '' }}>2h</option>
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
