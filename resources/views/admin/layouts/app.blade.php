<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Department Management System (IT-DMS)') - Admin</title>
    @include('partials.pwa-head', ['themeColor' => '#FF0037'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
    <style data-mobile-static-style>
        @media (max-width: 640px) {
            #adminLayoutShell .fixed.inset-0.pointer-events-none {
                opacity: 0.04;
            }

            #adminLayoutShell .fixed.inset-0.pointer-events-none img {
                width: 180px;
                height: 180px;
            }

            #adminLayoutShell .fixed.inset-0.pointer-events-none i {
                font-size: 9rem;
            }
        }

        html.dark .student-page-card,
        html.dark .student-page-section,
        html.dark .student-side-card,
        html.dark .student-sticky-bar,
        html.dark .student-view-card,
        html.dark .student-view-section,
        html.dark .student-stat-card,
        html.dark .student-list-card,
        html.dark .student-detail-box,
        html.dark .student-exam-accordion {
            border-color: rgba(51, 65, 85, 0.95);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.96) 0%, rgba(17, 24, 39, 0.96) 100%);
            box-shadow: 0 24px 48px -34px rgba(2, 6, 23, 0.78);
            color: #e2e8f0;
        }

        html.dark .student-page-hero,
        html.dark .student-view-hero {
            border-color: rgba(251, 113, 133, 0.38);
            background: linear-gradient(135deg, rgba(69, 10, 10, 0.6) 0%, rgba(15, 23, 42, 0.96) 55%, rgba(30, 41, 59, 0.96) 100%);
            box-shadow: 0 26px 52px -36px rgba(2, 6, 23, 0.82);
        }

        html.dark .student-page-hero:after,
        html.dark .student-view-hero:after {
            background: radial-gradient(circle, rgba(251, 113, 133, 0.2), rgba(251, 113, 133, 0) 72%);
        }

        html.dark .student-label {
            color: #94a3b8;
        }

        html.dark .student-input,
        html.dark .student-select,
        html.dark .student-textarea,
        html.dark .student-file {
            border-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.84);
            color: #e5e7eb;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.5);
            color-scheme: dark;
        }

        html.dark .student-input::placeholder,
        html.dark .student-textarea::placeholder {
            color: #64748b;
        }

        html.dark .student-input:focus,
        html.dark .student-select:focus,
        html.dark .student-textarea:focus,
        html.dark .student-file:focus {
            border-color: #fb7185;
            box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.18);
            background: rgba(15, 23, 42, 0.95);
        }

        html.dark .student-file::file-selector-button {
            border: 1px solid rgba(71, 85, 105, 0.9);
            border-radius: 999px;
            background: rgba(30, 41, 59, 0.95);
            color: #e2e8f0;
            padding: 0.45rem 0.9rem;
            margin-right: 0.75rem;
            font-weight: 700;
            cursor: pointer;
        }

        html.dark .student-photo-dropzone {
            border-color: rgba(251, 113, 133, 0.52);
            background: linear-gradient(180deg, rgba(127, 29, 29, 0.16) 0%, rgba(15, 23, 42, 0.86) 100%);
        }

        html.dark .student-photo-dropzone:hover,
        html.dark .student-photo-dropzone.is-dragover {
            border-color: rgba(251, 113, 133, 0.76);
            box-shadow: 0 20px 38px -28px rgba(2, 6, 23, 0.74);
        }

        html.dark .student-photo-frame {
            border-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.96);
            box-shadow: 0 22px 44px -30px rgba(2, 6, 23, 0.82);
        }

        html.dark .student-btn-secondary,
        html.dark .student-btn-soft {
            border-color: rgba(71, 85, 105, 0.9);
            background: rgba(15, 23, 42, 0.84);
            color: #e2e8f0;
        }

        html.dark .student-btn-soft {
            border-color: rgba(251, 113, 133, 0.42);
            background: rgba(136, 19, 55, 0.26);
            color: #fecdd3;
        }

        html.dark .student-chip.active,
        html.dark .student-chip.pass {
            background: rgba(22, 101, 52, 0.25);
            color: #bbf7d0;
        }

        html.dark .student-chip.inactive,
        html.dark .student-chip.fail {
            background: rgba(153, 27, 27, 0.24);
            color: #fecaca;
        }

        html.dark .student-chip.pending,
        html.dark .student-chip.absent,
        html.dark .student-chip.suspended {
            background: rgba(120, 53, 15, 0.28);
            color: #fcd34d;
        }

        html.dark .student-chip.soft {
            background: rgba(30, 41, 59, 0.78);
            color: #cbd5e1;
        }

        html.dark .student-file-pill {
            border-color: rgba(71, 85, 105, 0.9);
            background: rgba(15, 23, 42, 0.84);
            color: #e2e8f0;
        }

        html.dark .student-toggle {
            background: rgba(71, 85, 105, 0.95);
        }

        html.dark .student-toggle:after {
            background: #e2e8f0;
            box-shadow: 0 10px 16px -12px rgba(2, 6, 23, 0.72);
        }

        html.dark .student-tab-bar,
        html.dark .student-view-tabs {
            border-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.92);
            box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.05);
        }

        html.dark .student-tab-button,
        html.dark .student-view-tab {
            color: #cbd5e1;
        }

        html.dark .student-tab-button:hover,
        html.dark .student-view-tab:hover {
            background: rgba(136, 19, 55, 0.18);
            color: #fecdd3;
        }

        html.dark .student-tab-button.is-active,
        html.dark .student-view-tab.is-active {
            border: 1px solid rgba(251, 113, 133, 0.7);
            background: linear-gradient(180deg, rgba(136, 19, 55, 0.42) 0%, rgba(127, 29, 29, 0.28) 100%);
            color: #ffe4e6;
            box-shadow: 0 12px 28px -20px rgba(244, 63, 94, 0.42);
        }

        html.dark .student-empty-state {
            border-color: rgba(71, 85, 105, 0.9);
            background: rgba(15, 23, 42, 0.72);
            color: #94a3b8;
        }

        html.dark .student-parent-status {
            border-color: rgba(245, 158, 11, 0.38);
            background: rgba(120, 53, 15, 0.26);
            color: #fcd34d;
        }

        html.dark .student-parent-status.is-success {
            border-color: rgba(34, 197, 94, 0.34);
            background: rgba(20, 83, 45, 0.3);
            color: #bbf7d0;
        }

        html.dark .student-parent-status.is-error {
            border-color: rgba(248, 113, 113, 0.35);
            background: rgba(127, 29, 29, 0.3);
            color: #fecaca;
        }

        html.dark .student-parent-status.is-muted {
            border-color: rgba(71, 85, 105, 0.9);
            background: rgba(15, 23, 42, 0.72);
            color: #cbd5e1;
        }

        html.dark .student-table th {
            border-bottom-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.86);
            color: #94a3b8;
        }

        html.dark .student-table td {
            border-bottom-color: rgba(51, 65, 85, 0.75);
            color: #e2e8f0;
        }

        html.dark .student-table tbody tr:hover td {
            background: rgba(136, 19, 55, 0.12);
        }

        html.dark .student-exam-summary {
            background: rgba(15, 23, 42, 0.92);
        }

        html.dark .student-exam-summary:hover {
            background: rgba(136, 19, 55, 0.12);
        }

        html.dark .student-exam-title {
            color: #f8fafc;
        }

        html.dark .student-exam-meta {
            color: #94a3b8;
        }

        html.dark .student-exam-body {
            border-top-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.82);
        }

        html.dark .student-exam-footer td {
            background: rgba(15, 23, 42, 0.92) !important;
            color: #f8fafc;
        }

        html.dark .student-page-shell .text-slate-900,
        html.dark .student-view-shell .text-slate-900 {
            color: #f8fafc !important;
        }

        html.dark .student-page-shell .text-slate-700,
        html.dark .student-view-shell .text-slate-700 {
            color: #cbd5e1 !important;
        }

        html.dark .student-page-shell .text-slate-600,
        html.dark .student-page-shell .text-slate-500,
        html.dark .student-page-shell .text-slate-400,
        html.dark .student-view-shell .text-slate-600,
        html.dark .student-view-shell .text-slate-500,
        html.dark .student-view-shell .text-slate-400 {
            color: #94a3b8 !important;
        }

        html.dark .student-page-shell .bg-white,
        html.dark .student-view-shell .bg-white {
            background: rgba(15, 23, 42, 0.72) !important;
        }

        html.dark .student-page-shell .bg-rose-50,
        html.dark .student-view-shell .bg-rose-50 {
            background: rgba(136, 19, 55, 0.2) !important;
        }

        html.dark .student-page-shell .border-slate-200,
        html.dark .student-view-shell .border-slate-200 {
            border-color: rgba(51, 65, 85, 0.95) !important;
        }

        html.dark .student-page-shell .border-rose-200,
        html.dark .student-view-shell .border-rose-200 {
            border-color: rgba(251, 113, 133, 0.34) !important;
        }

        html.dark .student-page-shell .text-rose-700,
        html.dark .student-view-shell .text-rose-700 {
            color: #fecdd3 !important;
        }

        html.dark .student-page-shell .text-rose-300,
        html.dark .student-view-shell .text-rose-300 {
            color: rgba(251, 113, 133, 0.72) !important;
        }

        html.dark .parent-panel,
        html.dark .parent-card,
        html.dark .parent-section,
        html.dark .parent-view-hero,
        html.dark .parent-view-card,
        html.dark .parent-view-section,
        html.dark .parent-summary,
        html.dark .parent-child-card {
            border-color: rgba(51, 65, 85, 0.95);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.96) 0%, rgba(17, 24, 39, 0.96) 100%);
            box-shadow: 0 24px 48px -34px rgba(2, 6, 23, 0.78);
            color: #e2e8f0;
        }

        html.dark .parent-label,
        html.dark .parent-title {
            color: #94a3b8;
        }

        html.dark .parent-input {
            border-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.84);
            color: #e5e7eb;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.5);
            color-scheme: dark;
        }

        html.dark .parent-input::placeholder {
            color: #64748b;
        }

        html.dark .parent-input:focus {
            border-color: #fb7185;
            box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.18);
            background: rgba(15, 23, 42, 0.95);
        }

        html.dark .parent-input::file-selector-button {
            border: 1px solid rgba(71, 85, 105, 0.9);
            border-radius: 999px;
            background: rgba(30, 41, 59, 0.95);
            color: #e2e8f0;
            padding: 0.45rem 0.9rem;
            margin-right: 0.75rem;
            font-weight: 700;
            cursor: pointer;
        }

        html.dark .parent-tab-bar,
        html.dark .parent-view-tabs {
            border-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.92);
            box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.05);
        }

        html.dark .parent-tab-btn,
        html.dark .parent-view-tab {
            color: #cbd5e1;
        }

        html.dark .parent-tab-btn:hover,
        html.dark .parent-view-tab:hover {
            background: rgba(136, 19, 55, 0.18);
            color: #fecdd3;
        }

        html.dark .parent-tab-btn.is-active,
        html.dark .parent-view-tab.is-active {
            border: 1px solid rgba(251, 113, 133, 0.7);
            background: linear-gradient(180deg, rgba(136, 19, 55, 0.42) 0%, rgba(127, 29, 29, 0.28) 100%);
            color: #ffe4e6;
            box-shadow: 0 12px 28px -20px rgba(244, 63, 94, 0.42);
        }

        html.dark .parent-photo-frame {
            border-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.96);
            box-shadow: 0 22px 44px -30px rgba(2, 6, 23, 0.82);
        }

        html.dark .parent-btn-secondary {
            border-color: rgba(71, 85, 105, 0.9);
            background: rgba(15, 23, 42, 0.84);
            color: #e2e8f0;
        }

        html.dark .parent-children-menu {
            background: rgba(15, 23, 42, 0.97) !important;
            border-color: rgba(51, 65, 85, 0.95);
            box-shadow: 0 20px 38px -28px rgba(2, 6, 23, 0.74);
        }

        html.dark .parent-children-option:hover {
            background: rgba(136, 19, 55, 0.12);
        }

        html.dark .parent-chip.active {
            background: rgba(22, 101, 52, 0.25);
            color: #bbf7d0;
        }

        html.dark .parent-chip.inactive {
            background: rgba(153, 27, 27, 0.24);
            color: #fecaca;
        }

        html.dark .parent-chip.pending {
            background: rgba(120, 53, 15, 0.28);
            color: #fcd34d;
        }

        html.dark .parent-page-shell .text-slate-900,
        html.dark .parent-view-shell .text-slate-900 {
            color: #f8fafc !important;
        }

        html.dark .parent-page-shell .text-slate-800,
        html.dark .parent-page-shell .text-slate-700,
        html.dark .parent-view-shell .text-slate-800,
        html.dark .parent-view-shell .text-slate-700 {
            color: #cbd5e1 !important;
        }

        html.dark .parent-page-shell .text-slate-600,
        html.dark .parent-page-shell .text-slate-500,
        html.dark .parent-page-shell .text-slate-400,
        html.dark .parent-view-shell .text-slate-600,
        html.dark .parent-view-shell .text-slate-500,
        html.dark .parent-view-shell .text-slate-400 {
            color: #94a3b8 !important;
        }

        html.dark .parent-page-shell .bg-white,
        html.dark .parent-view-shell .bg-white {
            background: rgba(15, 23, 42, 0.72) !important;
        }

        html.dark .parent-page-shell .bg-slate-50,
        html.dark .parent-view-shell .bg-slate-50 {
            background: rgba(30, 41, 59, 0.68) !important;
        }

        html.dark .parent-page-shell .bg-rose-50,
        html.dark .parent-view-shell .bg-rose-50 {
            background: rgba(136, 19, 55, 0.2) !important;
        }

        html.dark .parent-page-shell .border-slate-200,
        html.dark .parent-view-shell .border-slate-200 {
            border-color: rgba(51, 65, 85, 0.95) !important;
        }

        html.dark .parent-page-shell .border-rose-200,
        html.dark .parent-view-shell .border-rose-200 {
            border-color: rgba(251, 113, 133, 0.34) !important;
        }

        html.dark .parent-page-shell .text-rose-700,
        html.dark .parent-view-shell .text-rose-700,
        html.dark .parent-page-shell .text-rose-600,
        html.dark .parent-view-shell .text-rose-600 {
            color: #fda4af !important;
        }

        html.dark .parent-page-shell .text-rose-300,
        html.dark .parent-view-shell .text-rose-300 {
            color: rgba(251, 113, 133, 0.72) !important;
        }

        html.dark .teacher-show-shell,
        html.dark .teacher-show-card,
        html.dark .teacher-stat,
        html.dark .teacher-doc-item,
        html.dark .teacher-detail-grid > div,
        html.dark .teacher-detail-block {
            border-color: rgba(51, 65, 85, 0.95);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.96) 0%, rgba(17, 24, 39, 0.96) 100%);
            box-shadow: 0 24px 48px -34px rgba(2, 6, 23, 0.78);
            color: #e2e8f0;
        }

        html.dark .teacher-show-nav a {
            border-color: rgba(71, 85, 105, 0.9);
            background: rgba(15, 23, 42, 0.84);
            color: #cbd5e1;
        }

        html.dark .teacher-show-nav a:hover {
            border-color: rgba(251, 113, 133, 0.6);
            background: rgba(136, 19, 55, 0.18);
            color: #fecdd3;
        }

        html.dark .teacher-detail-label {
            color: #94a3b8;
        }

        html.dark .teacher-photo-frame {
            border-color: rgba(251, 113, 133, 0.34);
            background: rgba(15, 23, 42, 0.96);
            box-shadow: 0 22px 44px -30px rgba(2, 6, 23, 0.82);
        }

        html.dark .teacher-chip-active {
            background: rgba(22, 101, 52, 0.25);
            color: #bbf7d0;
        }

        html.dark .teacher-chip-inactive {
            background: rgba(153, 27, 27, 0.24);
            color: #fecaca;
        }

        html.dark .teacher-chip-muted {
            background: rgba(30, 41, 59, 0.78);
            color: #cbd5e1;
        }

        html.dark .teacher-show-shell .text-slate-900,
        html.dark .teacher-show-card .text-slate-900 {
            color: #f8fafc !important;
        }

        html.dark .teacher-show-shell .text-slate-700,
        html.dark .teacher-show-card .text-slate-700 {
            color: #cbd5e1 !important;
        }

        html.dark .teacher-show-shell .text-slate-600,
        html.dark .teacher-show-shell .text-slate-500,
        html.dark .teacher-show-card .text-slate-600,
        html.dark .teacher-show-card .text-slate-500 {
            color: #94a3b8 !important;
        }

        html.dark .teacher-show-shell .bg-white,
        html.dark .teacher-show-card .bg-white {
            background: rgba(15, 23, 42, 0.72) !important;
        }

        html.dark .teacher-show-shell .border-slate-200,
        html.dark .teacher-show-card .border-slate-200 {
            border-color: rgba(51, 65, 85, 0.95) !important;
        }

        html.dark .teacher-show-shell .text-blue-700 {
            color: #bfdbfe !important;
        }

        html.dark .teacher-show-shell .text-emerald-700 {
            color: #a7f3d0 !important;
        }

        html.dark .teacher-show-shell .text-rose-300,
        html.dark .teacher-show-card .text-rose-300 {
            color: rgba(251, 113, 133, 0.72) !important;
        }
    </style>
    <script data-mobile-static-script>
        // Early initialization to prevent sidebar from showing on mobile on page load
        (function() {
            const mobileBreakpoint = 1024;
            const isMobileViewport = function() {
                return window.innerWidth < mobileBreakpoint;
            };
            
            // Immediately hide sidebar on mobile on page load
            if (isMobileViewport()) {
                document.addEventListener('DOMContentLoaded', function() {
                    const sidebar = document.getElementById('sidebar');
                    const backdrop = document.getElementById('sidebarBackdrop');
                    if (sidebar) {
                        sidebar.classList.add('hidden');
                        sidebar.classList.add('-translate-x-full');
                    }
                    if (backdrop) {
                        backdrop.classList.add('hidden');
                    }
                }, { once: true });
            }
        })();
    </script>
</head>
<body class="admin-panel font-sans antialiased bg-gray-50 overflow-x-hidden max-w-full" data-mobile-shell="admin" data-mobile-role="admin" data-mobile-route="{{ Route::currentRouteName() ?? '' }}">
    <div id="mobileAppShellRoot" data-mobile-shell-root>
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
            <p class="text-sm text-gray-700 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Professional Toast Notification Container - Above modals (z-[9999]) -->
	    <div id="toastNotification" class="hidden fixed top-4 right-4 z-[9999] rounded-xl shadow-2xl text-white text-sm transition-all duration-300 max-w-sm relative overflow-hidden animate-slide-in-right" style="position: fixed; top: 1rem; right: 1rem; left: auto; z-index: 9999;">
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

    <!-- Flash Messages Container -->
    @if(session('success'))
    <div id="flashSuccess" class="hidden" data-message="{{ session('success') }}"></div>
    @endif
    @if(session('error'))
    <div id="flashError" class="hidden" data-message="{{ session('error') }}"></div>
    @endif
    @if(session('warning'))
    <div id="flashWarning" class="hidden" data-message="{{ session('warning') }}"></div>
    @endif

    <!-- Main Container -->
    <div id="adminLayoutShell" class="flex min-h-[100dvh] lg:h-screen w-full max-w-full overflow-x-hidden" data-mobile-shell-layout>
        <!-- Sidebar Component -->
        @include('admin.components.sidebar')

        <!-- Sidebar Backdrop (for mobile) -->
        <div id="sidebarBackdrop" class="hidden lg:hidden fixed inset-0 z-[45] bg-black/40"></div>

        <!-- Main Content -->
        <div id="adminMainPanel" class="flex-1 min-w-0 flex flex-col" data-mobile-shell-panel>
            <!-- Header Component -->
            @include('admin.components.header')

            <!-- Page Content -->
            <main id="adminPageContent" class="flex-1 min-w-0 overflow-y-auto overflow-x-hidden min-h-0" data-mobile-main>
                <div class="min-h-full w-full px-3 py-3 sm:px-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div id="adminPrintPreviewModal" class="fixed inset-0 z-[70] hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="adminClosePrintPreview()"></div>
        <div class="relative mx-auto w-full max-w-6xl h-[92vh] flex flex-col overflow-hidden rounded-xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700">
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gradient-to-r from-rose-600 to-red-600">
                <div>
                    <h3 id="adminPrintPreviewTitle" class="text-base font-semibold text-white">{{ __('Print Preview') }}</h3>
                    <p id="adminPrintPreviewSubtitle" class="text-rose-100 text-xs">{{ __('A4 preview (use Print to open dialog)') }}</p>
                </div>
                <button onclick="adminClosePrintPreview()" class="text-rose-100 hover:text-white p-2 rounded-full hover:bg-white/10" aria-label="Close print preview">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="flex-1 bg-gray-100 dark:bg-slate-900 p-4 overflow-auto">
                <iframe id="adminPrintPreviewFrame" src="" class="w-full h-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white"></iframe>
            </div>

            <div class="px-5 py-3 border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-between items-center gap-3">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Tip: Use "New tab" for full-page preview.') }}</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="adminOpenPrintPreviewInNewTab()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        <i class="bi bi-box-arrow-up-right mr-1"></i> {{ __('New tab') }}
                    </button>
                    <button type="button" onclick="adminPrintPreviewFrame()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700 transition shadow-sm">
                        <i class="bi bi-printer mr-1"></i> {{ __('Print') }}
                    </button>
                    <button type="button" onclick="adminClosePrintPreview()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @yield('ajax-modal')
    @include('partials.mobile-bottom-nav', ['role' => 'admin'])
    </div>

    @yield('scripts')
    @stack('scripts')

    <script data-mobile-static-script>
        window.adminPrintPreviewState = {
            url: '',
            previousOverflow: '',
            ready: false,
            pendingPrint: false,
        };

        function adminOpenPrintPreview(url, options = {}) {
            const modal = document.getElementById('adminPrintPreviewModal');
            const frame = document.getElementById('adminPrintPreviewFrame');
            const title = document.getElementById('adminPrintPreviewTitle');
            const subtitle = document.getElementById('adminPrintPreviewSubtitle');

            if (!modal || !frame || !url) {
                return;
            }

            window.adminPrintPreviewState.url = url;
            window.adminPrintPreviewState.previousOverflow = document.body.style.overflow;
            window.adminPrintPreviewState.ready = false;
            window.adminPrintPreviewState.pendingPrint = false;

            if (title) {
                title.textContent = options.title || '{{ __('Print Preview') }}';
            }

            if (subtitle) {
                subtitle.textContent = options.subtitle || '{{ __('A4 preview (use Print to open dialog)') }}';
            }

            frame.onload = () => {
                window.adminPrintPreviewState.ready = true;

                if (window.adminPrintPreviewState.pendingPrint) {
                    window.adminPrintPreviewState.pendingPrint = false;
                    adminPrintPreviewFrame();
                }
            };

            frame.src = url;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function adminClosePrintPreview() {
            const modal = document.getElementById('adminPrintPreviewModal');
            const frame = document.getElementById('adminPrintPreviewFrame');

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');

            if (frame) {
                frame.src = '';
            }

            document.body.style.overflow = window.adminPrintPreviewState.previousOverflow || '';
            window.adminPrintPreviewState.url = '';
            window.adminPrintPreviewState.previousOverflow = '';
        }

        function adminOpenPrintPreviewInNewTab() {
            if (!window.adminPrintPreviewState.url) {
                return;
            }

            const url = window.adminPrintPreviewState.url + (window.adminPrintPreviewState.url.includes('?') ? '&' : '?') + 'newTab=1';
            window.open(url, '_blank');
        }

        function adminPrintPreviewFrame() {
            const frame = document.getElementById('adminPrintPreviewFrame');
            if (!frame || !frame.contentWindow) {
                return;
            }

            if (!window.adminPrintPreviewState.ready) {
                window.adminPrintPreviewState.pendingPrint = true;
                return;
            }

            try {
                frame.contentWindow.focus();
                setTimeout(() => {
                    try {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                    } catch (error) {
                        console.error('Admin print preview failed', error);
                    }
                }, 100);
            } catch (error) {
                console.error('Admin print preview failed', error);
            }
        }

        // Global Loader Functions
        function showLoading(message = 'Loading...') {
            const loader = document.getElementById('globalLoader');
            const loaderText = loader.querySelector('p');
            if (loaderText) loaderText.textContent = message;
            loader.classList.remove('hidden');
        }

        function hideLoading() {
            const loader = document.getElementById('globalLoader');
            loader.classList.add('hidden');
        }

        // Professional Toast notification system with progress bar and countdown
        let toastTimeout;
        let progressInterval;
        
	        function showToast(message, type = 'info', duration = 3500) {
	            const toast = document.getElementById('toastNotification');
	            const icon = document.getElementById('toastIcon');
	            const msg = document.getElementById('toastMessage');
	            const progressBar = document.getElementById('toastProgress');
	            const toastParent = toast.parentElement;
	            // Ensure toast never affects layout flow (some builds may miss utility CSS)
	            toast.style.position = 'fixed';
	            toast.style.top = '1rem';
	            toast.style.right = '1rem';
	            toast.style.left = 'auto';
	            toast.style.zIndex = '9999';
	            
	            msg.textContent = message;
	            toast.classList.remove('hidden');
	            toast.className = 'fixed top-4 right-4 z-[9999] rounded-xl shadow-2xl text-white text-sm transition-all duration-300 max-w-sm relative overflow-hidden animate-slide-in-right';
	            
	            const bgClass = type === 'success' ? 'bg-gradient-to-r from-green-600 to-emerald-600' :
	                           type === 'error' ? 'bg-gradient-to-r from-red-600 to-rose-600' :
	                           type === 'warning' ? 'bg-gradient-to-r from-amber-500 to-orange-600' :
	                           type === 'info' ? 'bg-gradient-to-r from-blue-600 to-cyan-600' :
                           'bg-gradient-to-r from-gray-600 to-slate-600';
            
            toast.style.background = '';
            toast.innerHTML = `
                <div class="${bgClass} backdrop-blur-md bg-opacity-95 p-4 flex items-center gap-3">
                    <div id="toastIcon" class="text-xl flex-shrink-0">${getToastIcon(type)}</div>
                    <div class="flex-1">
                        <span id="toastMessage" class="font-medium block">${message}</span>
                        <span id="toastSubMessage" class="text-xs opacity-90 block mt-0.5"></span>
                    </div>
                    <button onclick="closeNotification?.()" class="text-lg opacity-70 hover:opacity-100 transition-opacity flex-shrink-0">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div id="toastProgress" class="h-1 bg-white/40 absolute bottom-0 left-0 right-0"></div>
            `;
            
	            const newProgressBar = toast.querySelector('#toastProgress');
	            toast.classList.remove('hidden');
            
            // Animate progress bar
            newProgressBar.style.transition = 'none';
            newProgressBar.style.width = '100%';
            
            // Force reflow
            void toast.offsetWidth;
            
            newProgressBar.style.transition = `width ${duration}ms linear`;
            newProgressBar.style.width = '0%';
            
            // Clear existing timeout and interval
            if (toastTimeout) clearTimeout(toastTimeout);
            if (progressInterval) clearInterval(progressInterval);
            
            // Hide toast after duration
            toastTimeout = setTimeout(() => {
                toast.classList.add('hidden');
                if (newProgressBar) newProgressBar.style.width = '100%';
            }, duration);
        }

        function getToastIcon(type) {
            const icons = {
                success: '<i class="bi bi-check-circle-fill"></i>',
                error: '<i class="bi bi-exclamation-circle-fill"></i>',
                warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
                info: '<i class="bi bi-info-circle-fill"></i>'
            };
            return icons[type] || icons.info;
        }

        // Alias for compatibility
        function showNotification(title, message, type = 'info', duration = 3500) {
            const fullMessage = title ? `${title}` : message;
            showToast(fullMessage, type, duration);
        }

        function closeNotification() {
            const toast = document.getElementById('toastNotification');
            toast.classList.add('hidden');
            if (toastTimeout) clearTimeout(toastTimeout);
            if (progressInterval) clearInterval(progressInterval);
        }
    </script>

    <style>
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scale-up { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        @keyframes slide-in-right { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-fade-in { animation: fade-in 200ms ease-out; }
        .animate-scale-up { animation: scale-up 300ms cubic-bezier(0.34, 1.56, 0.64, 1); }
        .animate-slide-in-right { animation: slide-in-right 300ms ease-out; }
    </style>

    <style>
        @media (max-width: 1023px) {
            #adminLayoutShell {
                display: block !important;
            }

            #adminMainPanel,
            #adminPageContent,
            #adminTopHeader {
                min-width: 100% !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>

    <script data-mobile-static-script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle flash messages from Laravel
            const flashSuccess = document.getElementById('flashSuccess');
            const flashError = document.getElementById('flashError');
            const flashWarning = document.getElementById('flashWarning');

            if (flashSuccess) {
                showToast(flashSuccess.dataset.message, 'success');
            }
            if (flashError) {
                showToast(flashError.dataset.message, 'error');
            }
            if (flashWarning) {
                showToast(flashWarning.dataset.message, 'warning');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('adminPrintPreviewModal');
                if (modal && !modal.classList.contains('hidden')) {
                    adminClosePrintPreview();
                }
            }
        });
    </script>
</body>
</html>
