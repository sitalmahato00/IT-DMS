<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Department Management System (IT-DMS)') - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
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
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Component -->
        @include('admin.components.sidebar')

        <!-- Sidebar Backdrop (for mobile) -->
        <div id="sidebarBackdrop" class="hidden lg:hidden fixed inset-0 z-20 bg-black bg-opacity-40"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header Component -->
            @include('admin.components.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto min-h-0">
                <div class="px-6 py-3 min-h-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @yield('ajax-modal')

    @yield('scripts')
    @stack('scripts')

    <script>
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

    <script>
        // Mobile Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

                    if (sidebarToggle && sidebar) {
                        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

                        function openSidebar() {
                            sidebar.classList.remove('hidden');
                            if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
                            document.body.classList.add('overflow-hidden');
                        }

                        function closeSidebar() {
                            sidebar.classList.add('hidden');
                            if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                        }

                        sidebarToggle.addEventListener('click', function() {
                            if (sidebar.classList.contains('hidden')) openSidebar();
                            else closeSidebar();
                        });

                        // Close sidebar when clicking on a navigation link (mobile)
                        const navLinks = sidebar.querySelectorAll('a');
                        navLinks.forEach(link => {
                            link.addEventListener('click', function() {
                                if (window.innerWidth < 1024) {
                                    closeSidebar();
                                }
                            });
                        });

                        // Close when clicking on backdrop
                        if (sidebarBackdrop) {
                            sidebarBackdrop.addEventListener('click', function() {
                                closeSidebar();
                            });
                        }

                        // Handle window resize
                        window.addEventListener('resize', function() {
                            if (window.innerWidth >= 1024) {
                                // ensure sidebar visible on desktop and backdrop hidden
                                sidebar.classList.remove('hidden');
                                if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
                                document.body.classList.remove('overflow-hidden');
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
