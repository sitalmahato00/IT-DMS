<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IT-DMS') }}</title>
    @include('partials.pwa-head', ['themeColor' => '#FF0037'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
