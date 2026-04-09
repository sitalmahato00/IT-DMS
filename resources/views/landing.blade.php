@php
    $locale = app()->getLocale();

    $labels = $locale === 'ne'
        ? [
            'loading' => 'होमपेज डेटा लोड हुँदैछ...',
            'publicPortal' => 'सार्वजनिक विभागीय पोर्टल',
            'portalLogin' => 'PORTAL LOGIN',
            'aboutUs' => 'About Us',
            'notices' => 'Notices',
            'subjects' => 'Subjects',
            'resources' => 'Downloads',
            'gallery' => 'Gallery',
            'people' => 'People',
            'quickAccess' => 'Quick Access',
            'welcomeKicker' => 'API Powered Homepage',
            'welcomeTitle' => 'Welcome To IT-DMS',
            'aboutDepartment' => 'About Department',
            'meetFaculty' => 'Meet Faculty',
            'noticeBoard' => 'Notice Board',
            'liveFromDb' => 'Live From Database',
            'viewAll' => 'View All',
            'newsEvents' => 'News & Events',
            'featuredStory' => 'Featured Story',
            'downloads' => 'Downloads',
            'messageTitle' => 'Message From Department',
            'readMore' => 'Read More',
            'downloadNow' => 'Download',
            'latestUpdates' => 'Latest Updates',
            'runningSemesters' => 'Running Semesters',
            'moreCourses' => 'More Courses',
            'openMap' => 'Open Location',
            'footerTagline' => 'Academic information, notices, resources, and campus stories in one place.',
            'quickLinks' => 'Quick Links',
            'contactInfo' => 'Contact Information',
            'semester' => 'Semester',
            'credits' => 'Credits',
            'noUpdates' => 'No updates available right now.',
            'noDocuments' => 'No downloadable resources have been published yet.',
            'noPrograms' => 'No semester/course data is available yet.',
            'loadingFailed' => 'Landing page data could not be loaded right now.',
            'exploreGallery' => 'Explore Gallery',
        ]
        : [
            'loading' => 'Loading landing page data...',
            'publicPortal' => 'Public Department Portal',
            'portalLogin' => 'PORTAL LOGIN',
            'aboutUs' => 'About Us',
            'notices' => 'Notices',
            'subjects' => 'Subjects',
            'resources' => 'Downloads',
            'gallery' => 'Gallery',
            'people' => 'People',
            'quickAccess' => 'Quick Access',
            'welcomeKicker' => 'API Powered Homepage',
            'welcomeTitle' => 'Welcome To IT-DMS',
            'aboutDepartment' => 'About Department',
            'meetFaculty' => 'Meet Faculty',
            'noticeBoard' => 'Notice Board',
            'liveFromDb' => 'Live From Database',
            'viewAll' => 'View All',
            'newsEvents' => 'News & Events',
            'featuredStory' => 'Featured Story',
            'downloads' => 'Downloads',
            'messageTitle' => 'Message From Department',
            'readMore' => 'Read More',
            'downloadNow' => 'Download',
            'latestUpdates' => 'Latest Updates',
            'runningSemesters' => 'Running Semesters',
            'moreCourses' => 'More Courses',
            'openMap' => 'Open Location',
            'footerTagline' => 'Academic information, notices, resources, and campus stories in one place.',
            'quickLinks' => 'Quick Links',
            'contactInfo' => 'Contact Information',
            'semester' => 'Semester',
            'credits' => 'Credits',
            'noUpdates' => 'No updates available right now.',
            'noDocuments' => 'No downloadable resources have been published yet.',
            'noPrograms' => 'No semester/course data is available yet.',
            'loadingFailed' => 'Landing page data could not be loaded right now.',
            'exploreGallery' => 'Explore Gallery',
        ];

    $landingConfig = [
        'apiUrl' => $landingApiUrl,
        'locale' => $locale,
        'today' => now()->format('d M Y, l'),
        'fallbackHero' => asset('images/hero-image.jpg'),
        'fallbackLogo' => asset('images/default-logo.svg'),
        'labels' => $labels,
        'links' => [
            'about' => route('department.about'),
            'notices' => route('public.notices.index'),
            'subjects' => route('subjects.index'),
            'faculty' => route('faculty.index'),
            'resources' => route('public.resources.index'),
            'gallery' => route('gallery.index'),
            'login' => route('login'),
        ],
    ];
@endphp

@extends('layouts.public')

@push('head')
    <script>
        window.landingPageConfig = @json($landingConfig);
    </script>
    <style>
        :root {
            --mtu-red: #bf1f2f;
            --mtu-red-dark: #991b1b;
            --mtu-red-soft: #fee2e2;
            --mtu-red-tint: #fff4f4;
            --mtu-ink: #132238;
            --mtu-muted: #5f6f84;
            --mtu-line: #e4e7ec;
            --mtu-surface: #ffffff;
            --mtu-surface-soft: #f7f7f9;
            --mtu-shadow: 0 18px 48px rgba(15, 23, 42, 0.09);
            --mtu-shadow-soft: 0 10px 24px rgba(15, 23, 42, 0.06);
            --mtu-radius: 22px;
        }

        .mtu-page {
            background:
                radial-gradient(circle at top left, rgba(191, 31, 47, 0.1), transparent 28%),
                linear-gradient(180deg, #f3f4f6 0%, #f7f7f9 24%, #f9fafb 100%);
            color: var(--mtu-ink);
            min-height: 100vh;
        }

        .mtu-container {
            width: min(1120px, calc(100vw - 2rem));
            margin: 0 auto;
        }

        .mtu-topbar {
            background: linear-gradient(90deg, #a91c2a 0%, #c92b3c 100%);
            color: #fff;
            font-size: 0.8rem;
            letter-spacing: 0.03em;
        }

        .mtu-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.45rem 0;
        }

        .mtu-topbar-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            transition: background-color 180ms ease, transform 180ms ease;
        }

        .mtu-topbar-button:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }

        .mtu-hero {
            position: relative;
            min-height: 420px;
            overflow: hidden;
        }

        .mtu-hero-slides,
        .mtu-hero-overlay {
            position: absolute;
            inset: 0;
        }

        .mtu-hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            background-size: cover;
            background-position: center;
            transform: scale(1.03);
            transition: opacity 800ms ease;
        }

        .mtu-hero-slide.is-active {
            opacity: 1;
        }

        .mtu-hero-overlay {
            background:
                linear-gradient(180deg, rgba(19, 34, 56, 0.16) 0%, rgba(19, 34, 56, 0.42) 50%, rgba(19, 34, 56, 0.58) 100%),
                linear-gradient(90deg, rgba(19, 34, 56, 0.34) 0%, rgba(19, 34, 56, 0.1) 45%, rgba(19, 34, 56, 0.3) 100%);
        }

        .mtu-hero-inner {
            position: relative;
            z-index: 1;
            min-height: 420px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 290px;
            align-items: end;
            gap: 1.5rem;
            padding: 3.25rem 0 2rem;
        }

        .mtu-hero-copy {
            color: #fff;
            max-width: 720px;
        }

        .mtu-brand-row {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .mtu-brand-row img {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.94);
            padding: 0.5rem;
            box-shadow: var(--mtu-shadow-soft);
            object-fit: contain;
        }

        .mtu-eyebrow {
            margin: 0 0 0.35rem;
            font-size: 0.82rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            opacity: 0.92;
        }

        .mtu-hero-copy h1 {
            margin: 0;
            font-size: clamp(2.5rem, 4.3vw, 4rem);
            line-height: 1;
            letter-spacing: -0.04em;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.28);
        }

        .mtu-hero-tagline,
        .mtu-hero-meta {
            margin: 0.7rem 0 0;
            max-width: 660px;
            font-size: 1rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.94);
        }

        .mtu-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            margin-top: 1.35rem;
        }

        .mtu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.86rem 1.2rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        .mtu-btn:hover {
            transform: translateY(-1px);
        }

        .mtu-btn-solid {
            background: #fff;
            color: var(--mtu-red);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.16);
        }

        .mtu-btn-ghost {
            border: 1px solid rgba(255, 255, 255, 0.6);
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        .mtu-hero-card {
            border-radius: var(--mtu-radius);
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--mtu-shadow);
            padding: 1.15rem;
            color: var(--mtu-ink);
        }

        .mtu-hero-card-label,
        .mtu-panel-kicker {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: var(--mtu-red-soft);
            color: var(--mtu-red);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .mtu-hero-quick-list {
            display: grid;
            gap: 0.7rem;
            margin-top: 0.95rem;
        }

        .mtu-hero-quick-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            border-radius: 16px;
            border: 1px solid var(--mtu-line);
            background: #fff;
            padding: 0.78rem 0.9rem;
            color: var(--mtu-ink);
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            transition: border-color 180ms ease, transform 180ms ease;
        }

        .mtu-hero-quick-link:hover {
            border-color: rgba(191, 31, 47, 0.32);
            transform: translateY(-1px);
        }

        .mtu-hero-quick-link span:last-child {
            color: var(--mtu-red);
            font-size: 1rem;
        }

        .mtu-stat-strip {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
            margin-top: 1rem;
        }

        .mtu-stat-box {
            border-radius: 18px;
            background: var(--mtu-red-tint);
            padding: 0.8rem;
            text-align: center;
        }

        .mtu-stat-value {
            display: block;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--mtu-red);
        }

        .mtu-stat-label {
            display: block;
            margin-top: 0.15rem;
            font-size: 0.78rem;
            color: var(--mtu-muted);
        }

        .mtu-surface {
            position: relative;
            margin-top: -32px;
            z-index: 2;
        }

        .mtu-nav {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 1px;
            border-radius: 18px 18px 0 0;
            overflow: hidden;
            background: rgba(191, 31, 47, 0.15);
            box-shadow: var(--mtu-shadow);
        }

        .mtu-nav a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 56px;
            background: linear-gradient(180deg, #c62839 0%, #ab1e2d 100%);
            color: #fff;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            transition: background-color 180ms ease;
        }

        .mtu-nav a:hover {
            background: linear-gradient(180deg, #d13244 0%, #b31f30 100%);
        }

        .mtu-main {
            background: rgba(255, 255, 255, 0.96);
            padding: 1.25rem 1rem 2.25rem;
            border-radius: 0 0 26px 26px;
            box-shadow: var(--mtu-shadow);
        }

        .mtu-panel,
        .mtu-welcome-panel,
        .mtu-shortcut-card,
        .mtu-program-card,
        .mtu-person-card {
            background: var(--mtu-surface);
            border: 1px solid var(--mtu-line);
            box-shadow: var(--mtu-shadow-soft);
        }

        .mtu-welcome-panel {
            display: grid;
            grid-template-columns: 210px minmax(0, 1fr) 240px;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0 0 24px 24px;
            margin-bottom: 1.5rem;
        }

        .mtu-people-column {
            display: grid;
            gap: 0.9rem;
        }

        .mtu-person-card {
            border-radius: 18px;
            padding: 0.95rem;
        }

        .mtu-person-card.is-compact {
            text-align: center;
        }

        .mtu-person-media {
            width: 88px;
            height: 88px;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--mtu-red-soft), #fff);
            color: var(--mtu-red);
            font-size: 1.55rem;
            font-weight: 800;
            margin-bottom: 0.85rem;
        }

        .mtu-person-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mtu-person-card.is-compact .mtu-person-media {
            margin: 0 auto 0.85rem;
        }

        .mtu-person-card h3,
        .mtu-program-card h3 {
            margin: 0;
            font-size: 1rem;
            line-height: 1.35;
        }

        .mtu-person-title,
        .mtu-person-subtitle,
        .mtu-program-meta,
        .mtu-download-meta,
        .mtu-list-date,
        .mtu-contact-list {
            color: var(--mtu-muted);
        }

        .mtu-person-title {
            margin-top: 0.25rem;
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--mtu-red);
        }

        .mtu-person-subtitle,
        .mtu-person-bio {
            margin-top: 0.28rem;
            font-size: 0.83rem;
            line-height: 1.6;
        }

        .mtu-welcome-card {
            border-radius: 22px;
            background: linear-gradient(135deg, #b31f30 0%, #cf3143 100%);
            color: #fff;
            padding: 1.45rem 1.55rem;
            position: relative;
            overflow: hidden;
        }

        .mtu-welcome-card::after {
            content: "";
            position: absolute;
            bottom: -42px;
            left: 50%;
            width: 84px;
            height: 84px;
            background: #b31f30;
            transform: rotate(45deg);
            border-radius: 12px;
        }

        .mtu-welcome-card .mtu-panel-kicker {
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        .mtu-welcome-card h2 {
            margin: 0.95rem 0 0;
            font-size: 2rem;
            letter-spacing: -0.04em;
        }

        .mtu-welcome-card p {
            position: relative;
            z-index: 1;
            margin: 0.85rem 0 0;
            font-size: 0.96rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.94);
        }

        .mtu-welcome-actions {
            position: relative;
            z-index: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: center;
            margin-top: 1.15rem;
        }

        .mtu-small-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            background: #fff;
            color: var(--mtu-red);
            text-decoration: none;
            font-weight: 700;
        }

        .mtu-text-link {
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }

        .mtu-featured-person {
            display: grid;
            align-content: start;
        }

        .mtu-highlight-grid,
        .mtu-shortcuts,
        .mtu-program-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .mtu-highlight-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mtu-highlight-card,
        .mtu-shortcut-card,
        .mtu-program-card {
            border-radius: 18px;
            padding: 1.15rem;
        }

        .mtu-highlight-card h3,
        .mtu-shortcut-card h3 {
            margin: 0.55rem 0 0;
            font-size: 1rem;
        }

        .mtu-highlight-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--mtu-red);
        }

        .mtu-highlight-desc,
        .mtu-shortcut-card p {
            margin: 0.55rem 0 0;
            color: var(--mtu-muted);
            line-height: 1.6;
        }

        .mtu-highlight-link,
        .mtu-shortcut-link,
        .mtu-panel-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 0.8rem;
            color: var(--mtu-red);
            text-decoration: none;
            font-weight: 700;
        }

        .mtu-panel {
            border-radius: 20px;
            padding: 1.15rem;
            margin-top: 1.5rem;
        }

        .mtu-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .mtu-panel-head h2,
        .mtu-column-head {
            margin: 0;
            font-size: 1.35rem;
            letter-spacing: -0.03em;
        }

        .mtu-panel-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
        }

        .mtu-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.36rem 0.66rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .mtu-badge-primary {
            background: var(--mtu-red);
            color: #fff;
        }

        .mtu-badge-soft {
            background: var(--mtu-red-soft);
            color: var(--mtu-red);
        }

        .mtu-notice-list,
        .mtu-download-list,
        .mtu-blog-list,
        .mtu-program-list,
        .mtu-contact-list,
        .mtu-footer-links {
            display: grid;
            gap: 0.75rem;
        }

        .mtu-notice-item,
        .mtu-download-item,
        .mtu-program-card,
        .mtu-blog-item {
            border-radius: 16px;
            border: 1px solid var(--mtu-line);
            background: #fff;
            padding: 0.95rem 1rem;
        }

        .mtu-notice-item {
            display: flex;
            align-items: start;
            gap: 0.8rem;
        }

        .mtu-notice-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            margin-top: 0.45rem;
            flex: 0 0 auto;
            background: #f59e0b;
        }

        .mtu-notice-dot.is-important {
            background: var(--mtu-red);
            box-shadow: 0 0 0 5px rgba(191, 31, 47, 0.08);
        }

        .mtu-notice-item a,
        .mtu-download-item a,
        .mtu-blog-item a {
            color: inherit;
            text-decoration: none;
        }

        .mtu-notice-title,
        .mtu-download-title {
            font-weight: 700;
            line-height: 1.55;
        }

        .mtu-list-date,
        .mtu-download-meta {
            margin-top: 0.22rem;
            font-size: 0.82rem;
            line-height: 1.55;
        }

        .mtu-news-grid,
        .mtu-message-grid,
        .mtu-brand-band-inner,
        .mtu-footer-grid {
            display: grid;
            gap: 1rem;
        }

        .mtu-news-grid {
            grid-template-columns: 280px minmax(0, 1fr);
        }

        .mtu-news-list {
            display: grid;
            gap: 0.65rem;
            align-content: start;
        }

        .mtu-news-button {
            width: 100%;
            border: 1px solid var(--mtu-line);
            border-left: 4px solid transparent;
            border-radius: 16px;
            background: #fff;
            padding: 0.9rem;
            text-align: left;
            cursor: pointer;
            transition: border-color 180ms ease, transform 180ms ease, background-color 180ms ease;
        }

        .mtu-news-button.is-active {
            border-color: rgba(191, 31, 47, 0.2);
            border-left-color: var(--mtu-red);
            background: var(--mtu-red-tint);
        }

        .mtu-news-button:hover {
            transform: translateY(-1px);
        }

        .mtu-news-button h3,
        .mtu-blog-title {
            margin: 0;
            font-size: 0.94rem;
            line-height: 1.55;
        }

        .mtu-news-button p,
        .mtu-blog-meta,
        .mtu-panel-text {
            margin: 0.3rem 0 0;
            color: var(--mtu-muted);
            font-size: 0.84rem;
            line-height: 1.65;
        }

        .mtu-news-feature {
            position: relative;
            min-height: 360px;
            border-radius: 22px;
            overflow: hidden;
            background: #18253a;
            color: #fff;
        }

        .mtu-news-feature-media,
        .mtu-news-feature-overlay {
            position: absolute;
            inset: 0;
        }

        .mtu-news-feature-media {
            background-size: cover;
            background-position: center;
        }

        .mtu-news-feature-overlay {
            background: linear-gradient(180deg, rgba(19, 34, 56, 0.18), rgba(19, 34, 56, 0.82));
        }

        .mtu-news-feature-content {
            position: absolute;
            inset: auto 0 0;
            z-index: 1;
            padding: 1.35rem;
        }

        .mtu-news-feature h3 {
            margin: 0.55rem 0 0;
            font-size: clamp(1.4rem, 2vw, 2rem);
            line-height: 1.2;
        }

        .mtu-news-feature p {
            margin: 0.7rem 0 0;
            max-width: 560px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.9);
        }

        .mtu-message-grid {
            grid-template-columns: 300px minmax(0, 1fr);
        }

        .mtu-download-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .mtu-download-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--mtu-red-soft);
            color: var(--mtu-red);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .mtu-download-arrow {
            color: var(--mtu-red);
            font-weight: 800;
        }

        .mtu-message-panel {
            background: linear-gradient(135deg, #b31f30 0%, #c92b3c 100%);
            color: #fff;
        }

        .mtu-message-panel .mtu-panel-head h2,
        .mtu-message-panel .mtu-panel-text,
        .mtu-message-panel .mtu-person-subtitle {
            color: rgba(255, 255, 255, 0.92);
        }

        .mtu-message-panel .mtu-person-card {
            border-color: rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.09);
            box-shadow: none;
        }

        .mtu-message-panel .mtu-person-media {
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        .mtu-shortcuts {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mtu-shortcut-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.9rem;
            text-decoration: none;
            color: inherit;
        }

        .mtu-shortcut-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--mtu-red-soft);
            color: var(--mtu-red);
            font-size: 1.2rem;
            font-weight: 800;
        }

        .mtu-program-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mtu-blog-item {
            display: grid;
            grid-template-columns: 76px minmax(0, 1fr);
            gap: 0.75rem;
            align-items: start;
        }

        .mtu-blog-thumb {
            width: 76px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--mtu-red-soft), #f3f4f6);
            background-size: cover;
            background-position: center;
        }

        .mtu-program-card ul,
        .mtu-footer-links,
        .mtu-contact-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .mtu-program-card ul {
            margin-top: 0.8rem;
            display: grid;
            gap: 0.6rem;
        }

        .mtu-program-card li {
            display: flex;
            gap: 0.55rem;
            align-items: start;
            color: var(--mtu-ink);
            font-size: 0.9rem;
            line-height: 1.55;
        }

        .mtu-program-card li::before {
            content: "•";
            color: var(--mtu-red);
            font-weight: 900;
        }

        .mtu-brand-band {
            margin-top: 1.75rem;
            padding: 1.4rem 0;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 -8px 30px rgba(15, 23, 42, 0.04);
        }

        .mtu-brand-band-inner {
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
        }

        .mtu-brand-band-mark {
            display: flex;
            align-items: center;
            gap: 0.95rem;
        }

        .mtu-brand-band-mark img {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: #fff;
            padding: 0.3rem;
            box-shadow: var(--mtu-shadow-soft);
            object-fit: contain;
        }

        .mtu-brand-band-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .mtu-brand-band-subtitle {
            margin-top: 0.18rem;
            color: var(--mtu-muted);
        }

        .mtu-brand-band-search {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            padding: 0 1.1rem;
            border-radius: 999px;
            background: var(--mtu-red-tint);
            color: var(--mtu-red);
            text-decoration: none;
            font-weight: 700;
        }

        .mtu-footer {
            background: linear-gradient(135deg, #9f172a 0%, #bf1f2f 100%);
            color: #fff;
            padding: 2.2rem 0 2.6rem;
        }

        .mtu-footer-grid {
            grid-template-columns: 1.1fr 1fr 1fr;
            align-items: start;
        }

        .mtu-footer-brand h3,
        .mtu-footer-block h3 {
            margin: 0;
            font-size: 1.1rem;
            letter-spacing: -0.02em;
        }

        .mtu-footer-brand p,
        .mtu-footer-links a,
        .mtu-contact-list li,
        .mtu-footer-copy {
            color: rgba(255, 255, 255, 0.88);
            line-height: 1.7;
        }

        .mtu-footer-links a {
            text-decoration: none;
        }

        .mtu-footer-copy {
            margin-top: 1rem;
            font-size: 0.84rem;
        }

        .mtu-empty-state {
            border-radius: 16px;
            border: 1px dashed var(--mtu-line);
            background: var(--mtu-surface-soft);
            color: var(--mtu-muted);
            padding: 1rem;
            text-align: center;
            line-height: 1.65;
        }

        @media (max-width: 1024px) {
            .mtu-hero-inner,
            .mtu-welcome-panel,
            .mtu-news-grid,
            .mtu-message-grid,
            .mtu-footer-grid,
            .mtu-brand-band-inner {
                grid-template-columns: 1fr;
            }

            .mtu-nav {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .mtu-highlight-grid,
            .mtu-shortcuts,
            .mtu-program-grid {
                grid-template-columns: 1fr;
            }

            .mtu-featured-person {
                order: 3;
            }
        }

        @media (max-width: 720px) {
            .mtu-container {
                width: min(100vw - 1rem, 1120px);
            }

            .mtu-nav {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .mtu-main {
                padding: 1rem 0.85rem 2rem;
            }

            .mtu-brand-row {
                flex-direction: column;
                align-items: start;
            }

            .mtu-brand-row img {
                width: 72px;
                height: 72px;
            }

            .mtu-hero-actions,
            .mtu-welcome-actions,
            .mtu-topbar-inner {
                flex-direction: column;
                align-items: stretch;
            }

            .mtu-btn,
            .mtu-small-btn,
            .mtu-topbar-button,
            .mtu-brand-band-search {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="mtu-page" id="landingApp">
        <div class="mtu-topbar">
            <div class="mtu-container mtu-topbar-inner">
                <div>{{ $landingConfig['today'] }}</div>
                <a class="mtu-topbar-button" href="{{ route('login') }}">{{ $labels['portalLogin'] }}</a>
            </div>
        </div>

        <section class="mtu-hero" id="home">
            <div class="mtu-hero-slides" id="landingHeroSlides"></div>
            <div class="mtu-hero-overlay"></div>

            <div class="mtu-container mtu-hero-inner">
                <div class="mtu-hero-copy">
                    <div class="mtu-brand-row">
                        <img id="landingLogo" src="{{ asset('images/default-logo.svg') }}" alt="Department logo">
                        <div>
                            <p class="mtu-eyebrow">{{ $labels['publicPortal'] }}</p>
                            <h1 id="landingDepartmentName">IT-DMS</h1>
                            <p class="mtu-hero-tagline" id="landingTagline">{{ $labels['loading'] }}</p>
                            <p class="mtu-hero-meta" id="landingDepartmentMeta">{{ $labels['loading'] }}</p>
                        </div>
                    </div>

                    <div class="mtu-hero-actions">
                        <a class="mtu-btn mtu-btn-solid" id="landingAboutPrimary" href="{{ route('department.about') }}">{{ $labels['aboutUs'] }}</a>
                        <a class="mtu-btn mtu-btn-ghost" href="{{ route('public.notices.index') }}">{{ $labels['notices'] }}</a>
                    </div>
                </div>

                <aside class="mtu-hero-card">
                    <div class="mtu-hero-card-label">{{ $labels['quickAccess'] }}</div>

                    <div class="mtu-hero-quick-list">
                        <a class="mtu-hero-quick-link" href="{{ route('public.notices.index') }}"><span>{{ $labels['notices'] }}</span><span>&rsaquo;</span></a>
                        <a class="mtu-hero-quick-link" href="{{ route('public.resources.index') }}"><span>{{ $labels['resources'] }}</span><span>&rsaquo;</span></a>
                        <a class="mtu-hero-quick-link" href="{{ route('gallery.index') }}"><span>{{ $labels['gallery'] }}</span><span>&rsaquo;</span></a>
                        <a class="mtu-hero-quick-link" href="{{ route('faculty.index') }}"><span>{{ $labels['people'] }}</span><span>&rsaquo;</span></a>
                    </div>

                    <div class="mtu-stat-strip" id="landingStatStrip">
                        <div class="mtu-stat-box">
                            <span class="mtu-stat-value">0</span>
                            <span class="mtu-stat-label">Students</span>
                        </div>
                        <div class="mtu-stat-box">
                            <span class="mtu-stat-value">0</span>
                            <span class="mtu-stat-label">Teachers</span>
                        </div>
                        <div class="mtu-stat-box">
                            <span class="mtu-stat-value">0</span>
                            <span class="mtu-stat-label">Subjects</span>
                        </div>
                        <div class="mtu-stat-box">
                            <span class="mtu-stat-value">0</span>
                            <span class="mtu-stat-label">Semesters</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="mtu-surface">
            <div class="mtu-container">
                <nav class="mtu-nav">
                    <a href="#home">{{ $labels['aboutUs'] }}</a>
                    <a href="#landing-notices">{{ $labels['notices'] }}</a>
                    <a href="#landing-programs">{{ $labels['subjects'] }}</a>
                    <a href="#landing-downloads">{{ $labels['resources'] }}</a>
                    <a href="#landing-news">{{ $labels['gallery'] }}</a>
                    <a href="{{ route('faculty.index') }}">{{ $labels['people'] }}</a>
                    <a href="#contact">{{ $labels['contactInfo'] }}</a>
                </nav>

                <main class="mtu-main">
                    <section class="mtu-welcome-panel">
                        <div class="mtu-people-column" id="landingLeadersLeft">
                            <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                        </div>

                        <div class="mtu-welcome-card">
                            <div class="mtu-panel-kicker">{{ $labels['welcomeKicker'] }}</div>
                            <h2 id="landingWelcomeTitle">{{ $labels['welcomeTitle'] }}</h2>
                            <p id="landingWelcomeBody">{{ $labels['loading'] }}</p>

                            <div class="mtu-welcome-actions">
                                <a class="mtu-small-btn" id="landingAboutSecondary" href="{{ route('department.about') }}">{{ $labels['aboutDepartment'] }}</a>
                                <a class="mtu-text-link" href="{{ route('faculty.index') }}">{{ $labels['meetFaculty'] }}</a>
                            </div>
                        </div>

                        <div class="mtu-featured-person" id="landingLeaderRight">
                            <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                        </div>
                    </section>

                    <section class="mtu-highlight-grid" id="landingHighlights">
                        <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                    </section>

                    <section class="mtu-panel" id="landing-notices">
                        <div class="mtu-panel-head">
                            <div>
                                <div class="mtu-panel-badges">
                                    <span class="mtu-badge mtu-badge-primary">{{ $labels['noticeBoard'] }}</span>
                                    <span class="mtu-badge mtu-badge-soft">{{ $labels['liveFromDb'] }}</span>
                                </div>
                                <h2 style="margin-top:0.7rem;">{{ $labels['noticeBoard'] }}</h2>
                            </div>
                            <a class="mtu-panel-link" href="{{ route('public.notices.index') }}">{{ $labels['viewAll'] }}</a>
                        </div>

                        <div class="mtu-notice-list" id="landingNoticeList">
                            <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                        </div>
                    </section>

                    <section class="mtu-panel" id="landing-news">
                        <div class="mtu-panel-head">
                            <div>
                                <div class="mtu-panel-badges">
                                    <span class="mtu-badge mtu-badge-primary">{{ $labels['newsEvents'] }}</span>
                                    <span class="mtu-badge mtu-badge-soft">{{ $labels['featuredStory'] }}</span>
                                </div>
                                <h2 style="margin-top:0.7rem;">{{ $labels['newsEvents'] }}</h2>
                            </div>
                            <a class="mtu-panel-link" href="{{ route('gallery.index') }}">{{ $labels['exploreGallery'] }}</a>
                        </div>

                        <div class="mtu-news-grid">
                            <div class="mtu-news-list" id="landingNewsList">
                                <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                            </div>

                            <div class="mtu-news-feature" id="landingNewsFeature"></div>
                        </div>
                    </section>

                    <section class="mtu-message-grid" id="landing-downloads">
                        <section class="mtu-panel">
                            <div class="mtu-panel-head">
                                <h2>{{ $labels['downloads'] }}</h2>
                                <a class="mtu-panel-link" href="{{ route('public.resources.index') }}">{{ $labels['viewAll'] }}</a>
                            </div>

                            <div class="mtu-download-list" id="landingDownloadList">
                                <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                            </div>
                        </section>

                        <section class="mtu-panel mtu-message-panel">
                            <div class="mtu-panel-head">
                                <h2>{{ $labels['messageTitle'] }}</h2>
                            </div>

                            <div id="landingMessageAuthor"></div>
                            <p class="mtu-panel-text" id="landingMessageText">{{ $labels['loading'] }}</p>
                            <a class="mtu-panel-link" id="landingMessageLink" href="{{ route('department.about') }}" style="color:#fff;">{{ $labels['readMore'] }}</a>
                        </section>
                    </section>

                    <section class="mtu-shortcuts">
                        <a class="mtu-shortcut-card" href="{{ route('public.notices.index') }}">
                            <div class="mtu-shortcut-mark">N</div>
                            <div style="flex:1;">
                                <h3>{{ $labels['notices'] }}</h3>
                                <p>Latest public announcements from the department.</p>
                            </div>
                            <span class="mtu-download-arrow">&rsaquo;</span>
                        </a>

                        <a class="mtu-shortcut-card" href="{{ route('gallery.index') }}">
                            <div class="mtu-shortcut-mark">G</div>
                            <div style="flex:1;">
                                <h3>{{ $labels['gallery'] }}</h3>
                                <p>Event photos and campus highlights powered from the database.</p>
                            </div>
                            <span class="mtu-download-arrow">&rsaquo;</span>
                        </a>

                        <a class="mtu-shortcut-card" href="{{ route('public.resources.index') }}">
                            <div class="mtu-shortcut-mark">D</div>
                            <div style="flex:1;">
                                <h3>{{ $labels['downloads'] }}</h3>
                                <p>Published files and learning materials available to visitors.</p>
                            </div>
                            <span class="mtu-download-arrow">&rsaquo;</span>
                        </a>
                    </section>

                    <section class="mtu-program-grid" id="landing-programs">
                        <article class="mtu-panel">
                            <div class="mtu-column-head">{{ $labels['latestUpdates'] }}</div>
                            <div class="mtu-blog-list" id="landingBlogList" style="margin-top:1rem;">
                                <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                            </div>
                        </article>

                        <article class="mtu-panel">
                            <div class="mtu-column-head">{{ $labels['runningSemesters'] }}</div>
                            <div class="mtu-program-list" id="landingProgramColumnA" style="margin-top:1rem;">
                                <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                            </div>
                        </article>

                        <article class="mtu-panel">
                            <div class="mtu-column-head">{{ $labels['moreCourses'] }}</div>
                            <div class="mtu-program-list" id="landingProgramColumnB" style="margin-top:1rem;">
                                <div class="mtu-empty-state">{{ $labels['loading'] }}</div>
                            </div>
                        </article>
                    </section>
                </main>
            </div>

            <section class="mtu-brand-band">
                <div class="mtu-container mtu-brand-band-inner">
                    <div class="mtu-brand-band-mark">
                        <img id="landingFooterLogo" src="{{ asset('images/default-logo.svg') }}" alt="Department logo">
                        <div>
                            <div class="mtu-brand-band-title" id="landingFooterName">IT-DMS</div>
                            <div class="mtu-brand-band-subtitle">{{ $labels['footerTagline'] }}</div>
                        </div>
                    </div>

                    <a class="mtu-brand-band-search" id="landingMapLink" href="#" target="_blank" rel="noopener">{{ $labels['openMap'] }}</a>
                </div>
            </section>

            <footer class="mtu-footer" id="contact">
                <div class="mtu-container mtu-footer-grid">
                    <div class="mtu-footer-brand">
                        <h3 id="landingFooterBrand">IT-DMS</h3>
                        <p id="landingFooterDescription">{{ $labels['loading'] }}</p>
                        <div class="mtu-footer-copy">&copy; {{ date('Y') }} IT-DMS. All rights reserved.</div>
                    </div>

                    <div class="mtu-footer-block">
                        <h3>{{ $labels['quickLinks'] }}</h3>
                        <ul class="mtu-footer-links" style="margin-top:0.9rem;">
                            <li><a href="{{ route('department.about') }}">{{ $labels['aboutUs'] }}</a></li>
                            <li><a href="{{ route('public.notices.index') }}">{{ $labels['notices'] }}</a></li>
                            <li><a href="{{ route('subjects.index') }}">{{ $labels['subjects'] }}</a></li>
                            <li><a href="{{ route('public.resources.index') }}">{{ $labels['downloads'] }}</a></li>
                            <li><a href="{{ route('gallery.index') }}">{{ $labels['gallery'] }}</a></li>
                        </ul>
                    </div>

                    <div class="mtu-footer-block">
                        <h3>{{ $labels['contactInfo'] }}</h3>
                        <ul class="mtu-contact-list" id="landingContactList" style="margin-top:0.9rem;">
                            <li>{{ $labels['loading'] }}</li>
                        </ul>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const config = window.landingPageConfig || {};
            const labels = config.labels || {};
            const root = document.getElementById('landingApp');

            if (!root) {
                return;
            }

            const elements = {
                heroSlides: document.getElementById('landingHeroSlides'),
                logo: document.getElementById('landingLogo'),
                footerLogo: document.getElementById('landingFooterLogo'),
                departmentName: document.getElementById('landingDepartmentName'),
                tagline: document.getElementById('landingTagline'),
                departmentMeta: document.getElementById('landingDepartmentMeta'),
                aboutPrimary: document.getElementById('landingAboutPrimary'),
                aboutSecondary: document.getElementById('landingAboutSecondary'),
                welcomeTitle: document.getElementById('landingWelcomeTitle'),
                welcomeBody: document.getElementById('landingWelcomeBody'),
                statStrip: document.getElementById('landingStatStrip'),
                leadersLeft: document.getElementById('landingLeadersLeft'),
                leaderRight: document.getElementById('landingLeaderRight'),
                highlights: document.getElementById('landingHighlights'),
                noticeList: document.getElementById('landingNoticeList'),
                newsList: document.getElementById('landingNewsList'),
                newsFeature: document.getElementById('landingNewsFeature'),
                downloadList: document.getElementById('landingDownloadList'),
                messageAuthor: document.getElementById('landingMessageAuthor'),
                messageText: document.getElementById('landingMessageText'),
                messageLink: document.getElementById('landingMessageLink'),
                blogList: document.getElementById('landingBlogList'),
                programA: document.getElementById('landingProgramColumnA'),
                programB: document.getElementById('landingProgramColumnB'),
                footerName: document.getElementById('landingFooterName'),
                footerBrand: document.getElementById('landingFooterBrand'),
                footerDescription: document.getElementById('landingFooterDescription'),
                contactList: document.getElementById('landingContactList'),
                mapLink: document.getElementById('landingMapLink'),
            };

            let heroRotationHandle = null;
            let currentNews = [];

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');

            const initials = (value) => {
                const letters = String(value ?? '')
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean)
                    .slice(0, 2)
                    .map((part) => part.charAt(0).toUpperCase())
                    .join('');

                return letters || 'IT';
            };

            const emptyState = (message) => `<div class="mtu-empty-state">${escapeHtml(message)}</div>`;

            const personCard = (person, compact = false) => {
                if (!person) {
                    return emptyState(labels.loadingFailed || 'No profile data.');
                }

                const media = person.photo_url
                    ? `<div class="mtu-person-media"><img src="${escapeHtml(person.photo_url)}" alt="${escapeHtml(person.name)}"></div>`
                    : `<div class="mtu-person-media">${escapeHtml(person.initials || initials(person.name))}</div>`;

                return `
                    <article class="mtu-person-card ${compact ? 'is-compact' : ''}">
                        ${media}
                        <h3>${escapeHtml(person.name)}</h3>
                        <div class="mtu-person-title">${escapeHtml(person.title || '')}</div>
                        ${person.subtitle ? `<div class="mtu-person-subtitle">${escapeHtml(person.subtitle)}</div>` : ''}
                        ${person.bio ? `<p class="mtu-person-bio">${escapeHtml(person.bio)}</p>` : ''}
                    </article>
                `;
            };

            const renderStatStrip = (stats) => {
                const cards = [
                    { value: stats.students, label: 'Students' },
                    { value: stats.teachers, label: 'Teachers' },
                    { value: stats.subjects, label: 'Subjects' },
                    { value: stats.semesters, label: 'Semesters' },
                ];

                elements.statStrip.innerHTML = cards.map((card) => `
                    <div class="mtu-stat-box">
                        <span class="mtu-stat-value">${escapeHtml(card.value ?? 0)}</span>
                        <span class="mtu-stat-label">${escapeHtml(card.label)}</span>
                    </div>
                `).join('');
            };

            const renderHighlights = (items) => {
                if (!Array.isArray(items) || !items.length) {
                    elements.highlights.innerHTML = emptyState(labels.noUpdates || 'No highlights available.');
                    return;
                }

                elements.highlights.innerHTML = items.map((item) => `
                    <article class="mtu-highlight-card">
                        <div class="mtu-panel-kicker">${escapeHtml(labels.liveFromDb || 'Live')}</div>
                        <div class="mtu-highlight-value">${escapeHtml(item.value ?? '')}</div>
                        <h3>${escapeHtml(item.title ?? '')}</h3>
                        <p class="mtu-highlight-desc">${escapeHtml(item.description ?? '')}</p>
                        <a class="mtu-highlight-link" href="${escapeHtml(item.url || config.links?.subjects || '#')}">${escapeHtml(labels.readMore || 'Read More')} &rsaquo;</a>
                    </article>
                `).join('');
            };

            const renderNotices = (items) => {
                if (!Array.isArray(items) || !items.length) {
                    elements.noticeList.innerHTML = emptyState(labels.noUpdates || 'No notices yet.');
                    return;
                }

                elements.noticeList.innerHTML = items.map((item) => `
                    <article class="mtu-notice-item">
                        <span class="mtu-notice-dot ${item.important ? 'is-important' : ''}"></span>
                        <div>
                            <a href="${escapeHtml(item.url || config.links?.notices || '#')}">
                                <div class="mtu-notice-title">${escapeHtml(item.title ?? '')}</div>
                                <div class="mtu-list-date">${escapeHtml(item.date ?? '')}${item.audience ? ` • ${escapeHtml(item.audience)}` : ''}</div>
                                ${item.excerpt ? `<p class="mtu-panel-text">${escapeHtml(item.excerpt)}</p>` : ''}
                            </a>
                        </div>
                    </article>
                `).join('');
            };

            const renderNewsFeature = (index = 0) => {
                const item = currentNews[index];

                if (!item) {
                    elements.newsFeature.innerHTML = emptyState(labels.noUpdates || 'No news available.');
                    return;
                }

                elements.newsFeature.innerHTML = `
                    <div class="mtu-news-feature-media" style="background-image:url('${escapeHtml(item.image_url || config.fallbackHero)}');"></div>
                    <div class="mtu-news-feature-overlay"></div>
                    <div class="mtu-news-feature-content">
                        <div class="mtu-panel-badges">
                            <span class="mtu-badge mtu-badge-primary">${escapeHtml(item.category_label || labels.newsEvents || 'News')}</span>
                            ${item.date ? `<span class="mtu-badge mtu-badge-soft">${escapeHtml(item.date)}</span>` : ''}
                        </div>
                        <h3>${escapeHtml(item.title ?? '')}</h3>
                        ${item.excerpt ? `<p>${escapeHtml(item.excerpt)}</p>` : ''}
                        <a class="mtu-btn mtu-btn-solid" style="margin-top:1rem;" href="${escapeHtml(item.url || config.links?.gallery || '#')}">${escapeHtml(labels.readMore || 'Read More')}</a>
                    </div>
                `;

                elements.newsList.querySelectorAll('.mtu-news-button').forEach((button, buttonIndex) => {
                    button.classList.toggle('is-active', buttonIndex === index);
                });
            };

            const renderNews = (items) => {
                currentNews = Array.isArray(items) ? items : [];

                if (!currentNews.length) {
                    elements.newsList.innerHTML = emptyState(labels.noUpdates || 'No news available.');
                    elements.newsFeature.innerHTML = emptyState(labels.noUpdates || 'No news available.');
                    return;
                }

                elements.newsList.innerHTML = currentNews.map((item, index) => `
                    <button class="mtu-news-button ${index === 0 ? 'is-active' : ''}" type="button" data-news-index="${index}">
                        <div class="mtu-list-date">${escapeHtml(item.date ?? '')} • ${escapeHtml(item.category_label ?? labels.newsEvents || 'News')}</div>
                        <h3>${escapeHtml(item.title ?? '')}</h3>
                        ${item.excerpt ? `<p>${escapeHtml(item.excerpt)}</p>` : ''}
                    </button>
                `).join('');

                elements.newsList.querySelectorAll('[data-news-index]').forEach((button) => {
                    button.addEventListener('click', () => {
                        renderNewsFeature(Number(button.dataset.newsIndex || 0));
                    });
                });

                renderNewsFeature(0);
            };

            const renderDownloads = (items) => {
                if (!Array.isArray(items) || !items.length) {
                    elements.downloadList.innerHTML = emptyState(labels.noDocuments || 'No downloads available.');
                    return;
                }

                elements.downloadList.innerHTML = items.map((item) => {
                    const badge = String(item.type_label || 'File').slice(0, 3);

                    return `
                        <article class="mtu-download-item">
                            <div style="display:flex;align-items:center;gap:0.85rem;">
                                <div class="mtu-download-badge">${escapeHtml(badge)}</div>
                                <div>
                                    <a href="${escapeHtml(item.download_url || config.links?.resources || '#')}">
                                        <div class="mtu-download-title">${escapeHtml(item.title ?? '')}</div>
                                        <div class="mtu-download-meta">
                                            ${escapeHtml(item.type_label ?? '')}
                                            ${item.subject ? ` • ${escapeHtml(item.subject)}` : ''}
                                            ${item.size ? ` • ${escapeHtml(item.size)}` : ''}
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a class="mtu-download-arrow" href="${escapeHtml(item.download_url || config.links?.resources || '#')}">&rsaquo;</a>
                        </article>
                    `;
                }).join('');
            };

            const renderMessage = (department, leadership) => {
                const author = leadership[0] || {
                    name: department.message_author || department.name || 'Department',
                    title: department.message_author_title || '',
                    photo_url: null,
                    initials: initials(department.name || 'Department'),
                };

                elements.messageAuthor.innerHTML = personCard(author, false);
                elements.messageText.textContent = department.description || labels.loadingFailed || 'No message available.';
                elements.messageLink.href = config.links?.about || '#';
            };

            const renderBlogs = (items) => {
                if (!Array.isArray(items) || !items.length) {
                    elements.blogList.innerHTML = emptyState(labels.noUpdates || 'No updates available.');
                    return;
                }

                elements.blogList.innerHTML = items.slice(0, 3).map((item) => `
                    <article class="mtu-blog-item">
                        <div class="mtu-blog-thumb" style="background-image:url('${escapeHtml(item.image_url || config.fallbackHero)}');"></div>
                        <div>
                            <a href="${escapeHtml(item.url || config.links?.gallery || '#')}">
                                <div class="mtu-blog-title">${escapeHtml(item.title ?? '')}</div>
                                <div class="mtu-blog-meta">${escapeHtml(item.date ?? '')}</div>
                            </a>
                        </div>
                    </article>
                `).join('');
            };

            const renderPrograms = (groups) => {
                if (!Array.isArray(groups) || !groups.length) {
                    elements.programA.innerHTML = emptyState(labels.noPrograms || 'No course data available.');
                    elements.programB.innerHTML = emptyState(labels.noPrograms || 'No course data available.');
                    return;
                }

                const midpoint = Math.ceil(groups.length / 2);
                const left = groups.slice(0, midpoint);
                const right = groups.slice(midpoint);

                const template = (items) => items.length
                    ? items.map((group) => `
                        <article class="mtu-program-card">
                            <h3>${escapeHtml(group.semester_label || `${labels.semester || 'Semester'} ${group.semester_key || ''}`)}</h3>
                            <div class="mtu-program-meta">
                                ${escapeHtml(group.subject_count ?? 0)} ${escapeHtml(labels.subjects || 'Subjects')}
                                ${group.credit_total ? ` • ${escapeHtml(labels.credits || 'Credits')}: ${escapeHtml(group.credit_total)}` : ''}
                            </div>
                            <ul>
                                ${(Array.isArray(group.items) ? group.items : []).map((item) => `
                                    <li>${escapeHtml(item.title || item.code || '')}${item.credits ? ` (${escapeHtml(item.credits)})` : ''}</li>
                                `).join('')}
                            </ul>
                        </article>
                    `).join('')
                    : emptyState(labels.noPrograms || 'No course data available.');

                elements.programA.innerHTML = template(left);
                elements.programB.innerHTML = template(right);
            };

            const renderLeadership = (leadership) => {
                const people = Array.isArray(leadership) ? leadership : [];
                const left = people.slice(0, 2);
                const featured = people[2] || people[0] || null;

                elements.leadersLeft.innerHTML = left.length
                    ? left.map((person) => personCard(person, true)).join('')
                    : emptyState(labels.loadingFailed || 'No leadership profiles available.');

                elements.leaderRight.innerHTML = featured
                    ? personCard(featured, false)
                    : emptyState(labels.loadingFailed || 'No featured profile available.');
            };

            const renderContact = (department) => {
                const contactItems = [];

                if (department.address) {
                    contactItems.push(`<li>${escapeHtml(department.address)}</li>`);
                }

                if (department.phone) {
                    contactItems.push(`<li>${escapeHtml(department.phone)}</li>`);
                }

                if (department.email) {
                    contactItems.push(`<li>${escapeHtml(department.email)}</li>`);
                }

                if (department.website) {
                    contactItems.push(`<li><a href="${escapeHtml(department.website)}" target="_blank" rel="noopener" style="color:inherit;">${escapeHtml(department.website)}</a></li>`);
                }

                elements.contactList.innerHTML = contactItems.length
                    ? contactItems.join('')
                    : `<li>${escapeHtml(labels.loadingFailed || 'Contact information unavailable.')}</li>`;

                const mapUrl = department.map_url || '#';
                elements.mapLink.href = mapUrl;
                elements.mapLink.style.pointerEvents = department.map_url ? 'auto' : 'none';
                elements.mapLink.style.opacity = department.map_url ? '1' : '0.6';
            };

            const startHeroRotation = (images) => {
                const usableImages = (Array.isArray(images) ? images : []).filter(Boolean);

                if (!usableImages.length) {
                    elements.heroSlides.innerHTML = `<div class="mtu-hero-slide is-active" style="background-image:url('${escapeHtml(config.fallbackHero)}');"></div>`;
                    return;
                }

                elements.heroSlides.innerHTML = usableImages.map((image, index) => `
                    <div class="mtu-hero-slide ${index === 0 ? 'is-active' : ''}" style="background-image:url('${escapeHtml(image)}');"></div>
                `).join('');

                if (heroRotationHandle) {
                    window.clearInterval(heroRotationHandle);
                }

                if (usableImages.length < 2) {
                    return;
                }

                let activeIndex = 0;

                heroRotationHandle = window.setInterval(() => {
                    const slides = elements.heroSlides.querySelectorAll('.mtu-hero-slide');

                    if (!slides.length) {
                        return;
                    }

                    slides[activeIndex]?.classList.remove('is-active');
                    activeIndex = (activeIndex + 1) % slides.length;
                    slides[activeIndex]?.classList.add('is-active');
                }, 5500);
            };

            const render = (payload) => {
                const department = payload.department || {};
                const links = payload.links || {};

                elements.departmentName.textContent = department.name || 'IT-DMS';
                elements.footerName.textContent = department.name || 'IT-DMS';
                elements.footerBrand.textContent = department.short_name || department.name || 'IT-DMS';
                elements.footerDescription.textContent = department.description || labels.loadingFailed || '';
                elements.tagline.textContent = department.tagline || labels.loadingFailed || '';
                elements.departmentMeta.textContent = [department.address, department.email, department.phone].filter(Boolean).join(' • ');
                elements.welcomeTitle.textContent = department.welcome_title || labels.welcomeTitle || 'Welcome';
                elements.welcomeBody.textContent = department.description || labels.loadingFailed || '';
                elements.aboutPrimary.href = links.about || config.links?.about || '#';
                elements.aboutSecondary.href = links.about || config.links?.about || '#';
                elements.messageLink.href = links.about || config.links?.about || '#';

                const logoUrl = department.logo_url || config.fallbackLogo;
                elements.logo.src = logoUrl;
                elements.footerLogo.src = logoUrl;

                startHeroRotation(department.hero_images || [department.hero_image || config.fallbackHero]);
                renderStatStrip(payload.stats || {});
                renderLeadership(payload.leadership || []);
                renderHighlights(payload.academic_highlights || []);
                renderNotices(payload.notices || []);
                renderNews(payload.news_events || []);
                renderDownloads(payload.documents || []);
                renderMessage(department, payload.leadership || []);
                renderBlogs(payload.news_events || []);
                renderPrograms(payload.subject_groups || []);
                renderContact(department);
            };

            const showFailure = () => {
                const message = labels.loadingFailed || 'Landing page data could not be loaded.';
                elements.tagline.textContent = message;
                elements.departmentMeta.textContent = '';
                elements.welcomeBody.textContent = message;
                elements.noticeList.innerHTML = emptyState(message);
                elements.newsList.innerHTML = emptyState(message);
                elements.newsFeature.innerHTML = emptyState(message);
                elements.downloadList.innerHTML = emptyState(message);
                elements.blogList.innerHTML = emptyState(message);
                elements.programA.innerHTML = emptyState(message);
                elements.programB.innerHTML = emptyState(message);
                elements.leadersLeft.innerHTML = emptyState(message);
                elements.leaderRight.innerHTML = emptyState(message);
                elements.highlights.innerHTML = emptyState(message);
                elements.messageText.textContent = message;
                elements.contactList.innerHTML = `<li>${escapeHtml(message)}</li>`;
                startHeroRotation([config.fallbackHero]);
            };

            const loadLandingData = async () => {
                try {
                    const response = await fetch(config.apiUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const payload = await response.json();
                    render(payload);
                } catch (error) {
                    console.error('Failed to load landing data:', error);
                    showFailure();
                }
            };

            loadLandingData();
        })();
    </script>
@endpush
