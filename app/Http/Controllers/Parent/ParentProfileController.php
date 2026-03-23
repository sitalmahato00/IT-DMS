<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ParentProfileController extends Controller
{
    /**
     * Display the parent's profile form.
     */
    public function edit(Request $request): View
    {
        // Ensure only parents can access this page
        if ($request->user()->role !== 'parent') {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        
        $user = $request->user();
        $parent = $user->parent;
        
        return view('parent.profile.edit', [
            'user' => $user,
            'parent' => $parent,
        ]);
    }

    /**
     * Update the parent's profile information.
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
                return Redirect::route('parent.profile.edit')
                    ->withErrors(['photo' => 'File upload failed. Please try again.'])
                    ->withInput();
            }

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
                return Redirect::route('parent.profile.edit')
                    ->withErrors(['photo' => 'Failed to upload photo.'])
                    ->withInput();
            }
        }

        // Update user fields
        $user->fill(array_intersect_key($validated, array_flip(['name', 'email'])));
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->phone = $validated['phone'] ?? $user->phone;
        if ($path) {
            $user->profile_photo_path = $path;
        }
        
        try {
            $user->save();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return Redirect::route('parent.profile.edit')
                    ->with('error', 'duplicate_email')
                    ->withInput();
            }
            throw $e;
        }

        return Redirect::route('parent.profile.edit')->with('status', 'profile-updated');
    }
}

