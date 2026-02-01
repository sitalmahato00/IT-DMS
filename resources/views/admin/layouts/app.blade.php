<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'DMS')) - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Toast Notification Container -->
    <div id="toastNotification" class="hidden fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg text-white text-sm transition-all duration-300 max-w-sm">
        <div class="flex items-center gap-2">
            <i id="toastIcon" class="bi text-lg"></i>
            <span id="toastMessage"></span>
        </div>
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

    <!-- Mobile Navbar (Always at top) -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm p-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="bg-red-600 text-white p-1.5 rounded">
                <i class="bi bi-calendar-check text-sm"></i>
            </div>
            <div>
                <h1 class="font-bold text-sm text-gray-900">EduManage</h1>
            </div>
        </div>
        <button id="sidebarToggle" class="p-2 hover:bg-gray-100 rounded transition">
            <i class="bi bi-list text-lg text-gray-700"></i>
        </button>
    </div>

    <!-- Main Container -->
    <div class="flex h-screen overflow-hidden pt-12 lg:pt-0">
        <!-- Sidebar Component -->
        @include('admin.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header Component -->
            @include('admin.components.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="px-6 py-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @yield('ajax-modal')

    @yield('scripts')

    <script>
        // Toast notification system
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toastNotification');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMessage');
            
            msg.textContent = message;
            toast.classList.remove('hidden', 'bg-blue-500', 'bg-green-500', 'bg-red-500', 'bg-yellow-500');
            
            switch(type) {
                case 'success':
                    toast.classList.add('bg-green-500');
                    icon.className = 'bi bi-check-circle';
                    break;
                case 'error':
                    toast.classList.add('bg-red-500');
                    icon.className = 'bi bi-exclamation-circle';
                    break;
                case 'warning':
                    toast.classList.add('bg-yellow-500');
                    icon.className = 'bi bi-exclamation-triangle';
                    break;
                default:
                    toast.classList.add('bg-blue-500');
                    icon.className = 'bi bi-info-circle';
            }
            
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }

        // Mobile Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                });

                // Close sidebar when clicking on a navigation link (mobile)
                const navLinks = sidebar.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 1024) {
                            sidebar.classList.add('hidden');
                        }
                    });
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1024) {
                        sidebar.classList.remove('hidden');
                    } else if (sidebar.classList.contains('lg:flex')) {
                        sidebar.classList.add('hidden');
                    }
                });
            }

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
    </script>
</body>
</html>
