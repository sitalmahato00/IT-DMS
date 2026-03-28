@php
    $locale = app()->getLocale();
    $homeUrl = route('home');
    $titleLines = $locale === 'ne'
        ? ['आफ्नो', 'IT-DMS लगइन फेला पार्नुहोस्']
        : ['How to Access Your', 'IT-DMS Login'];
@endphp

@extends('layouts.public')

@push('head')
    @include('auth.partials.brand-theme')
@endpush

@section('content')
    <div class="auth-page">
        <div class="auth-shell">
            <section class="auth-hero">
                <div class="auth-hero-content">
                    <h1 class="auth-hero-title">
                        {{ $titleLines[0] }}<br>{{ $titleLines[1] }}
                    </h1>
                    <p class="auth-hero-text">
                        {{ $locale === 'ne'
                            ? 'विभागीय पोर्टलमा प्रवेश गरेर आफ्नो शैक्षिक सेवा, सूचना र अद्यावधिक सामग्रीमा पहुँच गर्नुहोस्।'
                            : 'Sign in to reach your department portal, academic services, notices, and daily updates.' }}
                    </p>
                </div>
            </section>

            <section class="auth-side" x-data="{ showPassword: false }">
                <div class="auth-stack">
                    <div class="auth-emblem" aria-hidden="true">
                        <span class="auth-emblem-mark">IT</span>
                    </div>

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
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="auth-field">
                                <label for="email" class="auth-label">{{ $locale === 'ne' ? 'इमेल ठेगाना' : 'Email Address' }}</label>
                                <div class="auth-input-wrap">
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

                                <button type="submit" class="auth-submit">
                                    {{ $locale === 'ne' ? 'लगइन' : 'Log In' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="auth-helper-links">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">{{ $locale === 'ne' ? 'पासवर्ड बिर्सनुभयो?' : 'Lost your password?' }}</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">{{ $locale === 'ne' ? 'नयाँ खाता बनाउनुहोस्' : 'Create an account' }}</a>
                        @endif
                        <a href="{{ $homeUrl }}">&#8592; {{ $locale === 'ne' ? 'मुखपृष्ठमा फर्कनुहोस्' : 'Back to Home' }}</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
