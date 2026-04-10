@php
    $locale = app()->getLocale();

    $labels = $locale === 'ne'
        ? [
            'loading' => '?????? ???? ??? ??????...',
            'publicPortal' => '????????? ??????',
            'portalLogin' => 'PORTAL LOGIN',
            'aboutUs' => 'About Us',
            'notices' => 'Notices',
            'subjects' => 'Subjects',
            'resources' => 'Downloads',
            'gallery' => 'Gallery',
            'people' => 'People',
            'quickAccess' => 'Quick Access',
            'welcomeKicker' => 'Welcome',
            'welcomeTitle' => 'Welcome to MMP',
            'aboutDepartment' => 'About MMP',
            'meetFaculty' => 'Meet Faculty',
            'noticeBoard' => 'Notice Board',
            'viewAll' => 'View All',
            'newsEvents' => 'News & Events',
            'downloads' => 'Downloads',
            'messageTitle' => 'Principal’s Message',
            'readMore' => 'Read More',
            'downloadNow' => 'Download',
            'latestUpdates' => 'Latest Updates',
            'runningSemesters' => 'Running Semesters',
            'moreCourses' => 'More Courses',
            'openMap' => 'Open Location',
            'footerTagline' => 'Manmohan Memorial Polytechnic public portal.',
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
            'publicPortal' => 'Public Portal',
            'portalLogin' => 'PORTAL LOGIN',
            'aboutUs' => 'About Us',
            'notices' => 'Notices',
            'subjects' => 'Subjects',
            'resources' => 'Downloads',
            'gallery' => 'Gallery',
            'people' => 'People',
            'quickAccess' => 'Quick Access',
            'welcomeKicker' => 'Welcome',
            'welcomeTitle' => 'Welcome to MMP',
            'aboutDepartment' => 'About MMP',
            'meetFaculty' => 'Meet Faculty',
            'noticeBoard' => 'Notice Board',
            'viewAll' => 'View All',
            'newsEvents' => 'News & Events',
            'downloads' => 'Downloads',
            'messageTitle' => 'Principal’s Message',
            'readMore' => 'Read More',
            'downloadNow' => 'Download',
            'latestUpdates' => 'Latest Updates',
            'runningSemesters' => 'Running Semesters',
            'moreCourses' => 'More Courses',
            'openMap' => 'Open Location',
            'footerTagline' => 'Manmohan Memorial Polytechnic public portal.',
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
            --mmp-blue: #b31221;
            --mmp-blue-dark: #7d0c18;
            --mmp-blue-soft: #c51f2d;
            --mmp-red: #bf1f2f;
            --mmp-ink: #0f172a;
            --mmp-muted: #5f6b7a;
            --mmp-line: #dbe1e8;
            --mmp-surface: #ffffff;
            --mmp-bg: #f2f4f7;
            --mmp-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        }

        .mmp-page {
            background: var(--mmp-bg);
            min-height: 100vh;
            color: var(--mmp-ink);
            padding-top: 0;
        }

        body,
        html {
            margin: 0 !important;
            padding: 0 !important;
            background: var(--mmp-red);
        }

        #mobileAppShellRoot,
        #mobileAppShellRoot > .min-h-screen,
        #mobileAppShellRoot > .min-h-screen > main {
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
        }

        .mmp-container {
            width: min(1120px, calc(100vw - 2rem));
            margin: 0 auto;
        }

        .mmp-topbar {
            background: var(--mmp-red);
            color: #fff;
            font-size: 0.6rem;
            position: relative;
            z-index: 7;
        }

        .mmp-topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0;
        }

        .mmp-topbar-inner a {
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            padding: 0.35rem 0.7rem;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.18);
        }

        .mmp-header {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid var(--mmp-line);
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9;
        }

        .mmp-header-inner {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            padding: 0.9rem 0;
        }

        .mmp-logo {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid var(--mmp-blue);
            padding: 0.3rem;
            object-fit: contain;
        }

        .mmp-brand h1 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--mmp-blue);
            font-weight: 800;
        }

        .mmp-brand p {
            margin: 0.2rem 0 0;
            color: var(--mmp-muted);
            font-size: 0.85rem;
        }

        .mmp-search {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mmp-search input {
            height: 38px;
            border: 1px solid var(--mmp-line);
            border-radius: 999px;
            padding: 0 1rem;
            min-width: 220px;
        }

        .mmp-search button {
            height: 38px;
            border-radius: 999px;
            border: 0;
            background: var(--mmp-blue);
            color: #fff;
            padding: 0 1rem;
            font-weight: 700;
        }

        .mmp-hero {
            position: relative;
            overflow: visible;
        }

        .mmp-hero img {
            width: 100%;
            height: 420px;
            object-fit: cover;
        }

        .mmp-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(191, 31, 47, 0.75) 0%, rgba(191, 31, 47, 0.25) 55%, rgba(191, 31, 47, 0.55) 100%);
            display: flex;
            align-items: center;
        }

        .mmp-hero-copy {
            color: #fff;
            max-width: 520px;
        }

        .mmp-hero-copy h2 {
            margin: 0 0 0.75rem;
            font-size: 2rem;
        }

        .mmp-nav {
            background: var(--mmp-red);
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 6;
        }

        .mmp-nav-inner {
            display: flex;
            align-items: center;
            gap: 1.6rem;
            padding: 0.3rem 0;
        }

        .mmp-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 1.6rem;
            flex-wrap: wrap;
        }

        .mmp-nav li {
            position: relative;
        }

        .mmp-nav a {
            color: #fff;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: center;
            padding: 0.25rem 0;
            text-decoration: none;
            font-weight: 700;
            position: relative;
        }

        .mmp-nav a.has-caret::after {
            content: "";
            display: inline-block;
            margin-left: 0.3rem;
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #fff;
            transform: translateY(-1px);
        }

        .mmp-nav a.is-active::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -0.7rem;
            height: 3px;
            background: #fff;
            border-radius: 999px;
        }

        .mmp-nav .dropdown {
            position: absolute;
            top: calc(100% + 0.6rem);
            left: 0;
            min-width: 220px;
            background: #444;
            border-radius: 4px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
            padding: 0.25rem 0;
            display: block;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s ease;
            pointer-events: none;
            z-index: 10;
        }

        .mmp-nav .dropdown li {
            width: 100%;
        }

        .mmp-nav .dropdown a {
            display: block;
            padding: 0.7rem 1rem;
            text-transform: none;
            letter-spacing: normal;
            font-size: 0.85rem;
            font-weight: 600;
            color: #f8f8f8;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .mmp-nav .dropdown li:last-child a {
            border-bottom: 0;
        }

        .mmp-nav li:hover > .dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .mmp-nav li:focus-within > .dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .mmp-main {
            margin-top: 40px;
            position: relative;
            z-index: 2;
        }

        .mmp-card {
            background: #fff;
            border: 1px solid var(--mmp-line);
            box-shadow: var(--mmp-shadow);
            border-radius: 6px;
        }

        .mmp-welcome {
            display: grid;
            grid-template-columns: 220px minmax(0, 1fr) 220px;
            gap: 1rem;
            min-height: 320px;
            padding: 1.25rem 1.25rem 2.5rem;
        }

        .mmp-person {
            text-align: center;
            padding: 0.8rem;
        }

        .mmp-person img {
            width: 92px;
            height: 92px;
            border-radius: 12px;
            object-fit: cover;
            margin-bottom: 0.5rem;
        }

        .mmp-welcome-center {
            background: var(--mmp-blue);
            color: #fff;
            padding: 1.1rem;
            border-radius: 6px;
            text-align: center;
            position: relative;
        }

        .mmp-welcome-center::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -26px;
            width: 52px;
            height: 52px;
            background: var(--mmp-blue);
            transform: translateX(-50%) rotate(45deg);
        }

        .mmp-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .mmp-grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
        }

        .mmp-section {
            padding: 1rem;
            margin-top: 1rem;
        }

        .mmp-section h3 {
            margin: 0 0 0.75rem;
            color: var(--mmp-blue);
        }

        .mmp-footer {
            margin-top: 2rem;
            background: var(--mmp-blue-dark);
            color: #fff;
            padding: 2rem 0;
        }

        .mmp-footer-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 1100px) {
            .mmp-welcome {
                grid-template-columns: 1fr;
            }
            .mmp-grid-3,
            .mmp-grid-2,
            .mmp-footer-grid {
                grid-template-columns: 1fr;
            }

            .mmp-nav-inner,
            .mmp-nav ul {
                gap: 0.9rem;
                justify-content: center;
            }

            .mmp-nav a.is-active::after {
                bottom: -0.4rem;
            }
        }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.mmp-nav li');

        menuItems.forEach(item => {
            let hideTimeout;
            const dropdown = item.querySelector('.dropdown');

            if (!dropdown) {
                return;
            }

            item.addEventListener('mouseenter', function() {
                clearTimeout(hideTimeout);
                dropdown.style.opacity = '1';
                dropdown.style.visibility = 'visible';
                dropdown.style.transform = 'translateY(0)';
                dropdown.style.pointerEvents = 'auto';
            });

            item.addEventListener('mouseleave', function() {
                hideTimeout = setTimeout(() => {
                    dropdown.style.opacity = '0';
                    dropdown.style.visibility = 'hidden';
                    dropdown.style.transform = 'translateY(6px)';
                    dropdown.style.pointerEvents = 'none';
                }, 300);
            });
        });
    });
</script>
@endpush

@section('content')
<div class="mmp-page">
    <div class="mmp-topbar">
        <div class="mmp-container mmp-topbar-inner">
            <span>{{ $landingConfig['today'] }}</span>
            <a href="{{ route('login') }}">{{ $labels['portalLogin'] }}</a>
        </div>
    </div>

    <header class="mmp-header">
        <div class="mmp-container mmp-header-inner">
            <img id="landingLogo" class="mmp-logo" src="{{ asset('images/default-logo.svg') }}" alt="Logo">
            <div class="mmp-brand">
                <h1 id="landingcollegeName">Manmohan Memorial Polytechnic</h1>
                <p id="landingTagline">{{ $labels['footerTagline'] }}</p>
            </div>
            <form class="mmp-search" action="{{ route('public.notices.index') }}" method="get">
                <input type="search" placeholder="Search">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <section class="mmp-hero">
        <img id="landingHero" src="{{ asset('images/hero-image.jpg') }}" alt="Hero">
        <div class="mmp-hero-overlay">
            <div class="mmp-container">
                <div class="mmp-hero-copy">
                    <h2 id="landingHeroTitle">Welcome to MMP</h2>
                    <p id="landingHeroText">The constituent college of Manmohan Technical University.</p>
                </div>
            </div>
        </div>
    <nav class="mmp-nav">
        <div class="mmp-container mmp-nav-inner">
            <ul>
                <li><a class="is-active" href="{{ route('home') }}">Home</a></li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.about') }}">About Us</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.about.what-is-mmp') }}">What is MMP</a></li>
                        <li><a href="{{ route('public.pages.about.objectives') }}">Objectives</a></li>
                        <li><a href="{{ route('public.pages.about.presidents-principals') }}">Presidents and Principals</a></li>
                        <li><a href="{{ route('public.pages.about.contact') }}">Contact Us</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.courses') }}">Courses</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.courses.it') }}">Diploma in Information Technology</a></li>
                        <li><a href="{{ route('public.pages.courses.architecture') }}">Diploma in Architecture Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.electrical') }}">Diploma in Electrical Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.electronics') }}">Diploma in Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.mechanical') }}">Diploma in Mechanical Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.civil') }}">Diploma in Civil Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.eee') }}">Diploma in Electrical & Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.short-term') }}">Short Term Trainings</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.features') }}">Features</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.features.classrooms-labs') }}">Classrooms and Labs</a></li>
                        <li><a href="{{ route('public.pages.features.workshops') }}">Workshops</a></li>
                        <li><a href="{{ route('public.pages.features.scholarships') }}">Scholarship Schemes</a></li>
                        <li><a href="{{ route('public.pages.features.transportation') }}">Transportation</a></li>
                        <li><a href="{{ route('public.pages.features.internships') }}">Internships & Placements</a></li>
                        <li><a href="{{ route('public.pages.features.library-hostel') }}">Library and Hostel</a></li>
                        <li><a href="{{ route('public.pages.features.game-courts') }}">Game Courts</a></li>
                        <li><a href="{{ route('public.pages.features.first-aid') }}">First Aid Clinic</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.peoples') }}">Peoples</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.peoples.administrative-staffs') }}">Administrative Staffs</a></li>
                        <li><a href="{{ route('public.pages.peoples.architecture') }}">Department of Architecture Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.civil') }}">Department of Civil Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.electrical') }}">Department of Electrical Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.eee') }}">Department of Electrical & Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.electronics') }}">Department of Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.it') }}">Department of Information Technology</a></li>
                        <li><a href="{{ route('public.pages.peoples.mechanical') }}">Department of Mechanical Engineering</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('public.pages.news') }}">News & Events</a></li>
                <li><a href="{{ route('public.pages.gallery') }}">Gallery</a></li>
                <li><a href="{{ route('public.exam-result') }}">Check Result</a></li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.resources') }}">Resources</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.resources.formats') }}">Formats</a></li>
                        <li><a href="{{ route('public.pages.resources.question-bank') }}">Question Bank</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    </section>

    <div class="mmp-container mmp-main">
        <section class="mmp-card mmp-welcome" id="about">
            <div class="mmp-person" id="leaderLeft">
                <img src="{{ asset('images/default-avatar.png') }}" alt="Leader">
                <strong id="leaderLeftName">Leadership</strong>
                <div id="leaderLeftRole">Chairperson</div>
            </div>
            <div class="mmp-welcome-center">
                <div>{{ $labels['welcomeKicker'] }}</div>
                <h2 id="landingWelcomeTitle">{{ $labels['welcomeTitle'] }}</h2>
                <p id="landingWelcomeText">{{ $labels['loading'] }}</p>
            </div>
            <div class="mmp-person" id="leaderRight">
                <img src="{{ asset('images/default-avatar.png') }}" alt="Leader">
                <strong id="leaderRightName">Leadership</strong>
                <div id="leaderRightRole">Vice Chairperson</div>
            </div>
        </section>

        <section class="mmp-grid-3" id="subjects">
            <div class="mmp-card mmp-section">Departments loading...</div>
            <div class="mmp-card mmp-section">Departments loading...</div>
            <div class="mmp-card mmp-section">Departments loading...</div>
        </section>

        <section class="mmp-grid-2">
            <div class="mmp-card mmp-section" id="notices">
                <h3>{{ $labels['noticeBoard'] }}</h3>
                <div id="landingNotices">{{ $labels['loading'] }}</div>
            </div>
            <div class="mmp-card mmp-section" id="news">
                <h3>{{ $labels['newsEvents'] }}</h3>
                <div id="landingNews">{{ $labels['loading'] }}</div>
            </div>
        </section>

        <section class="mmp-grid-2">
            <div class="mmp-card mmp-section" id="downloads">
                <h3>{{ $labels['downloads'] }}</h3>
                <div id="landingDownloads">{{ $labels['loading'] }}</div>
            </div>
            <div class="mmp-card mmp-section">
                <h3>{{ $labels['messageTitle'] }}</h3>
                <div id="landingMessage">{{ $labels['loading'] }}</div>
            </div>
        </section>
    </div>

    <footer class="mmp-footer" id="contact">
        <div class="mmp-container mmp-footer-grid">
            <div>
                <h3 id="landingFooterBrand">MMP</h3>
                <p id="landingFooterDescription">{{ $labels['loading'] }}</p>
                <p>&copy; {{ date('Y') }} Manmohan Memorial Polytechnic</p>
            </div>
            <div>
                <h3>{{ $labels['quickLinks'] }}</h3>
                <p><a href="{{ route('public.notices.index') }}" style="color:#fff;">Notices</a></p>
                <p><a href="{{ route('public.resources.index') }}" style="color:#fff;">Downloads</a></p>
                <p><a href="{{ route('gallery.index') }}" style="color:#fff;">Gallery</a></p>
            </div>
            <div>
                <h3>{{ $labels['contactInfo'] }}</h3>
                <ul id="landingContactList"></ul>
            </div>
        </div>
    </footer>
</div>

<script>
(() => {
    const config = window.landingPageConfig;
    const labels = config.labels || {};

    const setText = (id, text) => {
        const el = document.getElementById(id);
        if (el) el.textContent = text ?? '';
    };

    const render = (payload) => {
        const department = payload.department || {};
        setText('landingDepartmentName', department.name || 'Manmohan Memorial Polytechnic');
        setText('landingTagline', department.tagline || labels.footerTagline || '');
        setText('landingWelcomeText', department.description || labels.loadingFailed || '');
        setText('landingHeroTitle', department.welcome_title || labels.welcomeTitle || '');
        setText('landingHeroText', department.description || '');
        setText('landingFooterBrand', department.short_name || 'MMP');
        setText('landingFooterDescription', department.description || '');

        const contact = [];
        if (department.address) contact.push(`<li>${department.address}</li>`);
        if (department.phone) contact.push(`<li>${department.phone}</li>`);
        if (department.email) contact.push(`<li>${department.email}</li>`);
        if (department.website) contact.push(`<li>${department.website}</li>`);
        const contactEl = document.getElementById('landingContactList');
        if (contactEl) contactEl.innerHTML = contact.join('');
    };

    fetch(config.apiUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(render)
        .catch(() => setText('landingWelcomeText', labels.loadingFailed || 'Failed to load.'));
})();
</script>
@endsection
