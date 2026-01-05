<x-app-layout>
    <main class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-3xl font-extrabold text-gray-800 mb-4 inline-block border-b-2 border-[#1E90A0] pb-0.5">
                Get In Touch With Our Academy
            </h1>
            <p class="text-gray-600 mb-10 max-w-3xl text-lg">
                Have questions about our programs, teachers, or online classes? We're here to help — reach out and our
                support team will respond within 24 hours.
            </p>

            <!-- Quick Contact Options -->
            <div class="space-y-6 mb-12">
                <a href="https://wa.me/1234567890" target="_blank"
                    class="flex items-center space-x-4 p-4 bg-white rounded-lg shadow-sm border border-gray-100 max-w-sm hover:shadow-md transition duration-300">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Chat on WhatsApp</p>
                        <p class="text-lg font-bold text-gray-800">+201275152799</p>
                    </div>
                </a>

                <a href="mailto:ascend.quran@gmail.com"
                    class="flex items-center space-x-4 p-4 bg-white rounded-lg shadow-sm border border-gray-100 max-w-sm hover:shadow-md transition duration-300">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Email Us</p>
                        <p class="text-lg font-bold text-gray-800">ascend.quran@gmail.com</p>
                    </div>
                </a>
            </div>

            <!-- Contact Form -->
            <div class="bg-white p-8 rounded-xl shadow-md border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Send Us a Message</h2>
                <p class="text-gray-600 text-sm mb-6">We'll get back to you within 24 hours.</p>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 rounded-lg text-green-700">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 rounded-lg text-red-700">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('inquiry.store') }}" method="POST" class="space-y-6">
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
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150"
                                        placeholder="Your full name" />
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150"
                                        placeholder="your@email.com" />
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                        class="phone-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150"
                                        placeholder="+1 234 567 8900" required/>
                                </div>
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                                    <select id="gender" name="gender" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age *</label>
                                    <input type="number" id="age" name="age" required value="{{ old('age') }}" min="3" max="100"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150"
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
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150">
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
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150"
                                        placeholder="Your City or State">
                                </div>
                            </div>
                        </div>

                        <!-- Preferences & Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Registration Details</h3>
                            <div class="space-y-5">
                                <div>
                                    <label for="courses_needed" class="block text-sm font-medium text-gray-700 mb-1">Course Needed *</label>
                                    <select id="courses_needed" name="courses_needed" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150">
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
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150">
                                            <option value="">Select Frequency</option>
                                            <option value="1 Session" {{ old('sessions_per_week') == '1 Session' ? 'selected' : '' }}>1 Session</option>
                                            <option value="2 Sessions" {{ old('sessions_per_week') == '2 Sessions' ? 'selected' : '' }}>2 Sessions</option>
                                            <option value="3 Sessions" {{ old('sessions_per_week') == '3 Sessions' ? 'selected' : '' }}>3 Sessions</option>
                                            <option value="4 Sessions" {{ old('sessions_per_week') == '4 Sessions' ? 'selected' : '' }}>4 Sessions</option>
                                            <option value="5 Sessions" {{ old('sessions_per_week') == '5 Sessions' ? 'selected' : '' }}>5 Sessions</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="study_hours" class="block text-sm font-medium text-gray-700 mb-1">Best Study Hours *</label>
                                        <select id="study_hours" name="study_hours" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150">
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
                                    <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Joining Date *</label>
                                    <input type="date" id="join_date" name="join_date" required value="{{ old('join_date') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150">
                                </div>

                                <div>
                                    <label for="referrer" class="block text-sm font-medium text-gray-700 mb-1">Who recommended us to you? *</label>
                                    <input type="text" id="referrer" name="referrer" required value="{{ old('referrer') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150"
                                        placeholder="Friend, Google, Facebook, etc.">
                                </div>

                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Additional Message</label>
                                    <textarea id="message" name="message" rows="5"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-[#1E90A0] focus:border-[#1E90A0] transition duration-150 resize-y"
                                        placeholder="How can we help you?">{{ old('message') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full md:w-auto bg-[#1E90A0] text-white px-8 py-3 rounded-lg font-bold hover:bg-teal-700 transition duration-150 shadow-md">
                            Submit Registration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Links -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('our-programs') }}" class="p-6 bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition duration-300 text-center">
                    <svg class="w-10 h-10 mx-auto mb-3 text-[#1E90A0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="font-semibold text-gray-800">Our Programs</h3>
                    <p class="text-sm text-gray-500 mt-1">Explore our courses</p>
                </a>
                <a href="{{ route('our-teachers') }}" class="p-6 bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition duration-300 text-center">
                    <svg class="w-10 h-10 mx-auto mb-3 text-[#1E90A0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-800">Our Teachers</h3>
                    <p class="text-sm text-gray-500 mt-1">Meet our instructors</p>
                </a>
                <a href="{{ route('get-started') }}" class="p-6 bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition duration-300 text-center">
                    <svg class="w-10 h-10 mx-auto mb-3 text-[#1E90A0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-800">Free Trial</h3>
                    <p class="text-sm text-gray-500 mt-1">Book a session</p>
                </a>
            </div>
        </div>
    </main>
</x-app-layout>
