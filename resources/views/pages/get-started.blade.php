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
                        <input type="hidden" name="type" value="trial">

                        <!-- Parent Info -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Your Information</h3>

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
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                    class="phone-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                    placeholder="+1 234 567 8900">
                            </div>
                        </div>

                        <!-- Child Info -->
                        <div class="space-y-4 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Student Information (Optional)</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="child_name" class="block text-sm font-medium text-gray-700 mb-1">Student Name</label>
                                    <input type="text" id="child_name" name="child_name" value="{{ old('child_name') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition"
                                        placeholder="Child's name">
                                </div>
                                <div>
                                    <label for="child_age" class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                    <select id="child_age" name="child_age"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                                        <option value="">Select age</option>
                                        <option value="3-5 years" {{ old('child_age') == '3-5 years' ? 'selected' : '' }}>3-5 years</option>
                                        <option value="6-9 years" {{ old('child_age') == '6-9 years' ? 'selected' : '' }}>6-9 years</option>
                                        <option value="10-13 years" {{ old('child_age') == '10-13 years' ? 'selected' : '' }}>10-13 years</option>
                                        <option value="14-17 years" {{ old('child_age') == '14-17 years' ? 'selected' : '' }}>14-17 years</option>
                                        <option value="18+ Adult" {{ old('child_age') == '18+ Adult' ? 'selected' : '' }}>18+ Adult</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="preferred_course" class="block text-sm font-medium text-gray-700 mb-1">Preferred Course</label>
                                <select id="preferred_course" name="preferred_course"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition">
                                    <option value="">Select a course</option>
                                    <option value="Quran Memorization" {{ old('preferred_course') == 'Quran Memorization' ? 'selected' : '' }}>Quran Memorization</option>
                                    <option value="Tajweed" {{ old('preferred_course') == 'Tajweed' ? 'selected' : '' }}>Tajweed & Recitation</option>
                                    <option value="Arabic Language" {{ old('preferred_course') == 'Arabic Language' ? 'selected' : '' }}>Arabic Language</option>
                                    <option value="Islamic Studies" {{ old('preferred_course') == 'Islamic Studies' ? 'selected' : '' }}>Islamic Studies</option>
                                    <option value="Not Sure" {{ old('preferred_course') == 'Not Sure' ? 'selected' : '' }}>Not Sure Yet</option>
                                </select>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                                <textarea id="message" name="message" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition resize-none"
                                    placeholder="Any specific requirements or questions?">{{ old('message') }}</textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-[#1E90A0] text-white font-bold py-4 rounded-lg hover:bg-teal-700 transition duration-300 shadow-md text-lg">
                            Request Free Trial
                        </button>

                        <p class="text-xs text-gray-500 text-center">
                            By submitting, you agree to be contacted by our team regarding your request.
                        </p>
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
