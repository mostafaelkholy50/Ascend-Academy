<x-dashboard-layout title="Edit Schedule">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.schedules.index') }}" class="hover:text-vibrant-green">Schedules</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Edit Schedule</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Schedule</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.schedules.update', $schedule->id) }}" class="max-w-3xl">
        @csrf
        @method('PATCH')

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Schedule Information</h2>

            <div class="space-y-4">
                <!-- Teacher -->
                <div>
                    <label for="teacher_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Teacher <span class="text-red-500">*</span>
                    </label>
                    <select name="teacher_id" id="teacher_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date & Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="starts_at" class="block text-sm font-semibold text-gray-700 mb-2">
                            Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="starts_at" id="starts_at" 
                            value="{{ old('starts_at', $schedule->starts_at->format('Y-m-d\TH:i')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        @error('starts_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                            Duration <span class="text-red-500">*</span>
                        </label>
                        <select name="duration_minutes" id="duration_minutes" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                            <option value="30" {{ old('duration_minutes', $schedule->getDurationInMinutes()) == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('duration_minutes', $schedule->getDurationInMinutes()) == 45 ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('duration_minutes', $schedule->getDurationInMinutes()) == 60 ? 'selected' : '' }}>1 hour</option>
                            <option value="90" {{ old('duration_minutes', $schedule->getDurationInMinutes()) == 90 ? 'selected' : '' }}>1.5 hours</option>
                            <option value="120" {{ old('duration_minutes', $schedule->getDurationInMinutes()) == 120 ? 'selected' : '' }}>2 hours</option>
                        </select>
                        @error('duration_minutes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        <option value="scheduled" {{ old('status', $schedule->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ old('status', $schedule->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $schedule->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Zoom Link -->
                <div>
                    <label for="zoom_link" class="block text-sm font-semibold text-gray-700 mb-2">
                        Zoom Link
                    </label>
                    <input type="url" name="zoom_link" id="zoom_link" value="{{ old('zoom_link', $schedule->zoom_link) }}"
                        placeholder="https://zoom.us/j/..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                    @error('zoom_link')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">{{ old('notes', $schedule->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-save mr-2"></i>Update Schedule
            </button>
            <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fa-solid fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</x-dashboard-layout>
