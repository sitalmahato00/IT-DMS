<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - IT Department Management System (IT-DMS)</title>
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

        <!-- Right Side - Reset Password Form -->
        <div class="w-full md:w-1/2 bg-white flex flex-col justify-center items-center p-8 relative overflow-y-auto pt-16">
            <div class="w-full max-w-md relative">
                <!-- Logo/Icon -->
                <div class="flex justify-center mb-8">
                    <div class="w-16 h-16 bg-red-600 rounded-lg flex items-center justify-center">
                        <i class="bi bi-calendar-check text-white" style="font-size: 1.75rem;"></i>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="text-2xl font-bold text-center text-gray-900 mb-1">Reset Password</h1>
                <p class="text-center text-sm text-gray-600 mb-6">Enter your email address to receive a password reset link</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-600 text-sm">{{ session('status') }}</p>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                        @if ($errors->has('email'))
                            <p class="text-red-600 text-sm mt-2">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <!-- Send Reset Link Button -->
                    <button 
                        type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-3 rounded-lg hover:bg-red-700 transition duration-200"
                    >
                        Send Password Reset Link
                    </button>

                    <!-- Back to Login Link -->
                    <a 
                        href="{{ route('login') }}"
                        class="w-full block text-center bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-300 transition duration-200"
                    >
                        Back to Login
                    </a>

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
            <p class="text-center text-xs text-gray-500 w-full pb-6 mt-8">
                © 2026 IT Department Management System (IT-DMS). All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
