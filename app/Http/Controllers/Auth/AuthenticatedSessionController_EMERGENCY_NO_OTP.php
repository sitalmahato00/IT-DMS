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

/**
 * EMERGENCY VERSION - 2FA DISABLED
 * Use this if 2FA is causing 500 errors
 * 
 * To use: Rename this file to AuthenticatedSessionController.php in app/Http/Controllers/Auth/
 * To revert: git checkout app/Http/Controllers/Auth/AuthenticatedSessionController.php
 */
class AuthenticatedSessionController_NoOTP extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request (2FA DISABLED).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

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
}
