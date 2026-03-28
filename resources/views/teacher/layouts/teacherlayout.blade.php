<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Department Management System (IT-DMS)') - Teacher</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <main class="flex-1 overflow-auto">
                <div class="p-6 lg:p-8">
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
