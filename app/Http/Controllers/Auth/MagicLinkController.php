<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MagicLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLinkController extends Controller
{
    public function consume(Request $request, string $token): RedirectResponse
    {
        $tokenHash = hash('sha256', $token);

        $magic = MagicLink::where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $magic) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid or expired sign-in link.']);
        }

        $magic->used_at = now();
        $magic->save();

        Auth::loginUsingId($magic->user_id);
        $request->session()->regenerate();

        return redirect()->intended(auth()->user()?->getDashboardRoute() ?? route('home'));
    }

    public function deny(Request $request, string $token): RedirectResponse
    {
        $tokenHash = hash('sha256', $token);

        $magic = MagicLink::where('token_hash', $tokenHash)->first();

        if ($magic && is_null($magic->used_at)) {
            // mark as used to prevent future use
            $magic->used_at = now();
            $magic->save();
        }

        return redirect()->route('login')->with('status', 'The sign-in request was cancelled.');
    }

    /**
     * AJAX status endpoint for waiting page
     */
    public function status(Request $request)
    {
        $pendingId = $request->session()->get('magic_link_pending_id');

        if (! $pendingId) {
            return response()->json(['status' => false, 'message' => 'No pending request']);
        }

        $magic = MagicLink::find($pendingId);

        if (! $magic) {
            return response()->json(['status' => false, 'message' => 'Request not found']);
        }

        // expired
        if ($magic->expires_at && $magic->expires_at->isPast()) {
            return response()->json(['status' => false, 'expired' => true]);
        }

        // If already used
        if ($magic->used_at) {
            // If this session belongs to the same session id, attempt to log user in
            if ($magic->session_id === $request->session()->getId()) {
                if (! Auth::check()) {
                    Auth::loginUsingId($magic->user_id);
                    $request->session()->regenerate();
                }

                return response()->json(['status' => true, 'logged_in' => Auth::check()]);
            }

            // used by another device/browser
            return response()->json(['status' => false, 'used_by_other' => true]);
        }

        return response()->json(['status' => false]);
    }
}
