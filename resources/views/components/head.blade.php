<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title>Ascend Quran Academy | Online Quran Memorization, Tajweed & Arabic Courses</title>
    <meta name="description" content="Ascend Quran Academy: Professional online academy for Quran memorization (Hifz), Tajweed mastery, Arabic learning, and Islamic studies. Certified teachers, flexible classes for all ages." />
    <meta name="keywords" content="Ascend Quran Academy, online Quran academy, Quran memorization, Hifz online, Tajweed courses, learn Arabic online, Islamic studies" />

    <!-- Favicon -->
    <link rel="icon" href="{{asset('assets/images/Gemini_Generated_Image_pez0qlpez0qlpez0.png')}}" type="image/png" />
    <link rel="apple-touch-icon" href="{{asset('assets/images/Gemini_Generated_Image_pez0qlpez0qlpez0.png')}}" />

    <!-- Open Graph Tags -->
    <meta property="og:title" content="Ascend Quran Academy - Learn Quran & Arabic Online" />
    <meta property="og:description" content="Ascend Quran Academy offers expert online courses for Quran memorization (Hifz), Tajweed, Arabic language, and Islamic studies for all ages." />
    <meta property="og:image" content="{{asset('assets/images/Gemini_Generated_Image_pez0qlpez0qlpez0.png')}}" />
    <meta property="og:image:alt" content="Ascend Quran Academy Logo" />
    <meta property="og:url" content="https://ascend-quran-academy.com" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Ascend Quran Academy" />

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Ascend Quran Academy - Learn Quran & Arabic Online" />
    <meta name="twitter:description" content="Expert online Quran memorization, Tajweed, and Arabic courses for kids and adults." />
    <meta name="twitter:image" content="{{asset('assets/images/Gemini_Generated_Image_pez0qlpez0qlpez0.png')}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    // Prevent form submission if CSRF expired
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const tokenInput = document.querySelector('input[name="_token"]');
            
            if (!token || token === '') {
                e.preventDefault();
                alert('Session expired. Page will reload.');
                location.reload();
                return false;
            }
            
            if (tokenInput) {
                tokenInput.value = token;
            }
        });
    });
</script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    
    <!-- Intl Tel Input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <style>
        .iti { width: 100%; }
        .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags.png"); }
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags@2x.png"); }
        }
        /* Fix for Tailwind overlap */
        .iti__flag-container {
             z-index: 20;
        }
        /* Ensure dropdown on body is visible */
        .iti--container {
            z-index: 9999 !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>