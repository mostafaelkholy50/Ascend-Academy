<x-dashboard-layout title="Edit Enrollment">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.enrollments.index') }}" class="hover:text-vibrant-green">Enrollments</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <a href="{{ route('admin.enrollments.show', $enrollment->id) }}" class="hover:text-vibrant-green">Details</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Edit</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Enrollment</h1>
        <p class="text-gray-600 text-sm">Update enrollment and payment information</p>
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

    <form method="POST" action="{{ route('admin.enrollments.update', $enrollment->id) }}" class="max-w-4xl">
        @csrf
        @method('PATCH')

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-user-graduate mr-2 text-vibrant-green"></i>
                Student & Course Selection
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student Selection -->
                <div>
                    <label for="student_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" id="student_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select a student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $enrollment->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Selection -->
                <div>
                    <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Course <span class="text-red-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select a course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $enrollment->course_id) == $course->id ? 'selected' : '' }}>
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

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-calendar mr-2 text-vibrant-green"></i>
                Enrollment Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date (Read-only) -->
                <div>
                    <label for="start_date_display" class="block text-sm font-semibold text-gray-700 mb-2">
                        Start Date
                    </label>
                    <input type="text" id="start_date_display" 
                        value="{{ $enrollment->start_date?->format('F d, Y') ?? $enrollment->created_at->format('F d, Y') }}" 
                        readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fa-solid fa-info-circle"></i> Start date is set automatically when enrollment is created
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Enrollment Status
                    </label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="active" {{ old('status', $enrollment->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $enrollment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $enrollment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-dollar-sign mr-2 text-vibrant-green"></i>
                Monthly Payments
            </h2>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800 font-semibold mb-2">
                    <i class="fa-solid fa-info-circle mr-2"></i>Payment Tracking
                </p>
                <p class="text-blue-700 text-sm mb-3">
                    Payments for this enrollment are tracked monthly. Each month automatically gets a payment record set to "unpaid" by default.
                </p>
                <a href="{{ route('admin.payments.show', $enrollment->id) }}" 
                    class="inline-block bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                    <i class="fa-solid fa-calendar-check mr-2"></i>View & Manage Monthly Payments
                </a>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-save mr-2"></i>Update Enrollment
            </button>
            <a href="{{ route('admin.enrollments.show', $enrollment->id) }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fa-solid fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</x-dashboard-layout>
