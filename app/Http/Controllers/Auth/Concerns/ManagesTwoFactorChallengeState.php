<?php

namespace App\Http\Controllers\Auth\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Cookie;

trait ManagesTwoFactorChallengeState
{
    protected const TWO_FACTOR_PENDING_COOKIE = 'itdms_two_factor_pending';

    /**
     * Persist the pending two-factor state in both the session and
     * a short-lived encrypted cookie so deployment environments with
     * fragile session storage can still complete the redirect.
     */
    protected function persistTwoFactorChallengeState(Request $request, array $challengeState): Cookie
    {
        $request->session()->put($challengeState);
        $request->session()->save();

        return cookie(
            static::TWO_FACTOR_PENDING_COOKIE,
            json_encode($challengeState, JSON_UNESCAPED_SLASHES),
            $this->twoFactorChallengeCookieMinutes($challengeState['two_factor.expires_at'] ?? null),
            config('session.path', '/'),
            config('session.domain'),
            $request->isSecure() || (bool) config('session.secure'),
            true,
            false,
            config('session.same_site', 'lax')
        );
    }

    protected function restoreTwoFactorChallengeState(Request $request): void
    {
        if ($request->session()->has('two_factor.pending_user_id')) {
            return;
        }

        $rawPayload = $request->cookie(static::TWO_FACTOR_PENDING_COOKIE);
        if (!is_string($rawPayload) || trim($rawPayload) === '') {
            return;
        }

        $decodedPayload = json_decode($rawPayload, true);
        if (!is_array($decodedPayload)) {
            return;
        }

        $challengeState = [];
        foreach ($this->twoFactorChallengeKeys() as $key) {
            if (array_key_exists($key, $decodedPayload)) {
                $challengeState[$key] = $decodedPayload[$key];
            }
        }

        if (
            empty($challengeState['two_factor.pending_user_id'])
            || empty($challengeState['two_factor.code'])
            || empty($challengeState['two_factor.expires_at'])
        ) {
            return;
        }

        try {
            if (Carbon::parse($challengeState['two_factor.expires_at'])->isPast()) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $request->session()->put($challengeState);
        $request->session()->save();
    }

    protected function clearTwoFactorChallengeState(Request $request): void
    {
        $request->session()->forget($this->twoFactorChallengeKeys());
        $request->session()->save();
    }

    protected function forgetTwoFactorChallengeStateCookie(): Cookie
    {
        return cookie()->forget(
            static::TWO_FACTOR_PENDING_COOKIE,
            config('session.path', '/'),
            config('session.domain')
        );
    }

    protected function twoFactorChallengeKeys(): array
    {
        return [
            'two_factor.pending_user_id',
            'two_factor.code',
            'two_factor.expires_at',
            'two_factor.remember',
            'two_factor.email',
            'two_factor.device_fingerprint',
            'two_factor.last_sent_at',
        ];
    }

    protected function twoFactorChallengeCookieMinutes(?string $expiresAt): int
    {
        if (!is_string($expiresAt) || trim($expiresAt) === '') {
            return 15;
        }

        try {
            $secondsUntilExpiry = max(0, now()->diffInSeconds(Carbon::parse($expiresAt), false));
        } catch (\Throwable) {
            return 15;
        }

        return max(15, (int) ceil($secondsUntilExpiry / 60));
    }
}

