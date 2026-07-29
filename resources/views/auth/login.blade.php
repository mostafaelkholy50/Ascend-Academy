@include('components.head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<style>
    body {
        font-family: 'Inter', sans-serif;
    }

    .gradient-bg {
        background: linear-gradient(180deg, #009FBC 0%, #ffffff 100%);
    }
</style>

<body class="min-h-screen flex items-center justify-center gradient-bg p-4">
    <div class="w-full max-w-md">
        <div
            class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 shadow-xl border border-white border-opacity-20">

            <!-- Logo -->
            <div class="text-center mb-6">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/Gemini_Generated_Image_pez0qlpez0qlpez0.png') }}"
                        alt="Ascend Quran Academy" class="h-16 w-auto mx-auto">
                </a>
                <h2 class="text-2xl font-bold text-white mt-4">Welcome Back!</h2>
                <p class="text-gray-200 text-sm">Sign in to continue your learning journey</p>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div
                    class="mb-4 p-3 bg-green-500 bg-opacity-20 border border-green-400 rounded-lg text-green-100 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-500 bg-opacity-20 border border-red-400 rounded-lg text-red-100 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="mb-5">
                    <label for="email" class="block text-sm text-white font-medium mb-2">Email Address*</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="block w-full pl-10 pr-3 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:border-transparent transition"
                            placeholder="example@gmail.com" />
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-white mb-2">Password*</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="block w-full pl-10 pr-10 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:border-transparent transition"
                            placeholder="••••••••" />
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg id="eye-icon" class="w-5 h-5 text-gray-400 hover:text-white transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6 text-sm">
                    <label class="flex items-center text-white cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 text-cyan-500 bg-white bg-opacity-20 border-white border-opacity-30 rounded focus:ring-cyan-300" />
                        <span class="ml-2 text-white">Remember me</span>
                    </label>
                    <a href="#" class="text-yellow-300 hover:text-yellow-100 transition">Forgot Password?</a>
                </div>

                <!-- Hidden Timezone Input for Automatic Detection -->
                <input type="hidden" name="timezone" id="timezone-input" value="Africa/Cairo">

                <button type="submit"
                    class="w-full py-3 px-4 bg-teal-700 hover:bg-teal-600 text-white font-semibold rounded-xl shadow-lg transition transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 focus:ring-offset-transparent">
                    Sign In
                </button>
            </form>


            <p class="mt-3 text-center">
                <a href="{{ route('home') }}" class="text-sm text-gray-300 hover:text-white transition">
                    ← Back to Home
                </a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>

    <script>
        // Automatic Timezone Detection
        (function() {
            try {
                // Detect user's timezone using browser Intl API
                const detectedTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

                if (detectedTimezone) {
                    document.getElementById('timezone-input').value = detectedTimezone;
                    console.log('Detected timezone:', detectedTimezone);
                } else {
                    console.log('Timezone detection failed, using default: Africa/Cairo');
                }
            } catch (error) {
                console.error('Timezone detection error:', error);
                // Fallback is already set to 'Africa/Cairo' in the hidden input
            }
        })();
    </script>

    <script>
        // Auto-refresh CSRF token before submitting the form to prevent 419 errors on mobile/cached pages
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Disable button to prevent double clicking
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing In...';
            }

            fetch('/refresh-csrf', { credentials: 'same-origin' })
                .then(response => response.json())
                .then(data => {
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.setAttribute('content', data.token);
                    }
                    
                    const csrfInput = form.querySelector('input[name="_token"]');
                    if (csrfInput) {
                        csrfInput.value = data.token;
                    }
                    
                    form.submit();
                })
                .catch(error => {
                    console.log('CSRF refresh failed:', error);
                    form.submit(); // Try to submit anyway if refresh fails
                });
        });
    </script>
</body>

</html>
