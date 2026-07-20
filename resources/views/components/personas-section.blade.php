@props([
    'title' => 'Who Is This For?',
    'subtitle' => "Whether you're a student, teacher, parent, or administrator, our platform offers tailored features to meet your specific needs.",
    'personas' => [
        [
            'icon' => '👨‍🎓',
            'color' => 'bg-gradient-to-br from-blue-500 to-blue-600',
            'title' => 'Students',
            'description' => 'Access study materials, view notices, check attendance, and track academic progress all in one place.',
            'benefits' => [
                'Access study materials anytime',
                'View notices and updates',
                'Check attendance records',
                'Track academic progress'
            ]
        ],
        [
            'icon' => '👨‍🏫',
            'color' => 'bg-gradient-to-br from-green-500 to-green-600',
            'title' => 'Teachers',
            'description' => 'Manage classes, upload materials, mark attendance, and assess student performance efficiently.',
            'benefits' => [
                'Manage class schedules',
                'Upload study materials',
                'Mark attendance easily',
                'Assess student performance'
            ]
        ],
        [
            'icon' => '👨‍👩‍👧',
            'color' => 'bg-gradient-to-br from-purple-500 to-purple-600',
            'title' => 'Parents',
            'description' => 'Stay informed about your child\'s academic journey with real-time updates on attendance, performance, and notices.',
            'benefits' => [
                'Monitor child\'s attendance',
                'Track academic performance',
                'Receive important notices',
                'Stay connected with school'
            ]
        ],
        [
            'icon' => '⚙️',
            'color' => 'bg-gradient-to-br from-orange-500 to-orange-600',
            'title' => 'Administrators',
            'description' => 'Oversee all operations, manage users, generate reports, and make data-driven decisions for your institution.',
            'benefits' => [
                'Comprehensive dashboard',
                'User management',
                'Generate reports',
                'Data-driven decisions'
            ]
        ]
    ]
])

<section class="section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

        <!-- Personas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($personas as $persona)
                <div class="persona-card bg-white overflow-hidden border-2 border-gray-100 hover:border-red-200">
                    
                    <!-- Image Header with gradient and animation -->
                    <div class="relative {{ $persona['color'] }} h-56 flex items-center justify-center text-7xl overflow-hidden group">
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
                            {{ __($persona['title']) }}
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-6 leading-relaxed group-hover:text-gray-700 transition-colors">
                            {{ __($persona['description']) }}
                        </p>

                        <!-- Benefits List -->
                        <ul class="space-y-3">
                            @foreach($persona['benefits'] as $benefit)
                                <li class="flex items-start gap-3 text-sm text-gray-700 group-hover:text-gray-900 transition-colors duration-300">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full flex-shrink-0 font-bold text-xs group-hover:scale-125 transition-transform">✓</span>
                                    <span class="leading-relaxed">{{ __($benefit) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <!-- CTA Arrow -->
                        <div class="mt-6 pt-6 border-t border-gray-100 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <span class="text-red-600 font-semibold text-sm flex items-center gap-2 cursor-pointer">
                                {{ __('Learn More') }}
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


