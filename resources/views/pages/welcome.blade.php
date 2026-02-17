<x-app-layout>

    <!-- شاشة التحميل -->
    <div id="loading-screen"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-white transition-opacity duration-800">
        <div class="text-center px-6">
            <div class="relative mb-8">
                <img src="{{ asset('assets/images/Gemini_Generated_Image_pez0qlpez0qlpez0.png') }}"
                    alt="Ascend Quran Logo" class="w-32 h-32 mx-auto drop-shadow-sm">
            </div>

            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">
                Ascend <span class="text-[#1E90A0]">Qur’an</span> Academy
            </h1>

            <div class="w-64 h-1.5 bg-gray-100 rounded-full overflow-hidden mx-auto mt-6">
                <div id="progress-bar"
                    class="h-full bg-gradient-to-r from-[#1E90A0] to-teal-400 w-0 transition-all duration-100 ease-linear">
                </div>
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div id="main-content" class="opacity-0 transition-opacity duration-1000">

        <section class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden"
            data-aos="fade-down" data-aos-duration="1000">
            <div class="absolute inset-0 z-0">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ asset('assets/images/header_mobile.png') }}">
                    <img src="{{ asset('assets/images/WhatsApp Image 2025-12-18 at 5.26.06 AM.jpeg') }}"
                        alt="Background" class="w-full h-full object-cover object-center">
                </picture>
                <div class="absolute inset-0 bg-black/45"></div>
            </div>

            <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
                <div class="mb-6 flex flex-col items-center">
                    <h2
                        class="text-4xl md:text-6xl lg:text-7xl font-serif font-bold text-white tracking-tight drop-shadow-2xl">
                        Ascend <span class="text-[#1E90A0]">Qur’an</span> Academy
                    </h2>
                    <div class="w-24 h-1 bg-[#1E90A0] mt-4 rounded-full"></div>
                </div>

                <h1
                    class="text-[18px] sm:text-[24px] md:text-[30px] font-medium text-gray-200 leading-tight mb-8 max-w-3xl mx-auto">
                    Learn Quran & Arabic the Smart Way Anytime, Anywhere!
                </h1>

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
        <section class="py-24 bg-white" data-aos="fade-up" data-aos-duration="900">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="mb-16 text-center">
                    <h3
                        class="inline-block text-xl md:text-2xl font-extrabold text-[#38481B] border-b-4 border-yellow-400 pb-1">
                        Value Pillars Section
                    </h3>
                    <p class="text-gray-600 mt-3 text-lg">What Makes Ascend Quran Academy Different.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-6 sm:gap-8">
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border-t-4 border-[#1E90A0] hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 text-center flex-1 min-w-[280px] max-w-[350px]"
                        data-aos="fade-up" data-aos-delay="100">
                        <img src="{{ asset('assets/images/quran-islam-svgrepo-com.png') }}" alt="Quran Icon"
                            class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6">
                        <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Perfect Recitation</h4>
                        <p class="text-gray-600 text-sm sm:text-base">Master Tajweed and accurate Quran recitation
                            through step-by-step personalized guidance.</p>
                    </div>
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border-t-4 border-[#1E90A0] hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 text-center flex-1 min-w-[280px] max-w-[350px]"
                        data-aos="fade-up" data-aos-delay="250">
                        <img src="{{ asset('assets/images/graduation-cap-svgrepo-com.png') }}" alt="Certified Teachers"
                            class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6">
                        <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Certified & Caring Teachers</h4>
                        <p class="text-gray-600 text-sm sm:text-base">Learn from qualified instructors dedicated to
                            nurturing both skill and spirituality.</p>
                    </div>
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border-t-4 border-[#1E90A0] hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 text-center flex-1 min-w-[280px] max-w-[350px]"
                        data-aos="fade-up" data-aos-delay="400">
                        <img src="{{ asset('assets/images/laptop-svgrepo-com(1).png') }}" alt="Online Learning"
                            class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6">
                        <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Flexible Online Learning</h4>
                        <p class="text-gray-600 text-sm sm:text-base">Study anytime, anywhere — tailored to your child's
                            schedule and pace.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Courses Section -->
        <section class="py-24 bg-gray-50" data-aos="fade-up" data-aos-duration="900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12 text-center">
                    <h3
                        class="inline-block text-3xl md:text-4xl font-extrabold text-gray-900 border-b-4 border-teal-500 pb-1">
                        Our Courses
                    </h3>
                    <p class="text-gray-600 mt-2 text-lg">Structured Learning for Every Level</p>
                </div>
                <div class="flex space-x-6 overflow-x-auto pb-6 scrollbar-hide">
                    @forelse($courses as $course)
                        <div class="flex-shrink-0 w-72 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                            data-aos="fade-right" data-aos-delay="{{ $loop->index * 150 }}">
                            <img src="{{ $course->photo ? asset('storage/' . $course->photo) : asset('assets/images/Saly-1.png') }}"
                                alt="{{ $course->title }}" class="h-48 w-full object-cover">
                            <div class="p-4">
                                <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $course->title }}</h4>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($course->description, 80) }}</p>
                                <div class="flex items-center space-x-1 mb-4">
                                    @for ($i = 0; $i < 5; $i++)
                                        <span class="text-yellow-400">★</span>
                                    @endfor
                                    <span class="text-sm text-gray-500">({{ $course->rating ?? 0 }})</span>
                                </div>
                                <a href="{{ route('courses') }}"
                                    class="flex justify-center items-center bg-[#E6F4F5] text-[#1E90A0] font-semibold py-2 rounded-lg text-center transition duration-300 hover:bg-teal-100">
                                    Learn More
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
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
        <section class="py-24 bg-white" data-aos="fade-up" data-aos-duration="900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h3
                        class="inline-block text-3xl md:text-4xl font-extrabold text-gray-900 border-b-4 border-yellow-400 pb-1">
                        Flexible Monthly Pricing
                    </h3>
                    <p class="text-gray-600 mt-3 text-lg max-w-2xl mx-auto">
                        Choose your preferred schedule and pay monthly. No long-term commitments required.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-teal-50 to-blue-50 rounded-2xl shadow-xl p-8 md:p-12"
                    data-aos="zoom-in" data-aos-delay="200">
                    <!-- باقي الكود بتاع الـ pricing زي ما هو -->
                    <div class="grid md:grid-cols-2 gap-8 mb-8">
                        <div class="flex items-start space-x-4" data-aos="fade-right" data-aos-delay="100">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-check text-white text-xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">Flexible Schedule</h4>
                                <p class="text-gray-600">Choose from 1 to 7 days per week based on your availability
                                    and learning goals.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4" data-aos="fade-left" data-aos-delay="200">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-clock text-white text-xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">Session Duration</h4>
                                <p class="text-gray-600">Select 30-minute or 1-hour sessions to match your child's
                                    attention span and learning pace.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4" data-aos="fade-right" data-aos-delay="300">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-dollar-sign text-white text-xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">Monthly Subscription</h4>
                                <p class="text-gray-600">Pay monthly with no long-term contracts. Cancel or adjust your
                                    schedule anytime.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4" data-aos="fade-left" data-aos-delay="400">
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

                    <!-- باقي الجدول والزرار زي ما هما -->
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
                                    @foreach ($sampleTiers as $tier)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-semibold text-gray-900">
                                                {{ $tier->getScheduleDescription() }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-900">
                                                CA${{ number_format($tier->price_cad, 0) }}</td>
                                            <td class="px-4 py-3 text-center text-gray-900">
                                                ${{ number_format($tier->price_usd, 0) }}</td>
                                            <td class="px-4 py-3 text-center text-gray-900">
                                                £{{ number_format($tier->price_gbp, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-center text-sm text-gray-500 mt-4">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            View all pricing options on our <a href="{{ route('courses') }}"
                                class="text-teal-600 hover:underline font-semibold">Courses page</a>
                        </p>
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ route('get-started') }}"
                            class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-10 py-4 rounded-lg transition duration-300 font-bold text-lg shadow-lg">
                            <i class="fa-solid fa-rocket mr-2"></i>Get Started Today
                        </a>
                        <p class="text-sm text-gray-600 mt-3">Start with a free trial session - no payment required!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Teachers Section -->
        <section class="py-24 bg-amber-50" data-aos="fade-up" data-aos-duration="900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12 text-center">
                    <h3
                        class="inline-block text-3xl md:text-4xl font-extrabold text-[#329E93] border-b-4 border-yellow-400 pb-1">
                        Meet Our Certified Teachers
                    </h3>
                    <p class="text-gray-600 mt-3 text-lg max-w-2xl">Our instructors are graduates of renowned Islamic
                        institutions.</p>
                </div>
                <div class="flex space-x-6 overflow-x-auto pb-6 scrollbar-hide">
                    @forelse($teachers as $teacher)
                        <div class="flex-shrink-0 w-72 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden text-center group cursor-pointer"
                            data-aos="fade-left" data-aos-delay="{{ $loop->index * 200 }}">
                            <div class="relative">
                                <img src="{{ $teacher->avatar ? asset('storage/' . $teacher->avatar) : asset('assets/images/teacher1.jpg') }}"
                                    alt="{{ $teacher->name }}" class="h-64 w-full object-cover">
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                    <a href="{{ route('our-teachers') }}"
                                        class="bg-[#1E90A0] text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-teal-700">View
                                        Profile</a>
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="text-lg font-semibold text-gray-900 mt-2">{{ $teacher->name }}</h4>
                                <p class="text-sm text-gray-500">Quran & Tajweed Specialist</p>
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

        <!-- News Section -->
        <section class="py-24 bg-white" data-aos="fade-up" data-aos-duration="900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12 text-center">
                    <h3
                        class="inline-block text-3xl md:text-4xl font-extrabold text-gray-900 border-b-4 border-teal-500 pb-1">
                        Latest News & Updates
                    </h3>
                    <p class="text-gray-600 mt-3 text-lg max-w-2xl mx-auto">
                        Stay informed with our latest announcements and educational insights
                    </p>
                </div>

                @if ($latestNews && $latestNews->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                        @foreach ($latestNews as $newsItem)
                            <article
                                class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300"
                                data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-teal-400 to-blue-500">
                                    @if ($newsItem->image)
                                        <img src="{{ asset('storage/' . $newsItem->image) }}"
                                            alt="{{ $newsItem->title }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fa-solid fa-newspaper text-6xl text-white opacity-50"></i>
                                        </div>
                                    @endif
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300">
                                    </div>
                                </div>

                                <div class="p-6">
                                    <div class="flex items-center text-xs text-gray-500 mb-3">
                                        <i class="fa-solid fa-calendar mr-2"></i>
                                        {{ $newsItem->published_at->format('F d, Y') }}
                                    </div>
                                    <h4
                                        class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#1E90A0] transition-colors">
                                        {{ $newsItem->title }}
                                    </h4>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                        {!! $newsItem->getExcerpt(120) !!}
                                    </p>
                                    <a href="{{ route('news.show', $newsItem->slug) }}"
                                        class="inline-flex items-center text-[#1E90A0] font-semibold hover:text-teal-700 transition-colors">
                                        Read More
                                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-12 text-center">
                        <a href="{{ route('news') }}"
                            class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-10 py-4 rounded-lg transition duration-300 font-bold text-lg shadow-lg">
                            <i class="fa-solid fa-newspaper mr-2"></i>View All News
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fa-solid fa-newspaper text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No news available at the moment.</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Trial Form Section -->
        <section id="trial-form" class="py-24 bg-white relative overflow-hidden" data-aos="fade-up"
            data-aos-duration="900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div data-aos="fade-right" data-aos-delay="100">
                        <h2
                            class="inline-block text-3xl md:text-4xl font-extrabold text-[#4D6C00] border-b-4 border-yellow-500 pb-1">
                            Start Learning Quran & Arabic the Easy Way.
                        </h2>
                        <p class="text-gray-600 mt-4 text-lg max-w-xl">
                            Enjoy a free one-on-one session with a friendly certified teacher — perfect for both kids
                            and adults.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">Free 30-minute trial session</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">No commitment required</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">Personalized learning assessment</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl lg:shadow-xl border border-gray-100" data-aos="fade-left"
                        data-aos-delay="200">
                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-100 border border-green-400 rounded-lg text-green-700">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ session('success') }}
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-100 border border-red-400 rounded-lg text-red-700">
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('inquiry.store') }}" method="POST" class="space-y-5">
                            @csrf
                            <input type="hidden" name="type" value="registration">

                            <!-- باقي الفورم زي ما هو بدون تغيير -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <input type="text" name="full_name" placeholder="Full Name *" required
                                            value="{{ old('full_name') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                    </div>
                                    <div>
                                        <input type="email" name="email" placeholder="Email Address *" required
                                            value="{{ old('email') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                    </div>
                                    <div>
                                        <input type="tel" name="phone" placeholder="Phone Number *"
                                            value="{{ old('phone') }}" required
                                            class="phone-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                    </div>
                                    <div>
                                        <select name="gender" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm text-gray-500">
                                            <option value="">Select Gender *</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                                Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                                Female</option>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="number" name="age" placeholder="Age *" required
                                            value="{{ old('age') }}" min="3" max="100"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                    </div>
                                    <div>
                                        <select name="country" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm text-gray-500">
                                            <option value="">Select Country *</option>
                                            <option value="United States"
                                                {{ old('country') == 'United States' ? 'selected' : '' }}>United States
                                            </option>
                                            <option value="United Kingdom"
                                                {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United
                                                Kingdom</option>
                                            <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>
                                                Canada</option>
                                            <option value="Australia"
                                                {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia
                                            </option>
                                            <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>
                                                Egypt</option>
                                            <option value="Saudi Arabia"
                                                {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia
                                            </option>
                                            <option value="UAE" {{ old('country') == 'UAE' ? 'selected' : '' }}>UAE
                                            </option>
                                            <option value="Qatar" {{ old('country') == 'Qatar' ? 'selected' : '' }}>
                                                Qatar</option>
                                            <option value="Kuwait" {{ old('country') == 'Kuwait' ? 'selected' : '' }}>
                                                Kuwait</option>
                                            <option value="Other" {{ old('country') == 'Other' ? 'selected' : '' }}>
                                                Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="text" name="city_state" placeholder="City/State *" required
                                            value="{{ old('city_state') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                    </div>
                                    <div>
                                        <input type="text" name="referrer"
                                            placeholder="How did you hear about us? *" required
                                            value="{{ old('referrer') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <select name="courses_needed" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm text-gray-500">
                                            <option value="">Course Needed *</option>
                                            <option value="Quran Memorization"
                                                {{ old('courses_needed') == 'Quran Memorization' ? 'selected' : '' }}>
                                                Quran Memorization</option>
                                            <option value="Tajweed"
                                                {{ old('courses_needed') == 'Tajweed' ? 'selected' : '' }}>Tajweed
                                            </option>
                                            <option value="Arabic Language"
                                                {{ old('courses_needed') == 'Arabic Language' ? 'selected' : '' }}>
                                                Arabic Language</option>
                                            <option value="Islamic Studies"
                                                {{ old('courses_needed') == 'Islamic Studies' ? 'selected' : '' }}>
                                                Islamic Studies</option>
                                        </select>
                                    </div>
                                    <div>
                                        <select name="sessions_per_week" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm text-gray-500">
                                            <option value="">Sessions Per Week *</option>
                                            <option value="1 Session"
                                                {{ old('sessions_per_week') == '1 Session' ? 'selected' : '' }}>1
                                                Session</option>
                                            <option value="2 Sessions"
                                                {{ old('sessions_per_week') == '2 Sessions' ? 'selected' : '' }}>2
                                                Sessions</option>
                                            <option value="3 Sessions"
                                                {{ old('sessions_per_week') == '3 Sessions' ? 'selected' : '' }}>3
                                                Sessions</option>
                                            <option value="4 Sessions"
                                                {{ old('sessions_per_week') == '4 Sessions' ? 'selected' : '' }}>4
                                                Sessions</option>
                                            <option value="5 Sessions"
                                                {{ old('sessions_per_week') == '5 Sessions' ? 'selected' : '' }}>5
                                                Sessions</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <select name="study_hours" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm text-gray-500">
                                        <option value="">Best Study Hours *</option>
                                        <option value="Morning (8AM - 12PM)"
                                            {{ old('study_hours') == 'Morning (8AM - 12PM)' ? 'selected' : '' }}>
                                            Morning (8AM - 12PM)</option>
                                        <option value="Afternoon (12PM - 4PM)"
                                            {{ old('study_hours') == 'Afternoon (12PM - 4PM)' ? 'selected' : '' }}>
                                            Afternoon (12PM - 4PM)</option>
                                        <option value="Evening (4PM - 8PM)"
                                            {{ old('study_hours') == 'Evening (4PM - 8PM)' ? 'selected' : '' }}>Evening
                                            (4PM - 8PM)</option>
                                        <option value="Night (8PM - Midnight)"
                                            {{ old('study_hours') == 'Night (8PM - Midnight)' ? 'selected' : '' }}>
                                            Night (8PM - Midnight)</option>
                                        <option value="Flexible"
                                            {{ old('study_hours') == 'Flexible' ? 'selected' : '' }}>Flexible</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Available Days
                                        *</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                            <label
                                                class="flex items-center space-x-2 cursor-pointer bg-gray-50 p-2 rounded border border-gray-200 hover:bg-gray-100">
                                                <input type="checkbox" name="available_days[]"
                                                    value="{{ $day }}"
                                                    {{ in_array($day, old('available_days', [])) ? 'checked' : '' }}
                                                    class="rounded text-[#1E90A0] focus:ring-[#1E90A0]">
                                                <span class="text-xs text-gray-700">{{ $day }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Joining
                                        *</label>
                                    <input type="date" name="join_date" required value="{{ old('join_date') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E90A0] text-sm text-gray-500">
                                </div>

                                <button type="submit"
                                    class="w-full bg-[#1E90A0] text-white font-semibold py-3 rounded-lg hover:bg-teal-700 transition duration-300 shadow-md">
                                    Submit Registration
                                </button>
                            </div>
                        </form>

                        <p class="text-center mt-4 text-sm text-gray-500">
                            Want more details? <a href="{{ route('get-started') }}"
                                class="text-[#1E90A0] font-semibold hover:underline">Visit our Get Started page</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </div> <!-- نهاية main-content -->

    <!-- Smooth Scroll للـ anchors -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

    <!-- AOS Initialization -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-out'
        });
    </script>

    <!-- Preloader Script -->
    <script>
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loading-screen');
            const mainContent = document.getElementById('main-content'); // اتأكد إن المحتوى واخد ID ده
            const progressBar = document.getElementById('progress-bar');

            let width = 0;
            const interval = setInterval(() => {
                width += Math.random() * 15; // سرعة وهمية للتحميل
                if (width >= 100) {
                    width = 100;
                    clearInterval(interval);

                    // اختفاء تدريجي للشاشة بعد اكتمال الشريط
                    setTimeout(() => {
                        loadingScreen.style.opacity = '0';
                        setTimeout(() => {
                            loadingScreen.style.display = 'none';
                            if (mainContent) mainContent.style.opacity = '1';
                        }, 800);
                    }, 200);
                }
                progressBar.style.width = width + '%';
            }, 100);
        });
    </script>

</x-app-layout>
