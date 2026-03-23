<!-- Scroll Progress Bar (Header) -->
<div class="fixed top-0 left-0 w-full h-1 z-[999] bg-transparent">
    <div id="headerScrollProgressBar" class="h-1 bg-gradient-to-r from-red-600 via-orange-500 to-yellow-400" style="width:100%;transform:scaleX(0);transform-origin:left;will-change:transform;transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);"></div>
</div>
<header class="bg-white dark:bg-gray-900 shadow-lg sticky top-0 z-50 transition-all duration-300">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">

            <!-- Logo with Gradient -->
            <div class="flex items-center gap-3 group">
                <div class="bg-gradient-to-br from-red-600 to-red-700 text-white font-bold px-4 py-2 rounded-lg text-lg shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-calendar-check"></i>
                         {{ __("IT-DMS") }}
                    </span>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="p-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition flex items-center justify-center w-10 h-10" title="Toggle Dark Mode">
                    <svg id="moonIcon" class="w-5 h-5 text-gray-700 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="sunIcon" class="w-5 h-5 text-yellow-400 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v2a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l1.414 1.414a1 1 0 001.414-1.414l-1.414-1.414a1 1 0 00-1.414 1.414zm2.828-2.828l1.414-1.414a1 1 0 00-1.414-1.414l-1.414 1.414a1 1 0 001.414 1.414zm4.242-4.242l1.414 1.414a1 1 0 01-1.414 1.414l-1.414-1.414a1 1 0 011.414-1.414zM3.464 3.464a1 1 0 00-1.414 1.414l1.414 1.414a1 1 0 001.414-1.414L3.464 3.464zm2.828 2.828l-1.414-1.414a1 1 0 00-1.414 1.414l1.414 1.414a1 1 0 001.414-1.414zm0 5.656l-1.414 1.414a1 1 0 01-1.414-1.414l1.414-1.414a1 1 0 011.414 1.414zM10 15a1 1 0 011 1v2a1 1 0 11-2 0v-2a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-8">
                @php $onLanding = request()->is('/') || request()->is('welcome'); @endphp
                <a href="{{ url('/') }}" class="text-gray-900 dark:text-gray-100 font-medium hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("Home") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#features' : url('/') . '#features' }}" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("Features") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#departments' : url('/') . '#departments' }}" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("Departments") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#notices' : url('/') . '#notices' }}" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("Notices") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#gallery' : url('/') . '#gallery' }}" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("Gallery") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#study-materials' : url('/') . '#study-materials' }}" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("Study Materials") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#about' : url('/') . '#about' }}" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("About") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $onLanding ? '#contact' : url('/') . '#contact' }}" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300 relative group">
                    {{ __("Contact") }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                </a>
            </div>

            <!-- Right Side Buttons -->
            <div class="flex items-center gap-4">
                <!-- Language Switcher -->
                <form method="POST" action="{{ route('language.switch') }}" class="mr-2">
                    @csrf
                    <select name="locale" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 w-28">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                        <option value="ne" {{ app()->getLocale() == 'ne' ? 'selected' : '' }}>ने</option>
                    </select>
                </form>
                @auth
                    <!-- Dashboard Button (for logged in users) -->
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="text-gray-900 dark:text-gray-100 font-medium hover:text-red-600 dark:hover:text-red-400 transition hidden sm:block relative group"
                    >
                        {{ __("Dashboard") }}
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                @else
                    <!-- Login Button -->
                    <a
                        href="{{ route('login') }}"
                        class="text-gray-900 dark:text-gray-100 font-medium hover:text-red-600 dark:hover:text-red-400 transition hidden sm:block relative group"
                    >
                        {{ __("Login") }}
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
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-200 dark:border-gray-700 dark:bg-gray-900 pt-4 animate-slide-down">
                @php $onLanding = request()->is('/') || request()->is('welcome'); @endphp
                <a href="{{ url('/') }}" class="block text-gray-900 dark:text-gray-100 font-medium py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("Home") }}
                </a>
                <a href="{{ $onLanding ? '#features' : url('/') . '#features' }}" class="block text-gray-600 dark:text-gray-300 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("Features") }}
                </a>
                <a href="{{ $onLanding ? '#departments' : url('/') . '#departments' }}" class="block text-gray-600 dark:text-gray-300 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("Departments") }}
                </a>
                <a href="{{ $onLanding ? '#notices' : url('/') . '#notices' }}" class="block text-gray-600 dark:text-gray-300 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("Notices") }}
                </a>
                <a href="{{ $onLanding ? '#gallery' : url('/') . '#gallery' }}" class="block text-gray-600 dark:text-gray-300 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("Gallery") }}
                </a>
                <a href="{{ $onLanding ? '#study-materials' : url('/') . '#study-materials' }}" class="block text-gray-600 dark:text-gray-300 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("Study Materials") }}
                </a>
                <a href="{{ $onLanding ? '#about' : url('/') . '#about' }}" class="block text-gray-600 dark:text-gray-300 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("About") }}
                </a>
                <a href="{{ $onLanding ? '#contact' : url('/') . '#contact' }}" class="block text-gray-600 dark:text-gray-300 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                    {{ __("Contact") }}
                </a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="block text-gray-900 dark:text-gray-100 py-2 hover:text-red-600 dark:hover:text-red-400 hover:pl-2 transition duration-300">
                        {{ __("Dashboard") }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block text-gray-900 dark:text-gray-100 py-2 hover:text-red-600 dark:hover:text-red-400 transition">
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
