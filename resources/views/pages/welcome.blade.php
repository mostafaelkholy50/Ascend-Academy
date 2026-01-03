<x-app-layout>
<section class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden">

    <div class="absolute inset-0 z-0">
        <picture>
            <source media="(max-width: 768px)" srcset="{{ asset('assets/images/header_mobile.png') }}">
            <img src="{{ asset('assets/images/WhatsApp Image 2025-12-18 at 5.26.06 AM.jpeg') }}"
                 alt="Background"
                 class="w-full h-full object-cover object-center">
        </picture>

        <div class="absolute inset-0 bg-black/45"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-[20px] sm:text-[28px] md:text-[36px] lg:text-[42px] font-bold text-white leading-tight mb-4 drop-shadow-md">
            Learn Quran & Arabic the Smart Way Anytime, Anywhere!
        </h1>

        <p class="text-[13px] sm:text-[15px] md:text-[17px] text-gray-100 mb-8 leading-relaxed max-w-2xl mx-auto drop-shadow-sm">
            Join thousands of global learners mastering Quran recitation and Arabic with certified native teachers —
            interactive, flexible, and tailored for all ages.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#trial-form"
                class="bg-[#1E90A0] text-white px-7 py-2.5 rounded-lg text-[13px] md:text-[15px] font-semibold hover:bg-teal-700 transition duration-300 shadow-lg">
                Schedule Free Assessment
            </a>
            <a href="{{ route('our-programs') }}"
                class="border border-white/80 text-white px-7 py-2.5 rounded-lg text-[13px] md:text-[15px] font-semibold hover:bg-white hover:text-black transition duration-300">
                Explore Our Programs
            </a>
        </div>
    </div>
</section>

    <!-- Value Pillars Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="mb-16 text-center">
                <h3 class="inline-block text-xl md:text-2xl font-extrabold text-[#38481B] border-b-4 border-yellow-400 pb-1">
                    Value Pillars Section
                </h3>
                <p class="text-gray-600 mt-3 text-lg">What Makes Ascend Quran Academy Different.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-4 sm:gap-8">
                <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border-t-4 border-[#1E90A0] hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 text-center flex-1 min-w-[280px] max-w-[350px]">
                    <img src="{{ asset('assets/images/quran-islam-svgrepo-com.png') }}" alt="Quran Icon" class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6">
                    <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Perfect Recitation</h4>
                    <p class="text-gray-600 text-sm sm:text-base">Master Tajweed and accurate Quran recitation through step-by-step personalized guidance.</p>
                </div>
                <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border-t-4 border-[#1E90A0] hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 text-center flex-1 min-w-[280px] max-w-[350px]">
                    <img src="{{ asset('assets/images/graduation-cap-svgrepo-com.png') }}" alt="Certified Teachers" class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6">
                    <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Certified & Caring Teachers</h4>
                    <p class="text-gray-600 text-sm sm:text-base">Learn from qualified instructors dedicated to nurturing both skill and spirituality.</p>
                </div>
                <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border-t-4 border-[#1E90A0] hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 text-center flex-1 min-w-[280px] max-w-[350px]">
                    <img src="{{ asset('assets/images/laptop-svgrepo-com(1).png') }}" alt="Online Learning" class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6">
                    <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Flexible Online Learning</h4>
                    <p class="text-gray-600 text-sm sm:text-base">Study anytime, anywhere — tailored to your child's schedule and pace.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Courses Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h3 class="inline-block text-3xl md:text-4xl font-extrabold text-gray-900 border-b-4 border-teal-500 pb-1">Our Courses</h3>
                <p class="text-gray-600 mt-2 text-lg">Structured Learning for Every Level</p>
            </div>
            <!-- Dynamic Courses Section -->
            <div class="flex space-x-6 overflow-x-auto pb-6 scrollbar-hide">
                @forelse($courses as $course)
                    <div class="flex-shrink-0 w-72 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <img src="{{ $course->photo ? asset('storage/' . $course->photo) : asset('assets/images/Saly-1.png') }}" alt="{{ $course->title }}" class="h-48 w-full object-cover">
                        <div class="p-4">
                            <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $course->title }}</h4>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($course->description, 80) }}</p>
                            <div class="flex items-center space-x-1 mb-4">
                                @for($i = 0; $i < 5; $i++)
                                    <span class="text-yellow-400">★</span>
                                @endfor
                                <span class="text-sm text-gray-500">({{ $course->rating ?? 0 }})</span>
                            </div>
                            <a href="{{ route('courses') }}" class="flex justify-center items-center bg-[#E6F4F5] text-[#1E90A0] font-semibold py-2 rounded-lg text-center transition duration-300 hover:bg-teal-100">
                                Learn More
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">No courses available.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Monthly Pricing Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h3 class="inline-block text-3xl md:text-4xl font-extrabold text-gray-900 border-b-4 border-yellow-400 pb-1">
                    Flexible Monthly Pricing
                </h3>
                <p class="text-gray-600 mt-3 text-lg max-w-2xl mx-auto">
                    Choose your preferred schedule and pay monthly. No long-term commitments required.
                </p>
            </div>

            <div class="bg-gradient-to-br from-teal-50 to-blue-50 rounded-2xl shadow-xl p-8 md:p-12">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-calendar-check text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Flexible Schedule</h4>
                            <p class="text-gray-600">Choose from 1 to 7 days per week based on your availability and learning goals.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-clock text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Session Duration</h4>
                            <p class="text-gray-600">Select 30-minute or 1-hour sessions to match your child's attention span and learning pace.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-dollar-sign text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Monthly Subscription</h4>
                            <p class="text-gray-600">Pay monthly with no long-term contracts. Cancel or adjust your schedule anytime.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-globe text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Multi-Currency Support</h4>
                            <p class="text-gray-600">Pay in CAD, USD, or GBP - whatever works best for you.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md">
                    <h4 class="text-xl font-bold text-gray-900 mb-4 text-center">Sample Monthly Pricing</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="px-4 py-3 text-left font-bold text-gray-700">Schedule</th>
                                    <th class="px-4 py-3 text-center font-bold text-gray-700">CAD</th>
                                    <th class="px-4 py-3 text-center font-bold text-gray-700">USD</th>
                                    <th class="px-4 py-3 text-center font-bold text-gray-700">GBP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @php
                                    $sampleTiers = \App\Models\PricingTier::active()->take(4)->get();
                                @endphp
                                @foreach($sampleTiers as $tier)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $tier->getScheduleDescription() }}</td>
                                        <td class="px-4 py-3 text-center text-gray-900">CA${{ number_format($tier->price_cad, 0) }}</td>
                                        <td class="px-4 py-3 text-center text-gray-900">${{ number_format($tier->price_usd, 0) }}</td>
                                        <td class="px-4 py-3 text-center text-gray-900">£{{ number_format($tier->price_gbp, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-center text-sm text-gray-500 mt-4">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        View all pricing options on our <a href="{{ route('courses') }}" class="text-teal-600 hover:underline font-semibold">Courses page</a>
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('get-started') }}" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-10 py-4 rounded-lg transition duration-300 font-bold text-lg shadow-lg">
                        <i class="fa-solid fa-rocket mr-2"></i>Get Started Today
                    </a>
                    <p class="text-sm text-gray-600 mt-3">Start with a free trial session - no payment required!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Teachers Section -->
    <section class="py-24 bg-amber-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h3 class="inline-block text-3xl md:text-4xl font-extrabold text-[#329E93] border-b-4 border-yellow-400 pb-1">Meet Our Certified Teachers</h3>
                <p class="text-gray-600 mt-3 text-lg max-w-2xl">Our instructors are graduates of renowned Islamic institutions.</p>
            </div>
            <!-- Dynamic Teachers Section -->
            <div class="flex space-x-6 overflow-x-auto pb-6 scrollbar-hide">
                @forelse($teachers as $teacher)
                    <div class="flex-shrink-0 w-72 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden text-center group cursor-pointer">
                        <div class="relative">
                            <img src="{{ $teacher->avatar ? asset('storage/' . $teacher->avatar) : asset('assets/images/teacher1.jpg') }}" alt="{{ $teacher->name }}" class="h-64 w-full object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                <a href="{{ route('our-teachers') }}" class="bg-[#1E90A0] text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-teal-700">View Profile</a>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mt-2">{{ $teacher->name }}</h4>
                            <p class="text-sm text-gray-500">Quran &amp; Tajweed Specialist</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">No teachers available.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Trial Form Section -->
    <section id="trial-form" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="inline-block text-3xl md:text-4xl font-extrabold text-[#4D6C00] border-b-4 border-yellow-500 pb-1">
                        Start Learning Quran & Arabic the Easy Way.
                    </h2>
                    <p class="text-gray-600 mt-4 text-lg max-w-xl">
                        Enjoy a free one-on-one session with a friendly certified teacher — perfect for both kids and adults.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Free 30-minute trial session</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">No commitment required</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Personalized learning assessment</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-xl lg:shadow-xl border border-gray-100">
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
                                <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('inquiry.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="type" value="trial">

                        <div>
                            <input type="text" name="full_name" placeholder="Your Full Name *" required value="{{ old('full_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] placeholder-gray-500 text-sm">
                        </div>
                        <div>
                            <input type="email" name="email" placeholder="Your Email Address *" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] placeholder-gray-500 text-sm">
                        </div>
                        <div>
                            <input type="tel" name="phone" placeholder="Phone Number" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] placeholder-gray-500 text-sm" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <select name="child_age" class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                <option value="" disabled {{ old('child_age') ? '' : 'selected' }}>Student Age</option>
                                <option value="3-5 years" {{ old('child_age') == '3-5 years' ? 'selected' : '' }}>3-5 years</option>
                                <option value="6-9 years" {{ old('child_age') == '6-9 years' ? 'selected' : '' }}>6-9 years</option>
                                <option value="10-13 years" {{ old('child_age') == '10-13 years' ? 'selected' : '' }}>10-13 years</option>
                                <option value="14+ years" {{ old('child_age') == '14+ years' ? 'selected' : '' }}>14+ years</option>
                                <option value="Adult" {{ old('child_age') == 'Adult' ? 'selected' : '' }}>Adult</option>
                            </select>
                            <select name="preferred_course" class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                <option value="" disabled {{ old('preferred_course') ? '' : 'selected' }}>Preferred Course</option>
                                <option value="Quran Memorization" {{ old('preferred_course') == 'Quran Memorization' ? 'selected' : '' }}>Quran Memorization</option>
                                <option value="Tajweed" {{ old('preferred_course') == 'Tajweed' ? 'selected' : '' }}>Tajweed</option>
                                <option value="Arabic Language" {{ old('preferred_course') == 'Arabic Language' ? 'selected' : '' }}>Arabic Language</option>
                                <option value="Islamic Studies" {{ old('preferred_course') == 'Islamic Studies' ? 'selected' : '' }}>Islamic Studies</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-[#1E90A0] text-white font-semibold py-3 rounded-lg hover:bg-teal-700 transition duration-300 shadow-md">
                            Request Free Trial
                        </button>
                    </form>

                    <p class="text-center mt-4 text-sm text-gray-500">
                        Want more details? <a href="{{ route('get-started') }}" class="text-[#1E90A0] font-semibold hover:underline">Visit our Get Started page</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });
    </script>
</x-app-layout>
