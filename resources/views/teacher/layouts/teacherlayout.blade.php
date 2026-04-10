<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manmohan Memorial Polytechnic') - Teacher</title>
    @include('partials.pwa-head', ['themeColor' => '#FF0037'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script data-mobile-static-script>
        document.documentElement.classList.add('teacher-ui-enhanced');
    </script>
    <style data-mobile-static-style>
        html.teacher-ui-enhanced:not(.dark) {
            --teacher-surface-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 249, 250, 0.97));
            --teacher-surface-border: rgba(241, 213, 219, 0.95);
            --teacher-surface-shadow: 0 28px 56px -40px rgba(148, 19, 52, 0.24);
            --teacher-soft-shadow: 0 18px 34px -28px rgba(15, 23, 42, 0.16);
            --teacher-focus-ring: 0 0 0 4px rgba(244, 63, 94, 0.12);
        }

        html.teacher-ui-enhanced.dark {
            --teacher-dark-bg: radial-gradient(circle at top, rgba(190, 24, 93, 0.18), rgba(15, 23, 42, 0) 34%), linear-gradient(180deg, #020617 0%, #07111f 48%, #020617 100%);
            --teacher-dark-surface: linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(7, 12, 24, 0.98));
            --teacher-dark-surface-soft: linear-gradient(180deg, rgba(30, 41, 59, 0.84), rgba(15, 23, 42, 0.96));
            --teacher-dark-muted-surface: rgba(15, 23, 42, 0.78);
            --teacher-dark-border: rgba(148, 163, 184, 0.2);
            --teacher-dark-border-strong: rgba(248, 113, 113, 0.18);
            --teacher-dark-shadow: 0 30px 60px -34px rgba(2, 6, 23, 0.86);
            --teacher-dark-soft-shadow: 0 20px 36px -28px rgba(2, 6, 23, 0.78);
            --teacher-dark-text: #e2e8f0;
            --teacher-dark-muted: #94a3b8;
            --teacher-dark-focus-ring: 0 0 0 4px rgba(244, 63, 94, 0.14);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell {
            color: #0f172a;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-page-body {
            color: inherit;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-page-header {
            margin-bottom: 1.75rem;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-page-header-title {
            letter-spacing: -0.02em;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-page-header-btn,
        html.teacher-ui-enhanced:not(.dark) .teacher-page-primary-btn,
        html.teacher-ui-enhanced:not(.dark) .teacher-page-secondary-btn,
        html.teacher-ui-enhanced:not(.dark) .teacher-action-pill {
            border-radius: 999px;
            font-weight: 700;
            box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.34);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-stats-grid {
            margin-bottom: 1.75rem;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            border-color: var(--teacher-surface-border);
            background: var(--teacher-surface-bg);
            box-shadow: var(--teacher-surface-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-stat-card::after {
            content: "";
            position: absolute;
            inset: auto -22% -55% auto;
            width: 7rem;
            height: 7rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(251, 113, 133, 0.18), rgba(251, 113, 133, 0));
            pointer-events: none;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-filter-panel {
            border-radius: 28px;
            border-color: var(--teacher-surface-border);
            background: var(--teacher-surface-bg);
            box-shadow: var(--teacher-surface-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell main {
            color: inherit;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-page-body > div[class*="space-y-"] > div:first-of-type:not([id]),
        html.teacher-ui-enhanced:not(.dark) .teacher-page-body > div[class*="space-y-"] > section:first-of-type {
            color: inherit;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded-lg.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded.shadow-sm.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded-lg.shadow-sm,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded-xl.shadow-sm,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded-lg.shadow-xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded-lg.shadow-2xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded.shadow-2xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.relative.bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.relative.bg-white.rounded.shadow-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.relative.bg-white.rounded.shadow-2xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded-xl.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-white.rounded-2xl.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell section.bg-white.rounded-lg.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell section.bg-white.rounded-xl.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell form.bg-white.rounded-lg.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell form.bg-white.rounded-xl.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-white.rounded-xl.shadow-xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-white.rounded-2xl.shadow-2xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .relative.bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .relative.bg-white.rounded-xl.shadow-xl {
            border-color: var(--teacher-surface-border);
            background: var(--teacher-surface-bg);
            box-shadow: var(--teacher-surface-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-white.rounded-xl.shadow-xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-white.rounded-2xl.shadow-2xl,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .relative.bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .relative.bg-white.rounded-xl.shadow-xl {
            box-shadow: 0 34px 70px -38px rgba(15, 23, 42, 0.42);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .sticky.top-0.bg-red-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-gradient-to-r.from-red-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-gradient-to-r.from-blue-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .fixed.inset-0 .bg-gradient-to-r.from-green-600 {
            border-bottom: none;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell table thead,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell thead.bg-gray-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell thead.dark\:bg-gray-700,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell thead.dark\:bg-slate-700,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-gray-50.border-b {
            background: linear-gradient(180deg, #fff5f7, #fffafb);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell tbody tr:hover {
            background: linear-gradient(90deg, rgba(255, 241, 242, 0.72), rgba(255, 255, 255, 0.97));
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .border-t.bg-gray-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .border-t.dark\:bg-slate-700\/50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .border-b.bg-gray-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-gray-50.dark\:bg-slate-700\/50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-gray-50.dark\:bg-gray-700\/50 {
            background: linear-gradient(180deg, #fff7f8, #fffdfd);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell select,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell textarea {
            border-radius: 16px;
            border-color: #e5d4d9;
            background-color: #fffdfd;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):focus,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell select:focus,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell textarea:focus {
            border-color: #f43f5e;
            box-shadow: var(--teacher-focus-ring);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.bg-red-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.bg-blue-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.bg-green-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.bg-yellow-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.bg-purple-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-red-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-blue-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-green-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-yellow-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-purple-600,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-red-600.text-white,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-blue-600.text-white,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-green-600.text-white,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-yellow-600.text-white,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .bg-purple-600.text-white {
            border-radius: 999px;
            box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.34);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.border,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.border {
            border-radius: 999px;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.rounded,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.rounded-md,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.rounded,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.rounded-md,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell label.inline-flex.rounded-md,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell label.inline-flex.rounded-lg {
            border-radius: 999px;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.inline-flex,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.inline-flex,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell label.inline-flex {
            border-radius: 999px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.inline-flex:hover,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell button.inline-flex:hover,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell label.inline-flex:hover {
            transform: translateY(-1px);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .rounded-full.bg-red-100,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .rounded-full.bg-blue-100,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .rounded-full.bg-green-100,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .rounded-full.bg-purple-100,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell span.rounded-full,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell span.inline-flex.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell span.inline-flex.rounded-full {
            box-shadow: var(--teacher-soft-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .w-10.h-10.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .w-10.h-10.rounded-full,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .w-9.h-9.rounded-full,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .w-8.h-8.rounded-full {
            box-shadow: 0 16px 28px -22px rgba(15, 23, 42, 0.26);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .rounded-lg.bg-white,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell .rounded-xl.bg-white {
            box-shadow: var(--teacher-soft-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-red-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-blue-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-green-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-purple-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell a.bg-amber-50,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-red-50.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-blue-50.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-green-50.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-purple-50.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-amber-50.rounded-lg,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-gray-50.rounded,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-gray-50.rounded-md,
        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell div.bg-gray-50.rounded-lg {
            box-shadow: var(--teacher-soft-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-content-shell canvas {
            filter: saturate(1.02);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-hero {
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow: 0 32px 64px -34px rgba(185, 28, 28, 0.48);
            isolation: isolate;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0));
            pointer-events: none;
            z-index: -1;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-hero-pill {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 14px 28px -22px rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(10px);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 249, 0.96));
            border-color: rgba(239, 206, 213, 0.92);
            box-shadow: 0 24px 48px -34px rgba(15, 23, 42, 0.22);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 30px 60px -36px rgba(148, 19, 52, 0.28);
            border-color: rgba(244, 114, 182, 0.24);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-notice-item,
        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-list-item,
        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-class-card,
        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-subject-card,
        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-mini-stat {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(255, 250, 251, 0.96));
            border-color: rgba(229, 213, 218, 0.96);
            box-shadow: 0 18px 32px -28px rgba(15, 23, 42, 0.16);
            transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-notice-item:hover,
        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-list-item:hover,
        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-class-card:hover,
        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-subject-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 40px -30px rgba(148, 19, 52, 0.22);
            border-color: rgba(251, 113, 133, 0.24);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-mini-stat {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 250, 251, 0.92));
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-period-btn:not(.bg-red-600) {
            background: rgba(241, 245, 249, 0.9);
            color: #334155;
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.92);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-period-btn.bg-red-600 {
            box-shadow: 0 18px 34px -22px rgba(220, 38, 38, 0.42);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-action-link {
            border-radius: 16px;
            box-shadow: 0 14px 28px -22px rgba(15, 23, 42, 0.16);
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-dashboard-page .teacher-dashboard-action-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 32px -22px rgba(148, 19, 52, 0.22);
        }

        html.teacher-ui-enhanced.dark body.teacher-panel {
            background: #020617;
            color: var(--teacher-dark-text);
        }

        html.teacher-ui-enhanced.dark body.teacher-panel > .fixed.inset-0.pointer-events-none {
            opacity: 0.05;
            filter: grayscale(1) contrast(0.88) brightness(0.82);
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell {
            background: var(--teacher-dark-bg);
            color: var(--teacher-dark-text);
        }

        html.teacher-ui-enhanced.dark .teacher-page-body,
        html.teacher-ui-enhanced.dark .teacher-content-shell main {
            color: inherit;
        }

        html.teacher-ui-enhanced.dark #sidebar {
            background: linear-gradient(180deg, rgba(7, 12, 24, 0.98), rgba(2, 6, 23, 0.98));
            color: var(--teacher-dark-text);
            border-right-color: rgba(248, 113, 113, 0.16);
            box-shadow: 0 26px 54px -28px rgba(2, 6, 23, 0.9);
        }

        html.teacher-ui-enhanced.dark #sidebar nav > div {
            background: rgba(15, 23, 42, 0.9);
            border-color: rgba(248, 113, 113, 0.12);
            box-shadow: var(--teacher-dark-soft-shadow);
        }

        html.teacher-ui-enhanced.dark #sidebar .nav-link,
        html.teacher-ui-enhanced.dark #sidebar button[class*="justify-between"] {
            color: #cbd5e1;
        }

        html.teacher-ui-enhanced.dark #sidebar .nav-link:hover,
        html.teacher-ui-enhanced.dark #sidebar button[class*="justify-between"]:hover {
            color: #f8fafc;
            background: rgba(248, 113, 113, 0.14);
        }

        html.teacher-ui-enhanced.dark #sidebar button[class*="justify-between"].bg-red-50,
        html.teacher-ui-enhanced.dark #sidebar button[class*="justify-between"].border-red-100,
        html.teacher-ui-enhanced.dark #sidebar button[class*="justify-between"].text-red-600 {
            background: rgba(127, 29, 29, 0.32);
            border-color: rgba(248, 113, 113, 0.16);
            color: #fecaca;
        }

        html.teacher-ui-enhanced.dark #sidebar .nav-link.bg-red-600,
        html.teacher-ui-enhanced.dark #sidebar .nav-link.text-white {
            box-shadow: 0 18px 34px -22px rgba(248, 113, 113, 0.54);
        }

        html.teacher-ui-enhanced.dark #sidebar .text-slate-500 {
            color: #64748b;
        }

        html.teacher-ui-enhanced.dark #sidebar .bg-slate-50 {
            background: rgba(15, 23, 42, 0.84);
            color: #cbd5e1;
        }

        html.teacher-ui-enhanced.dark #sidebar .bg-slate-50:hover {
            background: rgba(248, 113, 113, 0.14);
            color: #f8fafc;
        }

        html.teacher-ui-enhanced.dark #sidebar .border-slate-200 {
            border-color: rgba(148, 163, 184, 0.16);
        }

        html.teacher-ui-enhanced.dark .teacher-panel header {
            box-shadow: 0 22px 44px -28px rgba(2, 6, 23, 0.78);
        }

        html.teacher-ui-enhanced.dark #locale-select,
        html.teacher-ui-enhanced.dark #darkModeToggle,
        html.teacher-ui-enhanced.dark #notifToggle,
        html.teacher-ui-enhanced.dark #profileToggle {
            background: rgba(15, 23, 42, 0.86);
            border-color: rgba(148, 163, 184, 0.22);
            color: var(--teacher-dark-text);
        }

        html.teacher-ui-enhanced.dark #locale-select:hover,
        html.teacher-ui-enhanced.dark #darkModeToggle:hover,
        html.teacher-ui-enhanced.dark #notifToggle:hover,
        html.teacher-ui-enhanced.dark #profileToggle:hover {
            background: rgba(30, 41, 59, 0.94);
        }

        html.teacher-ui-enhanced.dark #darkModeToggle i,
        html.teacher-ui-enhanced.dark #notifToggle i,
        html.teacher-ui-enhanced.dark #profileToggle i,
        html.teacher-ui-enhanced.dark #profileToggle span {
            color: inherit;
        }

        html.teacher-ui-enhanced.dark #notifDropdown,
        html.teacher-ui-enhanced.dark #profileDropdown {
            background: rgba(7, 12, 24, 0.98);
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: var(--teacher-dark-shadow);
        }

        html.teacher-ui-enhanced.dark #notifDropdown > div:first-child,
        html.teacher-ui-enhanced.dark #notifDropdown > div:last-child {
            background: rgba(127, 29, 29, 0.24);
            border-color: rgba(248, 113, 113, 0.16);
        }

        html.teacher-ui-enhanced.dark #notifDropdown .max-h-72 > div:hover,
        html.teacher-ui-enhanced.dark #profileDropdown a:hover,
        html.teacher-ui-enhanced.dark #profileDropdown button:hover {
            background: rgba(148, 163, 184, 0.1);
        }

        html.teacher-ui-enhanced.dark #notifDropdown .text-gray-900,
        html.teacher-ui-enhanced.dark #profileDropdown .text-gray-900 {
            color: #f8fafc;
        }

        html.teacher-ui-enhanced.dark #notifDropdown .text-gray-700,
        html.teacher-ui-enhanced.dark #profileDropdown .text-gray-700 {
            color: #e2e8f0;
        }

        html.teacher-ui-enhanced.dark #notifDropdown .text-gray-500,
        html.teacher-ui-enhanced.dark #notifDropdown .text-gray-400,
        html.teacher-ui-enhanced.dark #profileDropdown .text-gray-500 {
            color: #94a3b8;
        }

        html.teacher-ui-enhanced.dark #notifDropdown .divide-y > div,
        html.teacher-ui-enhanced.dark #profileDropdown > div:first-child {
            border-color: rgba(148, 163, 184, 0.12);
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded-lg.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded.shadow-sm.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded-lg.shadow-sm,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded-xl.shadow-sm,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded-lg.shadow-xl,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded-lg.shadow-2xl,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded.shadow-2xl,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.relative.bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.relative.bg-white.rounded.shadow-lg,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.relative.bg-white.rounded.shadow-2xl,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded-xl.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell div.bg-white.rounded-2xl.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell section.bg-white.rounded-lg.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell section.bg-white.rounded-xl.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell form.bg-white.rounded-lg.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell form.bg-white.rounded-xl.border,
        html.teacher-ui-enhanced.dark .teacher-content-shell .fixed.inset-0 .bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced.dark .teacher-content-shell .fixed.inset-0 .bg-white.rounded-xl.shadow-xl,
        html.teacher-ui-enhanced.dark .teacher-content-shell .fixed.inset-0 .bg-white.rounded-2xl.shadow-2xl,
        html.teacher-ui-enhanced.dark .teacher-content-shell .fixed.inset-0 .relative.bg-white.rounded-lg.shadow-lg,
        html.teacher-ui-enhanced.dark .teacher-content-shell .fixed.inset-0 .relative.bg-white.rounded-xl.shadow-xl,
        html.teacher-ui-enhanced.dark .teacher-stat-card,
        html.teacher-ui-enhanced.dark .teacher-filter-panel {
            background: var(--teacher-dark-surface);
            border-color: var(--teacher-dark-border);
            box-shadow: var(--teacher-dark-shadow);
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell .bg-gray-50,
        html.teacher-ui-enhanced.dark .teacher-content-shell .bg-gray-100,
        html.teacher-ui-enhanced.dark .teacher-content-shell .border-t.bg-gray-50,
        html.teacher-ui-enhanced.dark .teacher-content-shell .border-b.bg-gray-50,
        html.teacher-ui-enhanced.dark .teacher-content-shell .bg-gray-50.dark\:bg-slate-700\/50,
        html.teacher-ui-enhanced.dark .teacher-content-shell .bg-gray-50.dark\:bg-gray-700\/50 {
            background: rgba(15, 23, 42, 0.78);
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell table thead,
        html.teacher-ui-enhanced.dark .teacher-content-shell thead.bg-gray-50,
        html.teacher-ui-enhanced.dark .teacher-content-shell thead.dark\:bg-gray-700,
        html.teacher-ui-enhanced.dark .teacher-content-shell thead.dark\:bg-slate-700,
        html.teacher-ui-enhanced.dark .teacher-content-shell .bg-gray-50.border-b {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.92), rgba(15, 23, 42, 0.96));
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell tbody tr:hover {
            background: rgba(148, 163, 184, 0.08);
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        html.teacher-ui-enhanced.dark .teacher-content-shell select,
        html.teacher-ui-enhanced.dark .teacher-content-shell textarea {
            border-color: rgba(148, 163, 184, 0.22);
            background: rgba(7, 12, 24, 0.88);
            color: var(--teacher-dark-text);
            box-shadow: inset 0 1px 2px rgba(2, 6, 23, 0.4);
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):focus,
        html.teacher-ui-enhanced.dark .teacher-content-shell select:focus,
        html.teacher-ui-enhanced.dark .teacher-content-shell textarea:focus {
            border-color: #fb7185;
            box-shadow: var(--teacher-dark-focus-ring);
        }

        html.teacher-ui-enhanced.dark .teacher-content-shell canvas {
            filter: saturate(1.08);
        }

        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-hero {
            box-shadow: 0 34px 68px -30px rgba(248, 113, 113, 0.38);
        }

        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-hero-pill {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.14);
            color: #ffe4e6;
        }

        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-card {
            background: var(--teacher-dark-surface-soft);
            border-color: var(--teacher-dark-border-strong);
            box-shadow: var(--teacher-dark-soft-shadow);
        }

        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-notice-item,
        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-list-item,
        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-class-card,
        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-subject-card,
        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-mini-stat {
            background: var(--teacher-dark-muted-surface);
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: none;
        }

        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-period-btn:not(.bg-red-600) {
            background: rgba(30, 41, 59, 0.88);
            color: #cbd5e1;
        }

        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-period-btn.bg-red-600 {
            box-shadow: 0 18px 30px -18px rgba(248, 113, 113, 0.56);
        }

        html.teacher-ui-enhanced.dark .teacher-dashboard-page .teacher-dashboard-action-link {
            box-shadow: none;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-panel,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-form-panel,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-table-card,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-empty,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-modal-shell,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-aside {
            border-radius: 28px;
            border-color: var(--teacher-surface-border);
            background: var(--teacher-surface-bg);
            box-shadow: var(--teacher-surface-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-list-card,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-quicklink,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-mini-card,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-summary-box {
            border-radius: 24px;
            border-color: rgba(229, 213, 218, 0.96);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 249, 250, 0.94));
            box-shadow: var(--teacher-soft-shadow);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-list-card:hover,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-quicklink:hover,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-mini-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 26px 44px -32px rgba(148, 19, 52, 0.26);
            border-color: rgba(244, 114, 182, 0.22);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-table-card,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-form-panel {
            overflow: hidden;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-panel-header,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-table-card > .border-b,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-form-panel > .border-b,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-aside > .border-b {
            background: linear-gradient(180deg, #fff5f7, #fffafb);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-icon {
            border-radius: 18px;
            box-shadow: 0 18px 30px -24px rgba(15, 23, 42, 0.22);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-photo-frame {
            border-radius: 32px;
            border: 1px solid rgba(229, 213, 218, 0.96);
            background: linear-gradient(180deg, rgba(255, 251, 252, 0.98), rgba(255, 246, 247, 0.95));
            box-shadow: var(--teacher-soft-shadow);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-upload-trigger {
            border-radius: 999px;
            box-shadow: 0 18px 28px -24px rgba(225, 29, 72, 0.34);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-chip,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-summary-box span.inline-flex,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-list-card span.inline-flex,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-table-card span.inline-flex {
            box-shadow: 0 12px 24px -20px rgba(15, 23, 42, 0.22);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-empty {
            background: linear-gradient(180deg, rgba(255, 251, 235, 0.96), rgba(255, 247, 237, 0.98));
            border-color: rgba(253, 224, 71, 0.84);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-table-card tbody tr:hover td,
        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-table-card tbody tr:hover th {
            background: linear-gradient(90deg, rgba(255, 241, 242, 0.72), rgba(255, 255, 255, 0.97));
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-smooth-page .teacher-smooth-kpi-bar {
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(255, 228, 230, 0.9), rgba(254, 242, 242, 0.96));
        }

        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-list-card,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-quicklink,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-mini-card,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-summary-box,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-panel,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-form-panel,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-table-card,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-modal-shell,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-aside,
        html.teacher-ui-enhanced.dark .teacher-smooth-page .teacher-smooth-photo-frame {
            border-color: var(--teacher-dark-border);
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body class="teacher-panel font-sans antialiased bg-gray-50 dark:bg-gray-900" data-mobile-shell="teacher" data-mobile-role="teacher" data-mobile-route="{{ Route::currentRouteName() ?? '' }}">
    <div id="mobileAppShellRoot" data-mobile-shell-root>
    <!-- College Logo Background for All Pages -->
    <div class="fixed inset-0 pointer-events-none opacity-10 z-0 flex items-center justify-center">
        @if(isset($departmentLogoUrl))
            <img src="{{ $departmentLogoUrl }}" alt="{{ __('College Logo') }}" class="w-[600px] h-[600px] object-contain">
        @else
            <i class="bi bi-mortarboard text-[30rem] text-gray-300 dark:text-gray-700"></i>
        @endif
    </div>

    <!-- Global Loader -->
    <div id="globalLoader" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col items-center gap-4">
            <div class="animate-spin w-12 h-12 border-4 border-t-red-600 border-gray-200 rounded-full"></div>
            <p class="text-sm text-gray-700 font-medium">{{ __('Loading') }}...</p>
        </div>
    </div>

    <!-- Professional Toast Notification Container -->
    <div id="toastNotification" class="hidden fixed top-4 right-4 z-50 rounded-xl shadow-2xl text-white text-sm transition-all duration-300 max-w-sm relative overflow-hidden animate-slide-in-right">
        <div class="backdrop-blur-md bg-opacity-95 p-4 flex items-center gap-3">
            <div id="toastIcon" class="text-xl flex-shrink-0"></div>
            <div class="flex-1">
                <span id="toastMessage" class="font-medium block"></span>
                <span id="toastSubMessage" class="text-xs opacity-90 block mt-0.5"></span>
            </div>
            <button onclick="closeNotification?.()" class="text-lg opacity-70 hover:opacity-100 transition-opacity flex-shrink-0">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div id="toastProgress" class="h-1 bg-white/40 absolute bottom-0 left-0 right-0"></div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div id="flashSuccess" class="hidden" data-message="{{ session('success') }}"></div>
    @endif
    @if(session('error'))
    <div id="flashError" class="hidden" data-message="{{ session('error') }}"></div>
    @endif

    <div class="flex min-h-[100dvh] lg:h-screen overflow-hidden dark:bg-gray-900" data-mobile-shell-layout>
        <!-- Teacher Sidebar -->
        @include('teacher.components.teachersidebar')
        <div id="sidebarBackdrop" class="hidden lg:hidden fixed inset-0 z-20 bg-black/50"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden" data-mobile-shell-panel>
            <!-- Teacher Header -->
            @include('teacher.components.teacherheader')

            <!-- Page Content -->
            <main class="teacher-content-shell flex-1 overflow-auto" data-mobile-main>
                <div class="teacher-page-body p-6 lg:p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div id="teacherPrintPreviewModal" class="fixed inset-0 z-[70] hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="teacherClosePrintPreview()"></div>
        <div class="relative mx-auto w-full max-w-6xl h-[92vh] flex flex-col overflow-hidden rounded-xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700">
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gradient-to-r from-rose-600 to-red-600">
                <div>
                    <h3 id="teacherPrintPreviewTitle" class="text-base font-semibold text-white">{{ __('Print Preview') }}</h3>
                    <p id="teacherPrintPreviewSubtitle" class="text-rose-100 text-xs">{{ __('A4 preview (use Print to open dialog)') }}</p>
                </div>
                <button onclick="teacherClosePrintPreview()" class="text-rose-100 hover:text-white p-2 rounded-full hover:bg-white/10" aria-label="Close print preview">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="flex-1 bg-gray-100 dark:bg-slate-900 p-4 overflow-auto">
                <iframe id="teacherPrintPreviewFrame" src="" class="w-full h-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white"></iframe>
            </div>

            <div class="px-5 py-3 border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-between items-center gap-3">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Tip: Use "New tab" for full-page preview.') }}</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="teacherOpenPrintPreviewInNewTab()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        <i class="bi bi-box-arrow-up-right mr-1"></i> {{ __('New tab') }}
                    </button>
                    <button type="button" onclick="teacherPrintPreviewFrame()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700 transition shadow-sm">
                        <i class="bi bi-printer mr-1"></i> {{ __('Print') }}
                    </button>
                    <button type="button" onclick="teacherClosePrintPreview()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('partials.mobile-bottom-nav', ['role' => 'teacher'])
    </div>

    <script data-mobile-static-script>
        window.teacherPrintPreviewState = {
            url: '',
            previousOverflow: '',
        };

        function teacherOpenPrintPreview(url, options = {}) {
            const modal = document.getElementById('teacherPrintPreviewModal');
            const frame = document.getElementById('teacherPrintPreviewFrame');
            const title = document.getElementById('teacherPrintPreviewTitle');
            const subtitle = document.getElementById('teacherPrintPreviewSubtitle');

            if (!modal || !frame || !url) {
                return;
            }

            window.teacherPrintPreviewState.url = url;
            window.teacherPrintPreviewState.previousOverflow = document.body.style.overflow;

            if (title) {
                title.textContent = options.title || '{{ __('Print Preview') }}';
            }

            if (subtitle) {
                subtitle.textContent = options.subtitle || '{{ __('A4 preview (use Print to open dialog)') }}';
            }

            frame.src = url;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function teacherClosePrintPreview() {
            const modal = document.getElementById('teacherPrintPreviewModal');
            const frame = document.getElementById('teacherPrintPreviewFrame');

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');

            if (frame) {
                frame.src = '';
            }

            document.body.style.overflow = window.teacherPrintPreviewState.previousOverflow || '';
            window.teacherPrintPreviewState.url = '';
            window.teacherPrintPreviewState.previousOverflow = '';
        }

        function teacherOpenPrintPreviewInNewTab() {
            if (!window.teacherPrintPreviewState.url) {
                return;
            }

            const url = window.teacherPrintPreviewState.url + (window.teacherPrintPreviewState.url.includes('?') ? '&' : '?') + 'newTab=1';
            window.open(url, '_blank');
        }

        function teacherPrintPreviewFrame() {
            const frame = document.getElementById('teacherPrintPreviewFrame');
            if (frame && frame.contentWindow) {
                frame.contentWindow.print();
            }
        }

        // Toast notification system
        function showToast(message, type = 'success', subMessage = '') {
            const toast = document.getElementById('toastNotification');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMessage');
            const subMsg = document.getElementById('toastSubMessage');
            const progress = document.getElementById('toastProgress');

            toast.classList.remove('hidden', 'bg-green-600', 'bg-red-600', 'bg-yellow-600', 'bg-blue-600');
            
            const colors = {
                success: { bg: 'bg-green-600', icon: '✓' },
                error: { bg: 'bg-red-600', icon: '✕' },
                warning: { bg: 'bg-yellow-600', icon: '⚠' },
                info: { bg: 'bg-blue-600', icon: 'ℹ' }
            };

            const color = colors[type] || colors.success;
            toast.classList.add(color.bg);
            icon.textContent = color.icon;
            msg.textContent = message;
            subMsg.textContent = subMessage;

            let progress_width = 100;
            const interval = setInterval(() => {
                progress_width -= 2;
                progress.style.width = progress_width + '%';
            }, 30);

            setTimeout(() => {
                clearInterval(interval);
                toast.classList.add('hidden');
            }, 3000);
        }

        // Check for flash messages
        document.addEventListener('DOMContentLoaded', function() {
            const success = document.getElementById('flashSuccess');
            const error = document.getElementById('flashError');
            if (success) showToast(success.dataset.message, 'success');
            if (error) showToast(error.dataset.message, 'error');
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('teacherPrintPreviewModal');
                if (modal && !modal.classList.contains('hidden')) {
                    teacherClosePrintPreview();
                }
            }
        });

        // Sidebar Toggle Functionality
        (function() {
            const sidebar = document.getElementById('sidebar');
            sidebar?.classList.remove('sidebar-collapsed', 'fixed', 'inset-0', 'z-40');
        })();
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>

