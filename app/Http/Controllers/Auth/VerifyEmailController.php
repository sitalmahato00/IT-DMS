<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->to($this->getProfileRoute($request->user()))->with('verified', 1);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->to($this->getProfileRoute($request->user()))->with('verified', 1);
    }

    /**
     * Get the profile route based on user role
     */
    private function getProfileRoute($user): string
    {
        return match($user->role) {
            'admin' => route('admin.dashboard'),
            'teacher' => route('teacher.profile.edit'),
            'student' => route('student.profile.edit'),
            'parent' => route('parent.profile.edit'),
            default => route('dashboard'),
        };
    }
}

