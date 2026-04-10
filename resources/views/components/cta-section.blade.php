@props([
    'title' => 'Ready to Transform Your Institution?',
    'description' => "Join hundreds of educational institutions that have already upgraded their management system with our comprehensive solution.",
    'primaryBtnText' => 'Get Started Now',
    'primaryBtnUrl' => '/login',
    'secondaryBtnText' => 'Contact Sales',
    'secondaryBtnUrl' => '#contact'
])

<section class="relative bg-gradient-to-r from-red-600 via-red-600 to-red-700 py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-white opacity-10 rounded-full -ml-48 -mt-48 blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-10 rounded-full -mr-48 -mb-48 blur-3xl"></div>

    <div class="max-w-7xl mx-auto text-center relative z-10">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white bg-opacity-20 text-white rounded-full font-semibold text-sm mb-8 animate-fade-scale backdrop-blur-sm">
            <span>🚀</span>
            {{ __('Special Offer') }}
        </div>

        <!-- Title -->
        <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6 animate-fade-up">
            {{ __($title) }}
        </h2>

        <!-- Description -->
        <p class="text-xl text-red-100 max-w-2xl mx-auto mb-12 animate-fade-up leading-relaxed">
            {{ __($description) }}
        </p>

        <!-- Buttons with Enhanced Styling -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center mb-16 animate-fade-up">
            <!-- Primary Button -->
            <a 
                href="{{ $primaryBtnUrl }}" 
                class="inline-flex items-center justify-center px-10 py-4 bg-white text-red-600 font-bold rounded-lg hover:bg-red-50 shadow-2xl hover:shadow-red-glow hover:scale-105 transition-all duration-300 transform active:scale-95 group"
            >
                <span class="flex items-center gap-2">
                    {{ __($primaryBtnText) }}
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
            </a>

            <!-- Secondary Button -->
            <a 
                href="{{ $secondaryBtnUrl }}" 
                class="inline-flex items-center justify-center px-10 py-4 border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:bg-opacity-10 hover:scale-105 shadow-lg hover:shadow-lg transition-all duration-300 transform active:scale-95 group backdrop-blur-sm"
            >
                <span class="flex items-center gap-2">
                    {{ __($secondaryBtnText) }}
                    <i class="bi bi-calendar-check group-hover:animate-bounce"></i>
                </span>
            </a>
        </div>

        <!-- Trust Indicators -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-12 text-white pt-8 border-t border-white border-opacity-20">
            <div class="group cursor-pointer hover:scale-110 transition-transform duration-300">
                <p class="text-4xl font-bold group-hover:text-red-100 transition">24/7</p>
                <p class="text-sm text-red-100 mt-1">{{ __('Support') }}</p>
            </div>
            <div class="group cursor-pointer hover:scale-110 transition-transform duration-300">
                <p class="text-4xl font-bold group-hover:text-red-100 transition">100%</p>
                <p class="text-sm text-red-100 mt-1">{{ __('Secure') }}</p>
            </div>
            <div class="group cursor-pointer hover:scale-110 transition-transform duration-300">
                <p class="text-4xl font-bold group-hover:text-red-100 transition">{{ __('Free') }}</p>
                <p class="text-sm text-red-100 mt-1">{{ __('Setup') }}</p>
            </div>
        </div>
    </div>
</section>

