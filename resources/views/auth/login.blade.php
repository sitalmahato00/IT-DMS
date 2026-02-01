<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Department Management System (IT-DMS)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
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
        <div class="w-full md:w-1/2 bg-white flex flex-col justify-center items-center p-8 relative">
            <div class="w-full max-w-md relative">
                <!-- Logo/Icon -->
                <div class="flex justify-center mb-8">
                    <div class="w-16 h-16 bg-red-600 rounded-lg flex items-center justify-center">
                        <i class="bi bi-calendar-check text-white" style="font-size: 1.75rem;"></i>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="text-2xl font-bold text-center text-gray-900 mb-1 pt-6">Welcome Back</h1>
                <p class="text-center text-sm text-gray-600 mb-6">Sign in to your account to continue</p>

                <!-- Session Status -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-600 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                required 
                                autofocus
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent transition"
                            />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent transition"
                            />
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-5 h-5" id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
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
                        class="w-full bg-red-600 text-white font-semibold py-3 rounded-lg hover:bg-red-700 transition duration-200"
                    >
                        Sign In
                    </button>

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
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</body>
</html>
