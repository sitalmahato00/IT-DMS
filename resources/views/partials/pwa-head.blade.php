@php
    $pwaThemeColor = $themeColor ?? '#FF0037';
    $pwaAppName = config('app.name', 'Manmohan Memorial Polytechnic');
    $pwaDescription = $description ?? __('Manmohan Memorial Polytechnic');
@endphp

<meta name="theme-color" content="{{ $pwaThemeColor }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $pwaAppName }}">
<meta name="application-name" content="{{ $pwaAppName }}">
<meta name="description" content="{{ $pwaDescription }}">
<meta name="format-detection" content="telephone=no">
<meta name="view-transition" content="same-origin">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" href="{{ asset('icons/app-icon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('icons/app-icon.svg') }}">
<link rel="mask-icon" href="{{ asset('icons/maskable-icon.svg') }}" color="{{ $pwaThemeColor }}">

