<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\ManagesTwoFactorChallengeState;
use App\Http\Controllers\Controller;
use App\Models\ErpSetting;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCodeNotification;
use App\Models\MagicLink;
use App\Notifications\MagicLinkNotification;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use ManagesTwoFactorChallengeState;
    /**
     * Display the login view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $this->restoreTwoFactorChallengeState($request);

        if ($request->session()->has('two_factor.pending_user_id')) {
            return redirect()->route('two-factor.challenge');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();
        
        try {
            $requiresTwoFactor = $user && $this->requiresTwoFactorChallenge($user->role ?? null);
        } catch (\Exception $e) {
            \Log::error('Error checking 2FA requirement: ' . $e->getMessage());
            $requiresTwoFactor = false;
        }

        if ($requiresTwoFactor) {
            if (empty($user->email)) {
                Auth::logout();
                return back()->withErrors(['email' => 'Two-factor authentication requires a valid email address.']);
            }

            try {
                $expiresMinutes = (int) ErpSetting::get('security_two_factor_expiry_minutes', 10);

                // Device fingerprint used to remember trusted devices
                $deviceFingerprint = hash_hmac(
                    'sha256',
                    $user->id . '|' . $request->ip() . '|' . $request->header('User-Agent'),
                    config('app.key')
                );

                // If the user already has a trusted device cookie matching this fingerprint, skip 2FA
                $trustedCookie = $request->cookie('trusted_device');
                if ($trustedCookie && hash_equals((string) $trustedCookie, (string) $deviceFingerprint)) {
                    $request->session()->regenerate();
                    $request->session()->regenerateToken();

                    return redirect()
                        ->to($user?->getDashboardRoute() ?? route('home'))
                        ->withCookie($this->forgetTwoFactorChallengeStateCookie());
                }

                // Create a one-time magic link token (store a hash of the token)
                $plainToken = Str::random(64);
                $tokenHash = hash('sha256', $plainToken);

                $magic = MagicLink::create([
                    'user_id' => $user->id,
                    'token_hash' => $tokenHash,
                    'expires_at' => now()->addMinutes($expiresMinutes),
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'session_id' => $request->session()->getId(),
                ]);

                Log::info('Magic link created', [
                    'magic_link_id' => $magic->id,
                    'pending_user_id' => $user->id,
                    'email' => $user->email,
                    'fingerprint' => $deviceFingerprint,
                    'session_id' => $request->session()->getId(),
                ]);

                // Send email with one-time link
                $user->notify(new MagicLinkNotification($plainToken, $expiresMinutes));
                Auth::logout();

                // store pending magic link id in session so we can poll for status
                $request->session()->put('magic_link_pending_id', $magic->id);

                return redirect()->route('magic.wait');
            } catch (\Exception $e) {
                \Log::error('Error creating magic link: ' . $e->getMessage());
                \Log::error('Magic link Error Stack: ' . $e->getTraceAsString());
                Auth::logout();

                $message = config('app.debug') ? 'Authentication error: ' . $e->getMessage() : 'Authentication error. Please try again.';
                return back()->withErrors(['email' => $message]);
            }
        }

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        // Redirect to role-based dashboard
        return redirect()
            ->to($user?->getDashboardRoute() ?? route('home'))
            ->withCookie($this->forgetTwoFactorChallengeStateCookie());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function requiresTwoFactorChallenge(?string $role): bool
    {
        // Two-factor / magic-link flow is disabled. Return false to allow direct login.
        return false;
    }
}
