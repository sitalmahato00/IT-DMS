<section class="section-spacing px-4 sm:px-6 lg:px-8 bg-gradient-section">
    <div class="max-w-7xl mx-auto">
        <!-- Section Header with Animation -->
        <div class="text-center mb-20 animate-fade-up">
            <h2 class="heading-md text-gray-900 mb-6">
                {{ $title }}
            </h2>
            <p class="subheading max-w-2xl mx-auto">
                {{ $subtitle }}
            </p>
            <!-- Decorative Line -->
            <div class="h-1 w-16 bg-gradient-to-r from-red-600 to-red-700 mx-auto mt-8 rounded-full"></div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($features as $feature)
                <div class="feature-card group hover:border-red-200 border-2 border-transparent">
                    <!-- Icon Container with Gradient -->
                    <div class="mb-6 relative">
                        <div class="{{ $feature['color'] }} w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-lg group-hover:shadow-xl group-hover:scale-125 transition-all duration-300 transform">
                            {{ $feature['icon'] }}
                        </div>
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-br from-transparent to-red-600 rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </div>

                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors duration-300">
                        {{ $feature['title'] }}
                    </h3>

                    <!-- Description -->
                    <p class="text-gray-600 leading-relaxed mb-4 group-hover:text-gray-700 transition-colors duration-300">
                        {{ $feature['description'] }}
                    </p>

                    <!-- Arrow Icon -->
                    <div class="flex items-center gap-2 text-red-600 opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 transition-all duration-300">
                        <span class="text-sm font-semibold">Learn More</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
