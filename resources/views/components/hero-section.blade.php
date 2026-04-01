@props([
    'description' => 'IT Department Management System (IT-DMS) is a comprehensive academic management solution designed to streamline administrative tasks, improve communication, and enhance the learning experience.',
    'primaryBtnText' => 'Get Started',
    'primaryBtnUrl' => '/login',
    'secondaryBtnText' => 'Learn More',
    'secondaryBtnUrl' => '#about',
    'imageSrc' => '/images/hero-image.jpg',
    'notices' => collect([]),
    'audience' => 'all',
    'counts' => []
])

@php
    $landingBase = rtrim(url('/'), '/');
@endphp

<div class="min-h-screen bg-gradient-hero relative overflow-hidden">
    <!-- Enhanced Decorative Elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-red-100 rounded-full opacity-20 -mr-40 -mt-40 blur-3xl animate-float-slow"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-red-50 rounded-full opacity-20 -ml-40 -mb-40 blur-3xl animate-float-slow delay-150"></div>
    <!-- Additional decorative elements -->
    <div class="absolute top-20 left-10 w-48 h-48 bg-red-50 rounded-full opacity-10 -mt-20 blur-2xl animate-float delay-300"></div>
    <div class="absolute bottom-20 right-10 w-32 h-32 bg-red-100 rounded-full opacity-15 -mb-20 blur-xl animate-float delay-600"></div>
    <!-- Floating red dots for extra decoration -->
    <div class="absolute top-1/4 left-1/4 w-4 h-4 bg-red-600 rounded-full opacity-30 animate-float delay-200"></div>
    <div class="absolute top-3/4 right-1/4 w-3 h-3 bg-red-400 rounded-full opacity-25 animate-float delay-500"></div>
    <div class="absolute bottom-1/4 left-3/4 w-5 h-5 bg-red-500 rounded-full opacity-20 animate-float delay-800"></div>

    <!-- Hero Section -->
    <section class="px-4 py-12 sm:px-6 lg:px-8 lg:py-20 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="grid gap-12 lg:grid-cols-[1fr_auto] items-center">
                <div class="space-y-8 animate-fade-up">
                    <div class="hero-badge">
                        <span class="text-red-600">✨</span>
                        {{ __('Modern Academic Solutions') }}
                    </div>
                    <h1 class="heading-lg leading-tight text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                        {{ __('Welcome to the') }} <span class="text-gradient">{{ __('IT Department Management System') }}</span> ({{ __('IT-DMS') }})
                    </h1>
                    <p class="text-lg text-gray-700 mb-6 max-w-xl">
                        {{ __('A unified digital platform for managing academics, administration, communication, and resources in the IT Department.') }}
                    </p>
                    <ul class="list-disc pl-6 text-gray-600 mb-8 space-y-1">
                        <li>{{ __('Centralized notice portal for all important updates') }}</li>
                        <li>{{ __('Easy access to study materials and resources') }}</li>
                        <li>{{ __('Modern attendance and academic tracking') }}</li>
                        <li>{{ __('Secure, user-friendly, and mobile responsive') }}</li>
                    </ul>
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="{{ $primaryBtnUrl }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 transform active:scale-95">
                            <span class="flex items-center gap-2">
                                {{ __($primaryBtnText) }}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                        </a>
                        <a href="{{ $secondaryBtnUrl }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#4b18a3] to-[#7b3ff1] text-white font-semibold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 transform active:scale-95">
                            {{ __($secondaryBtnText) }}
                        </a>
                    </div>
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-gray-200">
                        <div class="group cursor-pointer">
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-red-600 transition">24/7</p>
                            <p class="text-sm text-gray-600 group-hover:text-gray-900 transition">{{ __('Support') }}</p>
                        </div>
                        <div class="group cursor-pointer">
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-red-600 transition">100%</p>
                            <p class="text-sm text-gray-600 group-hover:text-gray-900 transition">{{ __('Secure') }}</p>
                        </div>
                        <div class="group cursor-pointer">
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-red-600 transition">{{ __('Free') }}</p>
                            <p class="text-sm text-gray-600 group-hover:text-gray-900 transition">{{ __('Setup') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center animate-float">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-700 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-xl -z-10"></div>
                        <img 
                            src="{{ $imageSrc }}" 
                            alt="Academic Management" 
                            class="w-full h-auto rounded-2xl shadow-2xl object-cover group-hover:shadow-red-glow transition-all duration-300"
                        >
                        <div class="absolute bottom-8 right-8 bg-white rounded-xl shadow-xl px-6 py-4 hover:shadow-2xl hover:scale-110 transition-all duration-300 transform">
                            <p class="text-3xl font-bold text-red-600">500+</p>
                            <p class="text-sm text-gray-600 font-medium">{{ __('Institutions Trust Us') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Professional Notice Portal Section -->
<div class="mt-0">
    <x-public-notices 
        :notices="$notices" 
        :audience="$audience" 
        :counts="$counts" 
    />
</div>
