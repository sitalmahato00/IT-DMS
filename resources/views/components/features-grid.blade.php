@props([
    'title' => 'Why Choose Our Platform?',
    'subtitle' => 'Discover the powerful features that make our platform the ideal choice for educational institutions.',
    'features' => [
        [
            'icon' => '📢',
            'color' => 'bg-red-100 text-red-600',
            'title' => 'Centralized Notice Management',
            'description' => 'Publish and manage notices efficiently with our intuitive notice board system. Target specific audiences and track engagement.'
        ],
        [
            'icon' => '📚',
            'color' => 'bg-blue-100 text-blue-600',
            'title' => 'Smart Study Materials',
            'description' => 'Organize and share study materials, assignments, and resources with students. Support multiple file formats and categories.'
        ],
        [
            'icon' => '✅',
            'color' => 'bg-green-100 text-green-600',
            'title' => 'Attendance Tracking',
            'description' => 'Track student attendance with ease using QR codes or manual entry. Generate detailed reports and analytics.'
        ],
        [
            'icon' => '📊',
            'color' => 'bg-purple-100 text-purple-600',
            'title' => 'Academic Performance',
            'description' => 'Monitor student performance with comprehensive assessment tools. Track grades, generate reports, and identify improvement areas.'
        ],
        [
            'icon' => '🖼️',
            'color' => 'bg-yellow-100 text-yellow-600',
            'title' => 'Photo Gallery',
            'description' => 'Capture and share memorable moments from campus events, activities, and everyday life through our photo gallery.'
        ],
        [
            'icon' => '👥',
            'color' => 'bg-indigo-100 text-indigo-600',
            'title' => 'User Management',
            'description' => 'Manage students, teachers, parents, and staff with our comprehensive user management system. Assign roles and permissions.'
        ]
    ]
])

<section class="section-spacing px-4 sm:px-6 lg:px-8 bg-gradient-section">
    <div class="max-w-7xl mx-auto">
        <!-- Section Header with Animation -->
        <div class="text-center mb-20 animate-fade-up">
            <h2 class="heading-md text-gray-900 mb-6">
                {{ __($title) }}
            </h2>
            <p class="subheading max-w-2xl mx-auto">
                {{ __($subtitle) }}
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
                        {{ __($feature['title']) }}
                    </h3>

                    <!-- Description -->
                    <p class="text-gray-600 leading-relaxed mb-4 group-hover:text-gray-700 transition-colors duration-300">
                        {{ __($feature['description']) }}
                    </p>

                    <!-- Arrow Icon -->
                    <div class="flex items-center gap-2 text-red-600 opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 transition-all duration-300">
                        <span class="text-sm font-semibold">{{ __('Learn More') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

