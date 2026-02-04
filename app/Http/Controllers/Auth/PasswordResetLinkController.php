<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // For local/dev environment: create a token, persist it, send notification directly and log the token
        if (app()->environment('local')) {
            try {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    $token = \Illuminate\Support\Str::random(64);

                    // Persist hashed token so Laravel broker can validate it later
                    \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
                        ['email' => $user->email],
                        ['token' => \Illuminate\Support\Facades\Hash::make($token), 'created_at' => now()]
                    );

                    // Send the standard ResetPassword notification with our token
                    $user->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));

                    // Log token for easy testing in local env
                    Log::info('DEV: password reset token for ' . $user->email . ' => ' . $token);

                    $status = Password::RESET_LINK_SENT; // emulate successful send
                } else {
                    $status = Password::INVALID_USER;
                }
            } catch (\Exception $e) {
                Log::error('DEV: failed to create/send reset token: ' . $e->getMessage());
                $status = Password::INVALID_USER;
            }
        } else {
            // Production/non-local: use default broker to send reset link
            $status = Password::sendResetLink(
                $request->only('email')
            );
        }

        // Log if no user found for easier debugging (local only)
        if ($status == Password::INVALID_USER) {
            Log::warning('Password reset requested for non-existing email: ' . $request->email);
        }

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
