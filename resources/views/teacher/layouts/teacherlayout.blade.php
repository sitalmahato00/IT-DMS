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
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <!-- College Logo Background for All Pages -->
    <div class="fixed inset-0 pointer-events-none opacity-10 z-0 flex items-center justify-center">
        @if(isset($collegeLogoUrl))
            <img src="{{ $collegeLogoUrl }}" alt="{{ __('College Logo') }}" class="w-[600px] h-[600px] object-contain">
        @else
            <i class="bi bi-mortarboard text-[30rem] text-gray-300 dark:text-gray-700"></i>
        @endif
    </div>

    <!-- Global Loader -->
    <div id="globalLoader" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col items-center gap-4">
            <div class="animate-spin w-12 h-12 border-4 border-t-green-600 border-gray-200 rounded-full"></div>
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

    <script>
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
</body>
</html>

