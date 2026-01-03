<x-dashboard-layout title="Create Enrollment">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.enrollments.index') }}" class="hover:text-vibrant-green">Enrollments</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Create New</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Enroll Student in Course</h1>
        <p class="text-gray-600 text-sm">Create a new enrollment and set payment details</p>
    </div>

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

    <form method="POST" action="{{ route('admin.enrollments.store') }}" class="max-w-4xl">
        @csrf

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
                            <option value="{{ $student->id }}" {{ old('student_id', $selectedStudent) == $student->id ? 'selected' : '' }}>
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


        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-clock mr-2 text-vibrant-green"></i>
                Flexible Scheduling
            </h2>
            <p class="text-sm text-gray-600 mb-4">
                <i class="fa-solid fa-info-circle"></i> Configure the student's schedule preferences
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Days Per Week -->
                <div>
                    <label for="days_per_week" class="block text-sm font-semibold text-gray-700 mb-2">
                        Days Per Week <span class="text-red-500">*</span>
                    </label>
                    <select name="days_per_week" id="days_per_week" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select days per week</option>
                        @for($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ old('days_per_week') == $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i == 1 ? 'day' : 'days' }} per week
                            </option>
                        @endfor
                    </select>
                    @error('days_per_week')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Session Duration -->
                <div>
                    <label for="session_duration" class="block text-sm font-semibold text-gray-700 mb-2">
                        Session Duration <span class="text-red-500">*</span>
                    </label>
                    <select name="session_duration" id="session_duration" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select duration</option>
                        <option value="30" {{ old('session_duration') == '30' ? 'selected' : '' }}>30 minutes</option>
                        <option value="60" {{ old('session_duration') == '60' ? 'selected' : '' }}>60 minutes (1 hour)</option>
                    </select>
                    @error('session_duration')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-dollar-sign mr-2 text-vibrant-green"></i>
                Monthly Pricing
            </h2>
            <p class="text-sm text-gray-600 mb-4">
                <i class="fa-solid fa-info-circle"></i> Set the monthly price for this enrollment. Payments will be tracked separately each month.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <!-- Admin Price -->
                <div>
                    <label for="admin_price" class="block text-sm font-semibold text-gray-700 mb-2">
                        Monthly Price <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="admin_price" id="admin_price" step="0.01" min="0" 
                        value="{{ old('admin_price') }}" required
                        placeholder="0.00"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    @error('admin_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-semibold text-gray-700 mb-2">
                        Currency <span class="text-red-500">*</span>
                    </label>
                    <select name="currency" id="currency" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="CAD" {{ old('currency', 'CAD') == 'CAD' ? 'selected' : '' }}>CAD (CA$)</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                    </select>
                    @error('currency')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>


        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-check mr-2"></i>Create Enrollment
            </button>
            <a href="{{ route('admin.enrollments.index') }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fa-solid fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>

    <script>
        // Pricing tier suggestions
        const pricingTiers = @json($pricingTiers);
        
        const daysSelect = document.getElementById('days_per_week');
        const durationSelect = document.getElementById('session_duration');
        const priceInput = document.getElementById('admin_price');
        const currencySelect = document.getElementById('currency');

        function updatePriceSuggestion() {
            const days = daysSelect.value;
            const duration = durationSelect.value;
            const currency = currencySelect.value;

            if (days && duration) {
                const tier = pricingTiers.find(t => 
                    t.days_per_week == days && t.session_duration == duration
                );

                if (tier) {
                    // Suggest price based on selected currency
                    const priceField = `price_${currency.toLowerCase()}`;
                    if (tier[priceField]) {
                        priceInput.value = tier[priceField];
                        priceInput.classList.add('border-green-500');
                        setTimeout(() => priceInput.classList.remove('border-green-500'), 2000);
                    }
                }
            }
        }

        daysSelect.addEventListener('change', updatePriceSuggestion);
        durationSelect.addEventListener('change', updatePriceSuggestion);
        currencySelect.addEventListener('change', updatePriceSuggestion);
    </script>
</x-dashboard-layout>
