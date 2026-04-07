<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\ManagesTwoFactorChallengeState;
use App\Http\Controllers\Controller;
use App\Models\ErpSetting;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class TwoFactorChallengeController extends Controller
{
    use ManagesTwoFactorChallengeState;

    private const RESEND_COOLDOWN_SECONDS = 30;

    public function create(Request $request): View|RedirectResponse
    {
        $this->restoreTwoFactorChallengeState($request);

        if (!$request->session()->has('two_factor.pending_user_id')) {
            Log::warning('Two-factor challenge requested but session missing', ['session_id' => $request->session()->getId()]);

            return redirect()
                ->route('login')
                ->withCookie($this->forgetTwoFactorChallengeStateCookie());
        }

        Log::info('Two-factor challenge page shown', [
            'pending_user_id' => $request->session()->get('two_factor.pending_user_id'),
            'session_id' => $request->session()->getId(),
        ]);

        return view('auth.two-factor-challenge', [
            'email' => $request->session()->get('two_factor.email'),
            'expiresAt' => $request->session()->get('two_factor.expires_at'),
            'resendSecondsRemaining' => $this->getResendSecondsRemaining($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->restoreTwoFactorChallengeState($request);

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $pendingUserId = $request->session()->get('two_factor.pending_user_id');
        $pendingCode = $request->session()->get('two_factor.code');
        $expiresAt = $request->session()->get('two_factor.expires_at');
        $remember = (bool) $request->session()->get('two_factor.remember', false);

        if (!$pendingUserId || !$pendingCode) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your login session expired. Please sign in again.'])
                ->withCookie($this->forgetTwoFactorChallengeStateCookie());
        }

        if ($expiresAt && Carbon::parse($expiresAt)->isPast()) {
            $this->clearChallengeSession($request);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'The verification code expired. Please sign in again.'])
                ->withCookie($this->forgetTwoFactorChallengeStateCookie());
        }

        if (!hash_equals((string) $pendingCode, (string) $request->string('code'))) {
            return back()->withErrors(['code' => 'The verification code is incorrect.'])->withInput();
        }

        Auth::loginUsingId($pendingUserId, $remember);

        // retrieve fingerprint to set trusted device cookie
        $deviceFingerprint = $request->session()->get('two_factor.device_fingerprint');

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        // clear challenge session after retrieving fingerprint
        $this->clearChallengeSession($request);

        $redirect = redirect()->to(Auth::user()?->getDashboardRoute() ?? route('home'));
        $redirect->withCookie($this->forgetTwoFactorChallengeStateCookie());

        if ($deviceFingerprint) {
            // set cookie for 1 year (minutes)
            $minutes = 60 * 24 * 365;
            $cookie = cookie('trusted_device', $deviceFingerprint, $minutes, '/', null, config('app.env') === 'production', true, false, 'Lax');
            return $redirect->withCookie($cookie);
        }

        return $redirect;
    }

    public function resend(Request $request): RedirectResponse
    {
        $this->restoreTwoFactorChallengeState($request);

        $pendingUserId = $request->session()->get('two_factor.pending_user_id');
        if (!$pendingUserId) {
            return redirect()
                ->route('login')
                ->withCookie($this->forgetTwoFactorChallengeStateCookie());
        }

        $secondsRemaining = $this->getResendSecondsRemaining($request);
        if ($secondsRemaining > 0) {
            return back()->withErrors([
                'code' => "Please wait {$secondsRemaining} seconds before requesting another code.",
            ]);
        }

        $user = \App\Models\User::find($pendingUserId);
        if (!$user || empty($user->email)) {
            return redirect()->route('login')->withErrors(['email' => 'Unable to resend verification code. Please sign in again.']);
        }

        $code = (string) random_int(100000, 999999);
        $expiresMinutes = (int) ErpSetting::get('security_two_factor_expiry_minutes', 10);

        $request->session()->put([
            'two_factor.code' => $code,
            'two_factor.expires_at' => now()->addMinutes($expiresMinutes)->toDateTimeString(),
            'two_factor.last_sent_at' => now()->toDateTimeString(),
        ]);
        $request->session()->save();

        $user->notify(new TwoFactorCodeNotification($code, $expiresMinutes));

        return back()
            ->with('status', 'A new verification code has been sent.')
            ->withCookie($this->persistTwoFactorChallengeState($request, [
                'two_factor.pending_user_id' => $request->session()->get('two_factor.pending_user_id'),
                'two_factor.code' => $request->session()->get('two_factor.code'),
                'two_factor.expires_at' => $request->session()->get('two_factor.expires_at'),
                'two_factor.remember' => $request->session()->get('two_factor.remember', false),
                'two_factor.email' => $request->session()->get('two_factor.email'),
                'two_factor.device_fingerprint' => $request->session()->get('two_factor.device_fingerprint'),
                'two_factor.last_sent_at' => $request->session()->get('two_factor.last_sent_at'),
            ]));
    }

    protected function clearChallengeSession(Request $request): void
    {
        $request->session()->forget([
            'two_factor.pending_user_id',
            'two_factor.code',
            'two_factor.expires_at',
            'two_factor.remember',
            'two_factor.email',
            'two_factor.last_sent_at',
        ]);
        $request->session()->save();
    }

    protected function getResendSecondsRemaining(Request $request): int
    {
        $lastSentAt = $request->session()->get('two_factor.last_sent_at');
        if (!$lastSentAt) {
            return 0;
        }

        $availableAt = Carbon::parse($lastSentAt)->addSeconds(self::RESEND_COOLDOWN_SECONDS);
        if ($availableAt->isPast()) {
            return 0;
        }

        $millisecondsRemaining = now()->diffInMilliseconds($availableAt, false);

        return max(0, (int) ceil($millisecondsRemaining / 1000));
    }
}
