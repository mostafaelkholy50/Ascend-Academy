<x-app-layout>
    <!-- Hero Section -->
    <section class="relative py-16 bg-gradient-to-br from-[#1E90A0] to-[#156d7a] overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img src="{{ asset('assets/images/Hero Area.png') }}" alt="" class="w-full h-full object-cover">
        </div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-4">
                Start Your Quran Journey Today
            </h1>
            <p class="text-lg md:text-xl text-gray-100 max-w-2xl mx-auto">
                Book a free trial session with one of our certified teachers. No commitment required — just a chance to experience quality Quran education.
            </p>
        </div>
    </section>

    <!-- Main Form Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                <!-- Left Side - Benefits -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">What You'll Get</h2>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Free 30-Minute Trial Session</h3>
                                    <p class="text-gray-600 text-sm">Experience our teaching method with no obligation</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Certified Native Teachers</h3>
                                    <p class="text-gray-600 text-sm">Learn from qualified instructors with ijazah</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Personalized Learning Plan</h3>
                                    <p class="text-gray-600 text-sm">Get a custom plan based on your child's level</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Flexible Scheduling</h3>
                                    <p class="text-gray-600 text-sm">Choose times that work for your family</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- How It Works -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">How It Works</h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">1</span>
                                <p class="text-gray-600">Fill out the form with your details</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">2</span>
                                <p class="text-gray-600">Our team contacts you within 24 hours</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">3</span>
                                <p class="text-gray-600">Schedule your free trial session</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">4</span>
                                <p class="text-gray-600">Start learning with your assigned teacher!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Already have account? -->
                    <div class="text-center lg:text-left">
                        <p class="text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-[#1E90A0] font-semibold hover:underline">Login here</a>
                        </p>
                    </div>
                </div>

                <!-- Right Side - Form -->
                <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Request Free Trial</h2>
                    <p class="text-gray-600 text-sm mb-6">Fill in your details and we'll contact you within 24 hours.</p>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 rounded-lg text-green-700">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 rounded-lg text-red-700">
                            @foreach($errors->all() as $error)
                                <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('inquiry.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="type" value="registration">

                        <div class="space-y-6">
                            <!-- Personal Info -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Personal Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                        <input type="text" id="full_name" name="full_name" required value="{{ old('full_name') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                            placeholder="Your full name">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                            placeholder="your@email.com">
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                        <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                                            class="phone-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                            placeholder="+1 234 567 8900">
                                    </div>
                                    <div>
                                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                                        <select id="gender" name="gender" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">How old are you? *</label>
                                        <input type="number" id="age" name="age" required value="{{ old('age') }}" min="3" max="100"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                            placeholder="Ex: 25">
                                    </div>
                                </div>
                            </div>

                            <!-- Location Info -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Location</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                    <select id="country" name="country" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                        <option value="">Select Country</option>
                        <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>United States</option>
                        <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                        <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                        <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia</option>
                        <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Egypt</option>
                        <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia</option>
                        <option value="UAE" {{ old('country') == 'UAE' ? 'selected' : '' }}>UAE</option>
                        <option value="Qatar" {{ old('country') == 'Qatar' ? 'selected' : '' }}>Qatar</option>
                        <option value="Kuwait" {{ old('country') == 'Kuwait' ? 'selected' : '' }}>Kuwait</option>
                        <option value="Other" {{ old('country') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                                    <div>
                                        <label for="city_state" class="block text-sm font-medium text-gray-700 mb-1">City/State *</label>
                                        <input type="text" id="city_state" name="city_state" required value="{{ old('city_state') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                            placeholder="Your City or State">
                                    </div>
                                </div>
                            </div>

                            <!-- Course Preferences -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Course Preferences</h3>
                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label for="courses_needed" class="block text-sm font-medium text-gray-700 mb-1">What courses do you need to join? *</label>
                                        <select id="courses_needed" name="courses_needed" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                                            <option value="">Select a Course</option>
                                            <option value="Quran Memorization" {{ old('courses_needed') == 'Quran Memorization' ? 'selected' : '' }}>Quran Memorization</option>
                                            <option value="Tajweed" {{ old('courses_needed') == 'Tajweed' ? 'selected' : '' }}>Tajweed & Recitation</option>
                                            <option value="Arabic Language" {{ old('courses_needed') == 'Arabic Language' ? 'selected' : '' }}>Arabic Language</option>
                                            <option value="Islamic Studies" {{ old('courses_needed') == 'Islamic Studies' ? 'selected' : '' }}>Islamic Studies</option>
                                            <option value="Ijazah Program" {{ old('courses_needed') == 'Ijazah Program' ? 'selected' : '' }}>Ijazah Program</option>
                                        </select>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label for="sessions_per_week" class="block text-sm font-medium text-gray-700 mb-1">Sessions per week *</label>
                                            <select id="sessions_per_week" name="sessions_per_week" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                                                <option value="">Select Frequency</option>
                                                <option value="1 Session" {{ old('sessions_per_week') == '1 Session' ? 'selected' : '' }}>1 Session</option>
                                                <option value="2 Sessions" {{ old('sessions_per_week') == '2 Sessions' ? 'selected' : '' }}>2 Sessions</option>
                                                <option value="3 Sessions" {{ old('sessions_per_week') == '3 Sessions' ? 'selected' : '' }}>3 Sessions</option>
                                                <option value="4 Sessions" {{ old('sessions_per_week') == '4 Sessions' ? 'selected' : '' }}>4 Sessions</option>
                                                <option value="5 Sessions" {{ old('sessions_per_week') == '5 Sessions' ? 'selected' : '' }}>5 Sessions</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="study_hours" class="block text-sm font-medium text-gray-700 mb-1">Best study hours *</label>
                                            <select id="study_hours" name="study_hours" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                                                <option value="">Select Time Preference</option>
                                                <option value="Morning (8AM - 12PM)" {{ old('study_hours') == 'Morning (8AM - 12PM)' ? 'selected' : '' }}>Morning (8AM - 12PM)</option>
                                                <option value="Afternoon (12PM - 4PM)" {{ old('study_hours') == 'Afternoon (12PM - 4PM)' ? 'selected' : '' }}>Afternoon (12PM - 4PM)</option>
                                                <option value="Evening (4PM - 8PM)" {{ old('study_hours') == 'Evening (4PM - 8PM)' ? 'selected' : '' }}>Evening (4PM - 8PM)</option>
                                                <option value="Night (8PM - Midnight)" {{ old('study_hours') == 'Night (8PM - Midnight)' ? 'selected' : '' }}>Night (8PM - Midnight)</option>
                                                <option value="Flexible" {{ old('study_hours') == 'Flexible' ? 'selected' : '' }}>Flexible</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Available Days *</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                <label class="flex items-center space-x-2 cursor-pointer bg-gray-50 p-2 rounded border border-gray-200 hover:bg-gray-100">
                                                    <input type="checkbox" name="available_days[]" value="{{ $day }}" 
                                                        {{ in_array($day, old('available_days', [])) ? 'checked' : '' }}
                                                        class="rounded text-[#1E90A0] focus:ring-[#1E90A0]">
                                                    <span class="text-sm text-gray-700">{{ $day }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">When do you plan to join? *</label>
                                        <input type="date" id="join_date" name="join_date" required value="{{ old('join_date') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Info -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Additional Details</h3>
                                <div class="space-y-5">
                                    <div>
                                        <label for="referrer" class="block text-sm font-medium text-gray-700 mb-1">Who recommended us to you? *</label>
                                        <input type="text" id="referrer" name="referrer" required value="{{ old('referrer') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                            placeholder="Friend, Social Media, Advertisement, etc.">
                                    </div>
                                    
                                    <div>
                                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Any additional notes?</label>
                                        <textarea id="message" name="message" rows="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition resize-none"
                                            placeholder="Any specific requirements or questions?">{{ old('message') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full bg-[#1E90A0] text-white font-bold py-4 rounded-lg hover:bg-teal-700 transition duration-300 shadow-md text-lg uppercase tracking-wide">
                                Submit Registration
                            </button>
                            <p class="text-xs text-gray-500 text-center mt-3">
                                By submitting, you agree to be contacted by our team regarding your registration.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial -->
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-gray-50 p-8 rounded-2xl">
                <svg class="w-12 h-12 text-yellow-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
                <p class="text-lg text-gray-700 italic mb-4">
                    "My children have learned so much in just a few months. The teachers are patient, knowledgeable, and truly care about their students' progress."
                </p>
                <p class="font-semibold text-gray-800">— Sarah M., Parent from USA</p>
            </div>
        </div>
    </section>
</x-app-layout>
