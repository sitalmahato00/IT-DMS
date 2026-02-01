<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle photo upload with better error handling
        if ($request->hasFile('photo')) {
            $user = $request->user();
            
            // Validate the file
            $file = $request->file('photo');
            if (!$file->isValid()) {
                return Redirect::route('profile.edit')
                    ->withErrors(['photo' => 'File upload failed. Please try again.'])
                    ->withInput();
            }

            // Delete old photo if exists
            if (!empty($user->profile_photo_path)) {
                try {
                    Storage::disk('public')->delete($user->profile_photo_path);
                } catch (\Exception $e) {
                    // Log error but continue with upload
                    \Log::error('Failed to delete old profile photo: ' . $e->getMessage());
                }
            }

            try {
                // Generate unique filename with timestamp
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile-photos', $filename, 'public');
                
                if (!$path) {
                    throw new \Exception('Failed to store file');
                }
                
                $validated['profile_photo_path'] = $path;
            } catch (\Exception $e) {
                \Log::error('Profile photo upload failed: ' . $e->getMessage());
                return Redirect::route('profile.edit')
                    ->withErrors(['photo' => 'Failed to upload photo. Please check file permissions and try again.'])
                    ->withInput();
            }
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
