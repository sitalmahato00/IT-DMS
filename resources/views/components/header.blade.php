<!-- Scroll Progress Bar (Header) -->
<div class="fixed top-0 left-0 w-full h-1 z-[999] bg-transparent">
    <div id="headerScrollProgressBar" class="h-1 bg-gradient-to-r from-red-600 via-orange-500 to-yellow-400" style="width:100%;transform:scaleX(0);transform-origin:left;will-change:transform;transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);"></div>
</div>
<header class="bg-white shadow-lg sticky top-0 z-50 transition-all duration-300">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            
            <!-- Logo with Gradient -->
            <div class="flex items-center gap-2 group">
                <div class="bg-gradient-to-br from-red-600 to-red-700 text-white font-bold px-4 py-2 rounded-lg text-lg shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-calendar-check"></i>
                         IT-DMS
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-8">
                @php $onLanding = request()->is('/') || request()->is('welcome'); @endphp
                <a href="{{ url('/') }}" class="text-gray-900 font-medium hover:text-red-600 transition duration-300 relative group">
                    {{ __('Home') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#features' : url('/') . '#features' }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    {{ __('Features') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#notices' : url('/') . '#notices' }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    {{ __('Notices') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#gallery' : url('/') . '#gallery' }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    {{ __('Gallery') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#about' : url('/') . '#about' }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    {{ __('Personas') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#contact' : url('/') . '#contact' }}" class="text-gray-600 hover:text-red-600 transition duration-300 relative group">
                    {{ __('Contact') }}
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
                        {{ __('Dashboard') }}
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                @else
                    <!-- Login Button -->
                    <a 
                        href="{{ route('login') }}" 
                        class="text-gray-900 font-medium hover:text-red-600 transition hidden sm:block relative group"
                    >
                        {{ __('Login') }}
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                @endauth

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
            @php $onLanding = request()->is('/') || request()->is('welcome'); @endphp
            <a href="{{ url('/') }}" class="block text-gray-900 font-medium py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                {{ __('Home') }}
            </a>
            <a href="{{ $onLanding ? '#features' : url('/') . '#features' }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                {{ __('Features') }}
            </a>
            <a href="{{ $onLanding ? '#notice-section' : url('/') . '#notice-section' }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                {{ __('Notices') }}
            </a>
            <a href="{{ $onLanding ? '#gallery' : url('/') . '#gallery' }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                {{ __('Gallery') }}
            </a>
            <a href="{{ $onLanding ? '#about' : url('/') . '#about' }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                {{ __('Personas') }}
            </a>
            <a href="{{ $onLanding ? '#contact' : url('/') . '#contact' }}" class="block text-gray-600 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                {{ __('Contact') }}
            </a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="block text-gray-900 py-2 hover:text-red-600 hover:pl-2 transition duration-300">
                    {{ __('Dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="block text-gray-900 py-2 hover:text-red-600 transition">
                    {{ __('Login') }}
                </a>
            @endauth
        </div>
    </nav>
</header>


<script>
    // Mobile menu toggle
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });

    // Ultra-smooth Scroll Progress Bar for Header
    (function() {
        const scrollBar = document.getElementById('headerScrollProgressBar');
        let lastScroll = 0;
        let ticking = false;
        function updateBar() {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const progress = docHeight > 0 ? scrollTop / docHeight : 0;
            scrollBar.style.transform = `scaleX(${progress})`;
            ticking = false;
        }
        window.addEventListener('scroll', function() {
            lastScroll = window.scrollY;
            if (!ticking) {
                window.requestAnimationFrame(updateBar);
                ticking = true;
            }
        });
        // Initial state
        scrollBar.style.transform = 'scaleX(0)';
        // Animate in on load
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                scrollBar.style.transform = 'scaleX(0.1)';
                setTimeout(() => {
                    scrollBar.style.transform = 'scaleX(0)';
                }, 400);
            }, 200);
        });
    })();
</script>
