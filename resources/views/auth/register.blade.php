@php
    use Illuminate\Support\Facades\Storage;
    $locale = app()->getLocale();
    $homeUrl = route('home');

    $department = \App\Models\Department::first();
    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology');
    $departmentShort = $department?->short_name ?: ($locale === 'ne' ? 'आईटी' : 'IT');
    $departmentLogoUrl = $department?->getLogoUrl() ?? asset('images/default-logo.svg');
    $addressText = $department
        ? (($locale === 'ne' && !empty($department->address_nepali)) ? $department->address_nepali : $department->address)
        : null;
    $addressInfo = $addressText ?: ($locale === 'ne' ? 'काठमाडौँ, नेपाल' : 'Kathmandu, Nepal');

    $titleLines = $locale === 'ne'
        ? ['खाता', 'बनाउनुहोस्']
        : ['Create', 'Account'];
@endphp

@extends('layouts.public')

@push('head')
    @include('auth.partials.brand-theme')
@endpush

@section('content')
    <div class="auth-page">
        <div class="auth-shell">
            {{-- LEFT: Hero panel --}}
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
                    <p class="auth-hero-summary">
                        {{ $locale === 'ne' ? 'IT विभाग पोर्टलमा सामेल हुनुहोस्' : 'Join the IT Department Portal' }}
                    </p>
                    <p class="auth-hero-text">
                        {{ $locale === 'ne'
                            ? 'सरल दर्ता प्रक्रियामार्फत विभागीय सेवाहरू, सूचना र शैक्षिक स्रोतहरूसँग जोडिन सुरु गर्नुहोस्।'
                            : 'Register to access department services, notices, attendance records, and academic resources.' }}
                    </p>

                    <div class="auth-info-list">
                        <div class="auth-info-item">
                            <span class="auth-info-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-4.35 6-10a6 6 0 1 0-12 0c0 5.65 6 10 6 10Z" />
                                    <circle cx="12" cy="11" r="2.25" stroke-width="1.8" />
                                </svg>
                            </span>
                            <span>{{ $addressInfo }}</span>
                        </div>
                        @if (!empty($department?->email))
                            <div class="auth-info-item">
                                <span class="auth-info-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 7.5 12 13.5l8.25-6" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v7.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 15.75v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                    </svg>
                                </span>
                                <span>{{ $department->email }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- RIGHT: Registration form --}}
            <section class="auth-side" x-data="{ showPassword: false, showPasswordConfirmation: false }">
                <div class="auth-stack">
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
                            <h3 class="auth-form-title">{{ $locale === 'ne' ? 'नयाँ खाता' : 'Create Account' }}</h3>
                            <p class="auth-form-text">
                                {{ $locale === 'ne'
                                    ? 'विभागीय पोर्टलको सेवाहरूमा पहुँचका लागि खाता बनाउनुहोस्।'
                                    : 'Fill in your details to create an account and access the department portal.' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Full Name --}}
                            <div class="auth-field">
                                <label for="name" class="auth-label">{{ $locale === 'ne' ? 'पूरा नाम' : 'Full Name' }}</label>
                                <div class="auth-input-wrap">
                                    <span class="auth-input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 0 1 15 0" />
                                        </svg>
                                    </span>
                                    <input
                                        id="name"
                                        class="auth-input"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        autofocus
                                        autocomplete="name"
                                        placeholder="{{ $locale === 'ne' ? 'आफ्नो पूरा नाम लेख्नुहोस्' : 'Enter your full name' }}"
                                    />
                                </div>
                            </div>

                            {{-- Email --}}
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
                                        autocomplete="username"
                                        placeholder="{{ $locale === 'ne' ? 'example@campus.edu.np' : 'example@campus.edu.np' }}"
                                    />
                                </div>
                            </div>

                            {{-- Password --}}
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
                                        autocomplete="new-password"
                                        placeholder="{{ $locale === 'ne' ? 'कम्तीमा ८ अक्षर' : 'Minimum 8 characters' }}"
                                    />
                                    <button type="button" class="auth-toggle" @click="showPassword = !showPassword"
                                        :aria-label="showPassword ? '{{ $locale === 'ne' ? 'लुकाउनुहोस्' : 'Hide password' }}' : '{{ $locale === 'ne' ? 'देखाउनुहोस्' : 'Show password' }}'">
                                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.036 12.322a1 1 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.73 5.08A10.45 10.45 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644 10.48 10.48 0 0 1-4.043 5.146M6.61 6.61A10.48 10.48 0 0 0 2.036 11.678a1 1 0 0 0 0 .644C3.423 16.49 7.36 19.5 12 19.5c1.855 0 3.598-.48 5.11-1.322M9.88 9.88A3 3 0 1 0 14.12 14.12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="auth-field">
                                <label for="password_confirmation" class="auth-label">{{ $locale === 'ne' ? 'पासवर्ड पुष्टि' : 'Confirm Password' }}</label>
                                <div class="auth-input-wrap">
                                    <span class="auth-input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <rect x="5.25" y="10.25" width="13.5" height="9" rx="2" stroke-width="1.8" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 10.25V8a3.75 3.75 0 0 1 7.5 0v2.25" />
                                        </svg>
                                    </span>
                                    <input
                                        id="password_confirmation"
                                        class="auth-input"
                                        type="password"
                                        x-bind:type="showPasswordConfirmation ? 'text' : 'password'"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        placeholder="{{ $locale === 'ne' ? 'पासवर्ड दोहोर्याउनुहोस्' : 'Re-enter password' }}"
                                    />
                                    <button type="button" class="auth-toggle" @click="showPasswordConfirmation = !showPasswordConfirmation"
                                        :aria-label="showPasswordConfirmation ? '{{ $locale === 'ne' ? 'लुकाउनुहोस्' : 'Hide' }}' : '{{ $locale === 'ne' ? 'देखाउनुहोस्' : 'Show' }}'">
                                        <svg x-show="!showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.036 12.322a1 1 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.73 5.08A10.45 10.45 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644 10.48 10.48 0 0 1-4.043 5.146M6.61 6.61A10.48 10.48 0 0 0 2.036 11.678a1 1 0 0 0 0 .644C3.423 16.49 7.36 19.5 12 19.5c1.855 0 3.598-.48 5.11-1.322M9.88 9.88A3 3 0 1 0 14.12 14.12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="auth-submit full" style="margin-top: 1.25rem;">
                                {{ $locale === 'ne' ? 'खाता बनाउनुहोस्' : 'Create Account' }}
                            </button>

                            <a href="{{ route('login') }}" class="auth-secondary-action">
                                {{ $locale === 'ne' ? 'पहिले नै खाता छ? लगइन गर्नुहोस्' : 'Already have an account? Sign In' }}
                            </a>

                            <a href="{{ $homeUrl }}" class="auth-back-link">
                                &#8592; {{ $locale === 'ne' ? 'मुखपृष्ठमा फर्कनुहोस्' : 'Back to Home' }}
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
