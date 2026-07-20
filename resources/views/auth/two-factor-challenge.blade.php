@php
    $department = $department ?? null;
    $departmentName = $department?->name ?? 'Information Technology';
    $departmentLogoUrl = $departmentLogoUrl ?? ($department?->getLogoUrl() ?? '/images/default-logo.svg');
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
                <div class="auth-brand">
                    <div class="auth-brand-mark">
                        <img src="{{ $departmentLogoUrl }}" alt="{{ $departmentName }} logo" class="auth-brand-logo" />
                    </div>
                    <div class="auth-brand-copy">
                        <span class="auth-brand-kicker">Security</span>
                        <div class="auth-brand-title">{{ $departmentName }} Verification</div>
                    </div>
                </div>

                <h1 class="auth-hero-title">Two-Factor<br>Authentication</h1>
                <p class="auth-hero-summary">We sent a verification code to your email address.</p>
                <p class="auth-hero-text">
                    Enter the 6-digit code to finish signing in. This keeps admin access protected even if a password is shared.
                </p>
            </div>
        </section>

        <section class="auth-side">
            <div class="auth-stack">
                @if (session('status'))
                    <div class="auth-status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-alert" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="auth-card">
                    <div class="auth-form-intro">
                        <h3 class="auth-form-title">Enter verification code</h3>
                        <p class="auth-form-text">
                            @if(!empty($email))
                                The code was sent to <strong>{{ $email }}</strong>.
                            @else
                                Check your inbox for the login code.
                            @endif
                        </p>
                    </div>

                    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
                        @csrf

                        <div class="auth-field">
                            <label for="code" class="auth-label">Verification Code</label>
                            <div class="auth-input-wrap">
                                <input
                                    id="code"
                                    name="code"
                                    type="text"
                                    maxlength="6"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    class="auth-input tracking-[0.35em] text-center text-lg font-bold"
                                    placeholder="123456"
                                    value="{{ old('code') }}"
                                    required
                                />
                            </div>
                        </div>

                        <button type="submit" class="auth-submit full">Verify and Continue</button>
                    </form>

                    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-3">
                        @csrf
                        <button
                            type="submit"
                            class="auth-secondary-action w-full"
                            data-otp-resend-button
                            data-resend-remaining="{{ $resendSecondsRemaining ?? 0 }}"
                            @disabled(($resendSecondsRemaining ?? 0) > 0)
                        >
                            Resend code
                        </button>
                    </form>
                    <p class="mt-3 text-sm text-slate-500" data-otp-resend-timer aria-live="polite">
                        @if (($resendSecondsRemaining ?? 0) > 0)
                            You can resend a new code in {{ $resendSecondsRemaining }} seconds.
                        @else
                            You can request a new code now.
                        @endif
                    </p>

                    <a href="{{ route('two-factor.cancel') }}" class="auth-back-link block mt-4">&#8592; Back to login</a>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const resendButton = document.querySelector('[data-otp-resend-button]');
        const resendTimer = document.querySelector('[data-otp-resend-timer]');

        if (!resendButton || !resendTimer) {
            return;
        }

        let remaining = Number(resendButton.dataset.resendRemaining || '0');
        let intervalId = null;

        const setButtonState = (disabled) => {
            resendButton.disabled = disabled;
            resendButton.classList.toggle('opacity-60', disabled);
            resendButton.classList.toggle('cursor-not-allowed', disabled);
        };

        const renderTimer = () => {
            if (remaining > 0) {
                setButtonState(true);
                resendTimer.textContent = `You can resend a new code in ${remaining} second${remaining === 1 ? '' : 's'}.`;
                remaining -= 1;
                return;
            }

            setButtonState(false);
            resendTimer.textContent = 'You can request a new code now.';

            if (intervalId) {
                window.clearInterval(intervalId);
                intervalId = null;
            }
        };

        renderTimer();

        if (remaining > 0) {
            intervalId = window.setInterval(renderTimer, 1000);
        }
    });
</script>
@endpush

