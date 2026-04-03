<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ErpSetting;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $requiresTwoFactor = $user && $this->requiresTwoFactorChallenge($user->role ?? null);

        if ($requiresTwoFactor) {
            if (empty($user->email)) {
                Auth::logout();
                return back()->withErrors(['email' => 'Two-factor authentication requires a valid email address.']);
            }

            $code = (string) random_int(100000, 999999);
            $expiresMinutes = (int) ErpSetting::get('security_two_factor_expiry_minutes', 10);

            $request->session()->put([
                'two_factor.pending_user_id' => $user->id,
                'two_factor.code' => $code,
                'two_factor.expires_at' => now()->addMinutes($expiresMinutes)->toDateTimeString(),
                'two_factor.remember' => $request->boolean('remember'),
                'two_factor.email' => $user->email,
            ]);

            $user->notify(new TwoFactorCodeNotification($code, $expiresMinutes));
            Auth::logout();
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            return redirect()->route('two-factor.challenge');
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
        if (!ErpSetting::isEnabled('security_two_factor_enabled', false)) {
            return false;
        }

        $roles = ErpSetting::asArray('security_two_factor_roles', ['admin']);

        return $role ? in_array($role, $roles, true) : false;
    }
}
