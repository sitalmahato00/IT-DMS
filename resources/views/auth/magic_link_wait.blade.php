@extends('layouts.app')

@section('content')
    <div class="auth-page">
        <div class="auth-shell">
            <section class="auth-side">
                <div class="auth-stack">
                    <div class="auth-card max-w-md mx-auto">
                        <div class="auth-panel-intro text-center">
                            <div class="auth-panel-logo-wrap" aria-hidden="true">
                                <img src="{{ $departmentLogoUrl ?? asset('build/icons/it-logo.png') }}" alt="IT-DMS" class="auth-panel-logo mx-auto" />
                            </div>
                            <div class="auth-panel-copy mt-4">
                                <h2 class="auth-panel-title">Check your email</h2>
                                <p class="auth-panel-subtitle">We've sent a secure sign-in link to your email. Please click Confirm in the email to complete sign-in.</p>
                            </div>
                        </div>

                        <div class="auth-divider"></div>

                        <div class="p-6">
                            <p id="statusText" class="text-sm text-gray-700">Waiting for confirmation…</p>

                            <div class="mt-4">
                                <a id="cancelLink" href="{{ route('login') }}" class="auth-link">&larr; Back to sign in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        (function poll(){
            fetch("{{ route('magic.wait.status') }}", { credentials: 'same-origin' })
                .then(r => r.json())
                .then(json => {
                    if (json.expired) {
                        document.getElementById('statusText').textContent = 'Sign-in link expired. Redirecting to sign in...';
                        setTimeout(() => location.href = '{{ route('login') }}', 2000);
                        return;
                    }

                    if (json.used_by_other) {
                        document.getElementById('statusText').textContent = 'Sign-in was confirmed on another device. Please sign in on this device.';
                        setTimeout(() => location.href = '{{ route('login') }}', 3000);
                        return;
                    }

                    if (json.status === true) {
                        // logged in
                        document.getElementById('statusText').textContent = 'Confirmed — signing you in now...';
                        setTimeout(() => location.href = '{{ url()->previous() ?? route('home') }}', 800);
                        return;
                    }

                    // keep polling
                    setTimeout(poll, 2500);
                })
                .catch(() => setTimeout(poll, 3000));
        })();
    </script>
@endsection
