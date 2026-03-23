@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology Department');
    $departmentLogoUrl = $department?->getLogoUrl() ?? asset('images/default-logo.svg');
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

    <main id="content" class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M7.28 7.28a.75.75 0 0 0 0 1.06l4.47 4.47H3.75a.75.75 0 0 0 0 1.5h8l-4.47 4.47a.75.75 0 1 0 1.06 1.06l5.5-5.5a.75.75 0 0 0 0-1.06l-5.5-5.5a.75.75 0 0 0-1.06 0Z" clip-rule="evenodd" />
                </svg>
                <span>{{ $locale === 'ne' ? 'पछाडि' : 'Back' }}</span>
            </a>
        </div>

        <!-- Header -->
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-6">
                <img src="{{ $departmentLogoUrl }}" alt="{{ $departmentName }}" class="h-16 w-16 rounded-lg bg-white/10 object-contain ring-1 ring-gray-200 dark:ring-gray-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">{{ $departmentName }}</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'पूर्ण विशेषताहरू' : 'Full Details' }}</p>
                </div>
            </div>
        </div>

        <!-- About Section -->
        <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-950">
            <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'विभागको परिचय' : 'About the Department' }}</h2>
            <div class="mt-6 whitespace-pre-wrap text-base leading-8 text-gray-700 dark:text-gray-300">
                {{ $aboutText ?: ($locale === 'ne'
                    ? 'यो विभागले विद्यार्थी, शिक्षक र अभिभावकका लागि एकीकृत सूचना प्रणालीमार्फत शैक्षिक व्यवस्थापनलाई डिजिटल बनाउँछ।'
                    : 'This department portal brings academics, resources, and communication together for students, faculty, and parents.') }}
            </div>
        </div>

        <!-- Additional Info Cards -->
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            @if (!empty($department?->established_year))
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'स्थापना वर्ष' : 'Established' }}</h3>
                    <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $department->established_year }}</p>
                </div>
            @endif

            @if (!empty($department?->registration_number))
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'दर्ता नं.' : 'Registration No.' }}</h3>
                    <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $department->registration_number }}</p>
                </div>
            @endif

            @if (!empty($department?->email))
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'इमेल' : 'Email' }}</h3>
                    <p class="mt-3 text-lg font-semibold">
                        <a href="mailto:{{ $department->email }}" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">{{ $department->email }}</a>
                    </p>
                </div>
            @endif

            @if (!empty($department?->phone))
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $locale === 'ne' ? 'फोन' : 'Phone' }}</h3>
                    <p class="mt-3 text-lg font-semibold">
                        <a href="tel:{{ preg_replace('/\s+/', '', $department->phone) }}" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">{{ $department->phone }}</a>
                    </p>
                </div>
            @endif
        </div>

        <!-- Back to Home Button -->
        <div class="mt-12 flex justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                {{ $locale === 'ne' ? 'गृहपृष्ठमा फर्कनुहोस्' : 'Back to Home' }}
            </a>
        </div>
    </main>

    <x-footer />
</body>
</html>
