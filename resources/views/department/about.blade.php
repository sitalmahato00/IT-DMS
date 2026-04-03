@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology Department');
    $departmentLogoUrl = $department?->getLogoUrl() ?? '/images/default-logo.svg';
    $aboutText = $department
        ? (($locale === 'ne' && !empty($department->description_nepali)) ? $department->description_nepali : $department->description)
        : null;
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ne' ? 'ltr' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $departmentName }} - About</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-b from-white via-red-50/30 to-white text-gray-900 dark:from-gray-950 dark:via-gray-950 dark:to-gray-950 dark:text-gray-100">
    <x-header />

    <main id="content" class="w-full px-4 py-8 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mx-auto max-w-7xl mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M7.28 7.28a.75.75 0 0 0 0 1.06l4.47 4.47H3.75a.75.75 0 0 0 0 1.5h8l-4.47 4.47a.75.75 0 1 0 1.06 1.06l5.5-5.5a.75.75 0 0 0 0-1.06l-5.5-5.5a.75.75 0 0 0-1.06 0Z" clip-rule="evenodd" />
                </svg>
                <span>{{ $locale === 'ne' ? 'पछाडि' : 'Back' }}</span>
            </a>
        </div>

        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-4">
                    <img src="{{ $departmentLogoUrl }}" alt="{{ $departmentName }}" class="h-14 w-14 rounded-lg bg-white/10 object-contain ring-1 ring-gray-200 dark:ring-gray-800">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">{{ $departmentName }}</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'विभागको परिचय' : 'About the Department' }}</p>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid items-start gap-6 lg:grid-cols-3 auto-rows-fr">
                <!-- Left Column: Main Content -->
                <div class="lg:col-span-2 flex flex-col min-h-full">
                    <!-- About Section -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950 flex-1">
                        <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'विभागको परिचय' : 'About the Department' }}</h2>
                        <div class="mt-4 whitespace-pre-wrap text-sm leading-7 text-gray-700 dark:text-gray-300">
                            {{ $aboutText ?: ($locale === 'ne'
                                ? 'यो विभागले विद्यार्थी, शिक्षक र अभिभावकका लागि एकीकृत सूचना प्रणालीमार्फत शैक्षिक व्यवस्थापनलाई डिजिटल बनाउँछ।'
                                : 'This department portal brings academics, resources, and communication together for students, faculty, and parents.') }}
                        </div>
                    </div>

                    <!-- Additional Info Cards -->
                    <div class="mt-6 grid gap-4 grid-cols-2">
                        @if (!empty($department?->established_year))
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                                <h3 class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'स्थापना वर्ष' : 'Established' }}</h3>
                                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $department->established_year }}</p>
                            </div>
                        @endif

                        @if (!empty($department?->registration_number))
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                                <h3 class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'दर्ता नं.' : 'Registration No.' }}</h3>
                                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $department->registration_number }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Quick Access & Contact -->
                <div class="lg:col-span-1 flex flex-col gap-6 relative top-16">
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950 h-full">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'छिटो पहुँच' : 'Quick Access' }}</h3>
                        <div class="mt-3 space-y-2">
                            <a href="{{ route('home') }}#notices" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-red-50 transition dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-red-950/20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-red-500">
                                    <path d="M1.5 4.5h21v2.25H1.5V4.5zm0 9h21v2.25h-21V13.5zm0 9h21V24h-21v-2.25z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'नोटिस बोर्ड' : 'Notice Board' }}
                            </a>
                            <a href="{{ route('home') }}#resources" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 transition dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-amber-950/20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-amber-600">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'कागजातहरू र स्रोतहरू' : 'Documents & Resources' }}
                            </a>
                            <a href="{{ route('home') }}#gallery" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-green-50 transition dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-green-950/20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-green-600">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'ग्यालेरी' : 'Gallery' }}
                            </a>
                            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'ड्याशबोर्ड खोल्नुहोस्' : 'Open Dashboard' }}
                            </a>
                        </div>

                        <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-800">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}</h4>
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-300">{{ $department?->location ?? ($locale === 'ne' ? 'काठमाडौं, नेपाल' : 'Kathmandu, Nepal') }}</p>
                            @if (!empty($department?->phone))
                                <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $department->phone }}</p>
                            @endif
                            @if (!empty($department?->email))
                                <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $department->email }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                    <!-- Quick Access Section -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'छिटो पहुँच' : 'Quick Access' }}</h3>
                        <div class="mt-3 space-y-2">
                            <a href="{{ route('home') }}#notices" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-red-50 transition dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-red-950/20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-red-500">
                                    <path d="M1.5 4.5h21v2.25H1.5V4.5zm0 9h21v2.25h-21V13.5zm0 9h21V24h-21v-2.25z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'नोटिस बोर्ड' : 'Notice Board' }}
                            </a>
                            <a href="{{ route('home') }}#resources" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 transition dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-amber-950/20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-amber-600">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'कागजातहरू र स्रोतहरू' : 'Documents & Resources' }}
                            </a>
                            <a href="{{ route('home') }}#gallery" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-green-50 transition dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-green-950/20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-green-600">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'ग्यालेरी' : 'Gallery' }}
                            </a>
                            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                {{ $locale === 'ne' ? 'ड्याशबोर्ड खोल्नुहोस्' : 'Open Dashboard' }}
                            </a>
                        </div>
                    </div>

                    <!-- Contact Section -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'सम्पर्क गर्नुहोस्' : 'Contact' }}</h3>
                        <div class="mt-3 space-y-3 text-xs">
                            <div>
                                <p class="font-medium text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'ठेगाना' : 'Address' }}</p>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $department?->location ?? ($locale === 'ne' ? 'काठमाडौं, नेपाल' : 'Kathmandu, Nepal') }}</p>
                            </div>
                            @if (!empty($department?->phone))
                                <div>
                                    <p class="font-medium text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'फोन' : 'Phone' }}</p>
                                    <p class="mt-1">
                                        <a href="tel:{{ preg_replace('/\s+/', '', $department->phone) }}" class="font-semibold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">{{ $department->phone }}</a>
                                    </p>
                                </div>
                            @endif
                            @if (!empty($department?->email))
                                <div>
                                    <p class="font-medium text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'इमेल' : 'Email' }}</p>
                                    <p class="mt-1">
                                        <a href="mailto:{{ $department->email }}" class="break-all font-semibold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">{{ $department->email }}</a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back to Home Button -->
            <div class="mt-8 flex justify-center">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    {{ $locale === 'ne' ? 'गृहपृष्ठमा फर्कनुहोस्' : 'Back to Home' }}
                </a>
            </div>
        </div>
    </main>

    <x-footer />
</body>
</html>
