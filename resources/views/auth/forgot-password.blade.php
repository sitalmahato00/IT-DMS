@php
    $locale = app()->getLocale();
    $homeUrl = route('home');
    $department = \App\Models\Department::first();
    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology');
    $departmentShort = $department?->short_name ?: ($locale === 'ne' ? 'आईटी' : 'IT');
    $departmentLogoUrl = $department?->getLogoUrl() ?? '/images/default-logo.svg';
    $addressText = $department
        ? (($locale === 'ne' && !empty($department->address_nepali)) ? $department->address_nepali : $department->address)
        : null;
    $addressInfo = $addressText ?: ($locale === 'ne' ? 'काठमाडौँ, नेपाल' : 'Kathmandu, Nepal');
    $phoneInfo = $department?->phone ?: ($locale === 'ne' ? 'फोन नम्बर एडमिन सेटिङबाट राख्नुहोस्।' : 'Set the phone number from admin settings.');
    $emailInfo = $department?->email ?: ($locale === 'ne' ? 'इमेल एडमिन सेटिङबाट राख्नुहोस्।' : 'Set the email from admin settings.');
    
    $titleLines = $locale === 'ne' ? ['पासवर्ड', 'बिर्सनुभयो?'] : ['Forgot', 'Password?'];
    
    $heroSummary = $locale === 'ne' ? 'खाता पुन:प्राप्त गर्नुहोस्' : 'Recover your account';
    $heroDescription = $locale === 'ne' 
        ? 'पासवर्ड बिर्सनुभयो? चिन्ता नगर्नुहोस्। तपाईंको इमेलमा रिसेट लिङ्क पठाउनुहोस्।'
        : 'Forgot your password? No problem. Send a reset link to your email address.';
    
    $contactItems = [
        ['icon' => 'location', 'text' => $addressInfo],
        ['icon' => 'phone', 'text' => $phoneInfo],
        ['icon' => 'email', 'text' => $emailInfo],
    ];
@endphp

@extends('layouts.public')

@push('head')
    @include('auth.partials.brand-theme')
    <title>{{ $locale === 'ne' ? 'पासवर्ड बिर्सनुभयो?' : 'Forgot Password' }} - {{ $departmentName }}</title>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-shell">
        {{-- LEFT HERO PANEL --}}
        <section class="auth-hero">
            <div class="auth-hero-content">
                <div class="auth-brand">
                    <div class="auth-brand-mark">
                        <img src="{{ $departmentLogoUrl }}" alt="{{ $departmentName }} logo" class="auth-brand-logo" />
                    </div>
                    <div class="auth-brand-copy">
                        <span class="auth-brand-kicker">{{ $departmentShort }}</span>
                        <div class="auth-brand-title">{{ $departmentName }} {{ $locale === 'ne' ? 'व्यवस्थापन प्रणाली' : 'Management System' }}</div>
                    </div>
                </div>

                <h1 class="auth-hero-title">
                    {{ $titleLines[0] }}<br>{{ $titleLines[1] }}
                </h1>
                <p class="auth-hero-summary">{{ $heroSummary }}</p>
                <p class="auth-hero-text">{{ $heroDescription }}</p>

                <div class="auth-info-list">
                    @foreach($contactItems as $item)
                        <div class="auth-info-item">
                            <span class="auth-info-icon">
                                @if($item['icon'] === 'location')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-4.35 6-10a6 6 0 1 0-12 0c0 5.65 6 10 6 10Z"/>
                                        <circle cx="12" cy="11" r="2.25" stroke-width="1.8"/>
                                    </svg>
                                @elseif($item['icon'] === 'phone')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4.75h3.1l1.1 4.41-1.73 1.73a15.04 15.04 0 0 0 5.64 5.64l1.73-1.73 4.41 1.1V19A1.75 1.75 0 0 1 17.5 20.75h-.5C9.96 20.75 4.25 15.04 4.25 8v-.5A1.75 1.75 0 0 1 6 5.75"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 7.5 12 13.5l8.25-6"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v7.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 15.75v-7.5a1.5 1.5 0 0 1 1.5-1.5Z"/>
                                    </svg>
                                @endif
                            </span>
                            <span>{{ $item['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- RIGHT FORM PANEL --}}
        <section class="auth-side">
            <div class="auth-stack">
                @if (session('status'))
                    <div class="auth-status">
                        {{ session('status') }}
                        <p class="text-xs mt-1 text-green-700" id="countdown-note">Next attempt in: <span id="countdown" class="font-mono font-semibold">00:05</span></p>
                        <script>
                            let timeLeft = 5;
                            function updateCountdown() {
                                const display = String(timeLeft).padStart(2, '0');
                                document.getElementById('countdown').textContent = '00:' + display;
                                if (timeLeft > 0) {
                                    timeLeft--;
                                    setTimeout(updateCountdown, 1000);
                                }
                            }
                            updateCountdown();
                        </script>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="auth-alert" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="auth-card">
                    <div class="auth-panel-intro">
                        <div class="auth-panel-logo-wrap" aria-hidden="true">
                            <img src="{{ $departmentLogoUrl }}" alt="" class="auth-panel-logo" />
                        </div>
                        <div class="auth-panel-copy">
                            <h2 class="auth-panel-title">{{ $departmentName }}</h2>
                            <p class="auth-panel-subtitle">{{ $locale === 'ne' ? 'IT विभाग व्यवस्थापन प्रणाली' : 'IT Department Management System' }}</p>
                            <p class="auth-panel-meta">{{ $addressInfo }}</p>
                        </div>
                    </div>

                    <div class="auth-divider"></div>

                    <div class="auth-form-intro">
                        <h3 class="auth-form-title">{{ $locale === 'ne' ? 'पासवर्ड रिसेट लिङ्क पठाउनुहोस्' : 'Send Password Reset Link' }}</h3>
                        <p class="auth-form-text">
                            {{ $locale === 'ne'
                                ? 'तपाईंको इमेल ठेगाना लेख्नुहोस्। हामी तपाईंको खाता रिसेट गर्न लिङ्क पठाउँछौँ।'
                                : 'Enter your email address and we will send you a link to reset your password.' }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="auth-field">
                            <label for="email" class="auth-label">{{ $locale === 'ne' ? 'इमेल ठेगाना' : 'Email Address' }}</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 7.5 12 13.5l8.25-6"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v7.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 15.75v-7.5a1.5 1.5 0 0 1 1.5-1.5Z"/>
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    class="auth-input"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="{{ $locale === 'ne' ? 'example@campus.edu.np' : 'example@campus.edu.np' }}"
                                />
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="auth-submit full">
                            {{ $locale === 'ne' ? 'रिसेट लिङ्क पठाउनुहोस्' : 'Send Password Reset Link' }}
                        </button>

                        <a href="{{ route('login') }}" class="auth-secondary-action">
                            {{ $locale === 'ne' ? 'साइन इनमा फर्कनुहोस्' : 'Back to Sign In' }}
                        </a>

                        <a href="{{ $homeUrl }}" class="auth-back-link">
                            &#8592; {{ $locale === 'ne' ? 'मुख्य पृष्ठमा फर्कनुहोस्' : 'Back to Home' }}
                        </a>

                        <p class="auth-footer-note">
                            &copy; {{ date('Y') }} {{ $departmentName }}. {{ $locale === 'ne' ? 'सर्वाधिकार सुरक्षित।' : 'All rights reserved.' }}
                        </p>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
