<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department;
use App\Models\ErpSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password as PasswordRule;

class SettingController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        $department = Department::query()->first();

        return view('admin.settings', [
            'department' => $department,
            'securitySettings' => [
                'security_password_min_length' => (int) ErpSetting::get('security_password_min_length', 10),
                'security_password_require_uppercase' => ErpSetting::isEnabled('security_password_require_uppercase', true),
                'security_password_require_lowercase' => ErpSetting::isEnabled('security_password_require_lowercase', true),
                'security_password_require_number' => ErpSetting::isEnabled('security_password_require_number', true),
                'security_password_require_symbol' => ErpSetting::isEnabled('security_password_require_symbol', true),
                'security_two_factor_enabled' => ErpSetting::isEnabled('security_two_factor_enabled', false),
                'security_two_factor_expiry_minutes' => (int) ErpSetting::get('security_two_factor_expiry_minutes', 10),
            ],
            'notificationSettings' => [
                'notification_email_enabled' => ErpSetting::isEnabled('notification_email_enabled', true),
                'notification_email_exam' => ErpSetting::isEnabled('notification_email_exam', true),
                'notification_email_attendance' => ErpSetting::isEnabled('notification_email_attendance', true),
                'notification_email_student' => ErpSetting::isEnabled('notification_email_student', true),
                'notification_email_assignment' => ErpSetting::isEnabled('notification_email_assignment', true),
                'notification_email_result' => ErpSetting::isEnabled('notification_email_result', true),
            ],
            'passwordPolicy' => [
                'min_length' => (int) ErpSetting::get('security_password_min_length', 10),
                'require_uppercase' => ErpSetting::isEnabled('security_password_require_uppercase', true),
                'require_lowercase' => ErpSetting::isEnabled('security_password_require_lowercase', true),
                'require_number' => ErpSetting::isEnabled('security_password_require_number', true),
                'require_symbol' => ErpSetting::isEnabled('security_password_require_symbol', true),
            ],
            'twoFactorRoles' => ErpSetting::asArray('security_two_factor_roles', ['admin']),
            'systemInfo' => [
                'laravel' => app()->version(),
                'php' => PHP_VERSION,
                'environment' => app()->environment(),
            ],
        ]);
    }

    /**
     * Update security and notification settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'security_password_min_length' => ['required', 'integer', 'min:8', 'max:64'],
            'security_password_require_uppercase' => ['nullable', 'boolean'],
            'security_password_require_lowercase' => ['nullable', 'boolean'],
            'security_password_require_number' => ['nullable', 'boolean'],
            'security_password_require_symbol' => ['nullable', 'boolean'],
            'security_two_factor_enabled' => ['nullable', 'boolean'],
            'security_two_factor_roles' => ['nullable', 'array'],
            'security_two_factor_roles.*' => ['in:admin,teacher,student,parent'],
            'security_two_factor_expiry_minutes' => ['required', 'integer', 'min:3', 'max:60'],
            'notification_email_enabled' => ['nullable', 'boolean'],
            'notification_email_exam' => ['nullable', 'boolean'],
            'notification_email_attendance' => ['nullable', 'boolean'],
            'notification_email_student' => ['nullable', 'boolean'],
            'notification_email_assignment' => ['nullable', 'boolean'],
            'notification_email_result' => ['nullable', 'boolean'],
        ]);

        ErpSetting::set('security_password_min_length', (int) $validated['security_password_min_length'], 'security', 'integer');
        ErpSetting::set('security_password_require_uppercase', $request->boolean('security_password_require_uppercase'), 'security', 'boolean');
        ErpSetting::set('security_password_require_lowercase', $request->boolean('security_password_require_lowercase'), 'security', 'boolean');
        ErpSetting::set('security_password_require_number', $request->boolean('security_password_require_number'), 'security', 'boolean');
        ErpSetting::set('security_password_require_symbol', $request->boolean('security_password_require_symbol'), 'security', 'boolean');
        ErpSetting::set('security_two_factor_enabled', $request->boolean('security_two_factor_enabled'), 'security', 'boolean');
        ErpSetting::set('security_two_factor_roles', array_values($validated['security_two_factor_roles'] ?? ['admin']), 'security', 'json');
        ErpSetting::set('security_two_factor_expiry_minutes', (int) $validated['security_two_factor_expiry_minutes'], 'security', 'integer');
        ErpSetting::set('notification_email_enabled', $request->boolean('notification_email_enabled'), 'notification', 'boolean');
        ErpSetting::set('notification_email_exam', $request->boolean('notification_email_exam'), 'notification', 'boolean');
        ErpSetting::set('notification_email_attendance', $request->boolean('notification_email_attendance'), 'notification', 'boolean');
        ErpSetting::set('notification_email_student', $request->boolean('notification_email_student'), 'notification', 'boolean');
        ErpSetting::set('notification_email_assignment', $request->boolean('notification_email_assignment'), 'notification', 'boolean');
        ErpSetting::set('notification_email_result', $request->boolean('notification_email_result'), 'notification', 'boolean');

        return back()->with('status', 'Settings updated successfully.');
    }

    /**
     * Update the authenticated admin's profile information from the settings page.
     */
    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        $oldPhotoPath = $user->profile_photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            if (!$file || !$file->isValid()) {
                return back()
                    ->withErrors(['photo' => 'File upload failed. Please try again.'])
                    ->withInput();
            }

            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $newPhotoPath = $file->storeAs('profile-photos', $filename, 'public');

            if (!$newPhotoPath) {
                return back()
                    ->withErrors(['photo' => 'Failed to upload photo.'])
                    ->withInput();
            }
        }

        $user->fill(array_intersect_key($validated, array_flip(['name', 'username', 'email'])));
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->phone = $validated['phone'] ?? $user->phone;
        $user->department = $validated['department'] ?? $user->department;
        $user->bio = $validated['bio'] ?? $user->bio;

        if ($newPhotoPath) {
            $user->profile_photo_path = $newPhotoPath;
        }

        try {
            $user->save();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()
                    ->withErrors(['email' => 'This email address or username is already in use.'])
                    ->withInput();
            }

            throw $e;
        }

        if ($newPhotoPath && !empty($oldPhotoPath) && $oldPhotoPath !== $newPhotoPath) {
            try {
                Storage::disk('public')->delete($oldPhotoPath);
            } catch (\Throwable $e) {
                \Log::warning('Failed to delete old admin profile photo: ' . $e->getMessage());
            }
        }

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update the authenticated user's password from settings.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $minLength = (int) ErpSetting::get('security_password_min_length', 10);
        $passwordRule = PasswordRule::min($minLength);

        if (ErpSetting::isEnabled('security_password_require_uppercase', true)) {
            $passwordRule = $passwordRule->mixedCase();
        }

        if (ErpSetting::isEnabled('security_password_require_lowercase', true)) {
            $passwordRule = $passwordRule->letters();
        }

        if (ErpSetting::isEnabled('security_password_require_number', true)) {
            $passwordRule = $passwordRule->numbers();
        }

        if (ErpSetting::isEnabled('security_password_require_symbol', true)) {
            $passwordRule = $passwordRule->symbols();
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', $passwordRule, 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('password_status', 'password-updated');
    }
}

