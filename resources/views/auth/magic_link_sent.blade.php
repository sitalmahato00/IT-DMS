@extends('layouts.app')

@section('content')
    <div class="auth-page">
        <div class="auth-shell">
            <section class="auth-side">
                <div class="auth-stack">
                    <div class="auth-card max-w-md mx-auto">
                        <div class="auth-panel-intro">
                            <div class="auth-panel-logo-wrap" aria-hidden="true">
                                <img src="{{ $departmentLogoUrl ?? asset('build/icons/it-logo.png') }}" alt="Manmohan Memorial Polytechnic" class="auth-panel-logo" />
                            </div>
                            <div class="auth-panel-copy">
                                <h2 class="auth-panel-title">Check your email</h2>
                                <p class="auth-panel-subtitle">We've sent a secure sign-in link to the email address on file. Click that link to complete sign-in. The link expires shortly.</p>
                            </div>
                        </div>

                        <div class="auth-divider"></div>

                        <div class="p-6">
                            <a href="{{ route('login') }}" class="auth-link">&larr; Back to sign in</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

