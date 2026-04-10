@php
    $locale = app()->getLocale();

    $pageTitle = $title ?? 'Public Page';
    $pageSubtitle = $subtitle ?? 'Manmohan Memorial Polytechnic';
@endphp

@extends('layouts.public')

@push('head')
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

        .mmp-page {
            background: var(--mmp-bg);
            min-height: 100vh;
            color: var(--mmp-ink);
            padding-top: 0;
        }

        .mmp-container {
            width: min(1120px, calc(100vw - 2rem));
            margin: 0 auto;
        }

        .mmp-topbar {
            background: var(--mmp-red);
            color: #fff;
            font-size: 0.75rem;
            position: relative;
            z-index: 7;
        }

        .mmp-topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
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
            background: rgba(255, 255, 255, 0.98);
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
            border: 2px solid var(--mmp-red);
            padding: 0.3rem;
            object-fit: contain;
        }

        .mmp-brand h1 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--mmp-red);
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
            background: var(--mmp-red);
            color: #fff;
            padding: 0 1rem;
            font-weight: 700;
        }

        .mmp-nav {
            background: var(--mmp-red);
        }

        .mmp-nav-inner {
            display: flex;
            align-items: center;
            gap: 1.6rem;
            padding: 0.7rem 0;
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

        .mmp-nav li:hover > .dropdown,
        .mmp-nav li:focus-within > .dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .mmp-hero-mini {
            background: linear-gradient(120deg, rgba(191, 31, 47, 0.88) 0%, rgba(191, 31, 47, 0.65) 100%);
            color: #fff;
            padding: 2.5rem 0;
        }

        .mmp-card {
            background: #fff;
            border: 1px solid var(--mmp-line);
            box-shadow: var(--mmp-shadow);
            border-radius: 6px;
            padding: 1.5rem;
        }

        .mmp-content {
            padding: 2rem 0 3rem;
        }

        @media (max-width: 1100px) {
            .mmp-nav-inner,
            .mmp-nav ul {
                gap: 0.9rem;
                justify-content: center;
            }
        }
    </style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.mmp-nav li');

    navItems.forEach(item => {
        let hideTimeout;

        item.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            const dropdown = this.querySelector('.dropdown');
            if (dropdown) {
                dropdown.style.opacity = '1';
                dropdown.style.visibility = 'visible';
                dropdown.style.transform = 'translateY(0)';
                dropdown.style.pointerEvents = 'auto';
            }
        });

        item.addEventListener('mouseleave', function() {
            const dropdown = this.querySelector('.dropdown');
            if (dropdown) {
                hideTimeout = setTimeout(() => {
                    dropdown.style.opacity = '0';
                    dropdown.style.visibility = 'hidden';
                    dropdown.style.transform = 'translateY(6px)';
                    dropdown.style.pointerEvents = 'none';
                }, 300); // 300ms delay
            }
        });
    });
});
</script>
@endpush

@section('content')
<div class="mmp-page">
    <div class="mmp-topbar">
        <div class="mmp-container mmp-topbar-inner">
            <span>{{ now()->format('d M Y, l') }}</span>
            <a href="{{ route('login') }}">PORTAL LOGIN</a>
        </div>
    </div>

    <header class="mmp-header">
        <div class="mmp-container mmp-header-inner">
            <img class="mmp-logo" src="{{ asset('images/default-logo.svg') }}" alt="Logo">
            <div class="mmp-brand">
                <h1>Manmohan Memorial Polytechnic</h1>
                <p>MMP is the constituent College of Manmohan Technical University, the first technical university on Nepal.</p>
            </div>
            <form class="mmp-search" action="{{ route('public.notices.index') }}" method="get">
                <input type="search" placeholder="Search">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <nav class="mmp-nav">
        <div class="mmp-container mmp-nav-inner">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>
                    <a class="has-caret is-active" href="{{ route('public.pages.about') }}">About Us</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.about') }}">What is MMP</a></li>
                        <li><a href="{{ route('public.pages.about') }}">Objectives</a></li>
                        <li><a href="{{ route('public.pages.about') }}">Presidents and Principals</a></li>
                        <li><a href="{{ route('public.pages.about') }}">Contact Us</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.courses') }}">Courses</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.courses') }}">Diploma in Information Technology</a></li>
                        <li><a href="{{ route('public.pages.courses') }}">Diploma in Architecture Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses') }}">Diploma in Electrical Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses') }}">Diploma in Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses') }}">Diploma in Mechanical Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses') }}">Diploma in Civil Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses') }}">Diploma in Electrical & Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses') }}">Short Term Trainings</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.features') }}">Features</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.features') }}">Classrooms and Labs</a></li>
                        <li><a href="{{ route('public.pages.features') }}">Workshops</a></li>
                        <li><a href="{{ route('public.pages.features') }}">Scholarship Schemes</a></li>
                        <li><a href="{{ route('public.pages.features') }}">Transportation</a></li>
                        <li><a href="{{ route('public.pages.features') }}">Internships & Placements</a></li>
                        <li><a href="{{ route('public.pages.features') }}">Library and Hostel</a></li>
                        <li><a href="{{ route('public.pages.features') }}">Game Courts</a></li>
                        <li><a href="{{ route('public.pages.features') }}">First Aid Clinic</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.peoples') }}">Peoples</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.peoples') }}">Administrative Staffs</a></li>
                        <li><a href="{{ route('public.pages.peoples') }}">Department of Architecture Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples') }}">Department of Civil Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples') }}">Department of Electrical Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples') }}">Department of Electrical & Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples') }}">Department of Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples') }}">Department of Information Technology</a></li>
                        <li><a href="{{ route('public.pages.peoples') }}">Department of Mechanical Engineering</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('public.pages.news') }}">News & Events</a></li>
                <li><a href="{{ route('public.pages.gallery') }}">Gallery</a></li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.resources') }}">Resources</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.resources') }}">Formats</a></li>
                        <li><a href="{{ route('public.pages.resources') }}">Question Bank</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <section class="mmp-hero-mini">
        <div class="mmp-container">
            <h2>{{ $pageTitle }}</h2>
            <p>{{ $pageSubtitle }}</p>
        </div>
    </section>

    <section class="mmp-content">
        <div class="mmp-container">
            <div class="mmp-card">
                <h3>{{ $pageTitle }}</h3>
                <p>This page is ready for content. Add your official details and sections here.</p>
            </div>
        </div>
    </section>
</div>
@endsection
