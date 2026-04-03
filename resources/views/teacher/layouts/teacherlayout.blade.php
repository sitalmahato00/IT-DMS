<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Department Management System (IT-DMS)') - Teacher</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.documentElement.classList.add('teacher-ui-enhanced');
    </script>
    <style>
        html.teacher-ui-enhanced:not(.dark) {
            --teacher-surface-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 249, 250, 0.97));
            --teacher-surface-border: rgba(241, 213, 219, 0.95);
            --teacher-surface-shadow: 0 28px 56px -40px rgba(148, 19, 52, 0.24);
            --teacher-soft-shadow: 0 18px 34px -28px rgba(15, 23, 42, 0.16);
            --teacher-focus-ring: 0 0 0 4px rgba(244, 63, 94, 0.12);
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
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body class="teacher-panel font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <!-- Department Logo Background for All Pages -->
    <div class="fixed inset-0 pointer-events-none opacity-10 z-0 flex items-center justify-center">
        @if(isset($departmentLogoUrl))
            <img src="{{ $departmentLogoUrl }}" alt="{{ __('Department Logo') }}" class="w-[600px] h-[600px] object-contain">
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

    <div class="flex h-screen overflow-hidden dark:bg-gray-900">
        <!-- Teacher Sidebar -->
        @include('teacher.components.teachersidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Teacher Header -->
            @include('teacher.components.teacherheader')

            <!-- Page Content -->
            <main class="teacher-content-shell flex-1 overflow-auto">
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

    <script>
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
            const mobileToggle = document.getElementById('sidebarToggle');
            const desktopToggle = document.getElementById('desktopSidebarToggle');
            
            // Mobile sidebar toggle
            if (mobileToggle && sidebar) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                    sidebar.classList.toggle('fixed');
                    sidebar.classList.toggle('inset-0');
                    sidebar.classList.toggle('z-40');
                    sidebar.classList.toggle('w-64');
                    
                    if (!sidebar.classList.contains('hidden')) {
                        sidebar.style.height = 'calc(100vh - 40px)';
                        sidebar.style.top = '40px';
                    }
                });
            }
            
            // Desktop sidebar collapse toggle
            if (desktopToggle && sidebar) {
                desktopToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('sidebar-collapsed');
                    
                    // Update toggle icon
                    const icon = desktopToggle.querySelector('i');
                    if (sidebar.classList.contains('sidebar-collapsed')) {
                        icon.classList.remove('bi-layout-sidebar');
                        icon.classList.add('bi-layout-sidebar-inset');
                    } else {
                        icon.classList.remove('bi-layout-sidebar-inset');
                        icon.classList.add('bi-layout-sidebar');
                    }
                });
            }

            // Close mobile sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (sidebar && !sidebar.classList.contains('hidden') && !sidebar.classList.contains('lg:flex')) {
                    if (!sidebar.contains(e.target) && !mobileToggle?.contains(e.target)) {
                        sidebar.classList.add('hidden');
                        sidebar.classList.remove('fixed', 'inset-0', 'z-40');
                    }
                }
            });
        })();
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
