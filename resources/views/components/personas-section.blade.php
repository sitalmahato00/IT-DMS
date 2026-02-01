<section class="section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

        <!-- Personas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($personas as $persona)
                <div class="persona-card bg-white overflow-hidden border-2 border-gray-100 hover:border-red-200">
                    
                    <!-- Image Header with gradient and animation -->
                    <div class="relative bg-gradient-to-br {{ $persona['color'] }} h-56 flex items-center justify-center text-7xl overflow-hidden group">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        @if(!empty($persona['image']))
                            <img src="{{ $persona['image'] }}" alt="{{ $persona['title'] }}" class="w-full h-full object-cover rounded-none group-hover:scale-110 transition-transform duration-300" />
                        @else
                            <span class="transform group-hover:scale-125 transition-transform duration-300">{{ $persona['icon'] }}</span>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-8">
                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors duration-300">
                            {{ $persona['title'] }}
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-6 leading-relaxed group-hover:text-gray-700 transition-colors">
                            {{ $persona['description'] }}
                        </p>

                        <!-- Benefits List -->
                        <ul class="space-y-3">
                            @foreach($persona['benefits'] as $benefit)
                                <li class="flex items-start gap-3 text-sm text-gray-700 group-hover:text-gray-900 transition-colors duration-300">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full flex-shrink-0 font-bold text-xs group-hover:scale-125 transition-transform">✓</span>
                                    <span class="leading-relaxed">{{ $benefit }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <!-- CTA Arrow -->
                        <div class="mt-6 pt-6 border-t border-gray-100 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <span class="text-red-600 font-semibold text-sm flex items-center gap-2 cursor-pointer">
                                Learn More
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
