@php
    $locale = app()->getLocale();
    $homeUrl = route('home');
    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology');
    $departmentShort = $department?->short_name ?: ($locale === 'ne' ? 'आईटी' : 'IT');
    $addressText = $department
        ? (($locale === 'ne' && !empty($department->address_nepali)) ? $department->address_nepali : $department->address)
        : null;

    $titleLines = $locale === 'ne'
        ? ['सूचना', 'प्रविधि']
        : ['Information', 'Technology'];

    $heroSummary = $locale === 'ne'
        ? 'विभागीय पोर्टलमा सुरक्षित पहुँच'
        : 'Secure access to your department portal';

    $heroDescription = $locale === 'ne'
        ? 'उपस्थिति, नतिजा, सूचना, अध्ययन सामग्री, र विभागीय अपडेटहरू एउटै प्रणालीमार्फत पहुँच गर्नुहोस्।'
        : 'Access attendance, results, notices, study materials, and department updates through one connected system.';

    $addressInfo = $addressText ?: ($locale === 'ne' ? 'काठमाडौँ, नेपाल' : 'Kathmandu, Nepal');
    $phoneInfo = $department?->phone ?: ($locale === 'ne' ? 'फोन नम्बर एडमिन सेटिङबाट राख्नुहोस्।' : 'Set the phone number from admin settings.');
    $emailInfo = $department?->email ?: ($locale === 'ne' ? 'इमेल एडमिन सेटिङबाट राख्नुहोस्।' : 'Set the email from admin settings.');

    $supportHref = !empty($department?->email)
        ? 'mailto:' . $department->email
        : (!empty($department?->phone) ? 'tel:' . preg_replace('/\s+/', '', $department->phone) : null);
    $supportLabel = !empty($department?->email) ? $department->email : ($department?->phone ?: null);
    $heroBrandTitle = $locale === 'ne' ? 'IT विभाग व्यवस्थापन प्रणाली' : 'IT Department Management System';

    $contactItems = [
        [
            'icon' => 'location',
            'text' => $addressInfo,
        ],
        [
            'icon' => 'phone',
            'text' => $phoneInfo,
        ],
        [
            'icon' => 'email',
            'text' => $emailInfo,
        ],
    ];
@endphp

@extends('layouts.public')

@push('head')
    @include('auth.partials.brand-theme')
@endpush

@section('content')
    <div class="auth-page auth-page-login">
        <div class="auth-shell">
            <section class="auth-hero">
                <div class="auth-hero-content">
                    <div class="auth-brand">
                        <div class="auth-brand-mark">
                            <img src="{{ $departmentLogoUrl }}" alt="{{ $departmentName }} logo" class="auth-brand-logo" />
                        </div>
                        <div class="auth-brand-copy">
                            <span class="auth-brand-kicker">{{ $departmentShort }}</span>
                            <div class="auth-brand-title">{{ $heroBrandTitle }}</div>
                        </div>
                    </div>

                    <h1 class="auth-hero-title">
                        {{ $titleLines[0] }}<br>{{ $titleLines[1] }}
                    </h1>
                    <p class="auth-hero-summary">
                        {{ $heroSummary }}
                    </p>
                    <p class="auth-hero-text">
                        {{ $heroDescription }}
                    </p>

                    <div class="auth-info-list" aria-label="{{ $locale === 'ne' ? 'सम्पर्क जानकारी' : 'Department contact information' }}">
                        @foreach ($contactItems as $item)
                            <div class="auth-info-item">
                                <span class="auth-info-icon" aria-hidden="true">
                                    @if ($item['icon'] === 'location')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-4.35 6-10a6 6 0 1 0-12 0c0 5.65 6 10 6 10Z" />
                                            <circle cx="12" cy="11" r="2.25" stroke-width="1.8" />
                                        </svg>
                                    @elseif ($item['icon'] === 'phone')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4.75h3.1l1.1 4.41-1.73 1.73a15.04 15.04 0 0 0 5.64 5.64l1.73-1.73 4.41 1.1V19A1.75 1.75 0 0 1 17.5 20.75h-.5C9.96 20.75 4.25 15.04 4.25 8v-.5A1.75 1.75 0 0 1 6 5.75" />
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 7.5 12 13.5l8.25-6" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v7.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 15.75v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                        </svg>
                                    @endif
                                </span>
                                <span>{{ $item['text'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="auth-side" x-data="{ showPassword: false }">
                <div class="auth-stack">
                    @if (session('status'))
                        <div class="auth-status">
                            {{ session('status') }}
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
                            <h3 class="auth-form-title">{{ $locale === 'ne' ? 'फेरि स्वागत छ' : 'Welcome Back' }}</h3>
                            <p class="auth-form-text">
                                {{ $locale === 'ne'
                                    ? 'विभागीय सेवाहरू निरन्तर प्रयोग गर्न आफ्नो खातामा साइन इन गर्नुहोस्।'
                                    : 'Sign in to your account to continue to the department services.' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="auth-field">
                                <label for="email" class="auth-label">{{ $locale === 'ne' ? 'इमेल ठेगाना' : 'Email Address' }}</label>
                                <div class="auth-input-wrap">
                                    <span class="auth-input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 7.5 12 13.5l8.25-6" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v7.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 15.75v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" />
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
                            </div>

                            <div class="auth-field">
                                <label for="password" class="auth-label">{{ $locale === 'ne' ? 'पासवर्ड' : 'Password' }}</label>
                                <div class="auth-input-wrap">
                                    <span class="auth-input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <rect x="5.25" y="10.25" width="13.5" height="9" rx="2" stroke-width="1.8" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 10.25V8a3.75 3.75 0 0 1 7.5 0v2.25" />
                                        </svg>
                                    </span>
                                    <input
                                        id="password"
                                        class="auth-input"
                                        type="password"
                                        x-bind:type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                    />
                                    <button type="button" class="auth-toggle" @click="showPassword = !showPassword" :aria-label="showPassword ? '{{ $locale === 'ne' ? 'पासवर्ड लुकाउनुहोस्' : 'Hide password' }}' : '{{ $locale === 'ne' ? 'पासवर्ड देखाउनुहोस्' : 'Show password' }}'">
                                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.036 12.322a1 1 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.73 5.08A10.45 10.45 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644 10.48 10.48 0 0 1-4.043 5.146M6.61 6.61A10.48 10.48 0 0 0 2.036 11.678a1 1 0 0 0 0 .644C3.423 16.49 7.36 19.5 12 19.5c1.855 0 3.598-.48 5.11-1.322M9.88 9.88A3 3 0 1 0 14.12 14.12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="auth-row">
                                <label for="remember" class="auth-remember">
                                    <input
                                        id="remember"
                                        type="checkbox"
                                        name="remember"
                                        @checked(old('remember'))
                                    />
                                    <span>{{ $locale === 'ne' ? 'मलाई सम्झनुहोस्' : 'Remember Me' }}</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="auth-link" href="{{ route('password.request') }}">{{ $locale === 'ne' ? 'पासवर्ड बिर्सनुभयो?' : 'Forgot password?' }}</a>
                                @endif
                            </div>

                            <button type="submit" class="auth-submit full">
                                {{ $locale === 'ne' ? 'साइन इन' : 'Sign In' }}
                            </button>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="auth-secondary-action">
                                    {{ $locale === 'ne' ? 'नयाँ खाता बनाउनुहोस्' : 'Create Account' }}
                                </a>
                            @endif

                            <p class="auth-support-note">
                                {{ $locale === 'ne' ? 'सहायता चाहिन्छ?' : 'Need help?' }}
                                @if ($supportHref && $supportLabel)
                                    <a href="{{ $supportHref }}" class="auth-link">
                                        {{ $locale === 'ne' ? 'IT सहायता सम्पर्क गर्नुहोस्' : 'Contact IT Support' }}
                                    </a>
                                @else
                                    <span class="auth-support-highlight">
                                        {{ $locale === 'ne' ? 'IT सहायता सम्पर्क गर्नुहोस्' : 'Contact IT Support' }}
                                    </span>
                                @endif
                            </p>

                            <a href="{{ $homeUrl }}" class="auth-back-link">&#8592; {{ $locale === 'ne' ? 'मुखपृष्ठमा फर्कनुहोस्' : 'Back to Home' }}</a>

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
