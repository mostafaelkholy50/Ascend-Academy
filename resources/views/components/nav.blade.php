<body class="antialiased">
    <header id="main-header" class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/95 backdrop-blur-sm shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 transition-all duration-300" id="header-container">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('assets/images/Gemini_Generated_Image_pez0qlpez0qسسسlpez0.png') }}" 
                             alt="Ascend Quran Academy Logo"
                             class="h-12 w-auto transition-all duration-300 group-hover:scale-105" 
                             id="header-logo">
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    @foreach([
                        ['home', 'Home'],
                        ['our-programs', 'Programs'],
                        ['our-teachers', 'Teachers'],
                        ['courses', 'Courses'],
                        ['contact', 'Contact']
                    ] as [$route, $label])
                        <a href="{{ route($route) }}"
                           class="px-3 py-2 text-sm lg:text-base font-medium transition-colors duration-200 rounded-md
                                  {{ request()->routeIs($route) 
                                     ? 'text-[#1E90A0] bg-teal-50' 
                                     : 'text-gray-600 hover:text-[#1E90A0] hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                    
                    <a href="{{ route('teacher-application.create') }}"
                       class="px-3 py-2 text-sm lg:text-base font-medium text-gray-600 hover:text-[#1E90A0] hover:bg-gray-50 rounded-md transition-colors duration-200">
                        Teach With Us
                    </a>
                </nav>

                <!-- Right Side Actions -->
                <div class="hidden md:flex items-center space-x-3 lg:space-x-4">
                    @auth
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-gray-700 hover:text-[#1E90A0] transition-colors duration-200 focus:outline-none bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-full border border-gray-200">
                                <div class="w-8 h-8 rounded-full bg-[#1E90A0] text-white flex items-center justify-center text-sm font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-sm hidden lg:block max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 py-2 z-50 origin-top-right focus:outline-none">
                                
                                <div class="px-4 py-3 border-b border-gray-100 mb-1">
                                    <p class="text-sm leading-5">Signed in as</p>
                                    <p class="text-sm font-medium leading-5 text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-[#1E90A0] transition-colors">
                                    <i class="fa-solid fa-gauge mr-2"></i> Dashboard
                                </a>
                                
                                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-gray-100 pt-1">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Actions -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" 
                               class="text-gray-600 hover:text-[#1E90A0] font-semibold text-sm lg:text-base transition-colors duration-200">
                                Log in
                            </a>
                            <a href="{{ route('get-started') }}"
                               class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-[#1E90A0] font-pj rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1E90A0] hover:bg-[#157a8a] shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                                <span>Get Started</span>
                                <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button id="menu-toggle" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-[#1E90A0] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#1E90A0] transition-colors">
                        <span class="sr-only">Open main menu</span>
                        <svg id="menu-icon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="close-icon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-nav" class="hidden md:hidden bg-white border-t border-gray-100 max-h-[85vh] overflow-y-auto shadow-xl">
            <div class="px-4 pt-2 pb-6 space-y-1">
                @foreach([
                    ['home', 'Home', 'fa-home'],
                    ['our-programs', 'Our Programs', 'fa-book-open'],
                    ['our-teachers', 'Our Teachers', 'fa-chalkboard-user'],
                    ['courses', 'Courses', 'fa-graduation-cap'],
                    ['teacher-application.create', 'Teach With Us', 'fa-briefcase'],
                    ['contact', 'Contact', 'fa-envelope']
                ] as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                       class="flex items-center px-4 py-3 text-base font-medium rounded-lg 
                              {{ request()->routeIs($route) || ($route == 'teacher-application.create' && request()->routeIs('teacher-application.*'))
                                 ? 'text-[#1E90A0] bg-teal-50 border-l-4 border-[#1E90A0]' 
                                 : 'text-gray-600 hover:text-[#1E90A0] hover:bg-gray-50 hover:pl-6 transition-all duration-200' }}">
                         @if(isset($icon)) <i class="fa-solid {{ $icon }} w-6 text-center mr-3 opacity-70"></i> @endif
                        {{ $label }}
                    </a>
                @endforeach

                <div class="border-t border-gray-100 pt-4 mt-4 px-2">
                    @auth
                        <div class="flex items-center px-4 mb-4">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-[#1E90A0] flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-[#1E90A0] hover:bg-teal-50 rounded-lg">
                            <i class="fa-solid fa-gauge w-6 text-center mr-2"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center mr-2"></i> Log out
                            </button>
                        </form>
                    @else
                        <div class="grid grid-cols-2 gap-4 px-2">
                            <a href="{{ route('login') }}" class="flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Log in
                            </a>
                            <a href="{{ route('get-started') }}" class="flex justify-center items-center px-4 py-3 border border-transparent shadow-md text-base font-bold rounded-lg text-white bg-[#1E90A0] hover:bg-[#157a8a] hover:-translate-y-0.5 transition-all">
                                Get Started
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Spacer for fixed header -->
    <div class="h-20"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Header Scroll Effect
            const header = document.getElementById('main-header');
            const headerContainer = document.getElementById('header-container');
            const logo = document.getElementById('header-logo');

            function updateHeader() {
                if (window.scrollY > 10) {
                    header.classList.add('shadow-md', 'bg-white/95');
                    header.classList.remove('bg-white');
                    if (headerContainer) headerContainer.classList.replace('h-20', 'h-16');
                    if (logo) logo.classList.add('scale-90');
                } else {
                    header.classList.remove('shadow-md', 'bg-white/95');
                    header.classList.add('bg-white');
                    if (headerContainer) headerContainer.classList.replace('h-16', 'h-20');
                    if (logo) logo.classList.remove('scale-90');
                }
            }

            window.addEventListener('scroll', updateHeader);
            // Initial check
            updateHeader();

            // Mobile Menu Toggle
            const menuToggle = document.getElementById('menu-toggle');
            const mobileNav = document.getElementById('mobile-nav');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');

            if (menuToggle && mobileNav) {
                menuToggle.addEventListener('click', () => {
                    const isExpanded = !mobileNav.classList.contains('hidden');
                    
                    if (isExpanded) {
                        mobileNav.classList.add('hidden');
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        document.body.style.overflow = '';
                    } else {
                        mobileNav.classList.remove('hidden');
                        menuIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                        // Prevent background scrolling when menu is open
                        // document.body.style.overflow = 'hidden'; 
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!mobileNav.contains(e.target) && !menuToggle.contains(e.target) && !mobileNav.classList.contains('hidden')) {
                        mobileNav.classList.add('hidden');
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });
            }
        });
    </script>

