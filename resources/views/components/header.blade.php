<header class="bg-white shadow-lg sticky top-0 z-50 transition-all duration-300">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            
            <!-- Logo with Gradient -->
            <div class="flex items-center gap-2 group">
                <div class="bg-gradient-to-br from-red-600 to-red-700 text-white font-bold px-4 py-2 rounded-lg text-lg shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-calendar-check"></i>
                        {{ $logoText }}
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ $homeUrl }}" class="text-gray-900 font-medium hover:text-red-600 transition duration-300 relative group">
                    Products
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $servicesUrl }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    User Plans
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $aboutUrl }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    About
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $contactUrl }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    Contact
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
            </div>

            <!-- Right Side Buttons -->
            <div class="flex items-center gap-4">
                @auth
                    <!-- Dashboard Button (for logged in users) -->
                    <a 
                        href="{{ route('admin.dashboard') }}" 
                        class="text-gray-900 font-medium hover:text-red-600 transition hidden sm:block relative group"
                    >
                        Dashboard
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                @else
                    <!-- Login Button -->
                    <a 
                        href="{{ $loginUrl }}" 
                        class="text-gray-900 font-medium hover:text-red-600 transition hidden sm:block relative group"
                    >
                        Login
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                @endauth

                <!-- Get Started Button with Animation (Disabled) -->
                <!-- <a 
                    href="#"
                    class="bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold px-6 py-2 rounded-lg hover:shadow-xl hover:scale-105 transition-all duration-300 transform active:scale-95"
                >
                    Get Started
                </a> -->

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="md:hidden text-gray-900 hover:text-red-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-200 pt-4 animate-slide-down">
            <a href="{{ $homeUrl }}" class="block text-gray-900 font-medium py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                Products
            </a>
            <a href="{{ $servicesUrl }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                User Plans
            </a>
            <a href="{{ $aboutUrl }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                About
            </a>
            <a href="{{ $contactUrl }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                Contact
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="block text-gray-900 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                    Dashboard
                </a>
            @else
                <a href="{{ $loginUrl }}" class="block text-gray-900 py-2 hover:text-red-600 transition">
                    Login
                </a>
            @endauth
        </div>
    </nav>
</header>

<script>
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });
</script>
