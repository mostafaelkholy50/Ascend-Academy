@include('components.head')
@include('components.nav')
<!-- Page Content -->
<main>
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
    {{ $slot }}
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
        // Start filling the progress bar instantly
        (function() {
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                // Animate progress bar quickly to 90% while page loads
                progressBar.style.transition = 'width 0.4s ease-out';
                setTimeout(() => {
                    progressBar.style.width = '90%';
                }, 50);
            }
        })();

        // Fade out as soon as the DOM is ready (much faster than waiting for all images to download)
        document.addEventListener('DOMContentLoaded', function() {
            const loadingScreen = document.getElementById('loading-screen');
            const progressBar = document.getElementById('progress-bar');
            const mainContent = document.getElementById('main-content');

            if (progressBar) {
                progressBar.style.transition = 'width 0.1s ease-out';
                progressBar.style.width = '100%';
            }

            // Instantly start fading out the loading screen
            setTimeout(() => {
                if (loadingScreen) {
                    loadingScreen.style.transition = 'opacity 0.3s ease-out';
                    loadingScreen.style.opacity = '0';
                    
                    // Show main content instantly
                    if (mainContent) {
                        mainContent.style.transition = 'opacity 0.4s ease-out';
                        mainContent.style.opacity = '1';
                    }

                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                    }, 300);
                }
            }, 150);
        });
    </script>

</main>
@include('components.footer')
</body>

</html>
