    @php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();

    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology Department');

    $departmentShort = $department?->short_name ?: ($locale === 'ne' ? 'आईटी' : 'IT');
    $departmentLogoUrl = $department?->getLogoUrl() ?? '/images/default-logo.svg';
    $heroTitle = trim((string) Str::of($departmentName)->replace([' Department', ' विभाग'], ''));

    if (blank($heroTitle)) {
        $heroTitle = $departmentShort ?: $departmentName;
    }

    $heroSlides = collect($department?->hero_images ?? [])
        ->filter()
        ->map(function ($path) {
            return \App\Support\Media::publicUrl($path) ?? asset('storage/' . ltrim((string) $path, '/'));
        })
        ->values();

    $referenceHeroRelativePath = 'images/landing-hero-reference.jpeg';

    if (is_file(public_path($referenceHeroRelativePath))) {
        $heroSlides = collect(['/' . $referenceHeroRelativePath])
            ->merge($heroSlides)
            ->unique()
            ->values();
    }

    if ($heroSlides->isEmpty()) {
        $heroSlides = collect(['/images/hero-image.jpg']);
    }

    $tagline = $locale === 'ne'
        ? 'सीप, नवप्रवर्तन र उत्कृष्टताका लागि एकीकृत शैक्षिक प्लेटफर्म'
        : 'A unified academic platform for skills, innovation, and excellence.';

    $aboutText = $department
        ? (($locale === 'ne' && !empty($department->description_nepali)) ? $department->description_nepali : $department->description)
        : null;

    $heroDescriptionSource = $aboutText ?: ($locale === 'ne'
        ? 'यो विभागले व्यावहारिक प्रयोगशाला, अद्यावधिक पाठ्यक्रम, शिक्षक मार्गदर्शन र डिजिटल स्रोतहरूमार्फत विद्यार्थीलाई भविष्यको प्रविधि संसारका लागि तयार पार्छ।'
        : 'The department combines practical labs, updated coursework, faculty mentorship, and accessible digital resources to prepare students for modern technology careers and real-world problem solving.');

    $heroDescription = Str::limit(
        preg_replace('/\s+/u', ' ', trim(strip_tags((string) $heroDescriptionSource))),
        $locale === 'ne' ? 220 : 260
    );

    $addressText = $department
        ? (($locale === 'ne' && !empty($department->address_nepali)) ? $department->address_nepali : $department->address)
        : null;

    $heroDepartmentLine = $locale === 'ne'
        ? $departmentName
        : 'Department of ' . $heroTitle;

    $heroHeadlinePrimary = $heroTitle;
    $heroHeadlineSecondary = null;

    $heroSupportingText = $locale === 'ne'
        ? 'अत्याधुनिक प्रविधि, व्यावहारिक सिकाइ र उद्योगमुखी शिक्षामार्फत हामी भविष्यका नवप्रवर्तकहरूलाई सशक्त बनाउँछौं। हाम्रो विभागले सृजनशीलता, समालोचनात्मक सोच र वास्तविक समस्या समाधान गर्ने क्षमता विकास गर्न मद्दत गर्दछ।'
        : 'Empowering future innovators through cutting-edge technology, practical learning, and industry-focused education. Our department fosters creativity, critical thinking, and real-world problem-solving to prepare students for the digital future.';

    $heroNavItems = [
        ['href' => '#home', 'label' => $locale === 'ne' ? 'होम' : 'Home'],
        ['href' => '#programs', 'label' => $locale === 'ne' ? 'कार्यक्रम' : 'Programs'],
        ['href' => '#curriculum', 'label' => $locale === 'ne' ? 'विषयहरू' : 'Subjects'],
        ['href' => '#curriculum', 'label' => $locale === 'ne' ? 'ल्याब' : 'Labs'],
        ['href' => '#exam-result', 'label' => $locale === 'ne' ? 'परीक्षा परिणाम' : 'Exam Result'],
        ['href' => '#contact', 'label' => $locale === 'ne' ? 'सम्पर्क' : 'Contact'],
    ];

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

    $programsImageUrl = $department?->getProgramsImageUrl();
    $hasProgramsImage = filled($programsImageUrl);

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
            $url = \App\Support\Media::publicUrl($d->file_path);

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

    $examResultMeta = $examResultMeta ?? ['years' => [], 'semesters' => [], 'assessmentMap' => []];
    $examResultSearchData = $examResultSearch ?? [];
    $examResultFilters = $examResultSearchData['filters'] ?? [
        'academic_year' => '',
        'semester' => '',
        'exam_category' => 'assessment',
        'assessment_number' => '',
        'student_id' => '',
        'dob' => '',
    ];
    $examResultAssessmentNumbers = $examResultSearchData['assessmentNumbers'] ?? [];
    $examResultStudent = $examResultSearchData['student'] ?? null;
    $examResultPayload = $examResultSearchData['payload'] ?? null;
    $examResultError = $examResultSearchData['error'] ?? null;
    $examResultSearchPerformed = (bool) ($examResultSearchData['searchAttempted'] ?? false);
    $examResultPrintUrl = $examResultSearchData['printUrl'] ?? route('home');
    $examResultAssessmentMap = $examResultMeta['assessmentMap'] ?? [];

@endphp

@extends('layouts.public')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        :root {
            --landing-bg: #f8fafc;
            --landing-red: #dc2626;
            --landing-red-deep: #b91c1c;
            --landing-red-soft: #fee2e2;
            --landing-surface: rgba(255, 255, 255, 0.995);
            --landing-surface-soft: #ffffff;
            --landing-surface-muted: #ffffff;
            --landing-border: rgba(226, 232, 240, 0.6);
            --landing-border-strong: rgba(220, 38, 38, 0.15);
            --landing-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            --landing-shadow-soft: 0 2px 8px rgba(15, 23, 42, 0.04);
            --landing-text-soft: #64748b;
        }

        .dark {
            --landing-bg: #0f172a;
            --landing-surface: rgba(15, 23, 42, 0.95);
            --landing-surface-soft: rgba(17, 24, 39, 0.98);
            --landing-surface-muted: rgba(15, 23, 42, 0.96);
            --landing-border: rgba(51, 65, 85, 0.5);
            --landing-border-strong: rgba(220, 38, 38, 0.2);
            --landing-shadow: 0 4px 16px rgba(2, 6, 23, 0.2);
            --landing-shadow-soft: 0 2px 8px rgba(2, 6, 23, 0.1);
            --landing-text-soft: #94a3b8;
        }

        .landing-page {
            overflow-x: clip;
        }

        .landing-shell {
            width: 100%;
        }

        .landing-section {
            position: relative;
            scroll-margin-top: 8.5rem;
        }

        .landing-stage {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 1.75rem;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            padding: clamp(1.5rem, 2.5vw, 2.5rem);
            transition: box-shadow 280ms ease;
        }

        .landing-stage:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
        }

        .landing-stage::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: transparent;
            background-size: 140% 140%, auto;
        }

        .dark .landing-stage {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(51, 65, 85, 0.5);
            box-shadow: 0 4px 16px rgba(2, 6, 23, 0.2);
        }

        .dark .landing-stage:hover {
            box-shadow: 0 8px 24px rgba(2, 6, 23, 0.3);
        }

        .dark .landing-stage::before {
            background: transparent;
            opacity: 1;
        }

        .landing-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.875rem;
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 1.25rem;
            background: rgba(248, 250, 252, 0.6);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            padding: 1rem;
            transition: all 200ms ease;
        }

        .landing-toolbar:hover {
            border-color: rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        .dark .landing-toolbar {
            background: rgba(17, 24, 39, 0.7);\n            border-color: rgba(51, 65, 85, 0.5);
            box-shadow: 0 2px 8px rgba(2, 6, 23, 0.15);
        }

        .dark .landing-toolbar:hover {
            border-color: rgba(51, 65, 85, 0.7);
            box-shadow: 0 4px 12px rgba(2, 6, 23, 0.2);
        }

        .landing-input,
        .landing-select {
            border-radius: 0.875rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: #ffffff;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
            transition: all 200ms ease;
        }

        .landing-input:hover,
        .landing-select:hover {
            border-color: rgba(226, 232, 240, 1);
        }

        .landing-input:focus,
        .landing-select:focus {
            border-color: #dc2626;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04), 0 0 0 3px rgba(220, 38, 38, 0.1);
            --tw-ring-color: rgb(220 38 38 / 0.1);
        }

        .dark .landing-input,
        .dark .landing-select {
            border-color: rgba(51, 65, 85, 0.6);
            background: rgba(17, 24, 39, 0.95);
            box-shadow: inset 0 1px 2px rgba(2, 6, 23, 0.1);
        }

        .dark .landing-input:hover,
        .dark .landing-select:hover {
            border-color: rgba(51, 65, 85, 0.8);
        }

        .dark .landing-input:focus,
        .dark .landing-select:focus {
            border-color: #f87171;
            box-shadow: inset 0 1px 2px rgba(2, 6, 23, 0.15), 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .landing-subtle {
            color: var(--landing-text-soft);
        }

        /* Faculty Star Display */
        .faculty-star-display {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1.5rem 0;
        }

        .faculty-star {
            font-size: 3.5rem;
            color: #dc2626;
            line-height: 1;
            filter: drop-shadow(0 4px 8px rgba(220, 38, 38, 0.2));
        }

        .faculty-star-border {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 5rem;
            height: 5rem;
            border: 2.5px solid #dc2626;
            border-radius: 50%;
            background: rgba(254, 226, 226, 0.2);
            margin: 0 auto;
        }

        .landing-panel {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.7);
            border-radius: 1.25rem;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            backdrop-filter: none;
            transition: box-shadow 280ms ease, border-color 280ms ease;
        }

        .landing-panel:hover {
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            border-color: rgba(226, 232, 240, 0.9);
        }

        .dark .landing-panel {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(51, 65, 85, 0.5);
            box-shadow: 0 2px 8px rgba(2, 6, 23, 0.2);
        }

        .dark .landing-panel:hover {
            box-shadow: 0 4px 16px rgba(2, 6, 23, 0.3);
            border-color: rgba(51, 65, 85, 0.7);
        }

        .landing-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: transparent;
        }

        .dark .landing-panel::before {
            background: transparent;
        }

        .landing-panel.lift-card {
            background: #ffffff;
            border-color: rgba(226, 232, 240, 0.7);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .landing-panel.lift-card:hover {
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            border-color: rgba(226, 232, 240, 0.9);
        }

        .dark .landing-panel.lift-card {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(51, 65, 85, 0.5);
            box-shadow: 0 2px 8px rgba(2, 6, 23, 0.2);
        }

        .dark .landing-panel.lift-card:hover {
            box-shadow: 0 4px 16px rgba(2, 6, 23, 0.3);
            border-color: rgba(51, 65, 85, 0.7);
        }

        .landing-grid-accent {
            position: relative;
            overflow: hidden;
        }

        .landing-hero-shell {
            min-height: clamp(24.5rem, 56vh, 33rem);
            display: flex;
            align-items: flex-end;
        }

        .landing-hero-layout {
            display: grid;
            width: 100%;
            gap: 1rem;
            align-items: stretch;
        }

        .landing-hero-shadow-left {
            background:
                linear-gradient(90deg, rgba(2, 6, 23, 0.94) 0%, rgba(2, 6, 23, 0.88) 26%, rgba(2, 6, 23, 0.64) 48%, rgba(2, 6, 23, 0.24) 74%, rgba(2, 6, 23, 0.04) 100%),
                radial-gradient(circle at 14% 28%, rgba(15, 23, 42, 0.46), transparent 36%);
        }

        .landing-hero-floor-shadow {
            background: linear-gradient(180deg, rgba(2, 6, 23, 0.4), rgba(2, 6, 23, 0.8));
        }

        .landing-hero-content {
            max-width: 41rem;
        }

        .landing-hero-copy {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100%;
        }

        .landing-hero-heading {
            max-width: none;
            letter-spacing: -0.065em;
            line-height: 0.94;
        }

        .landing-hero-tagline {
            color: rgba(255, 255, 255, 0.92);
            text-shadow: 0 8px 22px rgba(2, 6, 23, 0.34);
        }

        .landing-hero-department-line {
            color: rgba(255, 255, 255, 0.96);
            text-shadow: 0 8px 22px rgba(2, 6, 23, 0.34);
        }

        .landing-hero-support-copy {
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 8px 22px rgba(2, 6, 23, 0.28);
        }

        .landing-hero-kicker,
        .landing-hero-support {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
        }

        .landing-hero-kicker::before,
        .landing-hero-support::before {
            content: "";
            width: 4px;
            flex: 0 0 4px;
            align-self: stretch;
            border-radius: 9999px;
            background: #dc2626;
            box-shadow: 0 0 18px rgba(255, 0, 55, 0.38);
        }

        .landing-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.7rem;
        }

        .landing-hero-action {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            min-width: 11.75rem;
            border-radius: 1rem;
            padding: 0.74rem 0.95rem;
            font-size: 0.88rem;
            font-weight: 700;
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.22);
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease, color 180ms ease;
            backdrop-filter: blur(14px);
        }

        .landing-hero-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 36px rgba(15, 23, 42, 0.24);
        }

        .landing-hero-action-icon {
            display: inline-flex;
            align-items: center;
            gap: 0.58rem;
        }

        .landing-hero-action--primary {
            background: #dc2626;
            color: #ffffff;
        }

        .landing-hero-action--secondary {
            background: rgba(255, 255, 255, 0.96);
            color: #1d4ed8;
        }

        .landing-hero-action--contact {
            background: rgba(255, 255, 255, 0.96);
            color: #e11d48;
        }

        .landing-hero-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .landing-hero-stat {
            animation: heroStatFloat 7.5s ease-in-out infinite;
        }

        .landing-hero-stat-card {
            min-height: 6rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.2rem;
            padding: 0.9rem 1rem 1rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15);
            backdrop-filter: blur(12px);
            transition: all 280ms ease;
            will-change: auto;
        }

        .landing-hero-stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.28);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.2);
            filter: brightness(1.04);
        }

        .landing-hero-stat-card--students {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }

        .landing-hero-stat-card--faculty {
            background: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .landing-hero-stat-card--subjects {
            background: #f97316;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
        }

        .landing-hero-stat-card--labs {
            background: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .landing-hero-stat-icon {
            color: rgba(255, 255, 255, 0.84);
        }

        .landing-hero-stat-body {
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
            margin-top: 0.72rem;
        }

        .landing-hero-stat-number {
            color: #ffffff;
            font-size: clamp(1.55rem, 1.95vw, 1.9rem);
            line-height: 0.95;
            font-weight: 800;
            text-shadow: 0 10px 24px rgba(15, 23, 42, 0.28);
        }

        .landing-hero-stat-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.96rem;
            line-height: 1.1;
            font-weight: 500;
            padding-bottom: 0.12rem;
        }

        .landing-hero-stat-card:hover .landing-hero-stat-icon,
        .landing-hero-stat-card:hover .landing-hero-stat-number,
        .landing-hero-stat-card:hover .landing-hero-stat-label {
            color: #ffffff;
        }

        .landing-hero-rail {
            display: grid;
            gap: 0.8rem;
            width: 100%;
            max-width: 17rem;
            justify-self: end;
            align-self: start;
        }

        .landing-hero-panel {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1rem;
            background: rgba(30, 30, 35, 0.4);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 0.9rem;
            transition: all 200ms ease;
        }

        .landing-hero-panel:hover {
            border-color: rgba(255, 255, 255, 0.18);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .landing-hero-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: transparent;
        }

        .landing-hero-panel-title {
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.96);
        }

        .landing-hero-panel-stack {
            display: grid;
            gap: 0.6rem;
            margin-top: 0.75rem;
        }

        .landing-hero-panel-item {
            display: flex;
            gap: 0.72rem;
            align-items: flex-start;
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.72rem 0.78rem;
        }

        .landing-hero-panel-icon {
            color: rgba(255, 255, 255, 0.88);
            flex: 0 0 auto;
        }

        .landing-hero-panel-label {
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.95);
        }

        .landing-hero-panel-text {
            margin-top: 0.28rem;
            font-size: 0.82rem;
            line-height: 1.45;
            color: rgba(255, 255, 255, 0.82);
        }

        .landing-hero-panel-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .landing-hero-panel-mini {
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.72rem 0.7rem;
            text-align: center;
        }

        .landing-hero-panel-mini-value {
            margin-top: 0.35rem;
            font-size: 1.05rem;
            font-weight: 800;
            color: #ffffff;
        }

        .landing-hero-quick-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .landing-hero-quick-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.03em;
        }

        .landing-hero-quick-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 9999px;
            padding: 0.28rem 0.7rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.76rem;
            font-weight: 700;
        }

        .landing-hero-quick-links {
            display: grid;
            gap: 0.65rem;
            margin-top: 0.95rem;
        }

        .landing-hero-quick-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 0.85rem 0.95rem;
            transition: transform 180ms ease, background-color 180ms ease;
        }

        .landing-hero-quick-link:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.11);
        }

        .landing-hero-quick-link-left {
            display: inline-flex;
            align-items: center;
            gap: 0.72rem;
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.88rem;
            font-weight: 600;
        }

        .landing-hero-quick-link-index {
            color: rgba(255, 255, 255, 0.92);
            font-size: 1.35rem;
            line-height: 1;
            font-weight: 500;
        }

        .dark .landing-hero-stat-card {
            border-color: rgba(255, 255, 255, 0.12);
        }

        .dark .landing-hero-panel {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(51, 65, 85, 0.5);
        }

        .landing-hero-stat:nth-child(2) { animation-delay: 0.9s; }
        .landing-hero-stat:nth-child(3) { animation-delay: 1.8s; }
        .landing-hero-stat:nth-child(4) { animation-delay: 2.7s; }

        .landing-grid-accent::after {
            content: "";
            position: absolute;
            inset: 1px;
            pointer-events: none;
            border-radius: inherit;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.16) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.16) 1px, transparent 1px);
            background-size: 24px 24px;
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.38), transparent 82%);
            opacity: 0.35;
        }

        .section-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 99px;
            padding: 0.375rem 0.875rem;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #dc2626;
            background: rgba(254, 226, 226, 0.8);
            box-shadow: none;
            border: 1px solid rgba(254, 202, 202, 0.5);
            transition: all 200ms ease;
        }

        .section-chip:hover {
            background: rgba(254, 226, 226, 1);
            border-color: rgba(254, 202, 202, 0.8);
        }

        .dark .section-chip {
            color: #fca5a5;
            background: rgba(127, 29, 29, 0.3);
            border-color: rgba(153, 27, 27, 0.4);
        }

        .dark .section-chip:hover {
            background: rgba(127, 29, 29, 0.4);
            border-color: rgba(153, 27, 27, 0.6);
        }

        .section-title {
            letter-spacing: -0.04em;
            line-height: 1.05;
        }

        .float-orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(10px);
            opacity: 0.55;
            animation: floatOrb 10s ease-in-out infinite;
        }

        .stagger-rise {
            animation: riseIn 0.6s ease-out both;
        }

        .stagger-rise:nth-child(2) { animation-delay: 0.06s; }
        .stagger-rise:nth-child(3) { animation-delay: 0.12s; }
        .stagger-rise:nth-child(4) { animation-delay: 0.18s; }
        .stagger-rise:nth-child(5) { animation-delay: 0.24s; }

        .lift-card {
            transition: box-shadow 280ms ease, border-color 280ms ease;
        }

        .lift-card:hover {
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            border-color: rgba(226, 232, 240, 0.9);
        }

        .dark .lift-card:hover {
            box-shadow: 0 24px 48px rgba(2, 6, 23, 0.42);
        }

        .shine-button {
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15) !important;
            transition: all 240ms ease !important;
        }

        .shine-button:hover {
            box-shadow: 0 4px 16px rgba(220, 38, 38, 0.25) !important;
            transform: translateY(-1px) !important;
        }

        .shine-button:active {
            transform: scale(0.98) !important;
        }

        .leaflet-control-layers,
        .leaflet-control-zoom,
        .leaflet-control-scale {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .landing-hero-slide {
            animation: heroKenBurns 20s ease-in-out infinite;
        }

        .landing-hero-copy > * {
            text-shadow: 0 10px 24px rgba(2, 6, 23, 0.34);
        }

        .dark .landing-hero-copy > * {
            text-shadow: 0 10px 28px rgba(2, 6, 23, 0.5);
        }

        .landing-header-nav-link {
            color: #111827;
        }

        .landing-header-nav-link:hover {
            color: #dc2626;
            background: rgba(254, 242, 242, 0.95);
        }

        .landing-header-select {
            background: transparent;
            color: #111827;
        }

        .landing-header-select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(254, 202, 202, 0.7);
        }

        .landing-header-icon-button {
            background: #ffffff;
            color: #111827;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .landing-header-icon-button:hover {
            color: #dc2626;
            border-color: rgba(252, 165, 165, 0.85);
            background: #fff7f7;
        }

        .landing-header-mobile-panel {
            background: rgba(255, 255, 255, 0.98);
            border-top: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 24px 40px rgba(15, 23, 42, 0.08);
        }

        .dark .landing-header-nav-link,
        .dark .landing-header-select {
            color: #e5e7eb;
        }

        .dark .landing-header-nav-link:hover {
            color: #ffffff;
            background: rgba(15, 23, 42, 0.86);
        }

        .dark .landing-header-icon-button {
            background: rgba(15, 23, 42, 0.9);
            color: #e5e7eb;
            border-color: rgba(51, 65, 85, 0.95);
            box-shadow: none;
        }

        .dark .landing-header-icon-button:hover {
            background: rgba(17, 24, 39, 0.96);
            color: #ffffff;
            border-color: rgba(71, 85, 105, 0.95);
        }

        .dark .landing-header-mobile-panel {
            background: rgba(2, 6, 23, 0.98);
            border-top-color: rgba(30, 41, 59, 0.95);
            box-shadow: 0 24px 40px rgba(2, 6, 23, 0.42);
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: 0.6; }
            50% { transform: translate3d(0, -18px, 0) scale(1.08); opacity: 0.7; }
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes heroStatFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        @keyframes heroKenBurns {
            0%, 100% { transform: scale(1.02); }
            50% { transform: scale(1.06); }
        }

        @media (max-width: 1023px) {
            .landing-stage {
                border-radius: 1.5rem;
                padding: 1.25rem;
            }

            .landing-hero-shell {
                min-height: auto;
                align-items: flex-start;
            }

            .landing-hero-heading {
                max-width: 100%;
            }

            .landing-hero-copy {
                min-height: auto;
                justify-content: flex-start;
            }

            .landing-hero-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .landing-hero-rail {
                max-width: 100%;
            }

            .landing-hero-stat {
                animation: none;
            }
        }

        @media (max-width: 639px) {
            .landing-hero-action {
                width: 100%;
                min-width: 0;
            }

            .landing-hero-stat-label {
                font-size: 1.05rem;
            }

            .landing-hero-panel-mini-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 1024px) {
            .landing-hero-layout {
                grid-template-columns: minmax(0, 1fr) 17rem;
            }

            .landing-hero-heading {
                white-space: nowrap;
                font-size: clamp(3.45rem, 4vw, 4.1rem);
            }
        }
    </style>
@endpush

@section('content')
    <div class="landing-page relative text-gray-900 dark:text-white" style="background: var(--landing-bg);">
        <div class="pointer-events-none absolute inset-0 overflow-x-clip">
            <div class="float-orb left-[-5rem] top-28 h-36 w-36 bg-red-300/60 dark:bg-red-500/30"></div>
            <div class="float-orb right-[-3rem] top-[34rem] h-40 w-40 bg-red-200/55 [animation-delay:1.2s] dark:bg-red-400/30"></div>
            <div class="float-orb bottom-40 left-[12%] h-28 w-28 bg-red-400/45 [animation-delay:2.1s] dark:bg-red-500/30"></div>
        </div>
        <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-gray-900 dark:focus:bg-gray-900 dark:focus:text-gray-100">
            {{ $locale === 'ne' ? 'मुख्य सामग्रीमा जानुहोस्' : 'Skip to content' }}
        </a>

        <x-header />

        <main id="content" class="relative z-0">
            <section id="home" class="relative isolate overflow-hidden bg-slate-950" x-data="heroSlider({ slides: @js($heroSlides->values()->all()) })">
                <div class="absolute inset-0 -z-10">
                    <div class="relative h-full w-full">
                        <template x-for="(src, idx) in slides" :key="src">
                            <img :src="src" alt="" class="landing-hero-slide absolute inset-0 h-full w-full object-cover object-center transition-opacity duration-700"
                                 :class="idx === active ? 'opacity-100' : 'opacity-0'" />
                        </template>
                        <div class="landing-hero-shadow-left absolute inset-0"></div>
                        <div class="landing-hero-floor-shadow absolute inset-0"></div>
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_78%_16%,rgba(255,255,255,0.18),transparent_22%),radial-gradient(circle_at_20%_24%,rgba(15,23,42,0.34),transparent_38%)]"></div>
                    </div>
                </div>

                <div class="landing-shell landing-hero-shell mx-auto w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-10">
                    <div class="landing-hero-layout">
                        <div class="landing-hero-copy landing-hero-content">
                            <h1 class="landing-hero-heading stagger-rise text-4xl font-black text-white drop-shadow-[0_10px_24px_rgba(0,0,0,0.35)] sm:text-5xl">
                                <span>{{ $heroHeadlinePrimary }}</span>
                            </h1>

                            <p class="landing-hero-tagline stagger-rise mt-3 max-w-xl text-base leading-snug sm:text-lg lg:text-[0.98rem]">
                                {{ $tagline }}
                            </p>

                            <div class="landing-hero-kicker landing-hero-department-line stagger-rise mt-2.5 max-w-xl text-sm font-medium sm:text-base">
                                <span>{{ $heroDepartmentLine }}</span>
                            </div>

                            <div class="landing-hero-actions stagger-rise mt-4 sm:mt-5">
                                <a href="#programs" class="landing-hero-action landing-hero-action--primary shine-button focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <span class="landing-hero-action-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path d="M3.75 6.75A.75.75 0 0 1 4.5 6h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Zm0 5.25a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.5a.75.75 0 0 1-.75-.75Zm0 5.25a.75.75 0 0 1 .75-.75h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Z" />
                                        </svg>
                                        <span>{{ $locale === 'ne' ? 'कार्यक्रम हेर्नुहोस्' : 'Explore Programs' }}</span>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M9.97 5.97a.75.75 0 0 1 1.06 0l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06L14.94 12 9.97 7.03a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>

                                <a href="#curriculum" class="landing-hero-action landing-hero-action--secondary shine-button focus:outline-none focus:ring-2 focus:ring-white/60">
                                    <span class="landing-hero-action-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path d="M4.5 4.5A1.5 1.5 0 0 0 3 6v12a1.5 1.5 0 0 0 1.5 1.5h12A1.5 1.5 0 0 0 18 18V6a1.5 1.5 0 0 0-1.5-1.5h-12Zm1.5 3a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 6 7.5Zm0 3.75a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1-.75-.75Zm.75 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Zm13.5-8.25a.75.75 0 0 0-.75.75v10.5a.75.75 0 0 0 1.5 0V6.75a.75.75 0 0 0-.75-.75Z" />
                                        </svg>
                                        <span>{{ $locale === 'ne' ? 'विषयहरू ब्राउज' : 'Browse Subjects' }}</span>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M9.97 5.97a.75.75 0 0 1 1.06 0l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06L14.94 12 9.97 7.03a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>

                                <a href="#contact" class="landing-hero-action landing-hero-action--contact shine-button focus:outline-none focus:ring-2 focus:ring-white/60">
                                    <span class="landing-hero-action-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path d="M1.5 6.75A2.25 2.25 0 0 1 3.75 4.5h16.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 17.25V6.75Zm2.03-.75a.75.75 0 0 0-.48 1.33l8.47 6.97a.75.75 0 0 0 .96 0l8.47-6.97A.75.75 0 0 0 20.47 6H3.53Z" />
                                        </svg>
                                        <span>{{ $locale === 'ne' ? 'सम्पर्क गर्नुहोस्' : 'Contact Us' }}</span>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M9.97 5.97a.75.75 0 0 1 1.06 0l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06L14.94 12 9.97 7.03a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>

                            <div class="landing-hero-stats-grid mt-5">
                                <div class="landing-hero-stat landing-hero-stat-card landing-hero-stat-card--students stagger-rise">
                                    <div class="landing-hero-stat-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path d="M7.5 6a2.25 2.25 0 1 1 0 4.5A2.25 2.25 0 0 1 7.5 6Zm9 0a2.25 2.25 0 1 1 0 4.5A2.25 2.25 0 0 1 16.5 6ZM3.75 17.25a3.75 3.75 0 0 1 7.5 0v.75h-7.5v-.75Zm9 0a3.75 3.75 0 0 1 7.5 0v.75h-7.5v-.75Z" />
                                        </svg>
                                    </div>
                                    <div class="landing-hero-stat-body">
                                        <div class="landing-hero-stat-number">{{ number_format((int) ($stats['students'] ?? 0)) }}+</div>
                                        <div class="landing-hero-stat-label">{{ $locale === 'ne' ? 'विद्यार्थी' : 'Students' }}</div>
                                    </div>
                                </div>

                                <div class="landing-hero-stat landing-hero-stat-card landing-hero-stat-card--faculty stagger-rise">
                                    <div class="landing-hero-stat-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path d="M12 3 1.5 8.25 12 13.5l8.625-4.313V15H22.5V8.25L12 3Zm-6 11.25V18a1.5 1.5 0 0 0 .75 1.299c1.29.744 3.123 1.201 5.25 1.201s3.96-.457 5.25-1.201A1.5 1.5 0 0 0 18 18v-3.75L12 17.25 6 14.25Z" />
                                        </svg>
                                    </div>
                                    <div class="landing-hero-stat-body">
                                        <div class="landing-hero-stat-number">{{ number_format((int) ($stats['teachers'] ?? 0)) }}+</div>
                                        <div class="landing-hero-stat-label">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</div>
                                    </div>
                                </div>

                                <div class="landing-hero-stat landing-hero-stat-card landing-hero-stat-card--subjects stagger-rise">
                                    <div class="landing-hero-stat-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path d="M5.25 4.5A2.25 2.25 0 0 0 3 6.75v10.5A2.25 2.25 0 0 0 5.25 19.5h4.125a2.25 2.25 0 0 1 2.25 2.25V7.125A2.625 2.625 0 0 0 9 4.5H5.25Zm8.625 0a2.625 2.625 0 0 0-2.625 2.625V21.75a2.25 2.25 0 0 1 2.25-2.25h4.125A2.25 2.25 0 0 0 19.875 17.25V6.75A2.25 2.25 0 0 0 17.625 4.5H13.875Z" />
                                        </svg>
                                    </div>
                                    <div class="landing-hero-stat-body">
                                        <div class="landing-hero-stat-number">{{ number_format((int) ($stats['subjects'] ?? 0)) }}+</div>
                                        <div class="landing-hero-stat-label">{{ $locale === 'ne' ? 'विषय' : 'Subjects' }}</div>
                                    </div>
                                </div>

                                <div class="landing-hero-stat landing-hero-stat-card landing-hero-stat-card--labs stagger-rise">
                                    <div class="landing-hero-stat-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                            <path d="M9 3.75A.75.75 0 0 1 9.75 3h4.5a.75.75 0 0 1 0 1.5h-.75v3.69l4.94 8.478A2.25 2.25 0 0 1 16.5 20.25h-9a2.25 2.25 0 0 1-1.94-3.382L10.5 8.19V4.5h-.75A.75.75 0 0 1 9 3.75Zm1.45 8.01-3.59 6.158a.75.75 0 0 0 .64 1.132h9a.75.75 0 0 0 .64-1.132l-3.59-6.158h-3.1Z" />
                                        </svg>
                                    </div>
                                    <div class="landing-hero-stat-body">
                                        <div class="landing-hero-stat-number">{{ number_format((int) ($stats['labs'] ?? 0)) }}+</div>
                                        <div class="landing-hero-stat-label">{{ $locale === 'ne' ? 'ल्याब' : 'Labs' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="landing-hero-support landing-hero-support-copy stagger-rise mt-4 max-w-3xl text-sm leading-relaxed">
                                <p>{{ $heroSupportingText }}</p>
                            </div>
                        </div>

                        <aside class="landing-hero-rail">
                            <div class="landing-hero-panel stagger-rise">
                                <div class="landing-hero-panel-title">{{ $locale === 'ne' ? 'विभागीय झलक' : 'Department Snapshot' }}</div>
                                <div class="landing-hero-panel-stack">
                                    <div class="landing-hero-panel-item">
                                        <div class="landing-hero-panel-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path fill-rule="evenodd" d="M11.54 22.351a.75.75 0 0 0 .92 0c4.884-3.73 7.29-7.15 7.29-10.601a7.75 7.75 0 1 0-15.5 0c0 3.45 2.406 6.87 7.29 10.6ZM12 12.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="landing-hero-panel-label">{{ $locale === 'ne' ? 'स्थान' : 'Location' }}</div>
                                            <div class="landing-hero-panel-text">{{ $addressText ?: ($locale === 'ne' ? 'विभागीय कार्यालय' : 'Department Office') }}</div>
                                        </div>
                                    </div>

                                    <div class="landing-hero-panel-item">
                                        <div class="landing-hero-panel-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path d="M21.75 18.75a.75.75 0 0 1-.75.75h-2.386a2.25 2.25 0 0 1-1.591-.659l-2.256-2.256a2.25 2.25 0 0 1-.659-1.591V12.75a.75.75 0 0 1 .75-.75h1.173a.75.75 0 0 0 .724-.555l.638-2.39a.75.75 0 0 0-.196-.718l-2.315-2.315a.75.75 0 0 0-.86-.154l-2.305 1.153a.75.75 0 0 1-.848-.14L8.598 4.591a2.25 2.25 0 0 0-1.591-.659H4.5a.75.75 0 0 0-.75.75c0 9.113 7.387 16.5 16.5 16.5a.75.75 0 0 0 .75-.75v-1.682Z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="landing-hero-panel-label">{{ $locale === 'ne' ? 'सम्पर्क' : 'Connect' }}</div>
                                            <div class="landing-hero-panel-text">
                                                @if (!empty($department?->email))
                                                    {{ $department->email }}
                                                @elseif (!empty($department?->phone))
                                                    {{ $department->phone }}
                                                @else
                                                    {{ $locale === 'ne' ? 'सम्पर्क विवरण छिट्टै' : 'Contact details coming soon' }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="landing-hero-panel-mini-grid">
                                        <div class="landing-hero-panel-mini">
                                            <div class="landing-hero-panel-label">{{ $locale === 'ne' ? 'स्थापना' : 'Established' }}</div>
                                            <div class="landing-hero-panel-mini-value">{{ $department?->established_year ?: 'N/A' }}</div>
                                        </div>
                                        <div class="landing-hero-panel-mini">
                                            <div class="landing-hero-panel-label">{{ $locale === 'ne' ? 'दर्ता' : 'Register' }}</div>
                                            <div class="landing-hero-panel-mini-value">{{ $department?->registration_number ?: 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="landing-hero-panel stagger-rise">
                                <div class="landing-hero-quick-head">
                                    <div class="landing-hero-quick-title">{{ $locale === 'ne' ? 'छिटो पहुँच' : 'Quick Access' }}</div>
                                    <div class="landing-hero-quick-pill">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                            <path d="M11.25 4.5a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 .53 1.28l-9.75 9.75a.75.75 0 1 1-1.06-1.06l9.22-9.22H12a.75.75 0 0 1-.75-.75Zm-6 3A2.25 2.25 0 0 0 3 9.75v9A2.25 2.25 0 0 0 5.25 21h9a2.25 2.25 0 0 0 2.25-2.25v-4.5a.75.75 0 0 0-1.5 0v4.5a.75.75 0 0 1-.75.75h-9a.75.75 0 0 1-.75-.75v-9A.75.75 0 0 1 5.25 9h4.5a.75.75 0 0 0 0-1.5h-4.5Z" />
                                        </svg>
                                        <span>{{ $locale === 'ne' ? 'लिङ्क' : 'links' }}</span>
                                    </div>
                                </div>

                                <div class="landing-hero-quick-links">
                                    <a href="#notices" class="landing-hero-quick-link">
                                        <span class="landing-hero-quick-link-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path d="M12 3.75a6 6 0 0 0-6 6v2.386l-.97 1.94A1.5 1.5 0 0 0 6.372 16.5h11.256a1.5 1.5 0 0 0 1.341-2.424l-.969-1.94V9.75a6 6 0 0 0-6-6Zm0 17.25a2.25 2.25 0 0 0 2.122-1.5H9.878A2.25 2.25 0 0 0 12 21Z" />
                                            </svg>
                                            <span>{{ $locale === 'ne' ? 'सूचना तथा अपडेट' : 'Notices & Updates' }}</span>
                                        </span>
                                        <span class="landing-hero-quick-link-index">01</span>
                                    </a>

                                    <a href="#resources" class="landing-hero-quick-link">
                                        <span class="landing-hero-quick-link-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path d="M4.5 3.75A2.25 2.25 0 0 0 2.25 6v12A2.25 2.25 0 0 0 4.5 20.25h15A2.25 2.25 0 0 0 21.75 18V9.31a2.25 2.25 0 0 0-.659-1.591l-3.81-3.81A2.25 2.25 0 0 0 15.69 3.25H4.5Zm10.5 1.72 3.53 3.53H15.75a.75.75 0 0 1-.75-.75V5.47Zm-6.75 6.78a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm0 3a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Z" />
                                            </svg>
                                            <span>{{ $locale === 'ne' ? 'स्रोत तथा सामग्री' : 'Resources & Materials' }}</span>
                                        </span>
                                        <span class="landing-hero-quick-link-index">02</span>
                                    </a>

                                    <a href="{{ route('gallery.index') }}" class="landing-hero-quick-link">
                                        <span class="landing-hero-quick-link-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path d="M3.75 5.25A2.25 2.25 0 0 1 6 3h12a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 18 21H6a2.25 2.25 0 0 1-2.25-2.25V5.25Zm3.75 2.25a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm10.5 9-.01-1.125a1.5 1.5 0 0 0-.44-1.06l-2.5-2.5a1.5 1.5 0 0 0-2.121 0l-.689.689-1.19-1.19a1.5 1.5 0 0 0-2.122 0L6 14.25V16.5h12Z" />
                                            </svg>
                                            <span>{{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}</span>
                                        </span>
                                        <span class="landing-hero-quick-link-index">03</span>
                                    </a>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>

            <section id="about" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div>
                        <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            {{ $locale === 'ne' ? 'परिचय' : 'Introduction' }}
                        </p>
                        <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                            {{ $locale === 'ne' ? 'विभागको परिचय' : 'About the Department' }}
                        </h2>
                        <div class="landing-panel mt-5 rounded-[2rem] p-6 dark:bg-[#191919]">
                            <div class="whitespace-pre-wrap text-sm leading-7 text-gray-900 dark:text-white">
                                {{ $aboutText ?: ($locale === 'ne'
                                    ? 'यो विभागले विद्यार्थी, शिक्षक र अभिभावकका लागि एकीकृत सूचना प्रणालीमार्फत शैक्षिक व्यवस्थापनलाई डिजिटल बनाउँछ।'
                                    : 'This department portal brings academics, resources, and communication together for students, faculty, and parents.') }}
                            </div>
                        </div>
                        <div class="mt-4">
                                <a href="{{ route('department.about', ['id' => $department?->id ?? 1]) }}"
                               class="shine-button inline-flex items-center gap-2 rounded-full bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                                <span>{{ $locale === 'ne' ? 'थप पढ्नुहोस्' : 'Read More' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M16.72 7.72a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L21.44 12l-4.72-4.28a.75.75 0 0 1 0-1.06zM12 7a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-1.5 0V7.75A.75.75 0 0 1 12 7z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="landing-panel lift-card rounded-3xl p-5 dark:bg-[#191919]">
                                <div class="text-xs font-semibold text-gray-600 dark:text-gray-50">{{ $locale === 'ne' ? 'स्थापना वर्ष' : 'Established' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $department?->established_year ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not available') }}
                                </div>
                            </div>
                            <div class="landing-panel lift-card rounded-3xl p-5 dark:bg-[#191919]">
                                <div class="text-xs font-semibold text-gray-600 dark:text-gray-50">{{ $locale === 'ne' ? 'दर्ता नं.' : 'Registration No.' }}</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $department?->registration_number ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not available') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="programs" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                        <div class="lg:col-span-5">
                            <div class="landing-panel rounded-[2rem]">
                                @if ($hasProgramsImage)
                                    <img src="{{ $programsImageUrl }}" alt="{{ $programsTitle ?: ($locale === 'ne' ? 'कार्यक्रम तस्वीर' : 'Program image') }}" class="h-72 w-full object-cover sm:h-80" />
                                @else
                                    <div class="flex h-72 items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-red-950/70 px-6 text-center sm:h-80">
                                        <div class="max-w-sm">
                                            <p class="section-chip mx-auto bg-white/10 text-white ring-1 ring-white/15">
                                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-400"></span>
                                                {{ $locale === 'ne' ? 'कार्यक्रम फोटो' : 'Programs Photo' }}
                                            </p>
                                            <h3 class="mt-5 text-xl font-semibold text-white sm:text-2xl">
                                                {{ $programsTitle ?: ($locale === 'ne' ? 'शैक्षिक कार्यक्रम' : 'Academic Programs') }}
                                            </h3>
                                            <p class="mt-3 text-sm leading-6 text-white/75">
                                                {{ $locale === 'ne'
                                                    ? 'एडमिन सेटिङबाट कार्यक्रम फोटो अपलोड गरेपछि यहाँ देखाइनेछ।'
                                                    : 'This area will show the Academic Programs photo from admin settings once it is uploaded.' }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="lg:col-span-7">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                        {{ $locale === 'ne' ? 'कार्यक्रम' : 'Programs' }}
                                    </p>
                                    <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                                        {{ $programsTitle ?: ($locale === 'ne' ? 'शैक्षिक कार्यक्रम' : 'Academic Programs') }}
                                    </h2>
                                    <p class="mt-2 text-sm text-gray-900 dark:text-white">
                                        {{ $locale === 'ne' ? 'कार्यक्रम र सिकाइ यात्राको छोटो परिचय।' : 'A short overview of our programs and learning path.' }}
                                    </p>
                                </div>
                                <a href="#curriculum" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                                    <span>{{ $locale === 'ne' ? 'पाठ्यक्रम हेर्नुहोस्' : 'View Curriculum' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>

                            <div class="landing-panel mt-5 rounded-[2rem] bg-white p-6 text-sm leading-7 text-gray-700 dark:from-[#191919]/90 dark:to-[#191919] dark:text-gray-50">
                                {!! nl2br(e($programsContent ?: ($locale === 'ne'
                                    ? 'हाम्रो विभागले विद्यार्थीहरूको सीप विकास, व्यावहारिक प्रयोगशाला अभ्यास र उद्योगसँग जोडिएको सिकाइलाई प्राथमिकता दिने शैक्षिक कार्यक्रमहरू सञ्चालन गर्दछ।'
                                    : 'Our department runs academic programs focused on practical learning, lab-based skills, and industry-ready outcomes.'))) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="curriculum" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8" x-data="subjectCatalog({
                initialSemester: @js($defaultSemester),
                maxItems: @js($subjectPreviewMax),
                indexUrl: @js(route('subjects.index')),
                subjects: @js($subjectPayload),
                locale: @js($locale),
            })">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                                {{ $locale === 'ne' ? 'हाम्रा पाठ्यक्रम' : 'Our Courses' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-900 dark:text-white">
                                {{ $locale === 'ne' ? 'सेमेस्टर अनुसार विषयहरू हेर्नुहोस्।' : 'Browse semester-wise course highlights.' }}
                            </p>
                        </div>
                    </div>

                    <div class="landing-toolbar mt-6 flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="text-sm font-medium text-gray-800 dark:text-white" x-text="resultText"></div>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($semesterSummary->take(4) as $sem => $meta)
                                @continue(empty($sem))
                                <button type="button"
                                    class="rounded-xl border px-4 py-2 text-sm font-semibold shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    :class="String(semester) === @js((string) $sem) ? 'border-red-600 bg-red-600 text-white shadow-red-900/10 dark:border-red-400 dark:bg-red-400 dark:text-gray-950' : 'border-white/60 bg-white/80 text-gray-700 hover:-translate-y-0.5 hover:bg-white dark:border-gray-800 dark:bg-[#191919] dark:text-gray-50 dark:hover:bg-slate-900'"
                                    @click="semester=@js((string) $sem); query=''; openId=null;">
                                    {{ $locale === 'ne' ? "{$sem} सेमेस्टर" : "Semester {$sem}" }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <template x-for="s in visibleSubjects" :key="s.id">
                        <div class="landing-panel lift-card flex flex-col overflow-hidden rounded-[1.75rem] border-l-4 bg-white duration-300 dark:bg-slate-900" 
                             :class="[
                                 s.category === 'Core' ? 'border-l-red-500 dark:border-l-red-400' : 
                                 s.category === 'Elective' ? 'border-l-orange-500 dark:border-l-orange-400' : 
                                 s.category === 'Graduate' ? 'border-l-green-600 dark:border-l-green-400' : 
                                 'border-l-blue-500 dark:border-l-blue-400'
                             ]">
                            <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-6 py-3 dark:border-gray-700 dark:bg-gray-800"
                                 :class="s.has_lab ? 'dark:bg-gray-800' : 'dark:bg-gray-900'">
                                <div class="text-base font-bold text-gray-900 dark:text-white" x-text="s.title"></div>
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
                                <div class="mt-3 line-clamp-3 text-sm text-gray-900 dark:text-white" x-text="s.description || fallbackDescription"></div>

                                <div x-show="openId === s.id" x-cloak class="mt-4 rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50">
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
                                            : 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-950/50 dark:text-red-200 dark:hover:bg-red-950/50'"
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

                    <div x-show="visibleSubjects.length === 0" class="landing-panel rounded-[1.75rem] border-l-4 border-l-red-300 bg-gradient-to-br from-red-50 to-white p-10 text-center text-sm text-gray-700 dark:border-red-900/40 dark:from-red-950/20 dark:to-[#191919]/40 dark:text-gray-50 md:col-span-3">
                        {{ $locale === 'ne' ? 'हाल कुनै विषय उपलब्ध छैन।' : 'No courses available yet.' }}
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <a :href="viewAllUrl"
                       class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                        <span>{{ $locale === 'ne' ? 'सबै पाठ्यक्रम हेर्नुहोस्' : 'Explore All Courses' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                {{--
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                            {{ $locale === 'ne' ? 'विषय र पाठ्यक्रम' : 'Subjects & Curriculum' }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-900 dark:text-white">
                            {{ $locale === 'ne' ? 'विषय खोज्नुहोस् र विवरण/पूर्व-आवश्यकता हेर्नुहोस्।' : 'Search subjects and view details, prerequisites, and credits.' }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <label class="sr-only" for="subjectSearch">{{ $locale === 'ne' ? 'विषय खोज' : 'Search subjects' }}</label>
                        <input id="subjectSearch" type="search" x-model.trim="query" placeholder="{{ $locale === 'ne' ? 'विषय/कोड खोज्नुहोस्…' : 'Search by subject or code…' }}"
                            class="w-full rounded-xl border-gray-300 bg-white px-4 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-800 dark:bg-[#191919] dark:text-white sm:w-72" />

                        <label class="sr-only" for="semesterFilter">{{ $locale === 'ne' ? 'सेमेस्टर' : 'Semester' }}</label>
                        <select id="semesterFilter" x-model="semester"
                            class="w-full rounded-xl border-gray-300 bg-white px-4 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-800 dark:bg-[#191919] dark:text-white sm:w-44">
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
                        <div class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-[#191919]">
                            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $locale === 'ne' ? 'विषय सूची' : 'Subject catalog' }}
                                    </div>
                                    <div class="text-gray-600 dark:text-white" x-text="resultText"></div>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-200 dark:divide-gray-800">
                                <template x-for="s in visibleSubjects" :key="s.id">
                                    <div class="px-6 py-4">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-900 dark:text-gray-50" x-text="s.code || '—'"></span>
                                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300" x-text="semesterLabel(s.semester)"></span>
                                                    <template x-if="s.has_lab">
                                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $locale === 'ne' ? 'ल्याब' : 'Lab' }}</span>
                                                    </template>
                                                    <template x-if="s.is_elective_open">
                                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $locale === 'ne' ? 'इलेक्टिभ' : 'Elective' }}</span>
                                                    </template>
                                                </div>
                                                <div class="mt-2 truncate text-base font-semibold text-gray-900 dark:text-white" x-text="s.title"></div>
                                                <div class="mt-1 line-clamp-2 text-sm text-gray-900 dark:text-white" x-text="s.description || fallbackDescription"></div>
                                                <div class="mt-2 text-xs font-medium text-gray-800 dark:text-white" x-text="creditText(s.credits)"></div>
                                            </div>

                                            <div class="shrink-0">
                                                <button type="button" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-800 dark:bg-[#191919] dark:text-white dark:hover:bg-gray-900"
                                                    @click="toggle(s.id)">
                                                    <span x-text="openId === s.id ? closeLabel : detailLabel"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <div x-show="openId === s.id" x-transition class="mt-4 rounded-2xl bg-gray-50 p-4 text-sm text-gray-700 dark:bg-gray-900/60 dark:text-gray-50">
                                            <div class="grid gap-4 sm:grid-cols-2">
                                                <div>
                                                    <div class="text-gray-800 dark:text-white">{{ $locale === 'ne' ? 'क्रेडिट' : 'Credits' }}</div>
                                                    <div class="mt-1 font-semibold text-gray-900 dark:text-white" x-text="s.credits ?? '—'"></div>
                                                </div>
                                                <div>
                                                    <div class="text-gray-800 dark:text-white">{{ $locale === 'ne' ? 'पूर्व-आवश्यकता' : 'Prerequisite' }}</div>
                                                    <div class="mt-1 font-semibold text-gray-900 dark:text-white" x-text="s.prerequisite || '—'"></div>
                                                </div>
                                            </div>
                                            <template x-if="(s.teachers || []).length">
                                                <div class="mt-4">
                                                    <div class="text-gray-800 dark:text-white">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</div>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        <template x-for="t in s.teachers" :key="t">
                                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200 dark:bg-[#191919] dark:text-gray-50 dark:ring-gray-800" x-text="t"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="filteredSubjects.length === 0" class="px-6 py-10 text-center text-sm text-gray-900 dark:text-white">
                                    {{ $locale === 'ne' ? 'कुनै विषय भेटिएन।' : 'No subjects found.' }}
                                </div>
                            </div>

                            <div x-show="showViewAll" class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                                <a :href="viewAllUrl"
                                   class="shine-button inline-flex w-full items-center justify-center gap-2 rounded-full bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                                    <span>{{ $locale === 'ne' ? 'सबै विषय हेर्नुहोस्' : 'View all subjects' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-[#191919]">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $locale === 'ne' ? 'सेमेस्टर अनुसार सारांश' : 'Semester-wise overview' }}
                            </h3>
                            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                                @foreach ($semesterSummary as $sem => $meta)
                                    @continue(empty($sem))
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/60">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $locale === 'ne' ? "{$sem} सेमेस्टर" : "Semester {$sem}" }}
                                            </div>
                                            <div class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200 dark:bg-[#191919] dark:text-gray-50 dark:ring-gray-800">
                                                {{ $meta['count'] }} {{ $locale === 'ne' ? 'विषय' : 'subjects' }}
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-800 dark:text-white">
                                            {{ $locale === 'ne' ? 'कुल क्रेडिट' : 'Total credits' }}: <span class="font-semibold text-gray-900 dark:text-white">{{ $meta['credits'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                --}}
            </section>

            <section id="faculty" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                                {{ $locale === 'ne' ? 'हाम्रा शिक्षक' : 'Meet Our Faculty' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-900 dark:text-white">
                                {{ $locale === 'ne' ? 'विभागका सक्रिय शिक्षकहरूको झलक।' : 'Meet our active department faculty.' }}
                            </p>
                        </div>
                    </div>

                    @if (($hods ?? collect())->isNotEmpty())
                        <div class="mt-8">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
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
                                    <div class="landing-panel lift-card overflow-hidden rounded-[1.75rem] bg-white dark:bg-[#191919]">
                                        <div class="flex h-52 items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 sm:h-56">
                                            @if (!empty($leaderPhoto))
                                                <img src="{{ $leaderPhoto }}" alt="{{ $leaderName }}" class="h-full w-full object-cover" />
                                            @else
                                                <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-red-100/20 text-red-600 ring-1 ring-red-200/30 dark:bg-red-950/20 dark:text-red-400">
                                                    <span class="text-sm font-bold">{{ $leaderInitial }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="faculty-star-display">
                                            <div class="faculty-star-border">
                                                <span class="faculty-star">★</span>
                                            </div>
                                        </div>
                                        <div class="border-t border-slate-100 bg-white p-5 dark:border-slate-800 dark:bg-[#191919]">
                                            <div class="truncate text-sm font-bold text-gray-900 text-center dark:text-white">{{ $leaderName }}</div>
                                            <div class="mt-1 text-xs font-semibold text-red-600 text-center dark:text-red-400">{{ $locale === 'ne' ? 'विभाग प्रमुख' : 'HOD / Admin' }}</div>
                                            <div class="mt-3 text-xs text-gray-600 text-center dark:text-gray-400">
                                                {{ $leaderMeta ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not specified') }}
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
                            <div class="landing-panel lift-card overflow-hidden rounded-[1.75rem] bg-white dark:bg-[#191919]">
                                <div class="flex h-52 items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 sm:h-56">
                                    @if (!empty($photoUrl))
                                        <img src="{{ $photoUrl }}" alt="{{ $name }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-red-100/20 text-red-600 ring-1 ring-red-200/30 dark:bg-red-950/20 dark:text-red-400">
                                            <span class="text-sm font-bold">{{ $initials ?: Str::of($name)->substr(0, 1)->upper() }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="faculty-star-display">
                                    <div class="faculty-star-border">
                                        <span class="faculty-star">★</span>
                                    </div>
                                </div>
                                <div class="border-t border-slate-100 bg-white p-4 dark:border-slate-800 dark:bg-[#191919]">
                                    <div class="truncate text-sm font-bold text-gray-900 text-center dark:text-white">{{ $name }}</div>
                                    <div class="mt-1 text-xs font-semibold text-red-600 text-center dark:text-red-400">{{ $titleText }}</div>
                                    <div class="mt-3 text-xs text-gray-600 text-center dark:text-gray-400">
                                        {{ $expertiseText ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not specified') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="landing-panel rounded-[1.75rem] border-dashed border-red-200 bg-gradient-to-br from-white to-red-50 p-10 text-center text-sm text-gray-700 dark:border-red-900/40 dark:bg-gradient-to-br dark:from-[#191919] dark:to-red-950/10 dark:text-gray-50 sm:col-span-2 lg:col-span-4">
                                {{ $locale === 'ne' ? 'हाल कुनै शिक्षक उपलब्ध छैन।' : 'No faculty records available yet.' }}
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('faculty.index') }}"
                           class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                            <span>{{ $locale === 'ne' ? 'सबै शिक्षक हेर्नुहोस्' : 'View All Faculty' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>

            <section id="notices" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8" x-data="{ open: false, notice: null }">
                <div class="landing-shell mx-auto w-full landing-stage">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            {{ $locale === 'ne' ? 'सूचना' : 'Updates' }}
                        </p>
                        <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                            {{ $locale === 'ne' ? 'समाचार तथा कार्यक्रम' : 'News & Events' }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-900 dark:text-white">
                            {{ $locale === 'ne' ? 'नयाँ सूचना र घोषणा।' : 'Latest announcements and updates.' }}
                        </p>
                    </div>
                    <a href="{{ route('public.notices.index') }}" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                        <span>{{ $locale === 'ne' ? 'सबै सूचना हेर्नुहोस्' : 'View all notices' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse (($notices ?? collect()) as $n)
                        <button type="button" class="landing-panel lift-card text-left rounded-[1.75rem] bg-white p-6 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-slate-900"
                            @click="notice=@js([
                                'title' => $n->localized_title,
                                'date' => $n->formatted_date,
                                'audience' => $n->localized_audience_label,
                                'priority' => $n->localized_priority_label,
                                'full' => $n->localized_message,
                            ]); open=true;">
                            <div class="flex items-start justify-between gap-4 border-b border-red-100 pb-4 dark:border-red-900/25">
                                <div class="min-w-0">
                                    <div class="text-gray-800 dark:text-white">{{ $n->formatted_date }}</div>
                                    <div class="mt-2 line-clamp-2 text-base font-bold text-gray-900 dark:text-white">{{ $n->localized_title }}</div>
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">
                                        {{ $n->localized_priority_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-gray-900 dark:text-white">
                                {{ Str::limit(strip_tags($n->localized_message), 120) }}
                            </div>
                            <div class="mt-5 flex items-center justify-between text-xs font-semibold text-gray-800 dark:text-white">
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
                        <div class="landing-panel rounded-[1.75rem] border-l-4 border-l-red-300 bg-white p-10 text-center text-sm text-gray-700 dark:border-red-900/40 dark:bg-slate-900 dark:text-gray-50 md:col-span-2 lg:col-span-3">
                            {{ $locale === 'ne' ? 'हाल कुनै सूचना छैन।' : 'No notices published yet.' }}
                        </div>
                    @endforelse
                </div>

                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true">
                    <div class="absolute inset-0 bg-gray-950/70" aria-hidden="true"></div>
                    <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-red-100 dark:bg-slate-900 dark:ring-red-900/30">
                        <div class="h-1 w-full bg-red-600"></div>
                        <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                            <div class="min-w-0">
                                <div class="text-gray-800 dark:text-white" x-text="notice?.date || ''"></div>
                                <div class="mt-1 truncate text-lg font-bold text-gray-900 dark:text-white" x-text="notice?.title || ''"></div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-900 dark:text-gray-50" x-text="notice?.audience || ''"></span>
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-red-700 dark:bg-red-950/40 dark:text-red-300" x-text="notice?.priority || ''"></span>
                                </div>
                            </div>
                            <button type="button" class="rounded-xl border border-gray-200 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white dark:hover:bg-gray-800" @click="open=false" aria-label="{{ $locale === 'ne' ? 'बन्द गर्नुहोस्' : 'Close' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M6.22 6.22a.75.75 0 0 1 1.06 0L12 10.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L13.06 12l4.72 4.72a.75.75 0 1 1-1.06 1.06L12 13.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L10.94 12 6.22 7.28a.75.75 0 0 1 0-1.06Z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 py-6">
                            <div class="prose prose-sm max-w-none text-gray-800 dark:prose-invert dark:text-gray-50" x-html="notice?.full || ''"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="exam-result" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div class="space-y-8">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <p class="section-chip bg-rose-100 text-rose-700 ring-1 ring-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:ring-rose-900/50">
                                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                    {{ $locale === 'ne' ? 'परीक्षा परिणाम' : 'Exam Result' }}
                                </p>
                                <span class="rounded-full border border-rose-200 bg-white px-3 py-1 text-xs font-semibold text-rose-700 shadow-sm dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-300">
                                    {{ $locale === 'ne' ? 'Published results only' : 'Published results only' }}
                                </span>
                            </div>

                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                                {{ $locale === 'ne' ? 'मार्कसिटको लागि परीक्षा परिणाम खोज्नुहोस्' : 'Search published exam results' }}
                            </h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-700 dark:text-gray-50">
                                {{ $locale === 'ne'
                                    ? 'Academic Year, Semester, Exam Category, Assessment Number, Student ID / Roll No, र DOB प्रयोग गरेर आफ्नो प्रकाशित marksheet हेर्नुहोस्।'
                                    : 'Use Academic Year, Semester, Exam Category, Assessment Number, Student ID / Roll No, and DOB to open the published marksheet for each exam.' }}
                            </p>

                            @if (session('error'))
                                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 shadow-sm dark:border-rose-900/40 dark:bg-rose-950/30 dark:text-rose-200">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if($examResultSearchPerformed && $examResultError)
                                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 shadow-sm dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-200">
                                    {{ $examResultError }}
                                </div>
                            @endif

                            <form method="GET" action="{{ route('home') }}" class="mt-6 space-y-5 rounded-[1.75rem] border border-[var(--landing-border)] bg-white p-5 shadow-[0_20px_40px_rgba(15,23,42,0.08)] backdrop-blur dark:bg-[#191919]" @submit="setTimeout(() => { const elem = document.getElementById('exam-result-panel'); if(elem) elem.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 300)">
                                <input type="hidden" name="search_exam_result" value="1">

                                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                    <div>
                                        <label for="landingExamResultAcademicYear" class="mb-1.5 block text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $locale === 'ne' ? 'Academic Year (BS)' : 'Academic Year (BS)' }}
                                        </label>
                                        <select id="landingExamResultAcademicYear" name="academic_year" class="landing-select w-full px-3 py-2.5 text-sm text-gray-900 dark:text-white">
                                            <option value="">{{ $locale === 'ne' ? 'Academic Year छान्नुहोस्' : 'Select Academic Year' }}</option>
                                            @foreach($examResultMeta['years'] as $year)
                                                <option value="{{ $year }}" {{ ($examResultFilters['academic_year'] ?? '') === $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="landingExamResultSemester" class="mb-1.5 block text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $locale === 'ne' ? 'Semester' : 'Semester' }}
                                        </label>
                                        <select id="landingExamResultSemester" name="semester" class="landing-select w-full px-3 py-2.5 text-sm text-gray-900 dark:text-white">
                                            <option value="">{{ $locale === 'ne' ? 'Semester छान्नुहोस्' : 'Select Semester' }}</option>
                                            @foreach($examResultMeta['semesters'] as $semester)
                                                <option value="{{ $semester }}" {{ (string) ($examResultFilters['semester'] ?? '') === (string) $semester ? 'selected' : '' }}>
                                                    {{ $locale === 'ne' ? 'Semester ' : 'Semester ' }}{{ $semester }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="landingExamResultCategory" class="mb-1.5 block text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $locale === 'ne' ? 'Exam Category' : 'Exam Category' }}
                                        </label>
                                        <select id="landingExamResultCategory" name="exam_category" class="landing-select w-full px-3 py-2.5 text-sm text-gray-900 dark:text-white">
                                            <option value="assessment" {{ ($examResultFilters['exam_category'] ?? 'assessment') === 'assessment' ? 'selected' : '' }}>
                                                {{ $locale === 'ne' ? 'Assessment' : 'Assessment' }}
                                            </option>
                                            <option value="ctevt" {{ ($examResultFilters['exam_category'] ?? '') === 'ctevt' ? 'selected' : '' }}>
                                                {{ $locale === 'ne' ? 'CTEVT' : 'CTEVT' }}
                                            </option>
                                        </select>
                                    </div>

                                    <div id="landingExamResultAssessmentWrap" class="sm:col-span-2 xl:col-span-1 {{ ($examResultFilters['exam_category'] ?? 'assessment') !== 'assessment' ? 'hidden' : '' }}">
                                        <label for="landingExamResultAssessmentNumber" class="mb-1.5 block text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $locale === 'ne' ? 'Assessment Number' : 'Assessment Number' }}
                                        </label>
                                        <select id="landingExamResultAssessmentNumber" name="assessment_number" data-selected="{{ $examResultFilters['assessment_number'] ?? '' }}" class="landing-select w-full px-3 py-2.5 text-sm text-gray-900 dark:text-white">
                                            <option value="">{{ $locale === 'ne' ? 'All' : 'All' }}</option>
                                            @foreach($examResultAssessmentNumbers as $number)
                                                <option value="{{ $number }}" {{ (string) ($examResultFilters['assessment_number'] ?? '') === (string) $number ? 'selected' : '' }}>
                                                    {{ $locale === 'ne' ? 'Assessment ' : 'Assessment ' }}{{ $number }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="landingExamResultStudentId" class="mb-1.5 block text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $locale === 'ne' ? 'Student ID / Roll No' : 'Student ID / Roll No' }}
                                        </label>
                                        <input id="landingExamResultStudentId" name="student_id" type="text" value="{{ $examResultFilters['student_id'] ?? '' }}" placeholder="002" class="landing-input w-full px-3 py-2.5 text-sm text-gray-900 dark:text-white" required>
                                    </div>

                                    <div>
                                        <label for="landingExamResultDobBs" class="mb-1.5 block text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $locale === 'ne' ? 'Date of Birth (BS)' : 'Date of Birth (BS)' }}
                                        </label>
                                        <div class="relative">
                                            <input id="landingExamResultDobBs" name="dob_bs" type="text" placeholder="YYYY-MM-DD or pick date" value="{{ $examResultFilters['dob_bs'] ?? '' }}" class="bs-date landing-input w-full pr-10 px-3 py-2.5 text-sm text-gray-900 dark:text-white" autocomplete="off">
                                            <button type="button" aria-label="Pick BS date" onclick="event?.preventDefault(); event?.stopPropagation(); window.openBsDatePicker?.('landingExamResultDobBs'); return false;" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-700 dark:text-gray-50 dark:hover:text-gray-200">
                                                <i class="bi bi-calendar3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    <button type="submit" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-orange-900/15 transition hover:-translate-y-0.5 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                                        <i class="bi bi-search"></i>
                                        {{ $locale === 'ne' ? 'Exam Result Search' : 'Search Exam Result' }}
                                    </button>
                                    <a href="{{ route('home') }}#exam-result" class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-rose-300 hover:text-rose-700 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-50 dark:hover:border-rose-800 dark:hover:text-rose-300">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        {{ $locale === 'ne' ? 'Reset' : 'Reset' }}
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div>
                            <div id="exam-result-panel">
                                @if($examResultSearchPerformed && $examResultPayload)
                                    @php
                                        $marks = collect($examResultPayload['marksheetData']['exam_marks'] ?? []);
                                        $departmentEntity = \App\Models\Department::first();
                                        $departmentName = $departmentEntity?->name ?? config('app.name', 'IT DMS');
                                        $departmentAddress = trim(collect([
                                            $departmentEntity?->address ?? null,
                                            $departmentEntity?->city ?? null,
                                            $departmentEntity?->district ?? null,
                                        ])->filter()->implode(', '));
                                        $departmentLogo = $departmentEntity && method_exists($departmentEntity, 'getLogoUrl')
                                            ? $departmentEntity->getLogoUrl()
                                            : asset('images/default-logo.svg');
                                        $studentName = $examResultStudent?->user?->name ?? 'N/A';
                                        $studentId = $examResultStudent?->id ?? 'N/A';
                                        $rollNo = $examResultStudent?->roll_no ?? 'N/A';
                                        $academicYear = $examResultFilters['academic_year'] ?? 'All';
                                        $semester = $examResultFilters['semester'] ?? 'All';
                                        $category = strtolower((string) ($examResultFilters['exam_category'] ?? 'assessment'));
                                        $examCategoryLabel = strtoupper($category);
                                        $dob_bs = $examResultFilters['dob_bs'] ?? 'N/A';
                                        $programName = $examResultStudent?->program ?: ($examResultStudent?->department ?: ($departmentEntity?->short_name ?: ($departmentEntity?->name ?? 'N/A')));
                                        $examTitle = $examResultPayload['selectedExamName'] ?? 'Marksheet';
                                        $grandTotal = (float) ($examResultPayload['marksheetData']['total_obtained'] ?? 0);
                                        $totalFull = (float) ($examResultPayload['marksheetData']['total_full'] ?? 0);
                                        $percentage = (float) ($examResultPayload['marksheetData']['percentage'] ?? 0);
                                        $grade = $examResultPayload['marksheetData']['grade'] ?? '-';
                                        $result = strtoupper((string) ($examResultPayload['marksheetData']['result'] ?? 'FAIL'));
                                    @endphp

                                    <style>
                                        .marksheet-container {
                                            background: #ffffff;
                                            border: 1.5px solid #222222;
                                            padding: 14px;
                                            max-width: 100%;
                                            font-family: Arial, sans-serif;
                                            color: #111111;
                                        }
                                        .brand-grid {
                                            display: grid;
                                            grid-template-columns: 88px 1fr;
                                            gap: 14px;
                                            align-items: start;
                                            border-bottom: 1px solid #222222;
                                            padding-bottom: 10px;
                                            margin-bottom: 14px;
                                        }
                                        .logo-box {
                                            width: 72px;
                                            height: 72px;
                                            object-fit: contain;
                                        }
                                        .brand-center {
                                            text-align: center;
                                        }
                                        .brand-title {
                                            font-size: 26px;
                                            font-weight: 700;
                                            line-height: 1.2;
                                        }
                                        .brand-subtitle {
                                            font-size: 22px;
                                            font-weight: 700;
                                            margin-top: 4px;
                                            text-transform: uppercase;
                                        }
                                        .college-address {
                                            margin-top: 6px;
                                            font-size: 12px;
                                            line-height: 1.4;
                                        }
                                        .section-heading {
                                            margin-top: 14px;
                                            border: 1px solid #222222;
                                            background: #ececec;
                                            text-align: center;
                                            padding: 7px 10px;
                                            font-size: 13px;
                                            font-weight: 700;
                                            text-transform: uppercase;
                                            letter-spacing: 0.04em;
                                        }
                                        .info-table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            margin-top: 0;
                                            border: 1px solid #222222;
                                            border-top: 0;
                                        }
                                        .info-table td {
                                            border: 1px solid #222222;
                                            padding: 10px 8px;
                                            vertical-align: top;
                                            width: 25%;
                                        }
                                        .info-label {
                                            font-size: 10px;
                                            font-weight: 700;
                                            text-transform: uppercase;
                                            letter-spacing: 0.04em;
                                            color: #333333;
                                        }
                                        .info-value {
                                            margin-top: 6px;
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: #111111;
                                        }
                                        .marks-table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            margin-top: 0;
                                            border: 1px solid #222222;
                                            border-top: 0;
                                        }
                                        .marks-table th {
                                            background: #f0f0f0;
                                            color: #111111;
                                            text-transform: uppercase;
                                            font-size: 10px;
                                            font-weight: 700;
                                            text-align: center;
                                            border: 1px solid #222222;
                                            padding: 6px;
                                        }
                                        .marks-table td {
                                            border: 1px solid #222222;
                                            padding: 6px;
                                            font-size: 11px;
                                            text-align: center;
                                        }
                                        .marks-table td:first-child {
                                            text-align: center;
                                        }
                                        .marks-table td:nth-child(2) {
                                            text-align: left;
                                            font-weight: 700;
                                        }
                                        .marks-table tfoot td {
                                            background: #f0f0f0;
                                            font-weight: 700;
                                        }
                                        .summary-table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            margin-top: 0;
                                            border: 1px solid #222222;
                                            border-top: 0;
                                        }
                                        .summary-table td {
                                            border: 1px solid #222222;
                                            padding: 10px 8px;
                                            text-align: center;
                                        }
                                        .summary-label {
                                            font-size: 10px;
                                            font-weight: 700;
                                            text-transform: uppercase;
                                            letter-spacing: 0.04em;
                                        }
                                        .summary-value {
                                            margin-top: 6px;
                                            font-size: 14px;
                                            font-weight: 700;
                                        }
                                    </style>

                                    <div class="marksheet-container">
                                        <div class="brand-grid">
                                            <div>
                                                <img src="{{ $departmentLogo }}" alt="Logo" class="logo-box">
                                            </div>
                                            <div class="brand-center">
                                                <div class="brand-title">{{ $departmentName }}</div>
                                                <div class="brand-subtitle">Academic Transcript</div>
                                                <div class="college-address" style="margin-top: 4px; text-align: center; font-size: 14px;">{{ $departmentAddress ?: 'Department Address' }}</div>
                                                <div class="college-address">
                                                    Academic Year: {{ $academicYear }} | Semester: {{ $semester }} | Program: {{ $programName }} | Category: {{ $examCategoryLabel }} | Exam: {{ $examTitle }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="section-heading">Student Information</div>

                                        <table class="info-table">
                                            <tr>
                                                <td>
                                                    <div class="info-label">Student Name</div>
                                                    <div class="info-value">{{ $studentName }}</div>
                                                </td>
                                                <td>
                                                    <div class="info-label">Student ID</div>
                                                    <div class="info-value">{{ $studentId }}</div>
                                                </td>
                                                <td>
                                                    <div class="info-label">Roll Number</div>
                                                    <div class="info-value">{{ $rollNo }}</div>
                                                </td>
                                                <td>
                                                    <div class="info-label">Semester</div>
                                                    <div class="info-value">{{ $semester }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="info-label">Academic Year (BS)</div>
                                                    <div class="info-value">{{ $academicYear }}</div>
                                                </td>
                                                <td>
                                                    <div class="info-label">DOB (BS)</div>
                                                    <div class="info-value">{{ $dob_bs }}</div>
                                                </td>
                                                <td>
                                                    <div class="info-label">Exam Category</div>
                                                    <div class="info-value">{{ $examCategoryLabel }}</div>
                                                </td>
                                                <td>
                                                    <div class="info-label">Public Entries</div>
                                                    <div class="info-value">{{ $marks->count() }}</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="section-heading">Academic Performance</div>

                                        <table class="marks-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:6%;">S.N.</th>
                                                    <th style="width:40%;">Subject</th>
                                                    <th style="width:12%;">Full Marks</th>
                                                    <th style="width:12%;">Pass Mark</th>
                                                    <th style="width:12%;">Marks Obtained</th>
                                                    <th style="width:10%;">Percentage</th>
                                                    <th style="width:8%;">Grade</th>
                                                    <th style="width:10%;">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($marks as $index => $mark)
                                                    @php
                                                        $subjectName = $mark->subject?->subject_name ?? 'N/A';
                                                        $fullMarks = $mark->full_marks ?? $mark->exam?->full_marks;
                                                        $passMark = $mark->passing_marks ?? $mark->exam?->passing_marks;
                                                        $obtainedValue = $mark->marks_obtained;
                                                        $obtained = is_null($obtainedValue) ? '—' : number_format((float) $obtainedValue, 2);
                                                        $rowPercentage = is_null($obtainedValue) || is_null($fullMarks) || (float) $fullMarks <= 0
                                                            ? null
                                                            : round(((float) $obtainedValue / (float) $fullMarks) * 100, 2);
                                                        $rowGrade = $mark->grade ?? '—';
                                                        $rowResult = (($mark->percentage ?? $rowPercentage ?? 0) >= 40) ? 'PASS' : 'FAIL';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $subjectName }}</td>
                                                        <td>{{ is_null($fullMarks) ? '—' : number_format((float) $fullMarks, 2) }}</td>
                                                        <td>{{ is_null($passMark) ? '—' : number_format((float) $passMark, 2) }}</td>
                                                        <td>{{ $obtained }}</td>
                                                        <td>{{ is_null($rowPercentage) ? '—' : number_format($rowPercentage, 2) . '%' }}</td>
                                                        <td>{{ $rowGrade }}</td>
                                                        <td>{{ $rowResult }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" style="text-align: center; padding: 18px 10px;">No marks found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" style="text-align: right;">Grand Total</td>
                                                    <td>{{ number_format($grandTotal, 2) }}</td>
                                                    <td>{{ number_format($percentage, 2) }}%</td>
                                                    <td>{{ $grade }}</td>
                                                    <td>{{ $result }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <div class="section-heading">Result</div>

                                        <table class="summary-table">
                                            <tr>
                                                <td>
                                                    <div class="summary-label">Overall Percentage</div>
                                                    <div class="summary-value">{{ number_format($percentage, 2) }}%</div>
                                                </td>
                                                <td>
                                                    <div class="summary-label">Grade</div>
                                                    <div class="summary-value">{{ $grade }}</div>
                                                </td>
                                                <td>
                                                    <div class="summary-label">Result</div>
                                                    <div class="summary-value">{{ $result }}</div>
                                                </td>
                                                <td>
                                                    <div class="summary-label">Total Obtained</div>
                                                    <div class="summary-value">{{ number_format($grandTotal, 2) }}</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="mt-5 flex flex-wrap gap-3 justify-center" style="margin-top: 15px;">
                                            <a href="{{ $examResultPrintUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-full bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-900/15 transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-400">
                                                <i class="bi bi-printer"></i>
                                                {{ $locale === 'ne' ? 'Print' : 'Print' }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="resources" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8" x-data="documentRepo({ documents: @js($documentPayload), locale: @js($locale) })">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div class="flex flex-col gap-3">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'स्रोत' : 'Resources' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                                {{ $locale === 'ne' ? 'दस्तावेज तथा स्रोत' : 'Documents & Resources' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-900 dark:text-white">
                                {{ $locale === 'ne' ? 'सिलेबस, नोट्स, गाइड र अन्य सामग्री।' : 'Syllabus, notes, guides, and other materials.' }}
                            </p>
                        </div>

                        <div class="landing-toolbar lg:items-center lg:justify-between">
                            <div class="text-sm font-medium text-gray-800 dark:text-white" x-text="resultText"></div>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <label class="sr-only" for="docSearch">{{ $locale === 'ne' ? 'दस्तावेज खोज' : 'Search documents' }}</label>
                                <input id="docSearch" type="search" x-model.trim="query" placeholder="{{ $locale === 'ne' ? 'शीर्षक/विषय खोज्नुहोस्…' : 'Search by title or subject…' }}"
                                    class="landing-input w-full px-4 py-2 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:text-white sm:w-72" />

                                <label class="sr-only" for="docType">{{ $locale === 'ne' ? 'प्रकार' : 'Type' }}</label>
                                <select id="docType" x-model="type"
                                    class="landing-select w-full px-4 py-2 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:text-white sm:w-52">
                                    <option value="all">{{ $locale === 'ne' ? 'सबै प्रकार' : 'All types' }}</option>
                                    <template x-for="t in types" :key="t.value">
                                        <option :value="t.value" x-text="t.label"></option>
                                    </template>
                                </select>
                                <a href="{{ route('public.resources.index') }}" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <span>{{ $locale === 'ne' ? 'सबै स्रोत हेर्नुहोस्' : 'View all resources' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="d in filtered" :key="d.id">
                            <div class="landing-panel lift-card rounded-[1.75rem] border-l-4 bg-white p-6 transition duration-300 dark:bg-slate-900"
                                 :class="[
                                     d.type === 'syllabus' ? 'border-l-blue-500 dark:border-l-blue-400' : 
                                     d.type === 'notes' ? 'border-l-orange-400 dark:border-l-orange-300' : 
                                     d.type === 'guide' ? 'border-l-green-600 dark:border-l-green-500' : 
                                     'border-l-red-300 dark:border-l-red-400'
                                 ]">
                                <div class="flex h-full flex-col">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide transition duration-200"
                                              :class="[
                                                  d.type === 'syllabus' ? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-300' : 
                                                  d.type === 'notes' ? 'border-orange-200 bg-orange-50 text-orange-700 dark:border-orange-900 dark:bg-orange-950/40 dark:text-orange-300' : 
                                                  d.type === 'guide' ? 'border-green-200 bg-green-50 text-green-700 dark:border-green-900 dark:bg-green-950/40 dark:text-green-300' : 
                                                  'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300'
                                              ]"
                                              x-text="d.type_label"></span>
                                        <template x-if="d.size">
                                            <span class="text-gray-600 dark:text-white" x-text="d.size"></span>
                                        </template>
                                    </div>
                                    <div class="mt-4 line-clamp-2 text-base font-bold text-gray-900 dark:text-white" x-text="d.title"></div>
                                    <template x-if="d.subject">
                                        <div class="mt-2 text-sm text-gray-800 dark:text-white">
                                            <span class="font-medium text-gray-900 dark:text-gray-50" x-text="d.subject"></span>
                                        </div>
                                    </template>
                                    <template x-if="d.description">
                                        <div class="mt-3 line-clamp-2 text-sm text-gray-900 dark:text-white" x-text="d.description"></div>
                                    </template>
                                    <div class="mt-3 text-xs text-gray-800 dark:text-white" x-text="uploadedText(d.uploaded_at)"></div>
                                    <div class="mt-6 flex items-center gap-2">
                                        <template x-if="d.url">
                                            <a :href="d.url" target="_blank" rel="noopener" class="shine-button flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                                </svg>
                                                {{ $locale === 'ne' ? 'हेर्नुहोस्' : 'View' }}
                                            </a>
                                        </template>
                                        <template x-if="!d.url">
                                            <span class="flex-1 inline-flex items-center justify-center rounded-lg border border-red-100 bg-red-50/70 px-3 py-2 text-xs font-semibold text-red-300 dark:border-red-900/30 dark:bg-red-950/30 dark:text-red-300/70">
                                                {{ $locale === 'ne' ? 'उपलब्ध छैन' : 'N/A' }}
                                            </span>
                                        </template>

                                        <template x-if="d.download_url">
                                            <a :href="d.download_url" class="landing-panel lift-card flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path fill-rule="evenodd" d="M12 2a.75.75 0 0 1 .75.75v6.69l1.97-1.97a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L7.97 8.53a.75.75 0 0 1 1.06-1.06l1.97 1.97V2.75A.75.75 0 0 1 12 2zM3 14.25a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 14.25v-3.5a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75v-3.5a.75.75 0 0 0-1.5 0v3.5z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $locale === 'ne' ? 'डाउनलोड' : 'Download' }}
                                            </a>
                                        </template>
                                        <template x-if="!d.download_url">
                                            <span class="flex-1 inline-flex items-center justify-center rounded-lg border border-red-100 bg-red-50/70 px-3 py-2 text-xs font-semibold text-red-300 dark:border-red-900/30 dark:bg-red-950/30 dark:text-red-300/70">
                                                {{ $locale === 'ne' ? 'डाउनलोड छैन' : 'No download' }}
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="filtered.length === 0">
                        <div class="landing-panel rounded-[1.75rem] border-l-4 border-l-red-300 bg-white p-10 text-center text-sm text-gray-700 dark:border-red-900/40 dark:bg-slate-900 dark:text-gray-50 sm:col-span-2 lg:col-span-3">
                                {{ $locale === 'ne' ? 'कुनै सामग्री भेटिएन।' : 'No materials found.' }}
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <section class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                                {{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}
                            </h2>
                        <p class="mt-2 text-sm text-gray-900 dark:text-white">
                            {{ $locale === 'ne' ? 'विभागका गतिविधि र सुविधाका झलकहरू।' : 'Highlights from department events and facilities.' }}
                        </p>
                    </div>
                    <a href="{{ route('gallery.index') }}" class="shine-button inline-flex items-center justify-center gap-2 rounded-full bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-green-900/15 transition hover:-translate-y-0.5 hover:gap-3 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                        <span>{{ $locale === 'ne' ? 'सबै ग्यालरी' : 'View all' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M13.28 5.97a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4.5a.75.75 0 0 1 0-1.5h12.75l-3.97-3.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @forelse (($galleryItems ?? collect()) as $g)
                        <a href="{{ route('gallery.index', ['category' => $g->category]) }}" class="landing-panel group relative overflow-hidden rounded-[1.75rem] bg-white dark:bg-slate-900">
                            @if ($g->image_url)
                                <img src="{{ $g->image_url }}" alt="{{ $g->title }}" class="h-44 w-full object-cover transition duration-300 group-hover:scale-105 sm:h-48">
                            @else
                                <div class="flex h-44 items-center justify-center text-sm text-gray-900 dark:text-white sm:h-48">
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
                                            class="shine-button inline-flex items-center justify-center rounded-xl bg-orange-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                                            {{ $locale === 'ne' ? 'डाउनलोड' : 'Download' }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-gray-950/70 via-gray-950/35 to-transparent p-4">
                                <div class="truncate text-sm font-semibold text-white">{{ $g->title }}</div>
                                <div class="mt-1 text-xs font-medium text-white/95">{{ $g->category_text }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="landing-panel rounded-[1.75rem] border-dashed p-10 text-center text-sm text-gray-900 dark:text-white sm:col-span-3 lg:col-span-4">
                            {{ $locale === 'ne' ? 'हाल ग्यालरी सामग्री छैन।' : 'No gallery items available yet.' }}
                        </div>
                    @endforelse
                </div>
            </section>

            <section id="contact" class="landing-section w-full px-4 py-16 sm:px-6 lg:px-8">
                <div class="landing-shell mx-auto w-full landing-stage">
                    <div class="grid gap-8 lg:grid-cols-[22rem_minmax(0,1fr)] lg:items-start">
                        <div>
                            <p class="section-chip bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-900/50">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}
                            </p>
                            <h2 class="section-title mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                                {{ $locale === 'ne' ? 'सम्पर्क तथा सहयोग' : 'Contact & Support' }}
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-gray-900 dark:text-white">
                                {{ $locale === 'ne'
                                    ? 'कुनै प्रश्न, सुझाव वा सहयोगका लागि हामीलाई सम्पर्क गर्नुहोस्।'
                                    : 'Reach out for questions, suggestions, or support.' }}
                            </p>

                            <div class="mt-8 grid gap-4">
                                <div class="landing-panel lift-card rounded-[1.75rem] p-6 dark:bg-[#191919]">
                                    <div class="text-gray-800 dark:text-white">{{ $locale === 'ne' ? 'इमेल' : 'Email' }}</div>
                                    <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        @if (!empty($department?->email))
                                            <a class="text-red-700 hover:underline dark:text-red-400" href="mailto:{{ $department->email }}">{{ $department->email }}</a>
                                        @else
                                            <span class="text-gray-600 dark:text-gray-50">support@example.edu</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="landing-panel lift-card rounded-[1.75rem] p-6 dark:bg-[#191919]">
                                    <div class="text-gray-800 dark:text-white">{{ $locale === 'ne' ? 'फोन' : 'Phone' }}</div>
                                    <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $department?->phone ?: '+977-000-0000000' }}
                                    </div>
                                </div>
                                <div class="landing-panel lift-card rounded-[1.75rem] p-6 dark:bg-[#191919]">
                                    <div class="text-gray-800 dark:text-white">{{ $locale === 'ne' ? 'ठेगाना' : 'Address' }}</div>
                                    <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $addressText ?: ($locale === 'ne' ? 'विभागीय कार्यालय' : 'Department office') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="landing-panel overflow-hidden rounded-[2rem] bg-white/85 dark:bg-[#191919]">
                            @if (!empty($lat) && !empty($lng))
                                <div class="relative">
                                    <div id="departmentMap"
                                        class="h-[22rem] w-full sm:h-[26rem] lg:h-[34rem]"
                                        data-lat="{{ (float) $lat }}"
                                        data-lng="{{ (float) $lng }}"
                                        data-name="{{ e($departmentName) }}"
                                        data-label="{{ e($mapLabel) }}"></div>

                                    <div class="absolute bottom-3 left-3 z-[500] flex flex-wrap gap-2 rounded-2xl bg-white/80 p-2 shadow-sm backdrop-blur-md dark:bg-[#191919]/80">
                                        <button type="button" data-map-action="locate"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-white">
                                            {{ $locale === 'ne' ? 'मेरो स्थान' : 'My Location' }}
                                        </button>
                                        <button type="button" data-map-action="layers"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-white">
                                            {{ $locale === 'ne' ? 'लेयर' : 'Layers' }}
                                        </button>
                                        <button type="button" data-map-action="reset"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-white">
                                            {{ $locale === 'ne' ? 'रीसेट' : 'Reset' }}
                                        </button>
                                        <button type="button" data-map-action="copy"
                                            class="landing-panel lift-card inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-white">
                                            {{ $locale === 'ne' ? 'कपी' : 'Copy' }}
                                        </button>

                                        @if (!empty($mapOpenUrl))
                                            <a href="{{ $mapOpenUrl }}" target="_blank" rel="noopener"
                                                class="shine-button inline-flex items-center justify-center rounded-xl bg-green-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                                                {{ $locale === 'ne' ? 'खोल्नुहोस्' : 'Open' }}
                                            </a>
                                        @endif
                                        <button type="button" data-map-action="directions"
                                            class="shine-button inline-flex items-center justify-center rounded-xl bg-green-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                                            {{ $locale === 'ne' ? 'दिशा' : 'Directions' }}
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="flex h-[22rem] items-center justify-center bg-gray-100 text-sm text-gray-700 dark:bg-gray-900 dark:text-gray-50 sm:h-[26rem] lg:h-[34rem]">
                                    {{ $locale === 'ne' ? 'नक्सा डेटा उपलब्ध छैन' : 'Map data not available' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <footer class="border-t border-white/10 bg-slate-900 py-12 text-white">
                <div class="landing-shell mx-auto w-full px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-8 md:grid-cols-12">
                        <div class="md:col-span-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $departmentLogoUrl }}" alt="" class="h-10 w-10 rounded-lg bg-white object-contain ring-1 ring-gray-300 dark:ring-gray-800">
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
            <!-- Non-dismissible Info Modal -->
            <div x-data="infoModal()" x-show="open" x-cloak 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="info-modal fixed inset-0 z-[1000] flex items-center justify-center p-4"
                 role="dialog" aria-modal="true" aria-labelledby="modal-title">
                
                <!-- Backdrop - STRICTLY non-dismissible -->
                <div class="info-backdrop absolute inset-0 bg-gray-950/75 backdrop-blur-md transition-opacity" 
                     @click.away @keydown.esc @keydown.window.esc.prevent 
                     @click.away="false" @keydown.esc.prevent @keydown.window.esc.prevent></div>
                
                <!-- Modal Content -->
                <div class="info-content relative m-auto w-full max-w-md rounded-3xl bg-white/98 dark:bg-slate-900/95 p-6 shadow-2xl ring-1 ring-[var(--landing-border-strong)] backdrop-blur-xl drop-shadow-2xl sm:p-8 max-h-[90vh] overflow-y-auto border border-white/20 dark:border-slate-800/50">
                    <!-- Header -->
                    <div class="flex items-start justify-between border-b border-gray-200/80 dark:border-slate-700 pb-4">
                        <div>

                                {{ $locale === 'ne' ? 'स्वागत छ' : 'Welcome' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                {{ $locale === 'ne' ? 'आईटी विभाग डिजिटल पोर्टलमा' : 'to IT Department Digital Portal' }}
                            </p>
                        </div>
                        <button type="button" 
                                @click="close()"
                                class="ml-2 flex h-8 w-8 items-center justify-center rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-600 dark:hover:text-gray-300 dark:hover:bg-gray-800"
                                aria-label="{{ $locale === 'ne' ? 'बन्द गर्नुहोस्' : 'Close' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="mt-4 text-sm leading-6 text-gray-800 dark:text-gray-50 max-h-[40vh] overflow-y-auto">
                        <p class="font-medium drop-shadow-sm">{{ $locale === 'ne' ? 'यो एकीकृत प्लेटफर्ममा तपाईंले पाठ्यक्रम, सूचना, स्रोतहरू, शिक्षक विवरण र अन्य सेवाहरू पाउन सक्नुहुन्छ। पहिलो पटकको लागि स्वागत छ!' : 'This unified platform provides access to courses, notices, resources, faculty details, and more department services. Welcome on your first visit!' }}</p>
                        <ul class="mt-4 space-y-2 pl-4">
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 h-2 w-2 flex-shrink-0 rounded-full bg-red-500"></span>
                                <span>{{ $locale === 'ne' ? 'पाठ्यक्रम ब्राउज गर्नुहोस्' : 'Browse curriculum' }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 h-2 w-2 flex-shrink-0 rounded-full bg-red-500"></span>
                                <span>{{ $locale === 'ne' ? 'नयाँ सूचना जाँच्नुहोस्' : 'Check latest notices' }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 h-2 w-2 flex-shrink-0 rounded-full bg-red-500"></span>
                                <span>{{ $locale === 'ne' ? 'स्रोतहरू डाउनलोड गर्नुहोस्' : 'Download resources' }}</span>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Footer -->
                    <div class="mt-6 flex justify-end gap-3 border-t border-[var(--landing-border)] pt-4">
                        <button type="button" 
                                @click="close()"
                                class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/20 hover:bg-red-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-red-400 transition-all duration-200">
                            {{ $locale === 'ne' ? 'सम्झना भयो, बन्द गर्नुहोस्' : 'Got it, Close' }}
                        </button>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('infoModal', () => ({
                        open: false,
                        close() {
                            this.open = false;
                        },
                        init() {
                            // Prevent ALL esc/backdrop auto-close - STRICTLY X only
                            document.addEventListener('keydown', (e) => {
                                if (e.key === 'Escape') {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return false;
                                }
                            }, { capture: true });
                        }
                    }));
                });
            </script>

            {{-- Auto-scroll to exam results panel on page load if results are present --}}
            @if($examResultSearchPerformed && $examResultPayload)
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const resultPanel = document.getElementById('exam-result-panel');
                    if (resultPanel) {
                        setTimeout(() => {
                            resultPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                });
            </script>
            @endif
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

        document.addEventListener('DOMContentLoaded', () => {
            const categorySelect = document.getElementById('landingExamResultCategory');
            const yearSelect = document.getElementById('landingExamResultAcademicYear');
            const semesterSelect = document.getElementById('landingExamResultSemester');
            const assessmentWrap = document.getElementById('landingExamResultAssessmentWrap');
            const assessmentSelect = document.getElementById('landingExamResultAssessmentNumber');

            if (!categorySelect || !yearSelect || !semesterSelect || !assessmentWrap || !assessmentSelect) {
                return;
            }

            const assessmentMap = @json($examResultAssessmentMap);
            const fallbackLabel = @js($locale === 'ne' ? 'All' : 'All');

            const currentKey = () => {
                const year = (yearSelect.value || 'all').trim() || 'all';
                const semester = (semesterSelect.value || 'all').trim() || 'all';
                return `${year}|${semester}`;
            };

            const assessmentNumbersForCurrentSelection = () => {
                return assessmentMap[currentKey()] || assessmentMap['all|all'] || [];
            };

            const renderAssessmentNumbers = () => {
                const options = assessmentNumbersForCurrentSelection();
                const currentValue = assessmentSelect.value || assessmentSelect.dataset.selected || '';
                assessmentSelect.innerHTML = '';

                const allOption = document.createElement('option');
                allOption.value = '';
                allOption.textContent = fallbackLabel;
                assessmentSelect.appendChild(allOption);

                options.forEach((number) => {
                    const option = document.createElement('option');
                    option.value = String(number);
                    option.textContent = `Assessment ${number}`;
                    assessmentSelect.appendChild(option);
                });

                const optionValues = options.map((value) => String(value));

                if (currentValue && optionValues.includes(String(currentValue))) {
                    assessmentSelect.value = String(currentValue);
                } else if (assessmentSelect.value && !optionValues.includes(String(assessmentSelect.value))) {
                    assessmentSelect.value = '';
                }
            };

            const syncAssessmentVisibility = () => {
                const showAssessment = categorySelect.value === 'assessment';
                assessmentWrap.classList.toggle('hidden', !showAssessment);
                if (showAssessment) {
                    renderAssessmentNumbers();
                } else {
                    assessmentSelect.value = '';
                }
            };

            yearSelect.addEventListener('change', () => {
                if (categorySelect.value === 'assessment') {
                    renderAssessmentNumbers();
                }
            });

            semesterSelect.addEventListener('change', () => {
                if (categorySelect.value === 'assessment') {
                    renderAssessmentNumbers();
                }
            });

            categorySelect.addEventListener('change', syncAssessmentVisibility);
            syncAssessmentVisibility();
        });
    </script>
@endpush
