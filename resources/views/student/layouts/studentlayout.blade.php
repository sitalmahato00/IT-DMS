<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Manmohan Memorial Polytechnic') - Student</title>
    @include('partials.pwa-head', ['themeColor' => '#FF0037'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script data-mobile-static-script>
        document.documentElement.classList.add('student-ui-enhanced');
    </script>
    <style data-mobile-static-style>
        html.student-ui-enhanced:not(.dark) {
            --student-surface-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 249, 250, 0.97));
            --student-surface-border: rgba(241, 213, 219, 0.95);
            --student-surface-shadow: 0 28px 56px -40px rgba(148, 19, 52, 0.24);
            --student-soft-shadow: 0 18px 34px -28px rgba(15, 23, 42, 0.16);
            --student-focus-ring: 0 0 0 4px rgba(244, 63, 94, 0.12);
        }

        html.student-ui-enhanced.dark {
            --student-dark-bg: radial-gradient(circle at top, rgba(190, 24, 93, 0.18), rgba(15, 23, 42, 0) 34%), linear-gradient(180deg, #020617 0%, #07111f 48%, #020617 100%);
            --student-dark-surface: linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(7, 12, 24, 0.98));
            --student-dark-surface-soft: linear-gradient(180deg, rgba(30, 41, 59, 0.84), rgba(15, 23, 42, 0.96));
            --student-dark-muted-surface: rgba(15, 23, 42, 0.78);
            --student-dark-border: rgba(148, 163, 184, 0.2);
            --student-dark-border-strong: rgba(248, 113, 113, 0.18);
            --student-dark-shadow: 0 30px 60px -34px rgba(2, 6, 23, 0.86);
            --student-dark-soft-shadow: 0 20px 36px -28px rgba(2, 6, 23, 0.78);
            --student-dark-text: #e2e8f0;
            --student-dark-muted: #94a3b8;
            --student-dark-focus-ring: 0 0 0 4px rgba(244, 63, 94, 0.14);
        }

        html.student-ui-enhanced .student-content-shell {
            position: relative;
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell {
            color: #0f172a;
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            box-shadow: 0 34px 68px -30px rgba(225, 29, 72, 0.34);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-panel,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-form-panel,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-table-card,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-empty,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-aside,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-photo-frame,
        html.student-ui-enhanced:not(.dark) .student-content-shell div.bg-white.rounded-xl.shadow-lg,
        html.student-ui-enhanced:not(.dark) .student-content-shell div.bg-white.rounded-xl.border,
        html.student-ui-enhanced:not(.dark) .student-content-shell div.bg-white.rounded-lg.border,
        html.student-ui-enhanced:not(.dark) .student-content-shell div.bg-white.rounded-lg.shadow-sm,
        html.student-ui-enhanced:not(.dark) .student-content-shell div.bg-white.rounded-xl.shadow-sm,
        html.student-ui-enhanced:not(.dark) .student-content-shell section.bg-white.rounded-xl.border,
        html.student-ui-enhanced:not(.dark) .student-content-shell form.bg-white.rounded-lg.border {
            border-color: var(--student-surface-border);
            background: var(--student-surface-bg);
            box-shadow: var(--student-surface-shadow);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-panel,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-form-panel,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-table-card,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-empty,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-aside,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-photo-frame {
            border-radius: 28px;
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-card,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-list-card,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-quicklink,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-mini-card {
            border-radius: 24px;
            border: 1px solid rgba(229, 213, 218, 0.96);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(255, 249, 250, 0.94));
            box-shadow: var(--student-soft-shadow);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-card:hover,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-list-card:hover,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-quicklink:hover,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-mini-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 26px 44px -32px rgba(148, 19, 52, 0.26);
            border-color: rgba(244, 114, 182, 0.22);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-form-panel,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-table-card {
            overflow: hidden;
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-panel-header,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-form-panel > .border-b,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-table-card > .border-b {
            background: linear-gradient(180deg, #fff5f7, #fffafb);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-photo-frame {
            padding: 1.5rem;
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-upload-trigger {
            border-radius: 999px;
            box-shadow: 0 18px 28px -24px rgba(225, 29, 72, 0.34);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-static-field {
            border-radius: 18px;
            border: 1px solid rgba(226, 232, 240, 0.96);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-empty {
            border-style: dashed;
            background: linear-gradient(180deg, rgba(255, 251, 235, 0.96), rgba(255, 247, 237, 0.98));
            border-color: rgba(253, 224, 71, 0.84);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell table thead,
        html.student-ui-enhanced:not(.dark) .student-content-shell thead.bg-gray-50,
        html.student-ui-enhanced:not(.dark) .student-content-shell thead.dark\:bg-gray-700,
        html.student-ui-enhanced:not(.dark) .student-content-shell thead.dark\:bg-slate-700,
        html.student-ui-enhanced:not(.dark) .student-content-shell .bg-gray-50.border-b {
            background: linear-gradient(180deg, #fff5f7, #fffafb);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell tbody tr:hover,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-table-card tbody tr:hover td,
        html.student-ui-enhanced:not(.dark) .student-content-shell .student-smooth-page .student-smooth-table-card tbody tr:hover th {
            background: linear-gradient(90deg, rgba(255, 241, 242, 0.72), rgba(255, 255, 255, 0.97));
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        html.student-ui-enhanced:not(.dark) .student-content-shell select,
        html.student-ui-enhanced:not(.dark) .student-content-shell textarea {
            border-color: rgba(203, 213, 225, 0.9);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        html.student-ui-enhanced:not(.dark) .student-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):focus,
        html.student-ui-enhanced:not(.dark) .student-content-shell select:focus,
        html.student-ui-enhanced:not(.dark) .student-content-shell textarea:focus {
            border-color: #fb7185;
            box-shadow: var(--student-focus-ring);
        }

        html.student-ui-enhanced.dark .student-content-shell {
            color: var(--student-dark-text);
        }

        html.student-ui-enhanced.dark .student-content-shell .student-smooth-hero {
            position: relative;
            overflow: hidden;
            box-shadow: 0 32px 64px -28px rgba(190, 24, 93, 0.38);
        }

        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-panel,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-form-panel,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-table-card,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-empty,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-aside,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-photo-frame,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-card,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-list-card,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-quicklink,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-mini-card,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-xl.shadow-lg,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-xl.border,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-lg.border,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-lg.shadow-sm,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-xl.shadow-sm,
        html.student-ui-enhanced.dark .student-content-shell section.bg-white.rounded-xl.border,
        html.student-ui-enhanced.dark .student-content-shell form.bg-white.rounded-lg.border {
            border-color: var(--student-dark-border);
            box-shadow: var(--student-dark-shadow);
        }

        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-panel,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-form-panel,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-table-card,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-empty,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-aside,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-photo-frame,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-xl.shadow-lg,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-xl.border,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-lg.border,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-lg.shadow-sm,
        html.student-ui-enhanced.dark .student-content-shell div.bg-white.rounded-xl.shadow-sm,
        html.student-ui-enhanced.dark .student-content-shell section.bg-white.rounded-xl.border,
        html.student-ui-enhanced.dark .student-content-shell form.bg-white.rounded-lg.border {
            background: var(--student-dark-surface);
        }

        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-card,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-list-card,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-quicklink,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-mini-card {
            background: var(--student-dark-surface-soft);
            border-color: var(--student-dark-border-strong);
            box-shadow: var(--student-dark-soft-shadow);
        }

        html.student-ui-enhanced.dark .student-content-shell .bg-gray-50,
        html.student-ui-enhanced.dark .student-content-shell .bg-slate-50,
        html.student-ui-enhanced.dark .student-content-shell .bg-white\/10,
        html.student-ui-enhanced.dark .student-content-shell .bg-white\/15,
        html.student-ui-enhanced.dark .student-content-shell .bg-gray-50.dark\:bg-gray-700\/50,
        html.student-ui-enhanced.dark .student-content-shell .bg-gray-50.dark\:bg-gray-900\/40,
        html.student-ui-enhanced.dark .student-content-shell .bg-gray-50.dark\:bg-gray-900\/50 {
            background: rgba(15, 23, 42, 0.78);
        }

        html.student-ui-enhanced.dark .student-content-shell .text-gray-900,
        html.student-ui-enhanced.dark .student-content-shell .text-slate-900 {
            color: var(--student-dark-text);
        }

        html.student-ui-enhanced.dark .student-content-shell .text-gray-700,
        html.student-ui-enhanced.dark .student-content-shell .text-slate-700 {
            color: #cbd5e1;
        }

        html.student-ui-enhanced.dark .student-content-shell .text-gray-600,
        html.student-ui-enhanced.dark .student-content-shell .text-gray-500,
        html.student-ui-enhanced.dark .student-content-shell .text-slate-600,
        html.student-ui-enhanced.dark .student-content-shell .text-slate-500 {
            color: var(--student-dark-muted);
        }

        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-static-field {
            border-color: rgba(148, 163, 184, 0.22);
            background: rgba(15, 23, 42, 0.72);
            color: var(--student-dark-text);
        }

        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-panel-header,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-form-panel > .border-b,
        html.student-ui-enhanced.dark .student-content-shell .student-smooth-page .student-smooth-table-card > .border-b,
        html.student-ui-enhanced.dark .student-content-shell table thead,
        html.student-ui-enhanced.dark .student-content-shell thead.bg-gray-50,
        html.student-ui-enhanced.dark .student-content-shell thead.dark\:bg-gray-700,
        html.student-ui-enhanced.dark .student-content-shell thead.dark\:bg-slate-700,
        html.student-ui-enhanced.dark .student-content-shell .bg-gray-50.border-b {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.92), rgba(15, 23, 42, 0.96));
        }

        html.student-ui-enhanced.dark .student-content-shell tbody tr:hover {
            background: rgba(148, 163, 184, 0.08);
        }

        html.student-ui-enhanced.dark .student-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        html.student-ui-enhanced.dark .student-content-shell select,
        html.student-ui-enhanced.dark .student-content-shell textarea {
            border-color: rgba(148, 163, 184, 0.22);
            background: rgba(7, 12, 24, 0.88);
            color: var(--student-dark-text);
            box-shadow: inset 0 1px 2px rgba(2, 6, 23, 0.4);
        }

        html.student-ui-enhanced.dark .student-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):focus,
        html.student-ui-enhanced.dark .student-content-shell select:focus,
        html.student-ui-enhanced.dark .student-content-shell textarea:focus {
            border-color: #fb7185;
            box-shadow: var(--student-dark-focus-ring);
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body class="student-panel font-sans antialiased bg-gray-50 dark:bg-gray-900" data-mobile-shell="student" data-mobile-role="student" data-mobile-route="{{ Route::currentRouteName() ?? '' }}">
    <div id="mobileAppShellRoot" data-mobile-shell-root>
    <div class="fixed inset-0 pointer-events-none opacity-10 z-0 flex items-center justify-center">
        @if(isset($departmentLogoUrl))
                <img src="{{ $departmentLogoUrl }}" alt="{{ __('College Logo') }}" class="w-[600px] h-[600px] object-contain">
        @else
            <i class="bi bi-mortarboard text-[30rem] text-gray-300 dark:text-gray-700"></i>
        @endif
    </div>

    <div id="globalLoader" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col items-center gap-4">
            <div class="animate-spin w-12 h-12 border-4 border-t-red-600 border-gray-200 rounded-full"></div>
            <p class="text-sm text-gray-700 font-medium">{{ __('Loading') }}...</p>
        </div>
    </div>

    <div id="toastNotification" class="hidden fixed top-4 right-4 z-[9999] rounded-xl shadow-2xl text-white text-sm transition-all duration-300 max-w-sm relative overflow-hidden animate-slide-in-right">
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

    @if(session('success'))
        <div id="flashSuccess" class="hidden" data-message="{{ session('success') }}"></div>
    @endif
    @if(session('error'))
        <div id="flashError" class="hidden" data-message="{{ session('error') }}"></div>
    @endif
    @if(session('warning'))
        <div id="flashWarning" class="hidden" data-message="{{ session('warning') }}"></div>
    @endif

    <div class="flex min-h-[100dvh] lg:h-screen overflow-hidden dark:bg-gray-900" data-mobile-shell-layout>
        @include('student.components.studentsidebar')

        <div id="sidebarBackdrop" class="hidden lg:hidden fixed inset-0 z-20 bg-black/50"></div>

        <div class="flex-1 flex flex-col overflow-hidden" data-mobile-shell-panel>
            @include('student.components.studentheader')

            <main class="flex-1 overflow-y-auto min-h-0" data-mobile-main>
                <div class="student-content-shell px-6 py-3 min-h-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div id="studentPrintPreviewModal" class="fixed inset-0 z-[70] hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="studentClosePrintPreview()"></div>
        <div class="relative mx-auto w-full max-w-6xl h-[92vh] flex flex-col overflow-hidden rounded-xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700">
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gradient-to-r from-rose-600 to-red-600">
                <div>
                    <h3 id="studentPrintPreviewTitle" class="text-base font-semibold text-white">{{ __('Print Preview') }}</h3>
                    <p id="studentPrintPreviewSubtitle" class="text-rose-100 text-xs">{{ __('A4 preview (use Print to open dialog)') }}</p>
                </div>
                <button onclick="studentClosePrintPreview()" class="text-rose-100 hover:text-white p-2 rounded-full hover:bg-white/10" aria-label="{{ __('Close print preview') }}">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="flex-1 bg-gray-100 dark:bg-slate-900 p-4 overflow-auto">
                <iframe id="studentPrintPreviewFrame" src="" class="w-full h-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white"></iframe>
            </div>

            <div class="px-5 py-3 border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-between items-center gap-3">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Tip: Use "New tab" for full-page preview.') }}</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="studentOpenPrintPreviewInNewTab()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        <i class="bi bi-box-arrow-up-right mr-1"></i> {{ __('New tab') }}
                    </button>
                    <button type="button" onclick="studentPrintPreviewFrame()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700 transition shadow-sm">
                        <i class="bi bi-printer mr-1"></i> {{ __('Print') }}
                    </button>
                    <button type="button" onclick="studentClosePrintPreview()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @yield('ajax-modal')
    @include('partials.mobile-bottom-nav', ['role' => 'student'])
    </div>
    @yield('scripts')
    @stack('scripts')

    <script data-mobile-static-script>
        window.studentPrintPreviewState = {
            url: '',
            previousOverflow: '',
        };

        function studentOpenPrintPreview(url, options = {}) {
            const modal = document.getElementById('studentPrintPreviewModal');
            const frame = document.getElementById('studentPrintPreviewFrame');
            const title = document.getElementById('studentPrintPreviewTitle');
            const subtitle = document.getElementById('studentPrintPreviewSubtitle');

            if (!modal || !frame || !url) {
                return;
            }

            window.studentPrintPreviewState.url = url;
            window.studentPrintPreviewState.previousOverflow = document.body.style.overflow;

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

        function studentClosePrintPreview() {
            const modal = document.getElementById('studentPrintPreviewModal');
            const frame = document.getElementById('studentPrintPreviewFrame');

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');

            if (frame) {
                frame.src = '';
            }

            document.body.style.overflow = window.studentPrintPreviewState.previousOverflow || '';
            window.studentPrintPreviewState.url = '';
            window.studentPrintPreviewState.previousOverflow = '';
        }

        function studentOpenPrintPreviewInNewTab() {
            if (!window.studentPrintPreviewState.url) {
                return;
            }

            const url = window.studentPrintPreviewState.url + (window.studentPrintPreviewState.url.includes('?') ? '&' : '?') + 'newTab=1';
            window.open(url, '_blank');
        }

        function studentPrintPreviewFrame() {
            const frame = document.getElementById('studentPrintPreviewFrame');
            if (frame && frame.contentWindow) {
                frame.contentWindow.print();
            }
        }

        function showLoading(message = '{{ __('Loading') }}...') {
            const loader = document.getElementById('globalLoader');
            const loaderText = loader?.querySelector('p');

            if (loaderText) {
                loaderText.textContent = message;
            }

            loader?.classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('globalLoader')?.classList.add('hidden');
        }

        let toastTimeout;

        function getToastIcon(type) {
            if (type === 'success') return '<i class="bi bi-check-circle-fill"></i>';
            if (type === 'error') return '<i class="bi bi-exclamation-octagon-fill"></i>';
            if (type === 'warning') return '<i class="bi bi-exclamation-triangle-fill"></i>';
            return '<i class="bi bi-info-circle-fill"></i>';
        }

        function closeNotification() {
            document.getElementById('toastNotification')?.classList.add('hidden');
            if (toastTimeout) {
                clearTimeout(toastTimeout);
            }
        }

        function showToast(message, type = 'info', subMessage = '', duration = 3500) {
            const toast = document.getElementById('toastNotification');

            if (!toast) {
                return;
            }

            const bgClass = type === 'success'
                ? 'bg-gradient-to-r from-green-600 to-emerald-600'
                : type === 'error'
                    ? 'bg-gradient-to-r from-red-600 to-rose-600'
                    : type === 'warning'
                        ? 'bg-gradient-to-r from-amber-500 to-orange-600'
                        : 'bg-gradient-to-r from-blue-600 to-cyan-600';

            toast.innerHTML = `
                <div class="${bgClass} backdrop-blur-md bg-opacity-95 p-4 flex items-center gap-3">
                    <div id="toastIcon" class="text-xl flex-shrink-0">${getToastIcon(type)}</div>
                    <div class="flex-1">
                        <span id="toastMessage" class="font-medium block">${message}</span>
                        <span id="toastSubMessage" class="text-xs opacity-90 block mt-0.5">${subMessage || ''}</span>
                    </div>
                    <button onclick="closeNotification()" class="text-lg opacity-70 hover:opacity-100 transition-opacity flex-shrink-0">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div id="toastProgress" class="h-1 bg-white/40 absolute bottom-0 left-0 right-0"></div>
            `;

            const progressBar = toast.querySelector('#toastProgress');
            toast.classList.remove('hidden');

            if (progressBar) {
                progressBar.style.transition = 'none';
                progressBar.style.width = '100%';
                void toast.offsetWidth;
                progressBar.style.transition = `width ${duration}ms linear`;
                progressBar.style.width = '0%';
            }

            if (toastTimeout) {
                clearTimeout(toastTimeout);
            }

            toastTimeout = setTimeout(() => {
                toast.classList.add('hidden');
                if (progressBar) {
                    progressBar.style.width = '100%';
                }
            }, duration);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            sidebar?.classList.remove('sidebar-collapsed');
            localStorage.removeItem('student-sidebar-collapsed');
            sidebarBackdrop?.classList.add('hidden');

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) {
                    sidebarBackdrop?.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });

            const success = document.getElementById('flashSuccess');
            const error = document.getElementById('flashError');
            const warning = document.getElementById('flashWarning');

            if (success) {
                showToast(success.dataset.message, 'success');
            }

            if (error) {
                showToast(error.dataset.message, 'error');
            }

            if (warning) {
                showToast(warning.dataset.message, 'warning');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            const previewModal = document.getElementById('studentPrintPreviewModal');
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            if (previewModal && !previewModal.classList.contains('hidden')) {
                studentClosePrintPreview();
            }

            if (sidebar && window.innerWidth < 1024 && !sidebar.classList.contains('hidden')) {
                sidebar.classList.add('hidden');
                sidebarBackdrop?.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>

