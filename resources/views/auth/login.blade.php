<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Department Management System (IT-DMS)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body.bg-gray-100 {
            background: linear-gradient(120deg, #f8fafc 0%, #f1f5f9 100%);
        }
        .login-bg {
            background: url('https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=800&q=80') center/cover no-repeat;
        }
        .logo-animate {
            animation: bounce 1.2s infinite alternate;
        }
        @keyframes bounce {
            0% { transform: translateY(0); }
            100% { transform: translateY(-8px); }
        }
        .floating-label {
            position: relative;
        }
        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            top: -18px;
            left: 8px;
            font-size: 0.85rem;
            color: #dc2626;
            background: #fff;
            padding: 0 4px;
        }
        .floating-label label {
            position: absolute;
            top: 12px;
            left: 40px;
            color: #6b7280;
            pointer-events: none;
            transition: all 0.2s;
        }
        .motivational-quote {
            font-size: 0.95rem;
            color: #64748b;
            text-align: center;
            margin-top: 1.5rem;
            font-style: italic;
        }
        .dark-mode-toggle-animated {
            transition: transform 0.3s;
        }
        .dark-mode-toggle-animated:active {
            transform: rotate(30deg) scale(1.1);
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #dc2626;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            animation: spin 0.8s linear infinite;
            display: inline-block;
            vertical-align: middle;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Login Page Dark Mode Styles */
        html.dark body.bg-gray-100 { background-color: #0b1220 !important; }
        html.dark .bg-gray-100 { background-color: #0b1220 !important; }
        
        /* Left Side - Red Section in Dark Mode */
        html.dark .hidden.md\:flex.md\:w-1\/2.bg-red-900 {
            background-color: #1a0a0a !important;
            border-color: #374151 !important;
        }
        html.dark .hidden.md\:flex.md\:w-1\/2.bg-red-900 .text-red-100 {
            color: #d1d5db !important;
        }
        
        /* Right Side - Login Form in Dark Mode */
        html.dark .w-full.md\:w-1\/2.bg-white {
            background-color: #0b1220 !important;
        }
        html.dark .w-full.md\:w-1\/2.bg-white .text-gray-900 {
            color: #e5e7eb !important;
        }
        html.dark .w-full.md\:w-1\/2.bg-white .text-gray-600 {
            color: #9ca3af !important;
        }
        html.dark .w-full.md\:w-1\/2 .text-sm.text-gray-600 {
            color: #9ca3af !important;
        }
        
        /* Form Elements */
        html.dark input {
            background-color: #071224 !important;
            color: #e5e7eb !important;
            border-color: #374151 !important;
        }
        html.dark input::placeholder {
            color: #6b7280 !important;
        }
        html.dark input:focus {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        }
        
        /* Labels */
        html.dark label {
            color: #d1d5db !important;
        }
        
        /* Error Messages */
        html.dark .bg-red-50 {
            background-color: rgba(220, 38, 38, 0.1) !important;
        }
        html.dark .bg-red-50 .text-red-600 {
            color: #f87171 !important;
        }
        html.dark .border-red-200 {
            border-color: rgba(220, 38, 38, 0.3) !important;
        }
        
        /* Buttons */
        html.dark .bg-red-600 {
            background-color: #dc2626 !important;
        }
        html.dark .bg-red-600:hover {
            background-color: #b91c1c !important;
        }
        
        /* Create Account Button */
        html.dark a.bg-gray-200 {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
        }
        html.dark a.bg-gray-200:hover {
            background-color: #374151 !important;
        }
        
        /* Links */
        html.dark a.text-red-600 {
            color: #f87171 !important;
        }
        html.dark a.text-red-600:hover {
            color: #fca5a5 !important;
        }
        html.dark a.font-medium.text-gray-900 {
            color: #e5e7eb !important;
        }
        
        /* Checkbox */
        html.dark input[type="checkbox"] {
            background-color: #071224 !important;
            border-color: #374151 !important;
        }
        html.dark input[type="checkbox"]:checked {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }
        html.dark input + span {
            color: #9ca3af !important;
        }
        
        /* Footer */
        html.dark .absolute.bottom-1 .text-gray-500 {
            color: #6b7280 !important;
        }
        
        /* Icon colors */
        html.dark .text-gray-400 {
            color: #6b7280 !important;
        }
        
        /* Back to Home Button */
        html.dark a.bg-red-600.text-white {
            background-color: #dc2626 !important;
        }
        html.dark a.bg-red-600.text-white:hover {
            background-color: #b91c1c !important;
        }
        
        /* Transition */
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Dark Mode Toggle -->
    <button id="darkModeToggle" class="fixed top-4 right-4 z-50 p-3 bg-white rounded-full shadow-lg hover:bg-gray-100 dark-mode-toggle-animated transition" title="Toggle Dark Mode">
        <i class="bi bi-moon-fill text-gray-600 text-xl" id="darkModeIcon"></i>
    </button>

    <div class="flex h-screen">
        <!-- Left Side - Red Section -->
        <div class="hidden md:flex md:w-1/2 bg-red-900 text-white flex-col justify-between p-12">
            <div></div>
            <div class="text-center">
                <!-- Icon -->
                <div class="flex justify-center mb-8">
                    <div class="w-24 h-24 bg-red-800 rounded-full flex items-center justify-center border-4 border-red-700">
                        <i class="bi bi-calendar-check text-white" style="font-size: 3rem;"></i>
                    </div>
                </div>
                <!-- Text -->
                <h2 class="text-4xl font-bold mb-6">
                    Department<br>Management System
                </h2>
                <p class="text-red-100 text-sm leading-relaxed max-w-xs mx-auto">
                    Streamline academic operations with our comprehensive management platform designed for modern educational institutions.
                </p>
            </div>
            <div></div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full md:w-1/2 bg-white login-bg flex flex-col justify-center items-center p-8 relative">
            <div class="w-full max-w-md relative">
                <!-- Logo/Icon -->
                <div class="flex justify-center mb-8">
                    <div class="w-16 h-16 bg-red-600 rounded-lg flex items-center justify-center logo-animate shadow-lg">
                        <i class="bi bi-calendar-check text-white" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <!-- Heading -->
                <h1 class="text-3xl font-extrabold text-center text-gray-900 mb-1 pt-6 tracking-tight">IT Department Login</h1>
                <p class="text-center text-base text-gray-600 mb-6">Sign in to your account to continue</p>
                <div class="motivational-quote">"Empowering minds, one login at a time."</div>

                <!-- Session Status -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-600 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6" id="loginForm">
                    @csrf

                    <!-- Email Address -->
                    <div class="floating-label">
                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder=" "
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent transition"
                            aria-label="Email Address"
                        />
                        <label for="email">Email Address</label>
                    </div>

                    <!-- Password -->
                    <div class="floating-label">
                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            placeholder=" "
                            required
                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent transition"
                            aria-label="Password"
                        />
                        <label for="password">Password</label>
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                            aria-label="Show Password"
                        >
                            <svg class="w-5 h-5" id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label for="remember" class="flex items-center">
                            <input 
                                id="remember" 
                                type="checkbox" 
                                name="remember"
                                class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                            />
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-red-600 hover:text-red-700">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Sign In Button -->
                    <button 
                        type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-3 rounded-lg hover:bg-red-700 transition duration-200 flex items-center justify-center"
                        id="signInBtn"
                    >
                        <span id="signInText">Sign In</span>
                        <span id="signInSpinner" class="spinner ml-2" style="display:none;"></span>
                    </button>

                    <!-- Social Login Buttons (future extensibility) -->
                    <div class="flex gap-2 my-2">
                        <button type="button" class="flex-1 bg-white border border-gray-300 rounded-lg py-2 text-gray-700 font-semibold hover:bg-gray-50 transition flex items-center justify-center" aria-label="Sign in with Google">
                            <i class="bi bi-google me-2 text-red-500"></i> Google
                        </button>
                        <button type="button" class="flex-1 bg-white border border-gray-300 rounded-lg py-2 text-gray-700 font-semibold hover:bg-gray-50 transition flex items-center justify-center" aria-label="Sign in with Microsoft">
                            <i class="bi bi-microsoft me-2 text-blue-500"></i> Microsoft
                        </button>
                    </div>
                    <!-- Create Account Button -->
                    <a 
                        href="{{ route('register') }}"
                        class="w-full block text-center bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-300 transition duration-200"
                    >
                        Create Account
                    </a>

                    <!-- Support Link -->
                    <p class="text-center text-sm text-gray-600">
                        Need help? 
                        <a href="#" class="font-medium text-gray-900 hover:text-red-600">Contact IT Support</a>
                    </p>

                    <!-- Back to Home Button -->
                    <a 
                        href="/" 
                        class="w-full block text-center px-6 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition duration-300"
                    >
                        ← Back to Home
                    </a>
                </form>
            </div>

            <!-- Footer -->
            <p class="absolute bottom-1 text-center text-xs text-gray-500 w-full">
                © 2026 IT Department Management System (IT-DMS). All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-4.753 4.753m4.753-4.753L9.172 9.172m5.656 5.656l.94.94"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268-2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
        // Show spinner on submit
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const signInBtn = document.getElementById('signInBtn');
            const signInText = document.getElementById('signInText');
            const signInSpinner = document.getElementById('signInSpinner');
            if (loginForm) {
                loginForm.addEventListener('submit', function() {
                    signInText.style.display = 'none';
                    signInSpinner.style.display = 'inline-block';
                    signInBtn.disabled = true;
                });
            }
        });

        // Dark Mode Toggle Functionality
        (function() {
            const toggleBtn = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const html = document.documentElement;
            
            const moonIcon = 'bi-moon-fill';
            const sunIcon = 'bi-sun-fill';
            
            function getThemePreference() {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    return savedTheme;
                }
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return 'dark';
                }
                return 'light';
            }
            
            function applyTheme(theme) {
                if (theme === 'dark') {
                    html.classList.add('dark');
                    if (darkModeIcon) {
                        darkModeIcon.classList.remove(moonIcon);
                        darkModeIcon.classList.add(sunIcon);
                    }
                } else {
                    html.classList.remove('dark');
                    if (darkModeIcon) {
                        darkModeIcon.classList.remove(sunIcon);
                        darkModeIcon.classList.add(moonIcon);
                    }
                }
                localStorage.setItem('theme', theme);
            }
            
            function toggleTheme() {
                const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                const theme = getThemePreference();
                applyTheme(theme);
                
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', toggleTheme);
                }
                
                if (window.matchMedia) {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                        if (!localStorage.getItem('theme')) {
                            applyTheme(e.matches ? 'dark' : 'light');
                        }
                    });
                }
            });
        })();
    </script>
</body>
</html>
