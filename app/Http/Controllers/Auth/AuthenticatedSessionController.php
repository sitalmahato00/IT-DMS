<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ErpSetting;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
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
                $code = (string) random_int(100000, 999999);
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
                    // trusted device - proceed to login without 2FA
                    $request->session()->regenerate();
                    $request->session()->regenerateToken();
                    return redirect()->to($user?->getDashboardRoute() ?? route('home'));
                }

                $request->session()->put([
                    'two_factor.pending_user_id' => $user->id,
                    'two_factor.code' => $code,
                    'two_factor.expires_at' => now()->addMinutes($expiresMinutes)->toDateTimeString(),
                    'two_factor.remember' => $request->boolean('remember'),
                    'two_factor.email' => $user->email,
                    'two_factor.device_fingerprint' => $deviceFingerprint,
                ]);

                Log::info('2FA session set', [
                    'pending_user_id' => $user->id,
                    'email' => $user->email,
                    'fingerprint' => $deviceFingerprint,
                    'session_id' => $request->session()->getId(),
                ]);
                $user->notify(new TwoFactorCodeNotification($code, $expiresMinutes));
                Auth::logout();

                return redirect()->route('two-factor.challenge');
            } catch (\Exception $e) {
                \Log::error('Error during 2FA setup: ' . $e->getMessage());
                \Log::error('2FA Error Stack: ' . $e->getTraceAsString());
                // Fallback: skip OTP if there's an error
                Auth::logout();
                
                // Return detailed error in debug mode
                $message = config('app.debug') ? 'Authentication error: ' . $e->getMessage() : 'Authentication error. Please try again.';
                return back()->withErrors(['email' => $message]);
            }
        }

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        // Redirect to role-based dashboard
        return redirect()->to($user?->getDashboardRoute() ?? route('home'));
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
        try {
            \Log::info('DEBUG: Checking 2FA requirement for role: ' . ($role ?? 'null'));
            
            $twoFactorEnabled = ErpSetting::isEnabled('security_two_factor_enabled', false);
            \Log::info('DEBUG: 2FA Enabled setting: ' . ($twoFactorEnabled ? 'true' : 'false'));
            
            if (!$twoFactorEnabled) {
                \Log::info('DEBUG: 2FA is disabled');
                return false;
            }

            $roles = ErpSetting::asArray('security_two_factor_roles', ['admin']);
            \Log::info('DEBUG: 2FA Roles allowed: ' . json_encode($roles));
            
            $requiresOtp = $role ? in_array($role, $roles, true) : false;
            \Log::info('DEBUG: Role ' . ($role ?? 'null') . ' requires OTP: ' . ($requiresOtp ? 'yes' : 'no'));
            
            return $requiresOtp;
        } catch (\Exception $e) {
            \Log::error('ERROR in requiresTwoFactorChallenge: ' . $e->getMessage());
            // Default to disabled if there's an error
            return false;
        }
    }
}
