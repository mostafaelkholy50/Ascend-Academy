<x-app-layout>
    <section class="relative overflow-hidden">
        <div class="flex flex-col md:flex-row min-h-[600px] lg:min-h-[70vh] w-full">
            <div
                class="order-1 md:w-1/2 md:order-none lg:w-1/2 relative bg-[#1E90A0] flex justify-center items-center p-8">

                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex justify-center items-center">

                    {{-- الصورة الخلفية الشفافة --}}
                    <img src="{{ asset('assets/images/Hero Area.png') }}" alt=""
                        class="absolute inset-0 w-full h-full object-cover opacity-20 hidden md:block">

                    <div class="relative max-w-sm w-full z-10">
                        <div class="absolute inset-0 flex justify-center items-center opacity-10">
                            {{-- اللوجو في الخلفية --}}
                            <img src="{{ asset('assets/images/s 1.png') }}" alt="Ascend Logo Background"
                                class="w-full h-auto max-w-xs transform scale-150">
                        </div>
                        {{-- صورة البنت بتدرس --}}
                        <img src="{{ asset('assets/images/OBJECTS (1).png') }}" alt="A girl studying Quran or Arabic"
                            class="w-full h-auto relative z-20">
                    </div>

                    <div class="w-4 h-4 bg-yellow-400 transform rotate-45 absolute top-8 right-16 z-20"></div>
                </div>
            </div>

            <div
                class="order-2 md:w-1/2 md:order-none lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-yellow-500 text-white relative">

                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">

                    <div class="absolute inset-0 z-0">
                        <div class="w-2 h-2 rounded-full bg-yellow-400 absolute top-4 left-4"></div>
                        <div class="w-3 h-3 rounded-full bg-red-400 absolute top-1/4 left-8"></div>
                        <div class="w-4 h-4 rounded-full bg-purple-400 absolute top-8 right-1/4"></div>
                        <div class="w-4 h-4 bg-yellow-400 transform rotate-45 absolute top-12 left-12"></div>
                        <div class="w-3 h-3 bg-yellow-400 transform rotate-45 absolute bottom-1/4 left-1/2"></div>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6 relative z-10">
                        Master Quran & Arabic — Step by Step, for Every Age & Level.
                    </h1>
                    <p class="text-xl text-gray-800 mb-8 max-w-lg relative z-10">
                        Explore our structured programs designed for kids and adults. Learn at your own pace with
                        certified teachers guiding you every step of the way.
                    </p>
                    <a href="#"
                        class="bg-[#1E90A0] text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-teal-700 transition duration-300 shadow-xl self-start relative z-10">
                        Request Free Trial
                    </a>
                </div>
            </div>


        </div>
    </section>

<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-12 text-left">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800">
                Our Courses – <span class="text-[#1E90A0]">Designed for Real Progress</span>
            </h2>
            <div class="w-24 h-1 bg-yellow-500 mt-2"></div>
        </div>

        <div class="flex lg:grid lg:grid-cols-4 gap-8 overflow-x-auto hide-scrollbar touch-smooth snap-x">
            <div class="bg-yellow-50 p-6 rounded-xl shadow-lg relative overflow-hidden flex flex-col justify-between min-h-[280px] min-w-[280px] flex-shrink-0 snap-start">
                <div class="absolute top-4 right-4 text-yellow-300 opacity-60 z-0">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H19C20.1046 20 21 19.1046 21 18V6C21 4.89543 20.1046 4 19 4ZM6 18V6H19V18H6Z M12 7H17V9H12V7Z M12 11H17V13H12V11Z M7 7H10V17H7V7Z" />
                    </svg>
                </div>

                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Quran Memorization Program</h3>
                    <p class="text-gray-600 text-sm w-60">
                        Build a strong connection with the Quran. Step-by-step memorization with tajweed and meaning
                        for all ages.
                    </p>
                </div>

                <a href="#" class="mt-6 flex items-center justify-center bg-yellow-600 text-white font-semibold py-3 rounded-lg hover:bg-yellow-700 transition duration-300 shadow-md group">
                    Learn More
                    <span class="ml-3 p-1 rounded-md transition-colors duration-300 ">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </span>
                </a>
            </div>

            <div class="bg-yellow-50 p-6 rounded-xl shadow-lg relative overflow-hidden flex flex-col justify-between min-h-[280px] min-w-[280px] flex-shrink-0 snap-start">
                <div class="absolute top-4 right-4 text-yellow-300 opacity-60 z-0">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H19C20.1046 20 21 19.1046 21 18V6C21 4.89543 20.1046 4 19 4ZM6 18V6H19V18H6Z M12 7H17V9H12V7Z M12 11H17V13H12V11Z M7 7H10V17H7V7Z" />
                    </svg>
                </div>

                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Tajweed & Recitation for Beginners</h3>
                    <p class="text-gray-600 text-sm w-60">Learn how to recite beautifully with correct pronunciation and rules. Ideal for beginners and reverts.</p>
                </div>
                <a href="#" class="mt-6 flex items-center justify-center bg-yellow-600 text-white font-semibold py-3 rounded-lg hover:bg-yellow-700 transition duration-300 shadow-md group">Learn More ...</a>
            </div>

            <div class="bg-yellow-50 p-6 rounded-xl shadow-lg relative overflow-hidden flex flex-col justify-between min-h-[280px] min-w-[280px] flex-shrink-0 snap-start">
                <div class="absolute top-4 right-4 text-yellow-300 opacity-60 z-0">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H19C20.1046 20 21 19.1046 21 18V6C21 4.89543 20.1046 4 19 4ZM6 18V6H19V18H6Z M12 7H17V9H12V7Z M12 11H17V13H12V11Z M7 7H10V17H7V7Z" />
                    </svg>
                </div>

                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Arabic for Daily Use</h3>
                    <p class="text-gray-600 text-sm w-60">Speak and understand Arabic in everyday situations with confidence — from greetings to conversations.</p>
                </div>
                <a href="#" class="mt-6 flex items-center justify-center bg-yellow-600 text-white font-semibold py-3 rounded-lg hover:bg-yellow-700 transition duration-300 shadow-md group">Learn More ...</a>
            </div>

            <div class="bg-yellow-50 p-6 rounded-xl shadow-lg relative overflow-hidden flex flex-col justify-between min-h-[280px] min-w-[280px] flex-shrink-0 snap-start">
                <div class="absolute top-4 right-4 text-yellow-300 opacity-60 z-0">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H19C20.1046 20 21 19.1046 21 18V6C21 4.89543 20.1046 4 19 4ZM6 18V6H19V18H6Z M12 7H17V9H12V7Z M12 11H17V13H12V11Z M7 7H10V17H7V7Z" />
                    </svg>
                </div>

                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Quranic Understanding (Tafsir Basics)</h3>
                    <p class="text-gray-600 text-sm w-60">Discover the meanings behind Quranic verses to connect deeper with Allah's words.</p>
                </div>
                <a href="#" class="mt-6 flex items-center justify-center bg-yellow-600 text-white font-semibold py-3 rounded-lg hover:bg-yellow-700 transition duration-300 shadow-md group">Learn More ...</a>
            </div>

        </div>
    </div>
</section>

<section class="py-16 lg:py-24 bg-[#FFF8E6]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-12 text-left">
            <h2 class="text-3xl sm:text-3xl font-extrabold text-gray-800">Structured for Every Learner</h2>
            <div class="w-24 h-1 bg-yellow-500 mt-2"></div>
        </div>

        <div class="flex lg:grid lg:grid-cols-3 gap-8 overflow-x-auto hide-scrollbar touch-smooth snap-x">
            <div class="relative min-h-[300px] flex justify-center items-center min-w-[320px] flex-shrink-0 snap-start">
                <div class="absolute inset-0 bg-[#1E90A0] rounded-xl shadow-lg"></div>
                <div class="absolute inset-0 z-10 p-6 flex flex-col justify-center items-center">
                    {{-- صورة الخلفية للمستوى (Beginner) --}}
                    <img src="{{ asset('assets/images/Vector (1).png') }}" alt="Level shape background" class="absolute inset-0 w-full h-full object-fill opacity-100 rounded-xl" />
                    <div class="relative z-20 text-center pt-20 pb-10 px-4">
                        <h3 class="text-2xl font-bold mb-2 text-white">Beginner</h3>
                        <p class="text-lg text-gray-800">No prior knowledge needed</p>
                    </div>
                </div>
            </div>

            <div class="relative min-h-[300px] flex justify-center items-center min-w-[320px] flex-shrink-0 snap-start">
                <div class="absolute inset-0 bg-[#1E90A0] rounded-xl shadow-lg"></div>
                <div class="absolute inset-0 z-10 p-6 flex flex-col justify-center items-center">
                    {{-- صورة الخلفية للمستوى (Intermediate) --}}
                    <img src="{{ asset('assets/images/Vector (1).png') }}" alt="Level shape background" class="absolute inset-0 w-full h-full object-fill opacity-100 rounded-xl" />
                    <div class="relative z-20 text-center pt-20 pb-10 px-4">
                        <h3 class="text-2xl font-bold mb-2 text-white">Intermediate</h3>
                        <p class="text-lg text-gray-800">Basic reading skills, want to improve recitation</p>
                    </div>
                </div>
            </div>

            <div class="relative min-h-[300px] flex justify-center items-center min-w-[320px] flex-shrink-0 snap-start">
                <div class="absolute inset-0 bg-[#1E90A0] rounded-xl shadow-lg"></div>
                <div class="absolute inset-0 z-10 p-6 flex flex-col justify-center items-center">
                    {{-- صورة الخلفية للمستوى (Advanced) --}}
                    <img src="{{ asset('assets/images/Vector (1).png') }}" alt="Level shape background" class="absolute inset-0 w-full h-full object-fill opacity-100 rounded-xl" />
                    <div class="relative z-20 text-center pt-20 pb-10 px-4">
                        <h3 class="text-2xl font-bold mb-2 text-white">Advanced</h3>
                        <p class="text-lg text-gray-800">Fluent learners aiming for mastery and memorization</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-12 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800">
                Why Our Programs <span class="text-[#1E90A0]">Stand Out</span>
            </h2>
            <div class="w-32 h-1 bg-yellow-500 mt-2 mx-auto"></div>
        </div>

        <div class="flex lg:grid lg:grid-cols-4 gap-8 overflow-x-auto hide-scrollbar touch-smooth snap-x snap-mandatory py-3 px-2 w-full">

            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 flex flex-col items-center text-center
                         min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center">
                <div class="w-24 h-24 bg-yellow-500 rounded-full flex items-center justify-center mb-6 shadow-md group-hover:bg-yellow-600 transition duration-300">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 11V7a2 2 0 00-2-2H9a2 2 0 00-2 2v4c0 1.66 1.34 3 3 3h2c1.66 0 3-1.34 3-3zM12 11h-2V7h2v4zM20 16.5c0 .28-.22.5-.5.5h-15c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h15c.28 0 .5.22.5.5zM22 19c0 .28-.22.5-.5.5h-19c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h19c.28 0 .5.22.5.5z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Certified & Experienced<br>Teachers</h3>
                <p class="text-gray-600 text-sm w-60">
                    Learn With Qualified Instructors Who Combine <br>Traditional Islamic Knowledge With Modern Teaching Methods
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 flex flex-col items-center text-center
                         min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center group transition duration-300 hover:border-[#1E90A0]">
                <div class="w-24 h-24 bg-yellow-500 rounded-full flex items-center justify-center mb-6 shadow-md group-hover:bg-yellow-600 transition duration-300">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1zM5 8v8h14V8H5zM12 15a3 3 0 100-6 3 3 0 000 6zm0-2a1 1 0 110-2 1 1 0 010 2zM15 8h2v2h-2V8zM7 8h2v2H7V8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">One-on-One Online<br>Classes</h3>
                <p class="text-gray-600 text-sm w-60">
                    Get Personalized Attention In Private Sessions Designed To Match Your Pace And Learning Goals.
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 flex flex-col items-center text-center
                         min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center group transition duration-300 hover:border-[#1E90A0]">
                <div class="w-24 h-24 bg-yellow-500 rounded-full flex items-center justify-center mb-6 shadow-md group-hover:bg-yellow-600 transition duration-300">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm.25 15.5c-.41 0-.75-.34-.75-.75V12c0-.41.34-.75.75-.75s.75.34.75.75v4.75c0 .41-.34.75-.75.75zm.75-8.5h-1.5c-.55 0-1-.45-1-1s.45-1 1-1h1.5c.55 0 1 .45 1 1s-.45 1-1 1z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Flexible<br>Scheduling</h3>
                <p class="text-gray-600 text-sm w-60">
                    Study At A Time That Fits Your Routine — Morning, Evening, Or Weekend Sessions Available.
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 flex flex-col items-center text-center
                         min-w-[85vw] sm:min-w-[70vw] md:min-w-[360px] lg:min-w-0 flex-shrink-0 snap-center group transition duration-300 hover:border-[#1E90A0]">
                <div class="w-24 h-24 bg-yellow-500 rounded-full flex items-center justify-center mb-6 shadow-md group-hover:bg-yellow-600 transition duration-300">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 3H7c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM7 5h10v2H7V5zm0 14V9h10v10H7zm5-7c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-2 4h4v2h-4v-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Personalized Feedback<br>& Progress Reports</h3>
                <p class="text-gray-600 text-sm w-60">
                    Track Your Improvement With Detailed Feedback After Every Lesson.
                </p>
            </div>

        </div>
    </div>
</section>

    <section class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-[#1E90A0] rounded-xl p-10 sm:p-16 lg:p-20 text-white overflow-hidden shadow-xl">

                {{-- الصورة الخلفية للمستطيل --}}
                <img src="{{ asset('assets/images/pattern.png') }}" alt="Islamic Geometric Pattern"
                    class="absolute inset-0 w-full h-full object-cover opacity-15 bg-[url('{{ asset('assets/images/Vector.png') }}')] bg-no-repeat bg-right-bottom bg-contain" />

                <div class="relative z-10 max-w-3xl">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight mb-6">
                        START YOUR QURAN & ARABIC <span class="text-yellow-400">LEARNING JOURNEY</span> TODAY
                    </h2>
                    <p class="text-xl sm:text-2xl mb-8">
                        Try a free trial session and experience the difference yourself.
                    </p>
                    <a href="#"
                        class="inline-block bg-white text-[#1E90A0] px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-300 shadow-lg">
                        Request Free Trial
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
