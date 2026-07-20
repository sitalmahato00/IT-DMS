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
                        {{ $locale === 'ne' ? 'नयाँ' : 'New' }}<br>{{ $locale === 'ne' ? 'पासवर्ड' : 'Password' }}
                    </h1>
                    <p class="auth-hero-summary">
                        {{ $locale === 'ne' ? 'आफ्नो खाता सुरक्षित गर्नुहोस्' : 'Secure your account' }}
                    </p>
                    <p class="auth-hero-text">
                        {{ $locale === 'ne'
                            ? 'तपाईंको Manmohan Memorial Polytechnic खाता र शैक्षिक अभिलेखहरू सुरक्षित राख्न बलियो पासवर्ड बनाउनुहोस्।'
                            : 'Create a strong password to protect your Manmohan Memorial Polytechnic account and keep your academic records safe.' }}
                    </p>

                    <div class="auth-info-list">
                        <div class="auth-info-item">
                            <span class="auth-info-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </span>
                            <span>{{ $locale === 'ne' ? 'कम्तीमा ८ अक्षर' : 'Minimum 8 characters' }}</span>
                        </div>
                        <div class="auth-info-item">
                            <span class="auth-info-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="5.25" y="10.25" width="13.5" height="9" rx="2" stroke-width="1.8"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 10.25V8a3.75 3.75 0 0 1 7.5 0v2.25"/>
                                </svg>
                            </span>
                            <span>{{ $locale === 'ne' ? 'अंक र विशेष चिह्न समावेश गर्नुहोस्' : 'Include numbers & special characters' }}</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- RIGHT: Reset password form --}}
            <section class="auth-side" x-data="{ showPassword: false, showConfirm: false }">
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
                                <p class="auth-panel-subtitle">{{ $locale === 'ne' ? 'IT विभाग व्यवस्थापन प्रणाली' : 'Manmohan Memorial Polytechnic' }}</p>
                                <p class="auth-panel-meta">{{ $addressInfo }}</p>
                            </div>
                        </div>

                        <div class="auth-divider"></div>

                        <div class="auth-form-intro">
                            <h3 class="auth-form-title">{{ $locale === 'ne' ? 'नयाँ पासवर्ड' : 'Create New Password' }}</h3>
                            <p class="auth-form-text">
                                {{ $locale === 'ne'
                                    ? 'आफ्नो खाताको लागि नयाँ बलियो पासवर्ड बनाउनुहोस्।'
                                    : 'Enter a strong new password to reset your account access.' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}" />

                            {{-- Email (readonly) --}}
                            <div class="auth-field">
                                <label for="email" class="auth-label">{{ $locale === 'ne' ? 'इमेल ठेगाना' : 'Email Address' }}</label>
                                <div class="auth-input-wrap">
                                    <span class="auth-input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 7.5 12 13.5l8.25-6" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v7.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 15.75v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                        </svg>
                                    </span>
                                    <input id="email" class="auth-input" type="email" name="email"
                                        value="{{ old('email', $request->email) }}" readonly
                                        style="background:#f9fafb; color:#6b7280;" />
                                </div>
                            </div>

                            {{-- New Password --}}
                            <div class="auth-field">
                                <label for="password" class="auth-label">{{ $locale === 'ne' ? 'नयाँ पासवर्ड' : 'New Password' }}</label>
                                <div class="auth-input-wrap">
                                    <span class="auth-input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <rect x="5.25" y="10.25" width="13.5" height="9" rx="2" stroke-width="1.8"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 10.25V8a3.75 3.75 0 0 1 7.5 0v2.25"/>
                                        </svg>
                                    </span>
                                    <input id="password" class="auth-input" name="password" required
                                        x-bind:type="showPassword ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        placeholder="{{ $locale === 'ne' ? 'नयाँ पासवर्ड लेख्नुहोस्' : 'Enter new password' }}" />
                                    <button type="button" class="auth-toggle" @click="showPassword = !showPassword">
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
                                            <rect x="5.25" y="10.25" width="13.5" height="9" rx="2" stroke-width="1.8"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 10.25V8a3.75 3.75 0 0 1 7.5 0v2.25"/>
                                        </svg>
                                    </span>
                                    <input id="password_confirmation" class="auth-input" name="password_confirmation" required
                                        x-bind:type="showConfirm ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        placeholder="{{ $locale === 'ne' ? 'पासवर्ड दोहोर्याउनुहोस्' : 'Confirm new password' }}" />
                                    <button type="button" class="auth-toggle" @click="showConfirm = !showConfirm">
                                        <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.036 12.322a1 1 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.73 5.08A10.45 10.45 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 0 1 0 .644 10.48 10.48 0 0 1-4.043 5.146M6.61 6.61A10.48 10.48 0 0 0 2.036 11.678a1 1 0 0 0 0 .644C3.423 16.49 7.36 19.5 12 19.5c1.855 0 3.598-.48 5.11-1.322M9.88 9.88A3 3 0 1 0 14.12 14.12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="auth-submit full" style="margin-top:1.25rem;">
                                {{ $locale === 'ne' ? 'पासवर्ड रिसेट गर्नुहोस्' : 'Reset Password' }}
                            </button>

                            <a href="{{ route('login') }}" class="auth-secondary-action">
                                {{ $locale === 'ne' ? 'लगइनमा फर्कनुहोस्' : 'Back to Sign In' }}
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

