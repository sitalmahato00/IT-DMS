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

        $user = $request->user();

        // Handle photo upload
        $path = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (!$file->isValid()) {
                return Redirect::route('profile.edit')
                    ->withErrors(['photo' => 'File upload failed. Please try again.'])
                    ->withInput();
            }

            // Delete old photo if exists on user details
            try {
                if (!empty($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to delete old profile photo: ' . $e->getMessage());
            }

            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile-photos', $filename, 'public');
            if (!$path) {
                return Redirect::route('profile.edit')
                    ->withErrors(['photo' => 'Failed to upload photo.'])
                    ->withInput();
            }
        }

        // Update basic user fields
        $user->fill(array_intersect_key($validated, array_flip(['name','username','email'])));
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        // Update profile fields directly on user
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->department = $validated['department'] ?? $user->department;
        $user->bio = $validated['bio'] ?? $user->bio;
        if ($path) {
            $user->profile_photo_path = $path;
        }
        
        // Try to save and catch duplicate email error
        try {
            $user->save();
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for duplicate entry error
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return Redirect::route('profile.edit')
                    ->with('error', 'duplicate_email')
                    ->withInput();
            }
            // Re-throw other exceptions
            throw $e;
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        
        // Get additional data based on role
        $courses = null;
        $recentActivities = null;
        $attendanceStats = null;
        
        // Determine the appropriate layout based on role
        $layout = 'layouts.app';
        if ($user->role === 'admin') {
            $layout = 'admin.layouts.app';
        } elseif ($user->role === 'teacher') {
            $layout = 'teacher.layouts.teacherlayout';
        } elseif ($user->role === 'student') {
            $layout = 'student.layouts.studentlayout';
        } elseif ($user->role === 'parent') {
            $layout = 'parent.layouts.parentlayout';
        }

        return view('profile.show', [
            'user' => $user,
            'courses' => $courses,
            'recentActivities' => $recentActivities,
            'attendanceStats' => $attendanceStats,
            'layout' => $layout,
        ]);
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

