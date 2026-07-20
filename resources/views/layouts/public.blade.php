<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Manmohan Memorial Polytechnic') }}</title>
    @include('partials.pwa-head', ['themeColor' => '#FF0037'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Prevent dark mode on auth pages -->
    <script>
        @php
            $isAuthRoute = in_array(Route::currentRouteName(), [
                'login',
                'register',
                'password.request',
                'password.reset',
                'password.confirm',
                'verification.send',
                'verification.verify',
                'two-factor.challenge',
                'two-factor.verify',
            ]);
        @endphp
        @if($isAuthRoute)
            // Force light mode on auth pages
            localStorage.setItem('theme', 'light');
            document.documentElement.classList.remove('dark');
        @endif
    </script>

    <!-- CSS for auth pages - force light theme -->
    @if($isAuthRoute)
    <style>
        html, html.dark {
            color-scheme: light;
        }
        html.dark, html.dark * {
            background-color: var(--color-bg-light, #ffffff) !important;
            color: var(--color-text-light, #111827) !important;
            border-color: var(--color-border-light, #e5e7eb) !important;
        }
    </style>
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="font-sans antialiased {{ app()->getLocale() === 'ne' ? 'locale-ne' : '' }}" data-mobile-shell="public" data-mobile-role="public" data-mobile-route="{{ Route::currentRouteName() ?? '' }}">
    <div id="mobileAppShellRoot" data-mobile-shell-root>
    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <!-- Page Content -->
        <main data-mobile-main>
            @yield('content')
        </main>
    </div>
    @include('partials.mobile-bottom-nav', ['role' => 'public'])
    </div>

    @stack('scripts')
</body>
</html>

