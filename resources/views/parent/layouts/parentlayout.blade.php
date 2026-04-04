<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Department Management System (IT-DMS)') - Parent</title>
    @include('partials.pwa-head', ['themeColor' => '#FF0037'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script data-mobile-static-script>
        document.documentElement.classList.add('parent-ui-enhanced');
    </script>
    <style data-mobile-static-style>
        html.parent-ui-enhanced:not(.dark) {
            --parent-surface-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 249, 250, 0.97));
            --parent-surface-border: rgba(241, 213, 219, 0.95);
            --parent-surface-shadow: 0 28px 56px -40px rgba(148, 19, 52, 0.24);
            --parent-soft-shadow: 0 18px 34px -28px rgba(15, 23, 42, 0.16);
            --parent-focus-ring: 0 0 0 4px rgba(244, 63, 94, 0.12);
        }

        html.parent-ui-enhanced.dark {
            --parent-dark-surface: linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(7, 12, 24, 0.98));
            --parent-dark-surface-soft: linear-gradient(180deg, rgba(30, 41, 59, 0.84), rgba(15, 23, 42, 0.96));
            --parent-dark-muted-surface: rgba(15, 23, 42, 0.78);
            --parent-dark-border: rgba(148, 163, 184, 0.2);
            --parent-dark-border-strong: rgba(248, 113, 113, 0.18);
            --parent-dark-shadow: 0 30px 60px -34px rgba(2, 6, 23, 0.86);
            --parent-dark-soft-shadow: 0 20px 36px -28px rgba(2, 6, 23, 0.78);
            --parent-dark-text: #e2e8f0;
            --parent-dark-muted: #94a3b8;
            --parent-dark-focus-ring: 0 0 0 4px rgba(244, 63, 94, 0.14);
        }

        html.parent-ui-enhanced .parent-content-shell {
            position: relative;
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell {
            color: #0f172a;
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            box-shadow: 0 34px 68px -30px rgba(225, 29, 72, 0.34);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-panel,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-form-panel,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-table-card,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-empty,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-photo-frame,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell div.rounded-2xl.border.bg-white,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell div.rounded-xl.border.bg-white,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell div.rounded-xl.shadow-lg.bg-white,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell div.rounded-lg.border.bg-white,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell div.rounded-lg.shadow-sm.bg-white,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell form.rounded-lg.border.bg-white {
            border-color: var(--parent-surface-border);
            background: var(--parent-surface-bg);
            box-shadow: var(--parent-surface-shadow);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-panel,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-form-panel,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-table-card,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-empty,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-photo-frame {
            border-radius: 28px;
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-card,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-list-card,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-quicklink,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-mini-card,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-chip {
            border-radius: 24px;
            border: 1px solid rgba(229, 213, 218, 0.96);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(255, 249, 250, 0.94));
            box-shadow: var(--parent-soft-shadow);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-card:hover,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-list-card:hover,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-quicklink:hover,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-mini-card:hover,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 26px 44px -32px rgba(148, 19, 52, 0.26);
            border-color: rgba(244, 114, 182, 0.22);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-form-panel,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-table-card {
            overflow: hidden;
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-panel-header,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-form-panel > .border-b,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-table-card > .border-b {
            background: linear-gradient(180deg, #fff5f7, #fffafb);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-photo-frame {
            padding: 1.5rem;
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-upload-trigger {
            border-radius: 999px;
            box-shadow: 0 18px 28px -24px rgba(225, 29, 72, 0.34);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-static-field {
            border-radius: 18px;
            border: 1px solid rgba(226, 232, 240, 0.96);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-empty {
            border-style: dashed;
            background: linear-gradient(180deg, rgba(255, 251, 235, 0.96), rgba(255, 247, 237, 0.98));
            border-color: rgba(253, 224, 71, 0.84);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell table thead,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell thead.bg-gray-50,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell thead.bg-gray-100,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell thead.dark\:bg-gray-700,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell thead.dark\:bg-slate-700,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .bg-gray-50.border-b,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .bg-gray-100.border-b {
            background: linear-gradient(180deg, #fff5f7, #fffafb);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell tbody tr:hover,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-table-card tbody tr:hover td,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell .parent-smooth-page .parent-smooth-table-card tbody tr:hover th {
            background: linear-gradient(90deg, rgba(255, 241, 242, 0.72), rgba(255, 255, 255, 0.97));
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        html.parent-ui-enhanced:not(.dark) .parent-content-shell select,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell textarea {
            border-color: rgba(203, 213, 225, 0.9);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        html.parent-ui-enhanced:not(.dark) .parent-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):focus,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell select:focus,
        html.parent-ui-enhanced:not(.dark) .parent-content-shell textarea:focus {
            border-color: #fb7185;
            box-shadow: var(--parent-focus-ring);
        }

        html.parent-ui-enhanced.dark .parent-content-shell {
            color: var(--parent-dark-text);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-hero {
            position: relative;
            overflow: hidden;
            box-shadow: 0 32px 64px -28px rgba(190, 24, 93, 0.38);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-panel,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-form-panel,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-table-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-empty,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-photo-frame,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-list-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-quicklink,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-mini-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-chip,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-2xl.border.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-xl.border.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-xl.shadow-lg.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-lg.border.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-lg.shadow-sm.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell form.rounded-lg.border.bg-white {
            border-color: var(--parent-dark-border);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-panel,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-form-panel,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-table-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-empty,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-photo-frame,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-2xl.border.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-xl.border.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-xl.shadow-lg.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-lg.border.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell div.rounded-lg.shadow-sm.bg-white,
        html.parent-ui-enhanced.dark .parent-content-shell form.rounded-lg.border.bg-white {
            background: var(--parent-dark-surface);
            box-shadow: var(--parent-dark-shadow);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-list-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-quicklink,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-mini-card,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-chip {
            background: var(--parent-dark-surface-soft);
            border-color: var(--parent-dark-border-strong);
            box-shadow: var(--parent-dark-soft-shadow);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .bg-gray-50,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-gray-100,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-slate-50,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-gray-50.dark\:bg-gray-900\/40,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-gray-50.dark\:bg-gray-800,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-white\/10,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-white\/15 {
            background: rgba(15, 23, 42, 0.78);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .text-gray-900,
        html.parent-ui-enhanced.dark .parent-content-shell .text-slate-900 {
            color: var(--parent-dark-text);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .text-gray-700,
        html.parent-ui-enhanced.dark .parent-content-shell .text-slate-700 {
            color: #cbd5e1;
        }

        html.parent-ui-enhanced.dark .parent-content-shell .text-gray-600,
        html.parent-ui-enhanced.dark .parent-content-shell .text-gray-500,
        html.parent-ui-enhanced.dark .parent-content-shell .text-slate-600,
        html.parent-ui-enhanced.dark .parent-content-shell .text-slate-500 {
            color: var(--parent-dark-muted);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-static-field {
            border-color: rgba(148, 163, 184, 0.22);
            background: rgba(15, 23, 42, 0.72);
            color: var(--parent-dark-text);
        }

        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-panel-header,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-form-panel > .border-b,
        html.parent-ui-enhanced.dark .parent-content-shell .parent-smooth-page .parent-smooth-table-card > .border-b,
        html.parent-ui-enhanced.dark .parent-content-shell table thead,
        html.parent-ui-enhanced.dark .parent-content-shell thead.bg-gray-50,
        html.parent-ui-enhanced.dark .parent-content-shell thead.bg-gray-100,
        html.parent-ui-enhanced.dark .parent-content-shell thead.dark\:bg-gray-700,
        html.parent-ui-enhanced.dark .parent-content-shell thead.dark\:bg-slate-700,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-gray-50.border-b,
        html.parent-ui-enhanced.dark .parent-content-shell .bg-gray-100.border-b {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.92), rgba(15, 23, 42, 0.96));
        }

        html.parent-ui-enhanced.dark .parent-content-shell tbody tr:hover {
            background: rgba(148, 163, 184, 0.08);
        }

        html.parent-ui-enhanced.dark .parent-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        html.parent-ui-enhanced.dark .parent-content-shell select,
        html.parent-ui-enhanced.dark .parent-content-shell textarea {
            border-color: rgba(148, 163, 184, 0.22);
            background: rgba(7, 12, 24, 0.88);
            color: var(--parent-dark-text);
            box-shadow: inset 0 1px 2px rgba(2, 6, 23, 0.4);
        }

        html.parent-ui-enhanced.dark .parent-content-shell input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):focus,
        html.parent-ui-enhanced.dark .parent-content-shell select:focus,
        html.parent-ui-enhanced.dark .parent-content-shell textarea:focus {
            border-color: #fb7185;
            box-shadow: var(--parent-dark-focus-ring);
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900" data-mobile-shell="parent" data-mobile-role="parent" data-mobile-route="{{ Route::currentRouteName() ?? '' }}">
    <div id="mobileAppShellRoot" data-mobile-shell-root>
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
        <!-- Parent Sidebar -->
        @include('parent.components.parentsidebar')
        <div id="sidebarBackdrop" class="hidden lg:hidden fixed inset-0 z-20 bg-black/50"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden" data-mobile-shell-panel>
            <!-- Parent Header -->
            @include('parent.components.parentheader')

            <!-- Page Content -->
            <main class="flex-1 overflow-auto" data-mobile-main>
                <div class="parent-content-shell p-6 lg:p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @include('partials.mobile-bottom-nav', ['role' => 'parent'])
    </div>

    <script data-mobile-static-script>
        // Toast notification system
        function showToast(message, type = 'success', subMessage = '') {
            const toast = document.getElementById('toastNotification');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMessage');
            const subMsg = document.getElementById('toastSubMessage');
            const progress = document.getElementById('toastProgress');

            toast.classList.remove('hidden', 'bg-red-600', 'bg-red-600', 'bg-yellow-600', 'bg-blue-600');
            
            const colors = {
                success: { bg: 'bg-red-600', icon: '✓' },
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
    </script>

    @yield('scripts')
</body>
</html>
