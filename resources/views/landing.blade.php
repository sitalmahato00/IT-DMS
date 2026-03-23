@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();

    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology Department');

    $departmentShort = $department?->short_name ?: ($locale === 'ne' ? 'आईटी' : 'IT');
    $departmentLogoUrl = $department?->getLogoUrl() ?? asset('images/default-logo.svg');

    $tagline = $locale === 'ne'
        ? 'सीप, नवप्रवर्तन र उत्कृष्टताका लागि एकीकृत शैक्षिक प्लेटफर्म'
        : 'A unified academic platform for skills, innovation, and excellence.';

    $aboutText = $department
        ? (($locale === 'ne' && !empty($department->description_nepali)) ? $department->description_nepali : $department->description)
        : null;

    $addressText = $department
        ? (($locale === 'ne' && !empty($department->address_nepali)) ? $department->address_nepali : $department->address)
        : null;

    $semesterSummary = ($subjects ?? collect())
        ->groupBy(fn ($s) => (string) ($s->semester ?? ''))
        ->map(function ($group) {
            return [
                'count' => $group->count(),
                'credits' => (int) $group->sum(fn ($s) => (float) ($s->credits ?? 0)),
            ];
        })
        ->sortKeys();

    $subjectPayload = ($subjects ?? collect())->map(function ($s) {
        $teachers = collect($s->teachers ?? [])
            ->map(fn ($t) => $t?->user?->name)
            ->filter()
            ->values()
            ->all();

        return [
            'id' => $s->id,
            'code' => $s->subject_code,
            'title' => $s->localized_name,
            'description' => $s->localized_description,
            'credits' => $s->credits,
            'semester' => (string) ($s->semester ?? ''),
            'prerequisite' => $s->prerequisite,
            'type' => $s->subject_type,
            'category' => $s->category,
            'teachers' => $teachers,
            'has_lab' => (bool) ($s->has_lab ?? false),
            'is_elective_open' => (bool) ($s->is_elective_open ?? false),
        ];
    })->values();

    $documentPayload = ($documents ?? collect())->map(function ($d) {
        $url = null;
        if (!empty($d->file_path)) {
            try {
                $url = Storage::disk('public')->url($d->file_path);
            } catch (\Throwable $e) {
                $url = asset('storage/' . ltrim($d->file_path, '/'));
            }
        }

        return [
            'id' => $d->id,
            'type' => $d->document_type,
            'type_label' => $d->localized_document_type_label,
            'title' => $d->localized_title,
            'description' => $d->localized_description,
            'subject' => $d->subject?->localized_name,
            'semester' => (string) ($d->semester ?? ''),
            'size' => $d->formatted_size,
            'url' => $url,
            'uploaded_at' => optional($d->uploaded_at ?? $d->created_at)?->format('Y-m-d'),
        ];
    })->values();

    $programs = collect([
        [
            'level' => 'ug',
            'title' => $locale === 'ne' ? 'स्नातक कार्यक्रम' : 'Undergraduate Programs',
            'subtitle' => $locale === 'ne' ? 'आईटी/कम्प्युटर सम्बन्धित स्नातक' : 'IT / Computer related bachelor degrees',
        ],
        [
            'level' => 'pg',
            'title' => $locale === 'ne' ? 'स्नातकोत्तर कार्यक्रम' : 'Postgraduate Programs',
            'subtitle' => $locale === 'ne' ? 'विशेषीकरण र अनुसन्धान केन्द्रित' : 'Specialization & research focused',
        ],
        [
            'level' => 'diploma',
            'title' => $locale === 'ne' ? 'डिप्लोमा/सर्टिफिकेट' : 'Diploma / Certificate',
            'subtitle' => $locale === 'ne' ? 'छोटो अवधि, सीप आधारित' : 'Short-term, skill-based',
        ],
    ]);
@endphp

@extends('layouts.public')

@section('content')
    <div class="bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-gray-900 dark:focus:bg-gray-900 dark:focus:text-gray-100">
            {{ $locale === 'ne' ? 'मुख्य सामग्रीमा जानुहोस्' : 'Skip to content' }}
        </a>

        <header class="sticky top-0 z-50 border-b border-gray-200/70 bg-white/90 backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/75">
            <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ $departmentLogoUrl }}" alt="{{ $departmentName }} logo" class="h-10 w-10 rounded-lg object-contain ring-1 ring-gray-200 dark:ring-gray-800">
                    <div class="leading-tight">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $departmentShort }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-300">{{ $departmentName }}</div>
                    </div>
                </a>

                <nav class="hidden flex-1 items-center justify-center gap-6 text-sm font-medium text-gray-700 dark:text-gray-200 lg:flex">
                    <a href="#about" class="hover:text-red-700 dark:hover:text-red-400">{{ $locale === 'ne' ? 'बारेमा' : 'About' }}</a>
                    <a href="#programs" class="hover:text-red-700 dark:hover:text-red-400">{{ $locale === 'ne' ? 'कार्यक्रम' : 'Programs' }}</a>
                    <a href="#curriculum" class="hover:text-red-700 dark:hover:text-red-400">{{ $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum' }}</a>
                    <a href="#faculty" class="hover:text-red-700 dark:hover:text-red-400">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</a>
                    <a href="#notices" class="hover:text-red-700 dark:hover:text-red-400">{{ $locale === 'ne' ? 'सूचना' : 'News & Events' }}</a>
                    <a href="#resources" class="hover:text-red-700 dark:hover:text-red-400">{{ $locale === 'ne' ? 'स्रोत' : 'Resources' }}</a>
                    <a href="#contact" class="hover:text-red-700 dark:hover:text-red-400">{{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}</a>
                </nav>

                <div class="ml-auto flex items-center gap-2">
                    <form method="POST" action="{{ route('language.switch') }}" class="hidden sm:block">
                        @csrf
                        <label class="sr-only" for="localeSelect">{{ $locale === 'ne' ? 'भाषा' : 'Language' }}</label>
                        <select id="localeSelect" name="locale" onchange="this.form.submit()" class="rounded-lg border-gray-300 bg-white text-sm text-gray-800 shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            @foreach (config('locales.supported') as $code => $label)
                                <option value="{{ $code }}" @selected($code === $locale)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>

                    <button id="darkModeToggle" type="button" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800" aria-label="{{ $locale === 'ne' ? 'डार्क मोड टगल' : 'Toggle dark mode' }}" aria-pressed="false">
                        <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path d="M21 14.1A8.5 8.5 0 0 1 9.9 3 7 7 0 1 0 21 14.1Z" />
                        </svg>
                        <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="hidden h-5 w-5">
                            <path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12ZM12 2.75a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V3.5a.75.75 0 0 1 .75-.75Zm0 16a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V19.5a.75.75 0 0 1 .75-.75ZM2.75 12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H3.5a.75.75 0 0 1-.75-.75Zm16 0a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM5.28 5.28a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 1 1-1.06 1.06L5.28 6.34a.75.75 0 0 1 0-1.06Zm11.26 11.26a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 0 1-1.06 1.06l-1.06-1.06a.75.75 0 0 1 0-1.06ZM18.72 5.28a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 1 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0ZM7.46 16.54a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 0 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0Z" />
                        </svg>
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 sm:inline-flex">
                            {{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 sm:inline-flex">
                            {{ $locale === 'ne' ? 'लगइन' : 'Login' }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main id="content">
            <section class="relative isolate overflow-hidden">
                <div class="absolute inset-0 -z-10">
                    <img src="{{ asset('images/hero-image.jpg') }}" alt="" class="h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-950/80 via-gray-950/55 to-gray-950/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/60 via-transparent to-transparent"></div>
                </div>

                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
                    <div class="max-w-3xl">
                        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold tracking-wide text-white ring-1 ring-white/15">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            {{ $locale === 'ne' ? 'विभागीय पोर्टल' : 'Department Portal' }}
                        </p>

                        <h1 class="mt-6 text-3xl font-bold tracking-tight text-white sm:text-5xl">
                            {{ $departmentName }}
                        </h1>
                        <p class="mt-5 text-base leading-relaxed text-white/85 sm:text-lg">
                            {{ $tagline }}
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <a href="#programs" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                                {{ $locale === 'ne' ? 'कार्यक्रम हेर्नुहोस्' : 'Explore Programs' }}
                            </a>
                            <a href="#curriculum" class="inline-flex items-center justify-center rounded-lg bg-white/10 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/40">
                                {{ $locale === 'ne' ? 'विषयहरू ब्राउज' : 'Browse Subjects' }}
                            </a>
                            <a href="#contact" class="inline-flex items-center justify-center rounded-lg bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white/60">
                                {{ $locale === 'ne' ? 'सम्पर्क गर्नुहोस्' : 'Contact Us' }}
                            </a>
                        </div>

                        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['students'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'विद्यार्थी' : 'Students' }}</div>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['teachers'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</div>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['subjects'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'विषय' : 'Subjects' }}</div>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['labs'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'ल्याब' : 'Labs' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="about" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-7">
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                            {{ $locale === 'ne' ? 'विभागको परिचय' : 'About the Department' }}
                        </h2>
                        <p class="mt-4 text-sm leading-7 text-gray-700 dark:text-gray-300">
                            {{ $aboutText ?: ($locale === 'ne'
                                ? 'यो विभागले विद्यार्थी, शिक्षक र अभिभावकका लागि एकीकृत सूचना प्रणालीमार्फत शैक्षिक व्यवस्थापनलाई डिजिटल बनाउँछ।'
                                : 'This department portal brings academics, resources, and communication together for students, faculty, and parents.') }}
                        </p>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-300">{{ $locale === 'ne' ? 'स्थापना वर्ष' : 'Established' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $department?->established_year ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not available') }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-300">{{ $locale === 'ne' ? 'दर्ता नं.' : 'Registration No.' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $department?->registration_number ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not available') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-3xl border border-gray-200 bg-gradient-to-br from-gray-50 to-white p-6 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:to-gray-900">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'छिटो पहुँच' : 'Quick Access' }}</h3>
                            <div class="mt-5 grid gap-3">
                                <a href="#notices" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-900">
                                    {{ $locale === 'ne' ? 'सूचना बोर्ड' : 'Notice Board' }}
                                </a>
                                <a href="#resources" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-900">
                                    {{ $locale === 'ne' ? 'दस्तावेज/स्रोत' : 'Documents & Resources' }}
                                </a>
                                <a href="{{ route('gallery.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-900">
                                    {{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}
                                </a>
                                @auth
                                    <a href="{{ route('dashboard') }}" class="rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        {{ $locale === 'ne' ? 'ड्यासबोर्ड खोल्नुहोस्' : 'Open Dashboard' }}
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        {{ $locale === 'ne' ? 'लगइन गरेर सुरु गर्नुहोस्' : 'Sign in to continue' }}
                                    </a>
                                @endauth
                            </div>

                            @if (!empty($addressText) || !empty($department?->phone) || !empty($department?->email))
                                <div class="mt-6 border-t border-gray-200 pt-5 text-sm text-gray-700 dark:border-gray-800 dark:text-gray-300">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}</div>
                                    <div class="mt-2 space-y-1">
                                        @if (!empty($addressText))
                                            <div>{{ $addressText }}</div>
                                        @endif
                                        @if (!empty($department?->phone))
                                            <div>{{ $department->phone }}</div>
                                        @endif
                                        @if (!empty($department?->email))
                                            <div>
                                                <a class="text-red-700 hover:underline dark:text-red-400" href="mailto:{{ $department->email }}">{{ $department->email }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <section id="programs" class="bg-gray-50 py-14 dark:bg-gray-900/30">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ $locale === 'ne' ? 'शैक्षिक कार्यक्रम' : 'Academic Programs' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $locale === 'ne' ? 'कार्यक्रम अनुसार पाठ्यक्रम र विषयहरू छान्नुहोस्।' : 'Discover programs and explore semester-wise curriculum.' }}
                            </p>
                        </div>
                        <a href="#curriculum" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-900">
                            {{ $locale === 'ne' ? 'पाठ्यक्रम हेर्नुहोस्' : 'View Curriculum' }}
                        </a>
                    </div>

                    <div x-data="programTabs()" class="mt-8">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset"
                                :class="active === 'all' ? 'bg-red-600 text-white ring-red-600' : 'bg-white text-gray-800 ring-gray-200 hover:bg-gray-50 dark:bg-gray-950 dark:text-gray-100 dark:ring-gray-800 dark:hover:bg-gray-900'"
                                @click="active='all'">
                                {{ $locale === 'ne' ? 'सबै' : 'All' }}
                            </button>
                            <button type="button" class="rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset"
                                :class="active === 'ug' ? 'bg-red-600 text-white ring-red-600' : 'bg-white text-gray-800 ring-gray-200 hover:bg-gray-50 dark:bg-gray-950 dark:text-gray-100 dark:ring-gray-800 dark:hover:bg-gray-900'"
                                @click="active='ug'">
                                {{ $locale === 'ne' ? 'स्नातक' : 'UG' }}
                            </button>
                            <button type="button" class="rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset"
                                :class="active === 'pg' ? 'bg-red-600 text-white ring-red-600' : 'bg-white text-gray-800 ring-gray-200 hover:bg-gray-50 dark:bg-gray-950 dark:text-gray-100 dark:ring-gray-800 dark:hover:bg-gray-900'"
                                @click="active='pg'">
                                {{ $locale === 'ne' ? 'स्नातकोत्तर' : 'PG' }}
                            </button>
                            <button type="button" class="rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset"
                                :class="active === 'diploma' ? 'bg-red-600 text-white ring-red-600' : 'bg-white text-gray-800 ring-gray-200 hover:bg-gray-50 dark:bg-gray-950 dark:text-gray-100 dark:ring-gray-800 dark:hover:bg-gray-900'"
                                @click="active='diploma'">
                                {{ $locale === 'ne' ? 'डिप्लोमा' : 'Diploma' }}
                            </button>
                        </div>

                        <div class="mt-6 grid gap-6 md:grid-cols-3">
                            <template x-for="program in filtered" :key="program.title">
                                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-950">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400" x-text="badge(program.level)"></div>
                                            <div class="mt-2 text-lg font-bold text-gray-900 dark:text-gray-100" x-text="program.title"></div>
                                            <div class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300" x-text="program.subtitle"></div>
                                        </div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-700 ring-1 ring-red-100 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                                                <path d="M12 3a1 1 0 0 1 .6.2l8 6a1 1 0 0 1 .4.8v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10a1 1 0 0 1 .4-.8l8-6A1 1 0 0 1 12 3Zm0 2.25L5 10.5V19h14v-8.5l-7-5.25ZM8 12h8a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <a href="#curriculum" class="inline-flex items-center gap-2 text-sm font-semibold text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            {{ $locale === 'ne' ? 'विषयहरू हेर्नुहोस्' : 'See subjects & curriculum' }}
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <section id="curriculum" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8" x-data="subjectCatalog({
                initialSemester: @js($selectedSemester ?: 'all'),
                subjects: @js($subjectPayload),
                locale: @js($locale),
            })">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                            {{ $locale === 'ne' ? 'विषय र पाठ्यक्रम' : 'Subjects & Curriculum' }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $locale === 'ne' ? 'विषय खोज्नुहोस् र विवरण/पूर्व-आवश्यकता हेर्नुहोस्।' : 'Search subjects and view details, prerequisites, and credits.' }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <label class="sr-only" for="subjectSearch">{{ $locale === 'ne' ? 'विषय खोज' : 'Search subjects' }}</label>
                        <input id="subjectSearch" type="search" x-model.trim="query" placeholder="{{ $locale === 'ne' ? 'विषय/कोड खोज्नुहोस्…' : 'Search by subject or code…' }}"
                            class="w-full rounded-xl border-gray-300 bg-white px-4 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 sm:w-72" />

                        <label class="sr-only" for="semesterFilter">{{ $locale === 'ne' ? 'सेमेस्टर' : 'Semester' }}</label>
                        <select id="semesterFilter" x-model="semester"
                            class="w-full rounded-xl border-gray-300 bg-white px-4 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 sm:w-44">
                            <option value="all">{{ $locale === 'ne' ? 'सबै सेमेस्टर' : 'All semesters' }}</option>
                            @foreach ($semesterSummary as $sem => $meta)
                                @if (!empty($sem))
                                    <option value="{{ $sem }}">{{ $locale === 'ne' ? "{$sem} सेमेस्टर" : "Semester {$sem}" }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-10 grid gap-8 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        <div class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950">
                            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $locale === 'ne' ? 'विषय सूची' : 'Subject catalog' }}
                                    </div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="resultText"></div>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-200 dark:divide-gray-800">
                                <template x-for="s in filteredSubjects" :key="s.id">
                                    <div class="px-6 py-4">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-900 dark:text-gray-200" x-text="s.code || '—'"></span>
                                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300" x-text="semesterLabel(s.semester)"></span>
                                                    <template x-if="s.has_lab">
                                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">{{ $locale === 'ne' ? 'ल्याब' : 'Lab' }}</span>
                                                    </template>
                                                    <template x-if="s.is_elective_open">
                                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">{{ $locale === 'ne' ? 'इलेक्टिभ' : 'Elective' }}</span>
                                                    </template>
                                                </div>
                                                <div class="mt-2 truncate text-base font-semibold text-gray-900 dark:text-gray-100" x-text="s.title"></div>
                                                <div class="mt-1 line-clamp-2 text-sm text-gray-700 dark:text-gray-300" x-text="s.description || fallbackDescription"></div>
                                                <div class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400" x-text="creditText(s.credits)"></div>
                                            </div>

                                            <div class="shrink-0">
                                                <button type="button" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-900"
                                                    @click="toggle(s.id)">
                                                    <span x-text="openId === s.id ? closeLabel : detailLabel"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <div x-show="openId === s.id" x-transition class="mt-4 rounded-2xl bg-gray-50 p-4 text-sm text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                            <div class="grid gap-4 sm:grid-cols-2">
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'क्रेडिट' : 'Credits' }}</div>
                                                    <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100" x-text="s.credits ?? '—'"></div>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'पूर्व-आवश्यकता' : 'Prerequisite' }}</div>
                                                    <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100" x-text="s.prerequisite || '—'"></div>
                                                </div>
                                            </div>
                                            <template x-if="(s.teachers || []).length">
                                                <div class="mt-4">
                                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</div>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        <template x-for="t in s.teachers" :key="t">
                                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200 dark:bg-gray-950 dark:text-gray-200 dark:ring-gray-800" x-text="t"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="filteredSubjects.length === 0" class="px-6 py-10 text-center text-sm text-gray-600 dark:text-gray-300">
                                    {{ $locale === 'ne' ? 'कुनै विषय भेटिएन।' : 'No subjects found.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $locale === 'ne' ? 'सेमेस्टर अनुसार सारांश' : 'Semester-wise overview' }}
                            </h3>
                            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                                @foreach ($semesterSummary as $sem => $meta)
                                    @continue(empty($sem))
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/40">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $locale === 'ne' ? "{$sem} सेमेस्टर" : "Semester {$sem}" }}
                                            </div>
                                            <div class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200 dark:bg-gray-950 dark:text-gray-200 dark:ring-gray-800">
                                                {{ $meta['count'] }} {{ $locale === 'ne' ? 'विषय' : 'subjects' }}
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                            {{ $locale === 'ne' ? 'कुल क्रेडिट' : 'Total credits' }}: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $meta['credits'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="faculty" class="bg-gray-50 py-14 dark:bg-gray-900/30">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ $locale === 'ne' ? 'शिक्षक तथा स्टाफ' : 'Faculty & Staff' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $locale === 'ne' ? 'विभागका सक्रिय शिक्षकहरूको झलक।' : 'Meet our active department faculty.' }}
                            </p>
                        </div>
                    </div>

                    @if (!empty($hod))
                        <div class="mt-8 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-700 ring-1 ring-red-100 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                        <span class="text-lg font-bold">{{ Str::of($hod->name ?? 'H')->substr(0, 1)->upper() }}</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'विभाग प्रमुख' : 'Head of Department' }}</div>
                                        <div class="mt-1 text-base font-bold text-gray-900 dark:text-gray-100">{{ $hod->name }}</div>
                                        @if (!empty($hod->email))
                                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                                <a href="mailto:{{ $hod->email }}" class="text-red-700 hover:underline dark:text-red-400">{{ $hod->email }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <a href="#contact" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    {{ $locale === 'ne' ? 'सम्पर्क गर्नुहोस्' : 'Contact' }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @forelse (($teachers ?? collect())->take(8) as $t)
                            @php
                                $name = $t->user?->name ?: 'Unknown';
                                $initials = Str::of($name)->trim()->explode(' ')->filter()->take(2)->map(fn ($p) => Str::substr($p, 0, 1))->join('');
                                $subjectCount = $t->subjects?->count() ?? 0;
                            @endphp
                            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-950">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700 ring-1 ring-gray-200 dark:bg-gray-900 dark:text-gray-100 dark:ring-gray-800">
                                        <span class="text-sm font-bold">{{ $initials ?: Str::of($name)->substr(0, 1)->upper() }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-bold text-gray-900 dark:text-gray-100">{{ $name }}</div>
                                        <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">{{ $t->qualification ?: ($locale === 'ne' ? 'शिक्षक' : 'Faculty') }}</div>
                                    </div>
                                </div>
                                <div class="mt-5 flex items-center justify-between text-xs font-semibold text-gray-600 dark:text-gray-300">
                                    <span>{{ $locale === 'ne' ? 'विषय' : 'Subjects' }}</span>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ $subjectCount }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300 sm:col-span-2 lg:col-span-4">
                                {{ $locale === 'ne' ? 'हाल कुनै शिक्षक उपलब्ध छैन।' : 'No faculty records available yet.' }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="notices" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8" x-data="{ open: false, notice: null }">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                            {{ $locale === 'ne' ? 'समाचार तथा कार्यक्रम' : 'News & Events' }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $locale === 'ne' ? 'नयाँ सूचना र घोषणा।' : 'Latest announcements and updates.' }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse (($notices ?? collect()) as $n)
                        <button type="button" class="text-left rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-950"
                            @click="notice=@js([
                                'title' => $n->localized_title,
                                'date' => $n->formatted_date,
                                'audience' => $n->localized_audience_label,
                                'priority' => $n->localized_priority_label,
                                'full' => $n->localized_message,
                            ]); open=true;">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $n->formatted_date }}</div>
                                    <div class="mt-2 line-clamp-2 text-base font-bold text-gray-900 dark:text-gray-100">{{ $n->localized_title }}</div>
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">
                                        {{ $n->localized_priority_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ Str::limit(strip_tags($n->localized_message), 120) }}
                            </div>
                            <div class="mt-5 flex items-center justify-between text-xs font-semibold text-gray-600 dark:text-gray-400">
                                <span>{{ $n->localized_audience_label }}</span>
                                <span class="text-red-700 dark:text-red-400">{{ $locale === 'ne' ? 'पढ्नुहोस्' : 'Read' }} →</span>
                            </div>
                        </button>
                    @empty
                        <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300 md:col-span-2 lg:col-span-3">
                            {{ $locale === 'ne' ? 'हाल कुनै सूचना छैन।' : 'No notices published yet.' }}
                        </div>
                    @endforelse
                </div>

                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true">
                    <div class="absolute inset-0 bg-gray-950/70" @click="open=false" aria-hidden="true"></div>
                    <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-gray-200 dark:bg-gray-950 dark:ring-gray-800">
                        <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400" x-text="notice?.date || ''"></div>
                                <div class="mt-1 truncate text-lg font-bold text-gray-900 dark:text-gray-100" x-text="notice?.title || ''"></div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-900 dark:text-gray-200" x-text="notice?.audience || ''"></span>
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-red-700 dark:bg-red-950/40 dark:text-red-300" x-text="notice?.priority || ''"></span>
                                </div>
                            </div>
                            <button type="button" class="rounded-xl border border-gray-200 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800" @click="open=false" aria-label="{{ $locale === 'ne' ? 'बन्द गर्नुहोस्' : 'Close' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M6.22 6.22a.75.75 0 0 1 1.06 0L12 10.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L13.06 12l4.72 4.72a.75.75 0 1 1-1.06 1.06L12 13.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L10.94 12 6.22 7.28a.75.75 0 0 1 0-1.06Z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 py-6">
                            <div class="prose prose-sm max-w-none text-gray-800 dark:prose-invert dark:text-gray-200" x-html="notice?.full || ''"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="resources" class="bg-gray-50 py-14 dark:bg-gray-900/30" x-data="documentRepo({ documents: @js($documentPayload), locale: @js($locale) })">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ $locale === 'ne' ? 'दस्तावेज तथा स्रोत' : 'Documents & Resources' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $locale === 'ne' ? 'सिलेबस, नोट्स, गाइड र अन्य सामग्री।' : 'Syllabus, notes, guides, and other materials.' }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <label class="sr-only" for="docSearch">{{ $locale === 'ne' ? 'दस्तावेज खोज' : 'Search documents' }}</label>
                            <input id="docSearch" type="search" x-model.trim="query" placeholder="{{ $locale === 'ne' ? 'शीर्षक/विषय खोज्नुहोस्…' : 'Search by title or subject…' }}"
                                class="w-full rounded-xl border-gray-300 bg-white px-4 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 sm:w-72" />

                            <label class="sr-only" for="docType">{{ $locale === 'ne' ? 'प्रकार' : 'Type' }}</label>
                            <select id="docType" x-model="type"
                                class="w-full rounded-xl border-gray-300 bg-white px-4 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 sm:w-52">
                                <option value="all">{{ $locale === 'ne' ? 'सबै प्रकार' : 'All types' }}</option>
                                <template x-for="t in types" :key="t.value">
                                    <option :value="t.value" x-text="t.label"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950">
                        <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $locale === 'ne' ? 'रिपोजिटरी' : 'Repository' }}
                            </div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="resultText"></div>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            <template x-for="d in filtered" :key="d.id">
                                <div class="px-6 py-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-900 dark:text-gray-200" x-text="d.type_label"></span>
                                                <template x-if="d.subject">
                                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200 dark:bg-gray-950 dark:text-gray-200 dark:ring-gray-800" x-text="d.subject"></span>
                                                </template>
                                                <template x-if="d.size">
                                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="d.size"></span>
                                                </template>
                                            </div>
                                            <div class="mt-2 truncate text-base font-semibold text-gray-900 dark:text-gray-100" x-text="d.title"></div>
                                            <div class="mt-1 line-clamp-2 text-sm text-gray-700 dark:text-gray-300" x-text="d.description || fallbackDescription"></div>
                                            <div class="mt-3 text-xs font-medium text-gray-500 dark:text-gray-400" x-text="uploadedText(d.uploaded_at)"></div>
                                        </div>
                                        <div class="shrink-0">
                                            <template x-if="d.url">
                                                <a :href="d.url" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    {{ $locale === 'ne' ? 'खोल्नुहोस्' : 'Open' }}
                                                </a>
                                            </template>
                                            <template x-if="!d.url">
                                                <span class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-400 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-500">
                                                    {{ $locale === 'ne' ? 'उपलब्ध छैन' : 'Unavailable' }}
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="filtered.length === 0" class="px-6 py-10 text-center text-sm text-gray-600 dark:text-gray-300">
                                {{ $locale === 'ne' ? 'कुनै सामग्री भेटिएन।' : 'No materials found.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                            {{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $locale === 'ne' ? 'विभागका गतिविधि र सुविधाका झलकहरू।' : 'Highlights from department events and facilities.' }}
                        </p>
                    </div>
                    <a href="{{ route('gallery.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-900">
                        {{ $locale === 'ne' ? 'सबै ग्यालरी' : 'View all' }}
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @forelse (($galleryItems ?? collect()) as $g)
                        <a href="{{ route('gallery.index', ['category' => $g->category]) }}" class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-gray-100 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            @if ($g->image_url)
                                <img src="{{ $g->image_url }}" alt="{{ $g->title }}" class="h-44 w-full object-cover transition duration-300 group-hover:scale-105 sm:h-48">
                            @else
                                <div class="flex h-44 items-center justify-center text-sm text-gray-500 dark:text-gray-400 sm:h-48">
                                    {{ $locale === 'ne' ? 'तस्विर छैन' : 'No image' }}
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-gray-950/70 via-gray-950/35 to-transparent p-4">
                                <div class="truncate text-sm font-semibold text-white">{{ $g->title }}</div>
                                <div class="mt-1 text-xs font-medium text-white/80">{{ $g->category_text }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300 sm:col-span-3 lg:col-span-4">
                            {{ $locale === 'ne' ? 'हाल ग्यालरी सामग्री छैन।' : 'No gallery items available yet.' }}
                        </div>
                    @endforelse
                </div>
            </section>

            <section id="contact" class="bg-gray-50 py-14 dark:bg-gray-900/30">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                        <div class="lg:col-span-5">
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ $locale === 'ne' ? 'सम्पर्क तथा सहयोग' : 'Contact & Support' }}
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-gray-700 dark:text-gray-300">
                                {{ $locale === 'ne'
                                    ? 'कुनै प्रश्न, सुझाव वा सहयोगका लागि हामीलाई सम्पर्क गर्नुहोस्।'
                                    : 'Reach out for questions, suggestions, or support.' }}
                            </p>

                            <div class="mt-6 space-y-4">
                                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'इमेल' : 'Email' }}</div>
                                    <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        @if (!empty($department?->email))
                                            <a class="text-red-700 hover:underline dark:text-red-400" href="mailto:{{ $department->email }}">{{ $department->email }}</a>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">support@example.edu</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'फोन' : 'Phone' }}</div>
                                    <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $department?->phone ?: '+977-000-0000000' }}
                                    </div>
                                </div>
                                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'ठेगाना' : 'Address' }}</div>
                                    <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $addressText ?: ($locale === 'ne' ? 'विभागीय कार्यालय' : 'Department office') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-7">
                            <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950">
                                @php
                                    $lat = $department?->latitude;
                                    $lng = $department?->longitude;
                                    $mapUrl = null;
                                    if ($lat && $lng) {
                                        $mapUrl = "https://www.openstreetmap.org/export/embed.html?bbox=" . ($lng - 0.01) . "%2C" . ($lat - 0.01) . "%2C" . ($lng + 0.01) . "%2C" . ($lat + 0.01) . "&layer=mapnik&marker={$lat}%2C{$lng}";
                                    }
                                @endphp
                                @if ($mapUrl)
                                    <iframe title="{{ $locale === 'ne' ? 'नक्सा' : 'Map' }}" src="{{ $mapUrl }}" class="h-80 w-full" loading="lazy"></iframe>
                                @else
                                    <div class="flex h-80 items-center justify-center bg-gray-100 text-sm text-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                        {{ $locale === 'ne' ? 'नक्सा डेटा उपलब्ध छैन' : 'Map data not available' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="border-t border-gray-200 bg-white py-10 dark:border-gray-800 dark:bg-gray-950">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-8 md:grid-cols-12">
                        <div class="md:col-span-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $departmentLogoUrl }}" alt="" class="h-10 w-10 rounded-lg object-contain ring-1 ring-gray-200 dark:ring-gray-800">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $departmentName }}</div>
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">{{ config('app.name', 'IT-DMS') }}</div>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $locale === 'ne'
                                    ? 'विद्यार्थी, शिक्षक र अभिभावकका लागि विभागीय सेवा र स्रोतहरू।'
                                    : 'Department services and resources for students, faculty, and parents.' }}
                            </p>
                        </div>

                        <div class="md:col-span-3">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'छिटो लिंक' : 'Quick Links' }}</div>
                            <ul class="mt-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <li><a class="hover:text-red-700 dark:hover:text-red-400" href="#about">{{ $locale === 'ne' ? 'बारेमा' : 'About' }}</a></li>
                                <li><a class="hover:text-red-700 dark:hover:text-red-400" href="#curriculum">{{ $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum' }}</a></li>
                                <li><a class="hover:text-red-700 dark:hover:text-red-400" href="#notices">{{ $locale === 'ne' ? 'सूचना' : 'Notices' }}</a></li>
                                <li><a class="hover:text-red-700 dark:hover:text-red-400" href="{{ route('gallery.index') }}">{{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}</a></li>
                            </ul>
                        </div>

                        <div class="md:col-span-4">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'Account' : 'Account' }}</div>
                            <ul class="mt-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                @auth
                                    <li><a class="hover:text-red-700 dark:hover:text-red-400" href="{{ route('dashboard') }}">{{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}</a></li>
                                @else
                                    <li><a class="hover:text-red-700 dark:hover:text-red-400" href="{{ route('login') }}">{{ $locale === 'ne' ? 'लगइन' : 'Login' }}</a></li>
                                    @if (Route::has('register'))
                                        <li><a class="hover:text-red-700 dark:hover:text-red-400" href="{{ route('register') }}">{{ $locale === 'ne' ? 'दर्ता' : 'Register' }}</a></li>
                                    @endif
                                @endauth
                            </ul>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col gap-2 border-t border-gray-200 pt-6 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400 sm:flex-row sm:items-center sm:justify-between">
                        <div>© {{ now()->year }} {{ $departmentShort }}. {{ $locale === 'ne' ? 'सबै अधिकार सुरक्षित।' : 'All rights reserved.' }}</div>
                        <form method="POST" action="{{ route('language.switch') }}" class="sm:hidden">
                            @csrf
                            <select name="locale" onchange="this.form.submit()" class="rounded-lg border-gray-300 bg-white text-xs text-gray-800 shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @foreach (config('locales.supported') as $code => $label)
                                    <option value="{{ $code }}" @selected($code === $locale)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </footer>

            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('programTabs', () => ({
                        active: 'all',
                        programs: @js($programs->values()),
                        get filtered() {
                            if (this.active === 'all') return this.programs;
                            return this.programs.filter(p => p.level === this.active);
                        },
                        badge(level) {
                            const map = { ug: 'UG', pg: 'PG', diploma: 'Diploma' };
                            return map[level] || 'Program';
                        },
                    }));

                    Alpine.data('subjectCatalog', (config) => ({
                        locale: config.locale || 'en',
                        subjects: Array.isArray(config.subjects) ? config.subjects : [],
                        query: '',
                        semester: config.initialSemester || 'all',
                        openId: null,
                        detailLabel: (config.locale === 'ne') ? 'विवरण' : 'Details',
                        closeLabel: (config.locale === 'ne') ? 'बन्द' : 'Close',
                        fallbackDescription: (config.locale === 'ne')
                            ? 'यस विषयको विवरण छिट्टै उपलब्ध हुनेछ।'
                            : 'Details for this subject will be available soon.',
                        get filteredSubjects() {
                            const q = (this.query || '').toLowerCase();
                            return this.subjects.filter((s) => {
                                if (this.semester !== 'all' && String(s.semester || '') !== String(this.semester)) return false;
                                if (!q) return true;
                                const hay = [s.code, s.title, s.description, s.category, s.type, (s.teachers || []).join(' ')]
                                    .filter(Boolean).join(' ').toLowerCase();
                                return hay.includes(q);
                            });
                        },
                        get resultText() {
                            const total = this.subjects.length;
                            const shown = this.filteredSubjects.length;
                            return (this.locale === 'ne') ? `${shown} / ${total} विषय` : `${shown} / ${total} subjects`;
                        },
                        semesterLabel(sem) {
                            const s = String(sem || '').trim();
                            if (!s) return (this.locale === 'ne') ? 'सेमेस्टर —' : 'Semester —';
                            return (this.locale === 'ne') ? `${s} सेमेस्टर` : `Semester ${s}`;
                        },
                        creditText(credits) {
                            const c = (credits ?? '—');
                            return (this.locale === 'ne') ? `क्रेडिट: ${c}` : `Credits: ${c}`;
                        },
                        toggle(id) {
                            this.openId = (this.openId === id) ? null : id;
                        },
                    }));

                    Alpine.data('documentRepo', (config) => ({
                        locale: config.locale || 'en',
                        documents: Array.isArray(config.documents) ? config.documents : [],
                        query: '',
                        type: 'all',
                        fallbackDescription: (config.locale === 'ne')
                            ? 'यस सामग्रीको विवरण छिट्टै उपलब्ध हुनेछ।'
                            : 'Details for this material will be available soon.',
                        get types() {
                            const uniq = new Map();
                            this.documents.forEach(d => {
                                if (!d || !d.type) return;
                                uniq.set(d.type, d.type_label || d.type);
                            });
                            return Array.from(uniq.entries()).map(([value, label]) => ({ value, label }));
                        },
                        get filtered() {
                            const q = (this.query || '').toLowerCase();
                            return this.documents.filter((d) => {
                                if (this.type !== 'all' && d.type !== this.type) return false;
                                if (!q) return true;
                                const hay = [d.title, d.description, d.subject, d.type_label].filter(Boolean).join(' ').toLowerCase();
                                return hay.includes(q);
                            });
                        },
                        get resultText() {
                            const total = this.documents.length;
                            const shown = this.filtered.length;
                            return (this.locale === 'ne') ? `${shown} / ${total} सामग्री` : `${shown} / ${total} materials`;
                        },
                        uploadedText(dateStr) {
                            if (!dateStr) return (this.locale === 'ne') ? 'अपलोड मिति उपलब्ध छैन' : 'Upload date unavailable';
                            return (this.locale === 'ne') ? `अपलोड: ${dateStr}` : `Uploaded: ${dateStr}`;
                        },
                    }));
                });
            </script>
        </main>
    </div>
@endsection
