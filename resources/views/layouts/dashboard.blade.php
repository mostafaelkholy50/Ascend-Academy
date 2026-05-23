<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Dashboard' }} - Ascend Quran Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'vibrant-green': '#009FBC',
                        'deep-blue': '#1D3A5F',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        .sidebar-overlay { 
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 60;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { 
            display: block;
            opacity: 1;
        }
        aside.drawer-active { 
            display: flex !important;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: min(320px, 85%);
            z-index: 70;
            transform: translateX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin: 0;
        }
        body.noscroll { 
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
        .hide-scrollbar::-webkit-scrollbar { 
            display: none;
        }
        .hide-scrollbar { 
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Better touch interactions */
        aside a, aside button {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        @media (max-width: 1024px) {
            aside:not(.drawer-active) {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body class="font-poppins bg-gray-50">
    <!-- Mobile Menu Button -->
    <button id="menuBtn" class="lg:hidden fixed top-4 left-4 z-40 w-12 h-12 bg-gradient-to-br from-vibrant-green to-deep-blue rounded-xl shadow-lg flex items-center justify-center hover:shadow-xl transition-all active:scale-95" aria-label="Open menu">
        <i class="fa-solid fa-bars text-white text-lg"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

    <div class="flex min-h-screen">
        <!-- Dynamic Sidebar based on role -->
        @include('components.dashboard.sidebar')

        <!-- Main Content -->
        <main id="mainContent" class="flex-grow p-3 sm:p-4 md:p-6 lg:p-8 overflow-y-auto overflow-x-hidden w-full lg:w-auto lg:ml-0">
            <!-- Header -->
            @include('components.dashboard.header')

            <!-- Page Content -->
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @include('components.dashboard.scripts')
    @stack('scripts')
</body>
</html>
