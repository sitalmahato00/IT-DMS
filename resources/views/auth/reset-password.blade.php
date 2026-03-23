<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - IT Department Management System (IT-DMS)</title>
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
                        <i class="bi bi-lock-fill text-white" style="font-size: 3rem;"></i>
                    </div>
                </div>
                <!-- Text -->
                <h2 class="text-4xl font-bold mb-6">
                    Secure Your<br>Account
                </h2>
                <p class="text-red-100 text-sm leading-relaxed max-w-xs mx-auto">
                    Create a strong password to protect your IT-DMS account and keep your academic records safe.
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
                        <i class="bi bi-lock-fill text-white" style="font-size: 1.75rem;"></i>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="text-2xl font-bold text-center text-gray-900 mb-1">Create New Password</h1>
                <p class="text-center text-sm text-gray-600 mb-6">Enter a strong password to reset your account</p>

                <!-- Form -->
                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address (Read-only) -->
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
                                value="{{ old('email', $request->email) }}"
                                readonly
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                            />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input 
                                id="password" 
                                type="password" 
                                name="password"
                                placeholder="Enter new password"
                                required
                                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent transition"
                            />
                            <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePasswordVisibility('password')" id="togglePassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                            <p class="text-red-600 text-sm mt-2">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                name="password_confirmation"
                                placeholder="Confirm your password"
                                required
                                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent transition"
                            />
                            <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePasswordVisibility('password_confirmation')" id="toggleConfirm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @if ($errors->has('password_confirmation'))
                            <p class="text-red-600 text-sm mt-2">{{ $errors->first('password_confirmation') }}</p>
                        @endif
                    </div>

                    <!-- Password Requirements -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-xs font-semibold text-blue-900 mb-2">Password Requirements:</p>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li>• At least 8 characters long</li>
                            <li>• Mix of uppercase and lowercase letters</li>
                            <li>• Include numbers and special characters</li>
                        </ul>
                    </div>

                    <!-- Reset Button -->
                    <button 
                        type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-3 rounded-lg hover:bg-red-700 transition duration-200"
                    >
                        Reset Password
                    </button>

                    <!-- Back to Login Link -->
                    <a 
                        href="{{ route('login') }}"
                        class="w-full block text-center text-sm text-red-600 hover:text-red-700 font-medium"
                    >
                        ← Back to Login
                    </a>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 w-full pb-6 mt-8">
                © 2026 IT Department Management System (IT-DMS). All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const button = fieldId === 'password' ? document.getElementById('togglePassword') : document.getElementById('toggleConfirm');
            
            if (field.type === 'password') {
                field.type = 'text';
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.604-1.159a3 3 0 112.251 5.724M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';
            } else {
                field.type = 'password';
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
            }
        }
    </script>
</body>
</html>
