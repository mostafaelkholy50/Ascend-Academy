<x-app-layout>
        <section class="relative h-[600px] bg-cover bg-center"
        style="background-image: url('{{ asset('assets/images/pexels-abdghat-2608353.jpg') }}');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="text-white relative z-10">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Meet Our Certified Quran & Arabic Teachers
                    </h1>
                    <p class="text-lg leading-relaxed mb-3">
                        Our teachers are graduates of prestigious Islamic institutions and bring years of experience in
                        teaching Quran, Tajweed, and Arabic to students around the world.
                    </p>
                    <p class="text-base">
                        They combine authentic Islamic knowledge with engaging teaching methods — making learning
                        spiritually enriching and academically effective.
                    </p>
                </div>
            </div>
        </div>
    </section>

 <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-1">
                What Makes Our Teachers <span class="text-yellow-500">Exceptional</span>
            </h2>
            <div class="w-32 h-1 bg-yellow-400"></div>
        </div>

        <div class="flex lg:grid lg:grid-cols-3 gap-6 overflow-x-auto hide-scrollbar touch-smooth snap-x py-3">
            <div class="bg-gray-50 p-6 rounded-lg text-center shadow-lg min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('assets/images/streamline-emojis_graduation-cap.png') }}" alt="Graduation Cap Icon" class="h-16 w-16 mx-auto mb-2">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Certified Experts</h3>
                <p class="text-gray-600 text-sm w-60">
                    All teachers hold recognized ijazah or academic certificates in Quran and Arabic studies.
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg text-center shadow-lg min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('assets/images/icon-park_communication.png') }}" alt="Communication Icon" class="h-16 w-16 mx-auto mb-2">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Fluent Communication</h3>
                <p class="text-gray-600 text-sm w-60">
                    Our instructors speak English fluently and are trained to teach both kids and adults.
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg text-center shadow-lg min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('assets/images/mdi_love.png') }}" alt="Love and Care Icon" class="h-16 w-16 mx-auto mb-2">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Dedicated & Caring</h3>
                <p class="text-gray-600 text-sm w-60">
                    They guide each student with patience, passion, and respect for individual learning needs.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-[#1E7B7B] mb-1">Meet Our Teachers</h2>
            <div class="w-32 h-1 bg-yellow-400"></div>
        </div>

        <div class="flex lg:grid lg:grid-cols-3 gap-6 overflow-x-auto hide-scrollbar touch-smooth snap-x py-3">
            @forelse($teachers as $teacher)
                <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-300 min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center">
                    <img src="{{ $teacher->avatar ? asset('storage/' . $teacher->avatar) : asset('assets/images/teacher1.jpg') }}" 
                        alt="{{ $teacher->name }}" class="h-64 w-full object-cover">
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $teacher->name }}</h3>
                        <p class="text-[#1E90A0] text-sm font-medium mb-2">Teacher</p>
                        <p class="text-gray-600 text-xs mb-3">Experienced Quran & Arabic Teacher</p>
                        <a href="#" class="inline-flex items-center text-[#1E90A0] text-sm font-medium hover:underline">
                            View profile
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No teachers available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative p-10 md:p-16 rounded-3xl overflow-hidden shadow-2xl"
                style="background: linear-gradient(135deg, #A8E6CF 0%, #1E90A0 100%);">

                <img src="{{ asset('assets/images/735c84ae6e0f832530eb8803461e74d383da3ff9.png') }}" alt="Diploma Scroll Icon"
                    class="absolute bottom-0 left-0 opacity-20 transform -translate-x-1/4 translate-y-1/4 w-40 h-40 md:w-64 md:h-64 object-contain"
                    style="filter: grayscale(100%);">

                <img src="{{ asset('assets/images/0470cec92a12164cc4a4a26ee98fd491b27a3fe8.png') }}" alt="Graduation Cap Icon"
                    class="absolute top-0 right-0 opacity-20 transform translate-x-1/4 -translate-y-1/4 w-40 h-40 md:w-64 md:h-64 object-contain">

                <div class="relative z-10 text-white max-w-lg">
                    <h2 class="text-3xl font-extrabold mb-4">
                        Are You a Qualified Quran or Arabic Teacher?
                    </h2>
                    <p class="text-lg mb-8">
                        Join our growing international teaching team and help students around the
                        world connect with the beauty of the Quran and Arabic language
                    </p>
                    <a href="#"
                        class="inline-block py-3 px-8 bg-white text-teal-600 font-semibold rounded-full shadow-lg hover:bg-gray-100 transition duration-300">
                        Apply Now
                    </a>
                </div>
            </div>
        </div>
    </section>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const slider = document.getElementById('testimonials-slider');
        const prevButton = document.getElementById('prev-testimonial');
        const nextButton = document.getElementById('next-testimonial');

        if (slider && prevButton && nextButton) {
            const scrollAmount = 350;

            nextButton.addEventListener('click', () => {
                slider.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });

            prevButton.addEventListener('click', () => {
                slider.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>
</x-app-layout>
