<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'IT Department Management System (IT-DMS)') - Student</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
    @stack('styles')
</head>
<body class="student-panel font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="fixed inset-0 pointer-events-none opacity-10 z-0 flex items-center justify-center">
        @if(isset($departmentLogoUrl))
            <img src="{{ $departmentLogoUrl }}" alt="{{ __('Department Logo') }}" class="w-[600px] h-[600px] object-contain">
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

    <div class="flex h-screen overflow-hidden dark:bg-gray-900">
        @include('student.components.studentsidebar')

        <div id="sidebarBackdrop" class="hidden lg:hidden fixed inset-0 z-20 bg-black/50"></div>

        <div class="flex-1 flex flex-col overflow-hidden">
            @include('student.components.studentheader')

            <main class="flex-1 overflow-y-auto min-h-0">
                <div class="px-6 py-3 min-h-full">
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
    @yield('scripts')
    @stack('scripts')

    <script>
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
            const mobileToggle = document.getElementById('sidebarToggle');
            const desktopToggle = document.getElementById('desktopSidebarToggle');
            const collapsedStorageKey = 'student-sidebar-collapsed';

            const openMobileSidebar = () => {
                if (!sidebar) {
                    return;
                }

                sidebar.classList.remove('hidden');
                sidebarBackdrop?.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            const closeMobileSidebar = () => {
                if (!sidebar || window.innerWidth >= 1024) {
                    sidebarBackdrop?.classList.add('hidden');
                    document.body.style.overflow = '';
                    return;
                }

                sidebar.classList.add('hidden');
                sidebarBackdrop?.classList.add('hidden');
                document.body.style.overflow = '';
            };

            mobileToggle?.addEventListener('click', function () {
                if (!sidebar) {
                    return;
                }

                if (sidebar.classList.contains('hidden')) {
                    openMobileSidebar();
                } else {
                    closeMobileSidebar();
                }
            });

            sidebarBackdrop?.addEventListener('click', closeMobileSidebar);

            desktopToggle?.addEventListener('click', function () {
                if (!sidebar) {
                    return;
                }

                sidebar.classList.toggle('sidebar-collapsed');

                const icon = desktopToggle.querySelector('i');
                if (sidebar.classList.contains('sidebar-collapsed')) {
                    icon?.classList.remove('bi-layout-sidebar');
                    icon?.classList.add('bi-layout-sidebar-reverse');
                    localStorage.setItem(collapsedStorageKey, '1');
                } else {
                    icon?.classList.remove('bi-layout-sidebar-reverse');
                    icon?.classList.add('bi-layout-sidebar');
                    localStorage.removeItem(collapsedStorageKey);
                }
            });

            if (sidebar && localStorage.getItem(collapsedStorageKey) === '1') {
                sidebar.classList.add('sidebar-collapsed');
                const icon = desktopToggle?.querySelector('i');
                icon?.classList.remove('bi-layout-sidebar');
                icon?.classList.add('bi-layout-sidebar-reverse');
            }

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
