@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();

    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology Department');

    $departmentShort = $department?->short_name ?: ($locale === 'ne' ? 'आईटी' : 'IT');
    $departmentLogoUrl = $department?->getLogoUrl() ?? asset('images/default-logo.svg');

    $heroSlides = collect($department?->hero_images ?? [])
        ->filter()
        ->map(function ($path) {
            try {
                if (Storage::disk('public')->exists($path)) {
                    return Storage::url($path);
                }
            } catch (\Throwable $e) {
            }

            return asset('storage/' . ltrim((string) $path, '/'));
        })
        ->values();

    if ($heroSlides->isEmpty()) {
        $heroSlides = collect([asset('images/hero-image.jpg')]);
    }

    $tagline = $locale === 'ne'
        ? 'सीप, नवप्रवर्तन र उत्कृष्टताका लागि एकीकृत शैक्षिक प्लेटफर्म'
        : 'A unified academic platform for skills, innovation, and excellence.';

    $aboutText = $department
        ? (($locale === 'ne' && !empty($department->description_nepali)) ? $department->description_nepali : $department->description)
        : null;

    $addressText = $department
        ? (($locale === 'ne' && !empty($department->address_nepali)) ? $department->address_nepali : $department->address)
        : null;

    $mapLabel = $department?->map_label ?: ($locale === 'ne' ? 'स्थान' : 'Location');
    $mapEmbedUrl = null;
    $mapOpenUrl = null;

    $lat = $department?->latitude;
    $lng = $department?->longitude;

    if (!empty($department?->map_embed_url)) {
        $mapEmbedUrl = $department->map_embed_url;
    } elseif (!empty($lat) && !empty($lng)) {
        $latF = (float) $lat;
        $lngF = (float) $lng;
        $d = 0.01;
        $bbox = implode(',', [
            $lngF - $d,
            $latF - $d,
            $lngF + $d,
            $latF + $d,
        ]);
        $mapEmbedUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' . rawurlencode($bbox) . '&layer=mapnik&marker=' . rawurlencode($latF . ',' . $lngF);
    }

    if (!empty($lat) && !empty($lng)) {
        $mapOpenUrl = 'https://www.google.com/maps?q=' . rawurlencode((float) $lat . ',' . (float) $lng);
    }

    $semesterSummary = ($subjects ?? collect())
        ->groupBy(fn ($s) => (string) ($s->semester ?? ''))
        ->map(function ($group) {
            return [
                'count' => $group->count(),
                'credits' => (int) $group->sum(fn ($s) => (float) ($s->credits ?? 0)),
            ];
        })
        ->sortKeys();

    $defaultSemester = $selectedSemester ?: ($semesterSummary->keys()->filter()->first() ?: 'all');

    $subjectPreviewMax = 3;

    $programsTitle = $department
        ? (($locale === 'ne' && !empty($department->programs_title_nepali)) ? $department->programs_title_nepali : $department->programs_title)
        : null;

    $programsContent = $department
        ? (($locale === 'ne' && !empty($department->programs_content_nepali)) ? $department->programs_content_nepali : $department->programs_content)
        : null;

    $defaultProgramsImage = asset('images/hero-image.jpg');
    $programsImageUrl = $defaultProgramsImage;
    if (!empty($department?->programs_image_path)) {
        $programImagePath = ltrim($department->programs_image_path, '/');
        try {
            if (Storage::disk('public')->exists($programImagePath)) {
                $programsImageUrl = Storage::disk('public')->url($programImagePath);
            } else {
                $programsImageUrl = asset('storage/' . $programImagePath);
            }
        } catch (\Throwable $e) {
            $programsImageUrl = asset('storage/' . $programImagePath);
        }
    }

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
        $downloadUrl = null;
        if (!empty($d->file_path)) {
            try {
                $url = Storage::disk('public')->url($d->file_path);
            } catch (\Throwable $e) {
                $url = asset('storage/' . ltrim($d->file_path, '/'));
            }

            $downloadUrl = route('materials.download', ['id' => $d->id]);
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
            'download_url' => $downloadUrl,
            'uploaded_at' => optional($d->uploaded_at ?? $d->created_at)?->format('Y-m-d'),
        ];
    })->values();

@endphp

@extends('layouts.public')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        :root {
            --landing-bg: linear-gradient(180deg, #fff5f5 0%, #fff1f2 26%, #fff8f8 58%, #fffdfd 100%);
            --landing-red: #dc2626;
            --landing-red-deep: #b91c1c;
            --landing-red-soft: #fca5a5;
            --landing-surface: rgba(255, 255, 255, 0.82);
            --landing-border: rgba(255, 255, 255, 0.65);
            --landing-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
        }

        .dark {
            --landing-bg: linear-gradient(180deg, #020617 0%, #111827 35%, #0f172a 100%);
            --landing-surface: rgba(15, 23, 42, 0.78);
            --landing-border: rgba(148, 163, 184, 0.14);
            --landing-shadow: 0 26px 70px rgba(2, 6, 23, 0.55);
        }

        .landing-shell {
            max-width: 100%;
        }

        .landing-panel {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--landing-border);
            background: var(--landing-surface);
            box-shadow: var(--landing-shadow);
            backdrop-filter: blur(16px);
        }

        .landing-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.24), transparent 42%);
        }

        .landing-grid-accent {
            position: relative;
        }

        .landing-grid-accent::after {
            content: "";
            position: absolute;
            inset: 1px;
            pointer-events: none;
            border-radius: inherit;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.14) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.14) 1px, transparent 1px);
            background-size: 24px 24px;
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.38), transparent 82%);
            opacity: 0.45;
        }

        .section-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            border-radius: 9999px;
            padding: 0.55rem 1rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .section-title {
            letter-spacing: -0.03em;
        }

        .float-orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(10px);
            opacity: 0.55;
            animation: floatOrb 10s ease-in-out infinite;
        }

        .stagger-rise {
            animation: riseIn 0.75s ease-out both;
        }

        .stagger-rise:nth-child(2) { animation-delay: 0.08s; }
        .stagger-rise:nth-child(3) { animation-delay: 0.16s; }
        .stagger-rise:nth-child(4) { animation-delay: 0.24s; }
        .stagger-rise:nth-child(5) { animation-delay: 0.32s; }

        .lift-card {
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease, background-color 220ms ease;
        }

        .lift-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.14);
        }

        .dark .lift-card:hover {
            box-shadow: 0 24px 48px rgba(2, 6, 23, 0.42);
        }

        .shine-button {
            position: relative;
            overflow: hidden;
        }

        .shine-button::after {
            content: "";
            position: absolute;
            inset: -120% auto auto -30%;
            width: 38%;
            height: 280%;
            transform: rotate(20deg);
            background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.45), transparent);
            opacity: 0;
            transition: opacity 200ms ease, transform 320ms ease;
        }

        .shine-button:hover::after {
            opacity: 1;
            transform: translateX(240%) rotate(20deg);
        }

        .leaflet-control-layers,
        .leaflet-control-zoom,
        .leaflet-control-scale {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50% { transform: translate3d(0, -14px, 0) scale(1.05); }
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="relative text-gray-900 dark:text-gray-100" style="background: var(--landing-bg);">
        <div class="pointer-events-none absolute inset-0 overflow-x-clip">
            <div class="float-orb left-[-5rem] top-28 h-36 w-36 bg-red-300/60 dark:bg-red-500/20"></div>
            <div class="float-orb right-[-3rem] top-[34rem] h-40 w-40 bg-red-200/55 [animation-delay:1.2s] dark:bg-red-400/20"></div>
            <div class="float-orb bottom-40 left-[12%] h-28 w-28 bg-red-400/45 [animation-delay:2.1s] dark:bg-red-500/20"></div>
        </div>
        <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-gray-900 dark:focus:bg-gray-900 dark:focus:text-gray-100">
            {{ $locale === 'ne' ? 'मुख्य सामग्रीमा जानुहोस्' : 'Skip to content' }}
        </a>

        <header class="sticky top-0 z-[1200] border-b border-white/10 bg-gradient-to-r from-red-700 via-red-600 to-red-800 shadow-[0_18px_45px_rgba(127,29,29,0.28)] backdrop-blur-xl">
            <div class="landing-shell mx-auto w-full px-4 py-1.5 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-1.5">
                        <div class="hidden items-center gap-4 text-xs font-medium text-white/75 xl:flex">
                            @if (!empty($department?->email))
                                <a href="mailto:{{ $department->email }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/8 px-3 py-0.5 ring-1 ring-white/10 transition hover:bg-white/12">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                        <path d="M1.5 6.75A2.25 2.25 0 0 1 3.75 4.5h16.5A2.25 2.25 0 0 1 22.5 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 17.25V6.75Zm2.25-.75a.75.75 0 0 0-.75.75v.243l7.5 4.5 7.5-4.5V6.75a.75.75 0 0 0-.75-.75H3.75Zm15.75 3.006-7.114 4.268a1.5 1.5 0 0 1-1.542 0L4.5 9.006v8.244c0 .414.336.75.75.75h15a.75.75 0 0 0 .75-.75V9.006Z"/>
                                    </svg>
                                    <span>{{ $department->email }}</span>
                                </a>
                            @endif
                            @if (!empty($department?->phone))
                                <a href="tel:{{ preg_replace('/\\s+/', '', $department->phone) }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/8 px-3 py-0.5 ring-1 ring-white/10 transition hover:bg-white/12">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                        <path fill-rule="evenodd" d="M6.648 2.25A2.25 2.25 0 0 0 4.5 4.5c0 9.113 7.387 16.5 16.5 16.5a2.25 2.25 0 0 0 2.25-2.148l.248-3.705a2.25 2.25 0 0 0-1.54-2.278l-3.405-1.136a2.25 2.25 0 0 0-2.56.94l-.724 1.086a18.11 18.11 0 0 1-5.028-5.028l1.086-.724a2.25 2.25 0 0 0 .94-2.56L11.13 2.54A2.25 2.25 0 0 0 8.852 1l-2.204.148Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $department->phone }}</span>
                                </a>
                            @endif
                            @if (!empty($addressText))
                                <div class="inline-flex max-w-xs items-center gap-1.5 truncate rounded-full bg-white/8 px-3 py-0.5 ring-1 ring-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5 shrink-0">
                                        <path fill-rule="evenodd" d="M11.54 22.351a.75.75 0 0 0 .92 0c4.884-3.73 7.29-7.15 7.29-10.601a7.75 7.75 0 1 0-15.5 0c0 3.45 2.406 6.87 7.29 10.6ZM12 12.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate">{{ $addressText }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="hidden items-center gap-2 lg:flex">
                            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-0.5 text-[11px] font-semibold text-white ring-1 ring-white/15">
                                <span class="h-2 w-2 rounded-full bg-red-200"></span>
                                <span>{{ $locale === 'ne' ? 'डिजिटल विभागीय पोर्टल' : 'Digital Department Portal' }}</span>
                            </div>
                            <div class="inline-flex items-center gap-3 rounded-full bg-white/8 px-3 py-0.5 text-[11px] font-semibold text-white/80 ring-1 ring-white/10">
                                <span>{{ number_format((int) ($stats['students'] ?? 0)) }} {{ $locale === 'ne' ? 'विद्यार्थी' : 'Students' }}</span>
                                <span class="h-1 w-1 rounded-full bg-white/40"></span>
                                <span>{{ number_format((int) ($stats['teachers'] ?? 0)) }} {{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</span>
                                <span class="h-1 w-1 rounded-full bg-white/40"></span>
                                <span>{{ number_format((int) ($stats['subjects'] ?? 0)) }} {{ $locale === 'ne' ? 'विषय' : 'Courses' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                            <div class="relative">
                                <img src="{{ $departmentLogoUrl }}" alt="{{ $departmentName }} logo" class="h-11 w-11 rounded-xl bg-white/10 object-contain ring-1 ring-white/20 shadow-lg shadow-red-900/15">
                                <span class="absolute -right-1 -top-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-white text-[8px] font-bold text-red-700 shadow-sm">IT</span>
                            </div>
                            <div class="min-w-0 leading-tight">
                                <div class="flex items-center gap-2">
                                    <div class="truncate text-sm font-bold text-white">{{ $departmentShort }}</div>
                                    <span class="hidden rounded-full bg-white/10 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-[0.18em] text-white/80 ring-1 ring-white/10 sm:inline-flex">
                                        {{ $locale === 'ne' ? 'आधिकारिक' : 'Official' }}
                                    </span>
                                </div>
                                <div class="truncate text-[11px] font-medium text-white/85 sm:text-xs">{{ $departmentName }}</div>
                            </div>
                        </a>

                        <nav class="hidden flex-1 items-center justify-center gap-1 xl:flex">
                            <a href="#about" class="rounded-full px-3 py-1 text-xs font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">{{ $locale === 'ne' ? 'बारेमा' : 'About' }}</a>
                            <a href="#programs" class="rounded-full px-3 py-1 text-xs font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">{{ $locale === 'ne' ? 'कार्यक्रम' : 'Programs' }}</a>
                            <a href="#curriculum" class="rounded-full px-3 py-1 text-xs font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">{{ $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum' }}</a>
                            <a href="#faculty" class="rounded-full px-3 py-1 text-xs font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</a>
                            <a href="#notices" class="rounded-full px-3 py-1 text-xs font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">{{ $locale === 'ne' ? 'सूचना' : 'News & Events' }}</a>
                            <a href="#resources" class="rounded-full px-3 py-1 text-xs font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">{{ $locale === 'ne' ? 'स्रोत' : 'Resources' }}</a>
                            <a href="#contact" class="rounded-full px-3 py-1 text-xs font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">{{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}</a>
                        </nav>

                        <div class="ml-auto flex items-center gap-2">
                            <a href="#contact" class="hidden items-center gap-2 rounded-full border border-white/15 bg-white/8 px-3 py-1 text-[11px] font-semibold text-white/90 transition hover:bg-white/12 lg:inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path d="M1.5 6.75A2.25 2.25 0 0 1 3.75 4.5h16.5A2.25 2.25 0 0 1 22.5 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 17.25V6.75Zm2.25-.75a.75.75 0 0 0-.75.75v.243l7.5 4.5 7.5-4.5V6.75a.75.75 0 0 0-.75-.75H3.75Zm15.75 3.006-7.114 4.268a1.5 1.5 0 0 1-1.542 0L4.5 9.006v8.244c0 .414.336.75.75.75h15a.75.75 0 0 0 .75-.75V9.006Z"/>
                                </svg>
                                <span>{{ $locale === 'ne' ? 'छिटो सम्पर्क' : 'Quick Contact' }}</span>
                            </a>

                            <form method="POST" action="{{ route('language.switch') }}" class="hidden sm:block">
                                @csrf
                                <label class="sr-only" for="localeSelect">{{ $locale === 'ne' ? 'भाषा' : 'Language' }}</label>
                                <select id="localeSelect" name="locale" onchange="this.form.submit()" class="rounded-xl border border-white/20 bg-slate-950/60 px-4 py-1 text-xs text-white shadow-sm focus:border-white/40 focus:ring-white/30">
                                    @foreach (config('locales.supported') as $code => $label)
                                        <option value="{{ $code }}" @selected($code === $locale)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>

                            <button id="darkModeToggle" type="button" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-slate-950/60 p-1 text-white shadow-sm hover:bg-slate-950/75 focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="{{ $locale === 'ne' ? 'डार्क मोड टगल' : 'Toggle dark mode' }}" aria-pressed="false">
                                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M21 14.1A8.5 8.5 0 0 1 9.9 3 7 7 0 1 0 21 14.1Z" />
                                </svg>
                                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="hidden h-5 w-5">
                                    <path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12ZM12 2.75a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V3.5a.75.75 0 0 1 .75-.75Zm0 16a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V19.5a.75.75 0 0 1 .75-.75ZM2.75 12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H3.5a.75.75 0 0 1-.75-.75Zm16 0a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM5.28 5.28a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 1 1-1.06 1.06L5.28 6.34a.75.75 0 0 1 0-1.06Zm11.26 11.26a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 0 1-1.06 1.06l-1.06-1.06a.75.75 0 0 1 0-1.06ZM18.72 5.28a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 1 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0ZM7.46 16.54a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 0 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0Z" />
                                </svg>
                            </button>

                            @auth
                                <a href="{{ route('dashboard') }}" class="shine-button hidden rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-red-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-white/40 sm:inline-flex">
                                    {{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="shine-button hidden rounded-full bg-slate-950 px-4 py-1.5 text-xs font-semibold text-white shadow-sm ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-white/40 sm:inline-flex">
                                    {{ $locale === 'ne' ? 'लगइन' : 'Login' }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main id="content">
            <section class="relative isolate overflow-hidden bg-slate-950" x-data="heroSlider({ slides: @js($heroSlides->values()->all()) })">
                <div class="absolute inset-0 -z-10">
                    <div class="relative h-full w-full">
                        <template x-for="(src, idx) in slides" :key="src">
                            <img :src="src" alt="" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700"
                                 :class="idx === active ? 'opacity-100' : 'opacity-0'" />
                        </template>
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-950/80 via-gray-950/55 to-gray-950/20"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/60 via-transparent to-transparent"></div>
                    </div>
                </div>

                <div class="landing-shell mx-auto w-full px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
                    <div class="max-w-4xl">
                        <p class="section-chip stagger-rise bg-white/12 text-white ring-1 ring-white/15">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-400 shadow-[0_0_18px_rgba(248,113,113,0.95)]"></span>
                            {{ $locale === 'ne' ? 'विभागीय पोर्टल' : 'Department Portal' }}
                        </p>

                        <h1 class="section-title stagger-rise mt-6 text-3xl font-bold text-white sm:text-5xl lg:text-6xl">
                            {{ $departmentName }}
                        </h1>
                        <p class="stagger-rise mt-5 max-w-3xl text-base leading-relaxed text-white/85 sm:text-lg">
                            {{ $tagline }}
                        </p>

                        <div class="stagger-rise mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <a href="#programs" class="shine-button inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-500 via-red-600 to-red-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/20 transition duration-200 hover:-translate-y-0.5 hover:from-red-400 hover:via-red-500 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                                <span>{{ $locale === 'ne' ? 'कार्यक्रम हेर्नुहोस्' : 'Explore Programs' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M12.72 6.72a.75.75 0 0 0-1.06 1.06L14.94 12l-3.28 4.22a.75.75 0 1 0 1.14.98l3.75-4.83a.75.75 0 0 0 0-.98l-3.75-4.83z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#curriculum" class="shine-button inline-flex items-center justify-center gap-2 rounded-xl bg-white/12 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/20 transition duration-200 hover:-translate-y-0.5 hover:bg-white/18 focus:outline-none focus:ring-2 focus:ring-white/40">
                                <span>{{ $locale === 'ne' ? 'विषयहरू ब्राउज' : 'Browse Subjects' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M12.72 6.72a.75.75 0 0 0-1.06 1.06L14.94 12l-3.28 4.22a.75.75 0 1 0 1.14.98l3.75-4.83a.75.75 0 0 0 0-.98l-3.75-4.83z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#contact" class="shine-button inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-red-700 shadow-lg shadow-red-900/10 transition duration-200 hover:-translate-y-0.5 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-200">
                                <span>{{ $locale === 'ne' ? 'सम्पर्क गर्नुहोस्' : 'Contact Us' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M12.72 6.72a.75.75 0 0 0-1.06 1.06L14.94 12l-3.28 4.22a.75.75 0 1 0 1.14.98l3.75-4.83a.75.75 0 0 0 0-.98l-3.75-4.83z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>

                        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div class="landing-grid-accent stagger-rise rounded-3xl bg-gradient-to-br from-red-500/30 to-red-300/15 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['students'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'विद्यार्थी' : 'Students' }}</div>
                            </div>
                            <div class="landing-grid-accent stagger-rise rounded-3xl bg-gradient-to-br from-red-600/30 to-red-300/15 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['teachers'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</div>
                            </div>
                            <div class="landing-grid-accent stagger-rise rounded-3xl bg-gradient-to-br from-red-400/30 to-red-200/15 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['subjects'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'विषय' : 'Subjects' }}</div>
                            </div>
                            <div class="landing-grid-accent stagger-rise rounded-3xl bg-gradient-to-br from-red-700/30 to-red-400/15 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-white">{{ number_format((int) ($stats['labs'] ?? 0)) }}</div>
                                <div class="mt-1 text-xs font-medium text-white/75">{{ $locale === 'ne' ? 'ल्याब' : 'Labs' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider controls: arrows left/right, dots bottom center -->
                <div x-show="slides.length > 1" class="pointer-events-none absolute inset-0">
                    <div class="pointer-events-auto absolute left-4 right-4 top-1/2 flex -translate-y-1/2 items-center justify-between sm:left-6 sm:right-6 lg:left-10 lg:right-10">
                        <button type="button"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-red-600 ring-1 ring-white/25 backdrop-blur hover:bg-white/25 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                @click="prev()" aria-label="Previous slide">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd" d="M15.78 4.22a.75.75 0 0 1 0 1.06L9.06 12l6.72 6.72a.75.75 0 1 1-1.06 1.06l-7.25-7.25a.75.75 0 0 1 0-1.06l7.25-7.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <button type="button"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-red-600 ring-1 ring-white/25 backdrop-blur hover:bg-white/25 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                @click="next()" aria-label="Next slide">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd" d="M8.22 19.78a.75.75 0 0 1 0-1.06L14.94 12 8.22 5.28a.75.75 0 1 1 1.06-1.06l7.25 7.25a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>

                    <div class="pointer-events-auto absolute bottom-6 left-1/2 -translate-x-1/2">
                        <div class="flex items-center gap-2 rounded-full bg-black/20 px-3 py-2 ring-1 ring-white/15 backdrop-blur">
                            <template x-for="(src, idx) in slides" :key="idx">
                                <button type="button"
                                        class="h-2.5 w-2.5 rounded-full ring-1 ring-white/60 transition"
                                        :class="idx === active ? 'bg-red-500 ring-red-300' : 'bg-red-100 hover:bg-red-300'"
                                        @click="go(idx)" :aria-label="`Slide ${idx+1}`"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </section>


    

            <section id="about" class="w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto grid w-full gap-10 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-7">
                        <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            {{ $locale === 'ne' ? 'परिचय' : 'Introduction' }}
                        </p>
                        <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
                            {{ $locale === 'ne' ? 'विभागको परिचय' : 'About the Department' }}
                        </h2>
                        <div class="landing-panel mt-5 overflow-hidden rounded-[2rem] p-5 dark:bg-slate-950/80" style="height: 500px;">
                            <div class="whitespace-pre-wrap text-sm font-bold leading-7 text-gray-700 dark:text-gray-300 overflow-hidden">
                                {{ $aboutText ?: ($locale === 'ne'
                                    ? 'यो विभागले विद्यार्थी, शिक्षक र अभिभावकका लागि एकीकृत सूचना प्रणालीमार्फत शैक्षिक व्यवस्थापनलाई डिजिटल बनाउँछ।'
                                    : 'This department portal brings academics, resources, and communication together for students, faculty, and parents.') }}
                            </div>
                        </div>
                        <div class="mt-4">
                                <a href="{{ route('department.about', ['id' => $department?->id ?? 1]) }}"
                               class="shine-button inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                <span>{{ $locale === 'ne' ? 'थप पढ्नुहोस्' : 'Read More' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M16.72 7.72a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L21.44 12l-4.72-4.28a.75.75 0 0 1 0-1.06zM12 7a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-1.5 0V7.75A.75.75 0 0 1 12 7z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="landing-panel lift-card rounded-3xl p-5 dark:bg-slate-950/80">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-300">{{ $locale === 'ne' ? 'स्थापना वर्ष' : 'Established' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $department?->established_year ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not available') }}
                                </div>
                            </div>
                            <div class="landing-panel lift-card rounded-3xl p-5 dark:bg-slate-950/80">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-300">{{ $locale === 'ne' ? 'दर्ता नं.' : 'Registration No.' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $department?->registration_number ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not available') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="landing-panel rounded-[2rem] bg-gradient-to-br from-white/90 via-red-50/70 to-white p-6 dark:from-slate-950/90 dark:via-slate-900 dark:to-slate-950">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $locale === 'ne' ? 'छिटो पहुँच' : 'Quick Access' }}</h3>
                            <div class="mt-5 grid gap-3">
                                <a href="#notices" class="landing-panel lift-card flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 ring-1 ring-red-100 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path d="M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h15A2.25 2.25 0 0 1 21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75Zm2.25-.75a.75.75 0 0 0-.75.75v.243l7.5 4.5 7.5-4.5V6.75a.75.75 0 0 0-.75-.75h-15Zm15.75 3.006-7.114 4.268a1.5 1.5 0 0 1-1.542 0L4.5 9.006v8.244c0 .414.336.75.75.75h15a.75.75 0 0 0 .75-.75V9.006Z"/>
                                        </svg>
                                    </span>
                                    <span>{{ $locale === 'ne' ? 'सूचना बोर्ड' : 'Notice Board' }}</span>
                                </a>
                                <a href="#resources" class="landing-panel lift-card flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 ring-1 ring-red-100 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path d="M6.75 2.25A2.25 2.25 0 0 0 4.5 4.5v15A2.25 2.25 0 0 0 6.75 21.75h10.5A2.25 2.25 0 0 0 19.5 19.5V7.5a.75.75 0 0 0-.22-.53l-4.5-4.5a.75.75 0 0 0-.53-.22H6.75Zm7.5 1.81L17.69 7.5h-2.94a.5.5 0 0 1-.5-.5V4.06Z"/>
                                        </svg>
                                    </span>
                                    <span>{{ $locale === 'ne' ? 'दस्तावेज/स्रोत' : 'Documents & Resources' }}</span>
                                </a>
                                <a href="{{ route('gallery.index') }}" class="landing-panel lift-card flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 ring-1 ring-red-100 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path d="M1.5 6.75A2.25 2.25 0 0 1 3.75 4.5h16.5A2.25 2.25 0 0 1 22.5 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 17.25V6.75ZM4.5 7.5a.75.75 0 0 0-.75.75v.5l4.286 3.214a.75.75 0 0 0 .9 0L13.5 8.25l4.564 3.709a.75.75 0 0 0 .936-.012L21 10.28V8.25a.75.75 0 0 0-.75-.75h-15.75Z"/>
                                        </svg>
                                    </span>
                                    <span>{{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}</span>
                                </a>
                                @auth
                                    <a href="{{ route('dashboard') }}" class="shine-button flex items-center gap-3 rounded-2xl bg-gradient-to-r from-red-500 via-red-600 to-red-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:from-red-400 hover:via-red-500 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/25">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path d="M11.25 3.75a.75.75 0 0 1 1.5 0v7.5h7.5a.75.75 0 0 1 0 1.5h-7.5v7.5a.75.75 0 0 1-1.5 0v-7.5h-7.5a.75.75 0 0 1 0-1.5h7.5v-7.5Z"/>
                                            </svg>
                                        </span>
                                        <span>{{ $locale === 'ne' ? 'ड्यासबोर्ड खोल्नुहोस्' : 'Open Dashboard' }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="shine-button flex items-center gap-3 rounded-2xl bg-gradient-to-r from-red-500 via-red-600 to-red-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:from-red-400 hover:via-red-500 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/25">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path fill-rule="evenodd" d="M7.5 3.75A3.75 3.75 0 0 1 11.25 0h1.5A3.75 3.75 0 0 1 16.5 3.75V9a.75.75 0 0 1-1.5 0V3.75c0-1.243-1.007-2.25-2.25-2.25h-1.5C10.007 1.5 9 2.507 9 3.75V9a.75.75 0 0 1-1.5 0V3.75Zm-6 9A2.25 2.25 0 0 1 3.75 10.5h16.5A2.25 2.25 0 0 1 22.5 12.75v7.5A3.75 3.75 0 0 1 18.75 24H5.25A3.75 3.75 0 0 1 1.5 20.25v-7.5Zm7.5 3a.75.75 0 0 0 0 1.5h6a.75.75 0 0 0 0-1.5h-6Z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                        <span>{{ $locale === 'ne' ? 'लगइन गरेर सुरु गर्नुहोस्' : 'Sign in to continue' }}</span>
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

            <section id="programs" class="w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full">
                    <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                        <div class="lg:col-span-5">
                            <div class="landing-panel rounded-[2rem]">
                                <img src="{{ $programsImageUrl ?: asset('images/hero-image.jpg') }}" alt="{{ $programsTitle ?: ($locale === 'ne' ? 'कार्यक्रम तस्वीर' : 'Program image') }}" class="h-72 w-full object-cover sm:h-80" onerror="this.onerror=null;this.src='{{ asset('images/hero-image.jpg') }}';" />
                            </div>
                        </div>
                        <div class="lg:col-span-7">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                        {{ $locale === 'ne' ? 'कार्यक्रम' : 'Programs' }}
                                    </p>
                                    <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
                                        {{ $programsTitle ?: ($locale === 'ne' ? 'शैक्षिक कार्यक्रम' : 'Academic Programs') }}
                                    </h2>
                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $locale === 'ne' ? 'कार्यक्रम र सिकाइ यात्राको छोटो परिचय।' : 'A short overview of our programs and learning path.' }}
                                    </p>
                                </div>
                                <a href="#curriculum" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                    <span>{{ $locale === 'ne' ? 'पाठ्यक्रम हेर्नुहोस्' : 'View Curriculum' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>

                            <div class="landing-panel mt-5 rounded-[2rem] bg-gradient-to-br from-white/90 via-red-50/70 to-white p-6 text-sm leading-7 text-gray-700 dark:from-slate-950/90 dark:via-slate-900 dark:to-slate-950 dark:text-gray-200">
                                {!! nl2br(e($programsContent ?: ($locale === 'ne'
                                    ? 'हाम्रो विभागले विद्यार्थीहरूको सीप विकास, व्यावहारिक प्रयोगशाला अभ्यास र उद्योगसँग जोडिएको सिकाइलाई प्राथमिकता दिने शैक्षिक कार्यक्रमहरू सञ्चालन गर्दछ।'
                                    : 'Our department runs academic programs focused on practical learning, lab-based skills, and industry-ready outcomes.'))) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="curriculum" class="w-full px-4 py-16 sm:px-6 lg:px-8" x-data="subjectCatalog({
                initialSemester: @js($defaultSemester),
                maxItems: @js($subjectPreviewMax),
                indexUrl: @js(route('subjects.index')),
                subjects: @js($subjectPayload),
                locale: @js($locale),
            })">
                <div class="landing-shell mx-auto w-full">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ $locale === 'ne' ? 'हाम्रा पाठ्यक्रम' : 'Our Courses' }}
                            </h2>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $locale === 'ne' ? 'सेमेस्टर अनुसार विषयहरू हेर्नुहोस्।' : 'Browse semester-wise course highlights.' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    @foreach ($semesterSummary->take(4) as $sem => $meta)
                        @continue(empty($sem))
                        <button type="button"
                            class="rounded-xl border px-4 py-2 text-sm font-semibold shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                            :class="String(semester) === @js((string) $sem) ? 'border-red-600 bg-red-600 text-white shadow-red-900/10 dark:border-red-400 dark:bg-red-400 dark:text-gray-950' : 'border-white/60 bg-white/80 text-gray-700 hover:-translate-y-0.5 hover:bg-white dark:border-gray-800 dark:bg-slate-950/80 dark:text-gray-200 dark:hover:bg-slate-900'"
                            @click="semester=@js((string) $sem); query=''; openId=null;">
                            {{ $locale === 'ne' ? "{$sem} सेमेस्टर" : "Semester {$sem}" }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <template x-for="s in visibleSubjects" :key="s.id">
                        <div class="landing-panel lift-card flex flex-col overflow-hidden rounded-[1.75rem] border-l-4 bg-gradient-to-br from-red-50 via-white to-red-100 duration-300 dark:bg-gradient-to-br dark:from-slate-950/95 dark:via-red-950/25 dark:to-slate-950" 
                             :class="[
                                 s.has_lab ? 'border-l-red-500 dark:border-l-red-400' : 'border-l-red-300 dark:border-l-red-500'
                             ]">
                            <div class="flex items-center justify-between border-b border-red-100 bg-gradient-to-r px-6 py-3 dark:border-red-900/30"
                                 :class="s.has_lab ? 'from-red-200 to-red-100 dark:from-red-900/40 dark:to-red-950/20' : 'from-red-100 to-red-50 dark:from-red-950/25 dark:to-slate-950/30'">
                                <div class="text-base font-bold text-gray-900 dark:text-gray-100" x-text="s.title"></div>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" :class="s.has_lab ? 'text-red-600 dark:text-red-400' : 'text-red-400 dark:text-red-300'">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <div class="p-6">
                                <div class="mt-2 text-xs font-semibold uppercase tracking-wide" :class="s.has_lab ? 'text-red-700 dark:text-red-300' : 'text-red-500 dark:text-red-300'">
                                    <span x-text="((s.teachers || [])[0]) ? s.teachers[0] : (locale === 'ne' ? 'शिक्षक तोकिएको छैन' : 'Instructor TBA')"></span>
                                    <span class="mx-2 inline">•</span>
                                    <span x-text="(s.has_lab || String(s.type || '').toLowerCase().includes('lab')) ? (locale === 'ne' ? 'प्रयोगशाला' : 'Lab') : (locale === 'ne' ? 'सिद्धान्त' : 'Theory')"></span>
                                </div>
                                <div class="mt-3 line-clamp-3 text-sm text-gray-700 dark:text-gray-300" x-text="s.description || fallbackDescription"></div>

                                <div x-show="openId === s.id" x-cloak class="mt-4 rounded-xl border border-red-200 bg-gradient-to-br from-red-100 to-white p-4 text-sm text-gray-700 dark:border-red-900/40 dark:from-red-950/30 dark:to-slate-950/40 dark:text-gray-200">
                                    <div class="flex flex-wrap gap-2 text-xs font-semibold">
                                        <template x-if="s.code">
                                            <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300" x-text="s.code"></span>
                                        </template>
                                        <template x-if="s.credits !== null && s.credits !== undefined">
                                            <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300" x-text="creditText(s.credits)"></span>
                                        </template>
                                        <template x-if="s.category">
                                            <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300" x-text="s.category"></span>
                                        </template>
                                    </div>
                                    <template x-if="s.prerequisite">
                                        <div class="mt-3 text-sm">
                                            <span class="font-semibold">{{ $locale === 'ne' ? 'पूर्व-आवश्यकता' : 'Prerequisite' }}:</span>
                                            <span x-text="s.prerequisite"></span>
                                        </div>
                                    </template>
                                </div>

                                <div class="mt-5 flex items-center justify-center">
                                    <button type="button"
                                   class="inline-flex w-28 items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition duration-200"
                                        :class="openId === s.id 
                                            ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60'
                                            : 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-950/30 dark:text-red-200 dark:hover:bg-red-950/50'"
                                        focus:outline-none focus:ring-2 focus:ring-red-500
                                        @click="toggle(s.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path fill-rule="evenodd" d="M2.25 12c0-6.215 5.785-11.25 12-11.25s12 5.035 12 11.25S19.215 23.25 12 23.25 2.25 18.215 2.25 12zm8.695-1.275a.75.75 0 00-1.19.53v.003c0 .141.048.274.135.38l4.147 5.411a.75.75 0 001.219-.06l6.144-7.7a.75.75 0 10-1.176-.936L12.75 16.8 10.945 10.755z" clip-rule="evenodd" />
                                        </svg>
                                        <span x-text="openId === s.id ? (locale === 'ne' ? 'बन्द' : 'Close') : (locale === 'ne' ? 'जानकारी' : 'Info')"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="visibleSubjects.length === 0" class="landing-panel rounded-[1.75rem] border-l-4 border-l-red-300 bg-gradient-to-br from-red-50 to-white p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:from-red-950/20 dark:to-slate-950/40 dark:text-gray-300 md:col-span-3">
                        {{ $locale === 'ne' ? 'हाल कुनै विषय उपलब्ध छैन।' : 'No courses available yet.' }}
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <a :href="viewAllUrl"
                       class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                        <span>{{ $locale === 'ne' ? 'सबै पाठ्यक्रम हेर्नुहोस्' : 'Explore All Courses' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                {{--
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
                                <template x-for="s in visibleSubjects" :key="s.id">
                                    <div class="px-6 py-4">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-900 dark:text-gray-200" x-text="s.code || '—'"></span>
                                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300" x-text="semesterLabel(s.semester)"></span>
                                                    <template x-if="s.has_lab">
                                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $locale === 'ne' ? 'ल्याब' : 'Lab' }}</span>
                                                    </template>
                                                    <template x-if="s.is_elective_open">
                                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $locale === 'ne' ? 'इलेक्टिभ' : 'Elective' }}</span>
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

                            <div x-show="showViewAll" class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                                <a :href="viewAllUrl"
                                   class="shine-button inline-flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                    <span>{{ $locale === 'ne' ? 'सबै विषय हेर्नुहोस्' : 'View all subjects' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
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
                --}}
            </section>

            <section id="faculty" class="w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ $locale === 'ne' ? 'हाम्रा शिक्षक' : 'Meet Our Faculty' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $locale === 'ne' ? 'विभागका सक्रिय शिक्षकहरूको झलक।' : 'Meet our active department faculty.' }}
                            </p>
                        </div>
                    </div>

                    @if (($hods ?? collect())->isNotEmpty())
                        <div class="mt-8">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $locale === 'ne' ? 'विभाग प्रमुख (HOD)' : 'HOD' }}
                                </div>
                                <a href="#contact" class="inline-flex items-center gap-2 text-xs font-semibold text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    {{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>

                            <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                                @foreach (($hods ?? collect()) as $leader)
                                    @php
                                        $leaderName = $leader->name ?: 'Admin';
                                        $leaderInitial = Str::of($leaderName)->trim()->substr(0, 1)->upper();
                                        $leaderDept = $leader->department ?? null;
                                        $leaderPhoto = $leader->profile_photo_url ?? null;
                                        $leaderMeta = $leaderDept ?: ($leader->email ?: null);
                                    @endphp
                                    <div class="landing-panel lift-card overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-red-50 via-white to-red-100 dark:bg-gradient-to-br dark:from-slate-950/95 dark:via-red-950/25 dark:to-slate-950">
                                        <div class="flex h-64 items-center justify-center bg-gradient-to-br from-red-300 via-red-400 to-red-600 dark:from-red-900/50 dark:via-red-800/40 dark:to-red-950/40">
                                            @if (!empty($leaderPhoto))
                                                <img src="{{ $leaderPhoto }}" alt="{{ $leaderName }}" class="h-full w-full object-cover" />
                                            @else
                                                <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-white/20 text-white ring-1 ring-white/25 dark:bg-white/10 dark:text-red-100">
                                                    <span class="text-sm font-bold">{{ $leaderInitial }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="border-t border-red-100 bg-gradient-to-br from-red-100 via-red-50 to-white p-5 dark:border-red-900/30 dark:bg-gradient-to-br dark:from-red-950/35 dark:via-slate-950 dark:to-slate-950">
                                            <div class="truncate text-sm font-bold text-gray-900 dark:text-gray-100">{{ $leaderName }}</div>
                                            <div class="mt-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $locale === 'ne' ? 'विभाग प्रमुख' : 'HOD / Admin' }}</div>
                                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                                {{ $locale === 'ne' ? 'विवरण' : 'Detail' }}:
                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $leaderMeta ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not specified') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @forelse (($teachers ?? collect())->take(4) as $t)
                            @php
                                $name = $t->user?->name ?: 'Unknown';
                                $initials = Str::of($name)->trim()->explode(' ')->filter()->take(2)->map(fn ($p) => Str::substr($p, 0, 1))->join('');
                                $photoUrl = $t->user?->profile_photo_url;
                                $titleText = $t->qualification ?: ($locale === 'ne' ? 'शिक्षक' : 'Professor');
                                $expertiseText = $t->bio ?: ($t->department ?: ($t->user?->department ?: null));
                            @endphp
                            <div class="landing-panel lift-card overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-red-50 via-white to-red-100 dark:bg-gradient-to-br dark:from-slate-950/95 dark:via-red-950/25 dark:to-slate-950">
                                <div class="flex h-64 items-center justify-center bg-gradient-to-br from-red-200 via-red-300 to-red-500 dark:from-red-900/45 dark:via-red-800/35 dark:to-red-950/35">
                                    @if (!empty($photoUrl))
                                        <img src="{{ $photoUrl }}" alt="{{ $name }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-white/20 text-white ring-1 ring-white/25 dark:bg-white/10 dark:text-red-100">
                                            <span class="text-sm font-bold">{{ $initials ?: Str::of($name)->substr(0, 1)->upper() }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="border-t border-red-100 bg-gradient-to-br from-red-100 via-red-50 to-white p-5 dark:border-red-900/30 dark:bg-gradient-to-br dark:from-red-950/35 dark:via-slate-950 dark:to-slate-950">
                                    <div class="truncate text-sm font-bold text-gray-900 dark:text-gray-100">{{ $name }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $titleText }}</div>
                                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                        {{ $locale === 'ne' ? 'विशेषज्ञता' : 'Expertise Area' }}:
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $expertiseText ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not specified') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="landing-panel rounded-[1.75rem] border-dashed border-red-200 bg-gradient-to-br from-white to-red-50 p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:bg-gradient-to-br dark:from-slate-950 dark:to-red-950/10 dark:text-gray-300 sm:col-span-2 lg:col-span-4">
                                {{ $locale === 'ne' ? 'हाल कुनै शिक्षक उपलब्ध छैन।' : 'No faculty records available yet.' }}
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('faculty.index') }}"
                           class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                            <span>{{ $locale === 'ne' ? 'सबै शिक्षक हेर्नुहोस्' : 'View All Faculty' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>

            <section id="notices" class="w-full px-4 py-16 sm:px-6 lg:px-8" x-data="{ open: false, notice: null }">
                <div class="landing-shell mx-auto w-full">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            {{ $locale === 'ne' ? 'सूचना' : 'Updates' }}
                        </p>
                        <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
                            {{ $locale === 'ne' ? 'समाचार तथा कार्यक्रम' : 'News & Events' }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $locale === 'ne' ? 'नयाँ सूचना र घोषणा।' : 'Latest announcements and updates.' }}
                        </p>
                    </div>
                    <a href="{{ route('public.notices.index') }}" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                        <span>{{ $locale === 'ne' ? 'सबै सूचना हेर्नुहोस्' : 'View all notices' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse (($notices ?? collect()) as $n)
                        <button type="button" class="landing-panel lift-card text-left rounded-[1.75rem] bg-gradient-to-br from-red-50 via-white to-red-100 p-6 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gradient-to-br dark:from-slate-950/95 dark:via-red-950/25 dark:to-slate-950"
                            @click="notice=@js([
                                'title' => $n->localized_title,
                                'date' => $n->formatted_date,
                                'audience' => $n->localized_audience_label,
                                'priority' => $n->localized_priority_label,
                                'full' => $n->localized_message,
                            ]); open=true;">
                            <div class="flex items-start justify-between gap-4 border-b border-red-100 pb-4 dark:border-red-900/25">
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
                                <span class="inline-flex items-center gap-2 rounded-full bg-red-100 px-3 py-1 text-red-700 dark:bg-red-950/40 dark:text-red-300">
                                    <span>{{ $locale === 'ne' ? 'पढ्नुहोस्' : 'Read' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3">
                                        <path d="M13.207 3.793a1 1 0 0 1 1.414 0l6 6a1 1 0 0 1 0 1.414l-6 6a1 1 0 0 1-1.414-1.414L17.586 11H4a1 1 0 1 1 0-2h13.586l-4.379-4.379a1 1 0 0 1 0-1.414z"/>
                                    </svg>
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="landing-panel rounded-[1.75rem] border-dashed border-red-200 bg-gradient-to-br from-white to-red-50 p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:bg-gradient-to-br dark:from-slate-950 dark:to-red-950/10 dark:text-gray-300 md:col-span-2 lg:col-span-3">
                            {{ $locale === 'ne' ? 'हाल कुनै सूचना छैन।' : 'No notices published yet.' }}
                        </div>
                    @endforelse
                </div>

                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true">
                    <div class="absolute inset-0 bg-gray-950/70" @click="open=false" aria-hidden="true"></div>
                    <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-gradient-to-br from-white via-red-50/80 to-red-100/60 shadow-xl ring-1 ring-red-100 dark:bg-gradient-to-br dark:from-slate-950/95 dark:via-red-950/20 dark:to-slate-950 dark:ring-red-900/30">
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

            <section id="resources" class="w-full px-4 py-16 sm:px-6 lg:px-8" x-data="documentRepo({ documents: @js($documentPayload), locale: @js($locale) })">
                <div class="landing-shell mx-auto w-full">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'स्रोत' : 'Resources' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
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
                            <a href="{{ route('public.resources.index') }}" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                <span>{{ $locale === 'ne' ? 'सबै स्रोत हेर्नुहोस्' : 'View all resources' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="d in filtered" :key="d.id">
                            <div class="landing-panel lift-card overflow-hidden rounded-[1.75rem] border-l-4 bg-gradient-to-br from-white via-red-50/80 to-red-100/60 transition duration-300 dark:bg-gradient-to-br dark:from-slate-950/90 dark:via-red-950/20 dark:to-slate-950"
                                 :class="[
                                     d.type === 'syllabus' ? 'border-l-red-500 dark:border-l-red-400' : 
                                     d.type === 'notes' ? 'border-l-red-400 dark:border-l-red-300' : 
                                     d.type === 'guide' ? 'border-l-red-600 dark:border-l-red-500' : 
                                     'border-l-red-300 dark:border-l-red-400'
                                 ]">
                                <div class="flex h-48 items-center justify-center transition duration-300" 
                                     :class="[
                                         d.type === 'syllabus' ? 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-950/30 dark:to-red-900/20' : 
                                         d.type === 'notes' ? 'bg-gradient-to-br from-red-50 to-white dark:from-red-950/20 dark:to-red-900/10' : 
                                         d.type === 'guide' ? 'bg-gradient-to-br from-red-100 to-red-50 dark:from-red-950/30 dark:to-red-900/20' : 
                                         'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-950/20 dark:to-red-900/15'
                                     ]">
                                    <div class="flex flex-col items-center justify-center gap-3"
                                         :class="[
                                             d.type === 'syllabus' ? 'text-red-400 dark:text-red-500' : 
                                            d.type === 'notes' ? 'text-red-400 dark:text-red-400' : 
                                            d.type === 'guide' ? 'text-red-600 dark:text-red-500' : 
                                            'text-red-300 dark:text-red-300'
                                         ]">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-20 w-20">
                                            <path d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 .621-.504 1.125-1.125 1.125H1.5V5.25a3.75 3.75 0 0 1 4.125-3.75Z" />
                                            <path fill-rule="evenodd" d="M23.25 7.5a.75.75 0 0 1-.75.75H.75a.75.75 0 0 1-.75-.75v-8a.75.75 0 0 1 .75-.75h22.5a.75.75 0 0 1 .75.75v8Zm-3-5.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" clip-rule="evenodd" />
                                            <path fill-rule="evenodd" d="M.75 14.25a.75.75 0 0 0 .75.75h22.5a.75.75 0 0 0 .75-.75v-6a.75.75 0 0 0-.75-.75H1.5a.75.75 0 0 0-.75.75v6Zm22.5 6a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 0-.75-.75H1.5a.75.75 0 0 0-.75.75v2a.75.75 0 0 0 .75.75h22.5Z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xs font-semibold uppercase tracking-wide" x-text="d.type_label"></span>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide transition duration-200"
                                              :class="[
                                                  d.type === 'syllabus' ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300' : 
                                                  d.type === 'notes' ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300' : 
                                                  d.type === 'guide' ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300' : 
                                                  'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300'
                                              ]"
                                              x-text="d.type_label"></span>
                                        <template x-if="d.size">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="d.size"></span>
                                        </template>
                                    </div>
                                    <div class="mt-3 line-clamp-2 text-sm font-bold text-gray-900 dark:text-gray-100" x-text="d.title"></div>
                                    <template x-if="d.subject">
                                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                            <span class="font-medium text-gray-900 dark:text-gray-200" x-text="d.subject"></span>
                                        </div>
                                    </template>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-text="uploadedText(d.uploaded_at)"></div>
                                    <div class="mt-4 flex items-center gap-2">
                                        <template x-if="d.url">
                                            <a :href="d.url" target="_blank" rel="noopener" class="shine-button flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:from-red-400 hover:to-red-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                                </svg>
                                                {{ $locale === 'ne' ? 'हेर्नुहोस्' : 'View' }}
                                            </a>
                                        </template>
                                        <template x-if="!d.url">
                                            <span class="flex-1 inline-flex items-center justify-center rounded-lg border border-red-100 bg-red-50/70 px-3 py-2 text-xs font-semibold text-red-300 dark:border-red-900/30 dark:bg-red-950/20 dark:text-red-300/70">
                                                {{ $locale === 'ne' ? 'उपलब्ध छैन' : 'N/A' }}
                                            </span>
                                        </template>

                                        <template x-if="d.download_url">
                                            <a :href="d.download_url" class="landing-panel lift-card flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path fill-rule="evenodd" d="M12 2a.75.75 0 0 1 .75.75v6.69l1.97-1.97a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L7.97 8.53a.75.75 0 0 1 1.06-1.06l1.97 1.97V2.75A.75.75 0 0 1 12 2zM3 14.25a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 14.25v-3.5a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75v-3.5a.75.75 0 0 0-1.5 0v3.5z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $locale === 'ne' ? 'डाउनलोड' : 'Download' }}
                                            </a>
                                        </template>
                                        <template x-if="!d.download_url">
                                            <span class="flex-1 inline-flex items-center justify-center rounded-lg border border-red-100 bg-red-50/70 px-3 py-2 text-xs font-semibold text-red-300 dark:border-red-900/30 dark:bg-red-950/20 dark:text-red-300/70">
                                                {{ $locale === 'ne' ? 'डाउनलोड छैन' : 'No download' }}
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="filtered.length === 0">
                        <div class="landing-panel rounded-[1.75rem] border-l-4 border-l-red-300 bg-gradient-to-br from-red-50 to-white p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:from-red-950/20 dark:to-slate-950/40 dark:text-gray-300 sm:col-span-2 lg:col-span-3">
                                {{ $locale === 'ne' ? 'कुनै सामग्री भेटिएन।' : 'No materials found.' }}
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <section class="w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}
                            </h2>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $locale === 'ne' ? 'विभागका गतिविधि र सुविधाका झलकहरू।' : 'Highlights from department events and facilities.' }}
                        </p>
                    </div>
                    <a href="{{ route('gallery.index') }}" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-red-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                        <span>{{ $locale === 'ne' ? 'सबै ग्यालरी' : 'View all' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @forelse (($galleryItems ?? collect()) as $g)
                        <a href="{{ route('gallery.index', ['category' => $g->category]) }}" class="landing-panel group relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-white via-red-50/60 to-red-100/60 dark:bg-gradient-to-br dark:from-slate-950/90 dark:via-red-950/20 dark:to-slate-950">
                            @if ($g->image_url)
                                <img src="{{ $g->image_url }}" alt="{{ $g->title }}" class="h-44 w-full object-cover transition duration-300 group-hover:scale-105 sm:h-48">
                            @else
                                <div class="flex h-44 items-center justify-center text-sm text-gray-500 dark:text-gray-400 sm:h-48">
                                    {{ $locale === 'ne' ? 'तस्विर छैन' : 'No image' }}
                                </div>
                            @endif
                            @if ($g->image_url)
                                <div class="pointer-events-none absolute inset-0 opacity-0 transition group-hover:opacity-100">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/65 via-gray-950/10 to-transparent"></div>
                                    <div class="pointer-events-auto absolute right-3 top-3 flex gap-2">
                                        <button type="button"
                                            onclick="event.preventDefault(); event.stopPropagation(); window.open(@js($g->image_url), '_blank', 'noopener');"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl bg-white/90 px-3 py-2 text-xs font-semibold text-gray-900">
                                            {{ $locale === 'ne' ? 'हेर्नुहोस्' : 'View' }}
                                        </button>
                                        <button type="button"
                                            onclick="event.preventDefault(); event.stopPropagation(); window.location.href = @js(route('gallery.download', ['id' => $g->id]));"
                                            class="shine-button inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                            {{ $locale === 'ne' ? 'डाउनलोड' : 'Download' }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-gray-950/70 via-gray-950/35 to-transparent p-4">
                                <div class="truncate text-sm font-semibold text-white">{{ $g->title }}</div>
                                <div class="mt-1 text-xs font-medium text-white/80">{{ $g->category_text }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="landing-panel rounded-[1.75rem] border-dashed p-10 text-center text-sm text-gray-600 dark:text-gray-300 sm:col-span-3 lg:col-span-4">
                            {{ $locale === 'ne' ? 'हाल ग्यालरी सामग्री छैन।' : 'No gallery items available yet.' }}
                        </div>
                    @endforelse
                </div>
            </section>

            <section id="contact" class="w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full">
                    <div>
                        <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            {{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}
                        </p>
                        <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">
                            {{ $locale === 'ne' ? 'सम्पर्क तथा सहयोग' : 'Contact & Support' }}
                        </h2>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-700 dark:text-gray-300">
                            {{ $locale === 'ne'
                                ? 'कुनै प्रश्न, सुझाव वा सहयोगका लागि हामीलाई सम्पर्क गर्नुहोस्।'
                                : 'Reach out for questions, suggestions, or support.' }}
                        </p>

                        <div class="landing-panel mt-8 overflow-hidden rounded-[2rem] bg-white/85 dark:bg-slate-950/80">
                            @if (!empty($lat) && !empty($lng))
                                <div class="relative">
                                    <div id="departmentMap"
                                        class="h-[22rem] w-full sm:h-[26rem] lg:h-[32rem]"
                                        data-lat="{{ (float) $lat }}"
                                        data-lng="{{ (float) $lng }}"
                                        data-name="{{ e($departmentName) }}"
                                        data-label="{{ e($mapLabel) }}"></div>

                                    <div class="absolute bottom-3 left-3 z-[500] flex flex-wrap gap-2 rounded-2xl bg-white/80 p-2 shadow-sm backdrop-blur-md dark:bg-gray-950/80">
                                        <button type="button" data-map-action="locate"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-100">
                                            {{ $locale === 'ne' ? 'मेरो स्थान' : 'My Location' }}
                                        </button>
                                        <button type="button" data-map-action="layers"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-100">
                                            {{ $locale === 'ne' ? 'लेयर' : 'Layers' }}
                                        </button>
                                        <button type="button" data-map-action="reset"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-100">
                                            {{ $locale === 'ne' ? 'रीसेट' : 'Reset' }}
                                        </button>
                                        <button type="button" data-map-action="copy"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-100">
                                            {{ $locale === 'ne' ? 'कपी' : 'Copy' }}
                                        </button>

                                        @if (!empty($mapOpenUrl))
                                            <a href="{{ $mapOpenUrl }}" target="_blank" rel="noopener"
                                                class="shine-button inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                                {{ $locale === 'ne' ? 'खोल्नुहोस्' : 'Open' }}
                                            </a>
                                        @endif
                                        <button type="button" data-map-action="directions"
                                            class="shine-button inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                            {{ $locale === 'ne' ? 'दिशा' : 'Directions' }}
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="flex h-[22rem] items-center justify-center bg-gray-100 text-sm text-gray-600 dark:bg-gray-900 dark:text-gray-300 sm:h-[26rem] lg:h-[32rem]">
                                    {{ $locale === 'ne' ? 'नक्सा डेटा उपलब्ध छैन' : 'Map data not available' }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="landing-panel lift-card rounded-[1.75rem] p-6 dark:bg-slate-950/80">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'इमेल' : 'Email' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    @if (!empty($department?->email))
                                        <a class="text-red-700 hover:underline dark:text-red-400" href="mailto:{{ $department->email }}">{{ $department->email }}</a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">support@example.edu</span>
                                    @endif
                                </div>
                            </div>
                            <div class="landing-panel lift-card rounded-[1.75rem] p-6 dark:bg-slate-950/80">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'फोन' : 'Phone' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $department?->phone ?: '+977-000-0000000' }}
                                </div>
                            </div>
                            <div class="landing-panel lift-card rounded-[1.75rem] p-6 dark:bg-slate-950/80">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'ठेगाना' : 'Address' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $addressText ?: ($locale === 'ne' ? 'विभागीय कार्यालय' : 'Department office') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="border-t border-white/10 bg-gradient-to-b from-gray-950 via-slate-950 to-black py-12 text-white">
                <div class="landing-shell mx-auto w-full px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-8 md:grid-cols-12">
                        <div class="md:col-span-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $departmentLogoUrl }}" alt="" class="h-10 w-10 rounded-lg bg-white/5 object-contain ring-1 ring-white/10">
                                <div>
                                    <div class="text-sm font-semibold text-white">{{ $departmentName }}</div>
                                    <div class="mt-1 text-xs text-white/70">{{ config('app.name', 'IT-DMS') }}</div>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-white/75">
                                {{ $locale === 'ne'
                                    ? 'विद्यार्थी, शिक्षक र अभिभावकका लागि विभागीय सेवा र स्रोतहरू।'
                                    : 'Department services and resources for students, faculty, and parents.' }}
                            </p>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                @if (!empty($department?->website))
                                    <a href="{{ Str::startsWith($department->website, ['http://', 'https://']) ? $department->website : 'https://' . ltrim($department->website, '/') }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 rounded-full bg-white/5 px-4 py-2 text-xs font-semibold text-white ring-1 ring-white/10 hover:bg-white/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75S6.615 21.75 12 21.75 21.75 17.385 21.75 12 17.385 2.25 12 2.25Zm-1.5 16.2a8.25 8.25 0 0 1 0-12.9v.2c0 2.2.75 4.68 2.25 6.45-1.5 1.77-2.25 4.25-2.25 6.45v-.2Zm3 0v.2a8.25 8.25 0 0 0 0-12.9v.2c0 2.2-.75 4.68-2.25 6.45 1.5 1.77 2.25 4.25 2.25 6.45Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $locale === 'ne' ? 'वेबसाइट' : 'Website' }}</span>
                                    </a>
                                @endif
                                @if (!empty($department?->email))
                                    <a href="mailto:{{ $department->email }}" class="inline-flex items-center gap-2 rounded-full bg-white/5 px-4 py-2 text-xs font-semibold text-white ring-1 ring-white/10 hover:bg-white/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path d="M1.5 6.75A2.25 2.25 0 0 1 3.75 4.5h16.5A2.25 2.25 0 0 1 22.5 6.75v10.5A2.25 2.25 0 0 1 20.25 19.5H3.75A2.25 2.25 0 0 1 1.5 17.25V6.75Zm2.25-.75a.75.75 0 0 0-.75.75v.243l7.5 4.5 7.5-4.5V6.75a.75.75 0 0 0-.75-.75H3.75Z"/>
                                        </svg>
                                        <span>{{ $locale === 'ne' ? 'इमेल' : 'Email' }}</span>
                                    </a>
                                @endif
                                @if (!empty($department?->phone))
                                    <a href="tel:{{ preg_replace('/\\s+/', '', $department->phone) }}" class="inline-flex items-center gap-2 rounded-full bg-white/5 px-4 py-2 text-xs font-semibold text-white ring-1 ring-white/10 hover:bg-white/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path fill-rule="evenodd" d="M1.5 4.5A3 3 0 0 1 4.5 1.5h.75A2.25 2.25 0 0 1 7.5 3.75v2.26a2.25 2.25 0 0 1-1.318 2.046l-.85.377a.75.75 0 0 0-.427.896 12.02 12.02 0 0 0 7.767 7.767.75.75 0 0 0 .896-.427l.377-.85A2.25 2.25 0 0 1 17.99 16.5h2.26A2.25 2.25 0 0 1 22.5 18.75v.75a3 3 0 0 1-3 3H18c-9.113 0-16.5-7.387-16.5-16.5V4.5Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $locale === 'ne' ? 'फोन' : 'Phone' }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <div class="text-sm font-semibold text-white">{{ $locale === 'ne' ? 'छिटो लिंक' : 'Quick Links' }}</div>
                            <ul class="mt-4 space-y-2 text-sm text-white/75">
                                <li><a class="inline-flex items-center gap-2 hover:text-white" href="#about"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>{{ $locale === 'ne' ? 'बारेमा' : 'About' }}</a></li>
                                <li><a class="inline-flex items-center gap-2 hover:text-white" href="#curriculum"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>{{ $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum' }}</a></li>
                                <li><a class="inline-flex items-center gap-2 hover:text-white" href="#notices"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>{{ $locale === 'ne' ? 'सूचना' : 'Notices' }}</a></li>
                                <li><a class="inline-flex items-center gap-2 hover:text-white" href="{{ route('gallery.index') }}"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>{{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}</a></li>
                            </ul>
                        </div>

                        <div class="md:col-span-2">
                            <div class="text-sm font-semibold text-white">{{ $locale === 'ne' ? 'खाता' : 'Account' }}</div>
                            <ul class="mt-4 space-y-2 text-sm text-white/75">
                                @auth
                                    <li><a class="inline-flex items-center gap-2 hover:text-white" href="{{ route('dashboard') }}"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>{{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}</a></li>
                                @else
                                    <li><a class="inline-flex items-center gap-2 hover:text-white" href="{{ route('login') }}"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>{{ $locale === 'ne' ? 'लगइन' : 'Login' }}</a></li>
                                    @if (Route::has('register'))
                                        <li><a class="inline-flex items-center gap-2 hover:text-white" href="{{ route('register') }}"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>{{ $locale === 'ne' ? 'दर्ता' : 'Register' }}</a></li>
                                    @endif
                                @endauth
                            </ul>
                        </div>

                        <div class="md:col-span-2">
                            <div class="text-sm font-semibold text-white">{{ $locale === 'ne' ? 'सामाजिक' : 'Connect' }}</div>
                            <div class="mt-4 flex items-center gap-2">
                                @php
                                    $mapHref = $mapOpenUrl ?? null;
                                @endphp
                                @if (!empty($mapHref))
                                    <a href="{{ $mapHref }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 hover:bg-white/10" aria-label="Map">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-white/80">
                                            <path fill-rule="evenodd" d="M11.54 22.351A.75.75 0 0 0 12.46 22.35l7.5-3.75a.75.75 0 0 0 .414-.67V3.75a.75.75 0 0 0-1.086-.67L12 6.773 4.71 3.08A.75.75 0 0 0 3.624 3.75v14.18c0 .284.16.544.414.67l7.5 3.75ZM11.25 8.07 5.124 4.987v12.4l6.126 3.062V8.07Zm1.5 12.38 6.126-3.062v-12.4L12.75 8.07v12.38Z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @endif
                                @if (!empty($department?->website))
                                    <a href="{{ Str::startsWith($department->website, ['http://', 'https://']) ? $department->website : 'https://' . ltrim($department->website, '/') }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 hover:bg-white/10" aria-label="Website">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-white/80">
                                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75S6.615 21.75 12 21.75 21.75 17.385 21.75 12 17.385 2.25 12 2.25Zm-3.375 9.75c0-1.42.29-2.766.815-3.988A18.7 18.7 0 0 0 7.5 12c0 1.42.29 2.766.815 3.988A18.7 18.7 0 0 0 8.625 12Zm1.912-5.655A15.54 15.54 0 0 1 12 3.902a15.54 15.54 0 0 1 1.463 2.443A18.73 18.73 0 0 0 12 6c-.507 0-.998.023-1.463.345ZM12 7.5c.62 0 1.217.03 1.781.09.29.86.469 1.791.469 2.66 0 .87-.179 1.8-.469 2.66A18.6 18.6 0 0 1 12 13.5a18.6 18.6 0 0 1-1.781-.09A8.53 8.53 0 0 1 9.75 10.25c0-.87.179-1.8.469-2.66.564-.06 1.161-.09 1.781-.09Z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @endif
                                @if (!empty($department?->email))
                                    <a href="mailto:{{ $department->email }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 hover:bg-white/10" aria-label="Email">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-white/80">
                                            <path d="M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h15A2.25 2.25 0 0 1 21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15A2.25 2.25 0 0 1 2.25 17.25V6.75Zm2.25-.75a.75.75 0 0 0-.75.75v.243l7.5 4.5 7.5-4.5V6.75a.75.75 0 0 0-.75-.75h-15Z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                            <p class="mt-4 text-xs text-white/60">
                                {{ $locale === 'ne' ? 'नयाँ अपडेटका लागि जोडिनुहोस्।' : 'Stay connected for updates.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col gap-2 border-t border-white/10 pt-6 text-xs text-white/60 sm:flex-row sm:items-center sm:justify-between">
                        <div>© {{ now()->year }} {{ $departmentShort }}. {{ $locale === 'ne' ? 'सबै अधिकार सुरक्षित।' : 'All rights reserved.' }}</div>
                        <form method="POST" action="{{ route('language.switch') }}" class="sm:hidden">
                            @csrf
                            <select name="locale" onchange="this.form.submit()" class="rounded-lg border-white/15 bg-white/5 text-xs text-white shadow-sm focus:border-red-500 focus:ring-red-500">
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
                    Alpine.data('heroSlider', (config) => ({
                        slides: Array.isArray(config.slides) ? config.slides : [],
                        active: 0,
                        timer: null,
                        go(i) {
                            const n = this.slides.length || 0;
                            if (!n) return;
                            const idx = Number(i);
                            this.active = ((Number.isFinite(idx) ? idx : 0) + n) % n;
                            this.restart();
                        },
                        next() {
                            this.go(this.active + 1);
                        },
                        prev() {
                            this.go(this.active - 1);
                        },
                        restart() {
                            if (this.timer) clearInterval(this.timer);
                            if (this.slides.length <= 1) return;
                            this.timer = setInterval(() => this.next(), 5000);
                        },
                        init() {
                            this.restart();
                        },
                    }));

                    Alpine.data('subjectCatalog', (config) => ({
                        locale: config.locale || 'en',
                        subjects: Array.isArray(config.subjects) ? config.subjects : [],
                        indexUrl: config.indexUrl || null,
                        maxItems: Number.isFinite(config.maxItems) ? Number(config.maxItems) : null,
                        query: config.initialQuery || '',
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
                        get visibleSubjects() {
                            const max = this.maxItems;
                            if (!max || max < 1) return this.filteredSubjects;
                            return this.filteredSubjects.slice(0, max);
                        },
                        get showViewAll() {
                            const max = this.maxItems;
                            if (!this.indexUrl) return false;
                            if (!max || max < 1) return false;
                            return this.filteredSubjects.length > max;
                        },
                        get viewAllUrl() {
                            if (!this.indexUrl) return '#';
                            const params = [];
                            if (this.semester && this.semester !== 'all') {
                                params.push(`semester=${encodeURIComponent(this.semester)}`);
                            }
                            if (this.query) {
                                params.push(`q=${encodeURIComponent(this.query)}`);
                            }
                            return this.indexUrl + (params.length ? `?${params.join('&')}` : '');
                        },
                        get resultText() {
                            const total = this.filteredSubjects.length;
                            const shown = this.visibleSubjects.length;
                            if (this.showViewAll) {
                                return (this.locale === 'ne') ? `${shown} / ${total} (पूर्वावलोकन)` : `${shown} / ${total} (preview)`;
                            }
                            return (this.locale === 'ne') ? `${total} विषय` : `${total} subjects`;
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

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('departmentMap');
            if (!el || typeof window.L === 'undefined') return;

            const lat = Number(el.dataset.lat);
            const lng = Number(el.dataset.lng);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const deptName = el.dataset.name || 'Department';
            const label = el.dataset.label || 'Location';

            const map = L.map('departmentMap', { scrollWheelZoom: false }).setView([lat, lng], 16);

            const layers = {
                'Standard': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }),
                'Humanitarian': L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors, HOT',
                }),
                'Satellite': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: 'Tiles &copy; Esri',
                }),
            };

            layers['Standard'].addTo(map);
            const layerControl = L.control.layers(layers, null, { collapsed: true, position: 'topright' }).addTo(map);
            L.control.scale({ position: 'bottomleft', imperial: false }).addTo(map);

            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(`<strong>${deptName}</strong><br/>${label}`);

            const container = el.closest('.relative');
            const actionButtons = container ? container.querySelectorAll('[data-map-action]') : [];

            const toggleLayersUi = () => {
                const c = layerControl.getContainer();
                const isHidden = c.style.display === 'none';
                c.style.display = isHidden ? '' : 'none';
            };

            actionButtons.forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const action = btn.getAttribute('data-map-action');

                    if (action === 'reset') {
                        map.setView([lat, lng], 16);
                        marker.openPopup();
                        return;
                    }

                    if (action === 'layers') {
                        toggleLayersUi();
                        return;
                    }

                    if (action === 'copy') {
                        const txt = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        try {
                            await navigator.clipboard.writeText(txt);
                            btn.textContent = '{{ $locale === 'ne' ? 'कपी भयो' : 'Copied' }}';
                            setTimeout(() => (btn.textContent = '{{ $locale === 'ne' ? 'कपी' : 'Copy' }}'), 1200);
                        } catch (e) {
                            window.prompt('Copy coordinates:', txt);
                        }
                        return;
                    }

                    if (action === 'locate') {
                        if (!navigator.geolocation) {
                            alert('{{ $locale === 'ne' ? 'जियोलोकेसन समर्थित छैन' : 'Geolocation not supported' }}');
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            (pos) => {
                                const userLat = pos.coords.latitude;
                                const userLng = pos.coords.longitude;

                                L.circleMarker([userLat, userLng], {
                                    radius: 8,
                                    color: '#ef4444',
                                    weight: 2,
                                    fillColor: '#ef4444',
                                    fillOpacity: 0.25,
                                })
                                    .addTo(map)
                                    .bindPopup('{{ $locale === 'ne' ? 'मेरो स्थान' : 'My location' }}')
                                    .openPopup();

                                map.fitBounds(L.latLngBounds([
                                    [lat, lng],
                                    [userLat, userLng],
                                ]), { padding: [24, 24] });
                            },
                            (err) => {
                                console.warn('Geolocation error:', err);
                            },
                            { enableHighAccuracy: true, timeout: 8000 }
                        );

                        return;
                    }

                    if (action === 'directions') {
                        const dest = `${lat},${lng}`;
                        const openDirections = (origin) => {
                            const url = origin
                                ? `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(dest)}`
                                : `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(dest)}`;
                            window.open(url, '_blank', 'noopener');
                        };

                        if (!navigator.geolocation) {
                            openDirections(null);
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            (pos) => openDirections(`${pos.coords.latitude},${pos.coords.longitude}`),
                            () => openDirections(null),
                            { enableHighAccuracy: true, timeout: 8000 }
                        );
                    }
                });
            });

            // Hide layers UI by default; button toggles it.
            layerControl.getContainer().style.display = 'none';

            // Enable scroll zoom only when user focuses the map.
            map.on('focus', () => map.scrollWheelZoom.enable());

            // Leaflet can sometimes draw blank if container is not fully painted yet; force sizing.
            map.invalidateSize();
            setTimeout(() => map.invalidateSize(), 300);
            window.addEventListener('resize', () => map.invalidateSize());
            map.on('blur', () => map.scrollWheelZoom.disable());
        });
    </script>
@endpush
