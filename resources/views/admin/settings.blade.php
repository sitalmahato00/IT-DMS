@extends('admin.layouts.app')

@section('title', 'Settings')

@section('styles')
<script>document.documentElement.classList.add('settings-ui-enhanced');</script>
<style>
    html.settings-ui-enhanced:not(.dark) .settings-card,
    html.settings-ui-enhanced:not(.dark) .settings-panel {
        border-radius: 28px;
        border-color: rgba(226, 232, 240, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 250, 251, 0.98));
        box-shadow: 0 24px 52px -40px rgba(15, 23, 42, 0.22);
    }
    html.settings-ui-enhanced:not(.dark) .settings-card-header {
        background: linear-gradient(180deg, #f8fafc, #ffffff);
    }

    /* Parent-like header styling (matches department page header style) */
    .settings-card-header {
        background: linear-gradient(90deg, #0f172a, #0c1221); 
        border-bottom: 1px solid #334155;
        color: #f8fafc;
    }

    .settings-card-header h3 {
        color: #f8fafc; 
    }

    .settings-card-header i {
        color: #60a5fa; 
    }
    .settings-tab-btn { border-bottom: 2px solid transparent; color: #475569; }
    .settings-tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; background: linear-gradient(180deg, #ffffff, #f8fbff); }
    .settings-field { border: 1px solid #dbe4ee; border-radius: 18px; background: #fff; padding: 1rem; }
    .settings-field-label { font-size: .7rem; letter-spacing: .18em; text-transform: uppercase; color: #64748b; font-weight: 800; }
    .settings-field-value { margin-top: .35rem; color: #0f172a; font-size: .95rem; font-weight: 600; word-break: break-word; }

    .settings-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        border-radius: .75rem;
        font-weight: 700;
        border: 1px solid transparent;
        padding: .58rem 1.25rem;
        transition: transform .15s ease, box-shadow .2s ease, background-color .2s ease, color .2s ease;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }

    .settings-action-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .settings-action-btn.primary {
        background: #1d4ed8;
        color: #fff;
        border-color: #1d4ed8;
        box-shadow: 0 8px 16px -8px rgba(29, 78, 216, .45);
    }

    .settings-action-btn.secondary {
        background: #ffffff;
        color: #334155;
        border-color: #cbd5e1;
    }

    .settings-action-btn.danger {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }

    .settings-action-btn:disabled,
    .settings-action-btn[disabled] {
        opacity: .65;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    html.settings-ui-enhanced.dark .settings-field,
    html.settings-ui-enhanced.dark .settings-field-label,
    html.settings-ui-enhanced.dark .settings-field-value {
        background: #111827;
        color: #cbd5e1;
        border-color: #334155;
    }

    html.settings-ui-enhanced.dark .settings-action-btn.primary {
        background: #1e40af;
        border-color: #1e40af;
        color: #fff;
    }

    html.settings-ui-enhanced.dark .settings-action-btn.secondary {
        background: #0f172a;
        color: #e2e8f0;
        border-color: #334155;
    }

    html.settings-ui-enhanced.dark .settings-action-btn.danger {
        background: #b91c1c;
        border-color: #b91c1c;
        color: #fff;
    }

    /* Force all white-based helpers to dark style in the dark theme */
    html.settings-ui-enhanced.dark .bg-white,
    html.settings-ui-enhanced.dark .bg-slate-50,
    html.settings-ui-enhanced.dark .bg-gray-50,
    html.settings-ui-enhanced.dark .bg-gray-100,
    html.settings-ui-enhanced.dark .border-slate-200,
    html.settings-ui-enhanced.dark .border-slate-100,
    html.settings-ui-enhanced.dark .border-slate-300 {
        background-color: #0b1221 !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    html.settings-ui-enhanced.dark .text-slate-900,
    html.settings-ui-enhanced.dark .text-gray-900,
    html.settings-ui-enhanced.dark .text-slate-800,
    html.settings-ui-enhanced.dark .text-gray-800 {
        color: #f8fafc !important;
    }

    html.settings-ui-enhanced.dark input,
    html.settings-ui-enhanced.dark textarea,
    html.settings-ui-enhanced.dark select,
    html.settings-ui-enhanced.dark .form-control,
    html.settings-ui-enhanced.dark .settings-field,
    html.settings-ui-enhanced.dark .settings-card,
    html.settings-ui-enhanced.dark .settings-card-header,
    html.settings-ui-enhanced.dark .settings-panel {
        background-color: #0f172a !important;
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }
</style>
@endsection

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Settings',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Settings']
    ]
])

@php
    $passwordPolicy = $passwordPolicy ?? ['min_length' => 10, 'require_uppercase' => true, 'require_lowercase' => true, 'require_number' => true, 'require_symbol' => true];
    $notificationSettings = $notificationSettings ?? [];
    $securitySettings = $securitySettings ?? [];
    $systemInfo = $systemInfo ?? ['laravel' => app()->version(), 'php' => PHP_VERSION, 'environment' => app()->environment()];
    $twoFactorRoles = $twoFactorRoles ?? ['admin'];
    $user = Auth::user();
    $heroImages = collect($department?->hero_images ?? [])->filter()->values();
    $statusMessage = session('status') === 'profile-updated' ? 'Profile updated successfully.' : session('status');
@endphp

<div class="settings-page grid grid-cols-1 gap-6">
    @if($statusMessage || session('password_status'))
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ $statusMessage ?: 'Password updated successfully.' }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            <div class="font-bold">Please fix the highlighted errors.</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="settings-card border border-slate-200 overflow-hidden">
        <div class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 backdrop-blur-sm overflow-x-auto">
            <div class="flex min-w-max px-5">
                <button type="button" class="settings-tab-btn active px-5 py-4 text-sm font-semibold whitespace-nowrap" data-tab="department"><i class="bi bi-building mr-2"></i>Department</button>
                <button type="button" class="settings-tab-btn px-5 py-4 text-sm font-semibold whitespace-nowrap" data-tab="profile"><i class="bi bi-person-circle mr-2"></i>Profile</button>
                <button type="button" class="settings-tab-btn px-5 py-4 text-sm font-semibold whitespace-nowrap" data-tab="security"><i class="bi bi-shield-lock mr-2"></i>Security</button>
                <button type="button" class="settings-tab-btn px-5 py-4 text-sm font-semibold whitespace-nowrap" data-tab="notifications"><i class="bi bi-bell mr-2"></i>Notifications</button>
                <button type="button" class="settings-tab-btn px-5 py-4 text-sm font-semibold whitespace-nowrap" data-tab="info"><i class="bi bi-info-circle mr-2"></i>Info</button>
                <button type="button" class="settings-tab-btn px-5 py-4 text-sm font-semibold whitespace-nowrap" data-tab="password"><i class="bi bi-key mr-2"></i>Password</button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <section id="department-pane" class="tab-pane space-y-6">
                @if($statusMessage)
                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                        {{ $statusMessage }}
                    </div>
                @endif
                <form id="departmentSettingsForm" method="POST" action="{{ route('admin.department.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="settings-card border border-slate-200">
                        <div class="settings-card-header border-b border-slate-200 px-5 py-4"><h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900"><i class="bi bi-building text-blue-600"></i>Department</h3></div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
                                    <div class="settings-field">
                                    <div class="settings-field-label">Logo</div>
                                    <div class="mt-4 flex items-center justify-center rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-4">
                                        <img src="{{ $department?->logo_url ?? asset('images/default-logo.svg') }}" alt="College logo" class="max-h-32 w-auto object-contain">
                                    </div>
                                    <div class="mt-4">
                                        <input type="file" name="logo" class="w-full text-sm text-slate-600" accept="image/*" />
                                        <p class="text-xs text-slate-500 mt-1">Optional. PNG/JPG/GIF/SVG, max 2MB.</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="name">Department Name</label>
                                        <input id="name" name="name" type="text" value="{{ old('name', $department?->name) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2" required>
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="name_nepali">Nepali Name</label>
                                        <input id="name_nepali" name="name_nepali" type="text" value="{{ old('name_nepali', $department?->name_nepali) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="short_name">Department Name</label>
                                        <input id="short_name" name="short_name" type="text" value="{{ old('short_name', $department?->short_name) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2" maxlength="50">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="phone">Phone</label>
                                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $department?->phone) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="email">Email</label>
                                        <input id="email" name="email" type="email" value="{{ old('email', $department?->email) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="website">Website</label>
                                        <input id="website" name="website" type="url" value="{{ old('website', $department?->website) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="city">City</label>
                                        <input id="city" name="city" type="text" value="{{ old('city', $department?->city) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="district">District</label>
                                        <input id="district" name="district" type="text" value="{{ old('district', $department?->district) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="province">Province</label>
                                        <input id="province" name="province" type="text" value="{{ old('province', $department?->province) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="latitude">Latitude</label>
                                        <input id="latitude" name="latitude" type="number" step="0.00000001" min="-90" max="90" value="{{ old('latitude', $department?->latitude) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="longitude">Longitude</label>
                                        <input id="longitude" name="longitude" type="number" step="0.00000001" min="-180" max="180" value="{{ old('longitude', $department?->longitude) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="map_label">Map Label</label>
                                        <input id="map_label" name="map_label" type="text" value="{{ old('map_label', $department?->map_label) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="principal_name">Principal Name</label>
                                        <input id="principal_name" name="principal_name" type="text" value="{{ old('principal_name', $department?->principal_name) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="principal_phone">Principal Phone</label>
                                        <input id="principal_phone" name="principal_phone" type="tel" value="{{ old('principal_phone', $department?->principal_phone) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="principal_email">Principal Email</label>
                                        <input id="principal_email" name="principal_email" type="email" value="{{ old('principal_email', $department?->principal_email) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="established_year">Established Year</label>
                                        <input id="established_year" name="established_year" type="number" min="1900" max="2200" value="{{ old('established_year', $department?->established_year) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field">
                                        <label class="settings-field-label" for="registration_number">Registration No.</label>
                                        <input id="registration_number" name="registration_number" type="text" value="{{ old('registration_number', $department?->registration_number) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                    </div>
                                    <div class="settings-field sm:col-span-2">
                                        <label class="settings-field-label" for="address">Address (English)</label>
                                        <textarea id="address" name="address" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">{{ old('address', $department?->address) }}</textarea>
                                    </div>
                                    <div class="settings-field sm:col-span-2">
                                        <label class="settings-field-label" for="address_nepali">Address (Nepali)</label>
                                        <textarea id="address_nepali" name="address_nepali" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">{{ old('address_nepali', $department?->address_nepali) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button type="submit" class="settings-action-btn primary"><i class="bi bi-save"></i>Save Department</button>
                                <a href="{{ route('admin.department.edit') }}" class="settings-action-btn secondary"><i class="bi bi-pencil-square"></i>Edit In Full Page</a>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

            <section id="profile-pane" class="tab-pane hidden space-y-6">
                <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="settings-card border border-slate-200">
                        <div class="settings-card-header border-b border-slate-200 px-5 py-4">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <i class="bi bi-person-circle text-blue-600"></i>Admin Profile
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            @if(session('status') === 'profile-updated')
                                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                                    Profile updated successfully.
                                </div>
                            @endif

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
                                <div class="settings-field">
                                    <div class="settings-field-label text-center">Profile Photo</div>
                                    <div class="mt-4 flex flex-col items-center justify-center">
                                        @if(!empty($user?->profile_photo_path) && $user?->profile_photo_url)
                                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-full border-2 border-slate-200 object-cover shadow-sm">
                                        @else
                                            <div class="flex h-32 w-32 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-5xl font-bold text-white shadow-sm">
                                                {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="mt-4 w-full">
                                            <label for="admin_profile_photo" class="settings-action-btn secondary w-full justify-center">
                                                <i class="bi bi-upload"></i>Choose Photo
                                            </label>
                                            <input id="admin_profile_photo" name="photo" type="file" accept="image/*" class="hidden">
                                            <p class="mt-2 text-xs text-slate-500">JPG, PNG, GIF, or SVG. Max 5 MB.</p>
                                            @error('photo')
                                                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div class="settings-field">
                                            <label class="settings-field-label" for="admin_name">Full Name</label>
                                            <input id="admin_name" name="name" type="text" value="{{ old('name', $user->name) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2" required autofocus>
                                            @error('name')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="settings-field">
                                            <label class="settings-field-label" for="admin_username">Username</label>
                                            <input id="admin_username" name="username" type="text" value="{{ old('username', $user->username ?? '') }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2" placeholder="Optional username">
                                            @error('username')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="settings-field">
                                            <label class="settings-field-label" for="admin_email">Email</label>
                                            <input id="admin_email" name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2" required>
                                            @error('email')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                                            <p class="mt-2 text-xs text-slate-500">Changing the email will require verification again.</p>
                                        </div>
                                        <div class="settings-field">
                                            <label class="settings-field-label" for="admin_phone">Phone</label>
                                            <input id="admin_phone" name="phone" type="tel" value="{{ old('phone', $user->phone ?? '') }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                            @error('phone')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="settings-field sm:col-span-2">
                                            <label class="settings-field-label" for="admin_department">Department</label>
                                            <input id="admin_department" name="department" type="text" value="{{ old('department', $user->department ?? '') }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2">
                                            @error('department')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                        <div class="settings-field">
                                            <div class="settings-field-label">Role</div>
                                            <div class="settings-field-value">{{ ucfirst($user->role ?? 'user') }}</div>
                                        </div>
                                        <div class="settings-field">
                                            <div class="settings-field-label">Email Status</div>
                                            <div class="settings-field-value {{ $user?->hasVerifiedEmail() ? 'text-emerald-600' : 'text-amber-600' }}">
                                                {{ $user?->hasVerifiedEmail() ? 'Verified' : 'Unverified' }}
                                            </div>
                                        </div>
                                        <div class="settings-field">
                                            <div class="settings-field-label">Member Since</div>
                                            <div class="settings-field-value">{{ optional($user->created_at)->format('M d, Y') ?? '—' }}</div>
                                        </div>
                                        <div class="settings-field">
                                            <div class="settings-field-label">Last Updated</div>
                                            <div class="settings-field-value">{{ optional($user->updated_at)->format('M d, Y h:i A') ?? '—' }}</div>
                                        </div>
                                    </div>

                                    <div class="settings-field">
                                        <label class="settings-field-label" for="admin_bio">Bio</label>
                                        <textarea id="admin_bio" name="bio" rows="4" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2" placeholder="Short bio...">{{ old('bio', $user->bio ?? '') }}</textarea>
                                        @error('bio')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3">
                                        <button type="submit" class="settings-action-btn inline-flex items-center gap-2 bg-blue-600 px-5 py-3 text-sm text-white hover:bg-blue-700">
                                            <i class="bi bi-save"></i>Save Profile
                                        </button>
                                        <a href="{{ route('admin.settings') }}" class="settings-action-btn inline-flex items-center gap-2 border border-slate-300 bg-white px-5 py-3 text-sm text-slate-700 hover:bg-slate-50">
                                            <i class="bi bi-arrow-clockwise"></i>Reset View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

            <section id="security-pane" class="tab-pane hidden">
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="notification_email_enabled" value="{{ !empty($notificationSettings['notification_email_enabled']) ? 1 : 0 }}">
                    <input type="hidden" name="notification_email_exam" value="{{ !empty($notificationSettings['notification_email_exam']) ? 1 : 0 }}">
                    <input type="hidden" name="notification_email_attendance" value="{{ !empty($notificationSettings['notification_email_attendance']) ? 1 : 0 }}">
                    <input type="hidden" name="notification_email_student" value="{{ !empty($notificationSettings['notification_email_student']) ? 1 : 0 }}">
                    <input type="hidden" name="notification_email_assignment" value="{{ !empty($notificationSettings['notification_email_assignment']) ? 1 : 0 }}">
                    <input type="hidden" name="notification_email_result" value="{{ !empty($notificationSettings['notification_email_result']) ? 1 : 0 }}">
                    <div class="settings-card border border-slate-200">
                        <div class="settings-card-header border-b border-slate-200 px-5 py-4"><h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900"><i class="bi bi-shield-lock text-blue-600"></i>Security</h3></div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <div class="settings-panel border border-slate-200 p-5">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Password Policy</div>
                                    <div class="mt-4 grid grid-cols-2 gap-4">
                                        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Minimum length</label><input type="number" min="8" max="64" name="security_password_min_length" value="{{ old('security_password_min_length', $passwordPolicy['min_length']) }}" class="settings-input w-full px-4 py-3 text-sm outline-none"></div>
                                        <div><label class="mb-2 block text-sm font-semibold text-slate-700">2FA expiry (minutes)</label><input type="number" min="3" max="60" name="security_two_factor_expiry_minutes" value="{{ old('security_two_factor_expiry_minutes', $securitySettings['security_two_factor_expiry_minutes'] ?? 10) }}" class="settings-input w-full px-4 py-3 text-sm outline-none"></div>
                                    </div>
                                    <div class="mt-4 grid grid-cols-1 gap-3">
                                        @foreach(['security_password_require_uppercase' => 'Require uppercase letter','security_password_require_lowercase' => 'Require lowercase letter','security_password_require_number' => 'Require number','security_password_require_symbol' => 'Require symbol'] as $key => $label)
                                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700"><input type="checkbox" name="{{ $key }}" value="1" class="settings-switch h-4 w-4 rounded border-slate-300" @checked(old($key, $passwordPolicy[str_replace('security_password_require_', 'require_', $key)] ?? false))><span>{{ $label }}</span></label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="settings-panel border border-slate-200 p-5">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Two-Factor Authentication</div>
                                    <div class="mt-4 space-y-4">
                                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700"><input type="checkbox" name="security_two_factor_enabled" value="1" class="settings-switch h-4 w-4 rounded border-slate-300" @checked(old('security_two_factor_enabled', $securitySettings['security_two_factor_enabled'] ?? false))><span>Enable 2FA for selected roles</span></label>
                                        <div class="grid grid-cols-1 gap-3">
                                            @foreach(['admin' => 'Admin','teacher' => 'Teacher','student' => 'Student','parent' => 'Parent'] as $roleValue => $roleLabel)
                                                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700"><input type="checkbox" name="security_two_factor_roles[]" value="{{ $roleValue }}" class="settings-switch h-4 w-4 rounded border-slate-300" @checked(in_array($roleValue, old('security_two_factor_roles', $twoFactorRoles), true))><span>{{ $roleLabel }}</span></label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3"><button type="submit" class="settings-action-btn inline-flex items-center gap-2 bg-blue-600 px-5 py-3 text-sm text-white hover:bg-blue-700"><i class="bi bi-save"></i>Save Settings</button><a href="{{ route('admin.settings') }}" class="settings-action-btn inline-flex items-center gap-2 border border-slate-300 bg-white px-5 py-3 text-sm text-slate-700 hover:bg-slate-50"><i class="bi bi-arrow-clockwise"></i>Reset View</a></div>
                        </div>
                    </div>
                </form>
            </section>

            <section id="notifications-pane" class="tab-pane hidden">
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="security_password_min_length" value="{{ old('security_password_min_length', $passwordPolicy['min_length']) }}">
                    <input type="hidden" name="security_two_factor_enabled" value="{{ !empty($securitySettings['security_two_factor_enabled']) ? 1 : 0 }}">
                    <input type="hidden" name="security_two_factor_expiry_minutes" value="{{ old('security_two_factor_expiry_minutes', $securitySettings['security_two_factor_expiry_minutes'] ?? 10) }}">
                    <input type="hidden" name="security_password_require_uppercase" value="{{ $passwordPolicy['require_uppercase'] ? 1 : 0 }}">
                    <input type="hidden" name="security_password_require_lowercase" value="{{ $passwordPolicy['require_lowercase'] ? 1 : 0 }}">
                    <input type="hidden" name="security_password_require_number" value="{{ $passwordPolicy['require_number'] ? 1 : 0 }}">
                    <input type="hidden" name="security_password_require_symbol" value="{{ $passwordPolicy['require_symbol'] ? 1 : 0 }}">
                    @foreach(old('security_two_factor_roles', $twoFactorRoles) as $role)
                        <input type="hidden" name="security_two_factor_roles[]" value="{{ $role }}">
                    @endforeach
                    <div class="settings-card border border-slate-200">
                        <div class="settings-card-header border-b border-slate-200 px-5 py-4"><h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900"><i class="bi bi-bell text-blue-600"></i>Notifications</h3></div>
                        <div class="p-6 space-y-6">
                            <div class="settings-panel border border-slate-200 p-5">
                                <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Email Notifications</div>
                                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                                    @foreach(['notification_email_enabled' => 'Enable email delivery','notification_email_exam' => 'Exam emails','notification_email_attendance' => 'Attendance emails','notification_email_student' => 'Student update emails','notification_email_assignment' => 'Notice / assignment emails','notification_email_result' => 'Result emails'] as $key => $label)
                                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700"><input type="checkbox" name="{{ $key }}" value="1" class="settings-switch h-4 w-4 rounded border-slate-300" @checked(old($key, $notificationSettings[$key] ?? false))><span>{{ $label }}</span></label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3"><button type="submit" class="settings-action-btn inline-flex items-center gap-2 bg-blue-600 px-5 py-3 text-sm text-white hover:bg-blue-700"><i class="bi bi-save"></i>Save Settings</button><a href="{{ route('admin.settings') }}" class="settings-action-btn inline-flex items-center gap-2 border border-slate-300 bg-white px-5 py-3 text-sm text-slate-700 hover:bg-slate-50"><i class="bi bi-arrow-clockwise"></i>Reset View</a></div>
                        </div>
                    </div>
                </form>
            </section>

            <section id="info-pane" class="tab-pane hidden space-y-6">
                <div class="settings-card border border-slate-200">
                    <div class="settings-card-header border-b border-slate-200 px-5 py-4"><h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900"><i class="bi bi-info-circle text-blue-600"></i>System Info</h3></div>
                    <div class="p-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="settings-field"><div class="settings-field-label">Laravel Version</div><div class="settings-field-value">{{ $systemInfo['laravel'] }}</div></div>
                        <div class="settings-field"><div class="settings-field-label">PHP Version</div><div class="settings-field-value">{{ $systemInfo['php'] }}</div></div>
                        <div class="settings-field"><div class="settings-field-label">Environment</div><div class="settings-field-value text-emerald-600">{{ $systemInfo['environment'] }}</div></div>
                    </div>
                </div>
                <div class="settings-card border border-slate-200">
                    <div class="settings-card-header border-b border-slate-200 px-5 py-4"><h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900"><i class="bi bi-check2-square text-blue-600"></i>Current State</h3></div>
                    <div class="p-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="settings-field"><div class="settings-field-label">Email Delivery</div><div class="settings-field-value">{{ !empty($notificationSettings['notification_email_enabled']) ? 'Enabled' : 'Disabled' }}</div></div>
                        <div class="settings-field"><div class="settings-field-label">2FA</div><div class="settings-field-value">{{ !empty($securitySettings['security_two_factor_enabled']) ? 'Enabled' : 'Disabled' }}</div></div>
                        <div class="settings-field"><div class="settings-field-label">Protected Roles</div><div class="settings-field-value">{{ collect($twoFactorRoles)->map(fn ($role) => ucfirst($role))->join(', ') }}</div></div>
                        <div class="settings-field"><div class="settings-field-label">Password Min Length</div><div class="settings-field-value">{{ $passwordPolicy['min_length'] }}</div></div>
                    </div>
                </div>
            </section>

            <section id="password-pane" class="tab-pane hidden">
                <div class="settings-card border border-slate-200">
                    <div class="settings-card-header border-b border-slate-200 px-5 py-4"><h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900"><i class="bi bi-key text-blue-600"></i>Change Password</h3></div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Current password</label><input type="password" name="current_password" id="adminCurrentPassword" class="settings-input w-full px-4 py-3 text-sm outline-none" autocomplete="current-password" required>@error('current_password')<div class="mt-2 text-xs font-medium text-red-600">{{ $message }}</div>@enderror</div>
                                <div><label class="mb-2 block text-sm font-semibold text-slate-700">New password</label><input type="password" name="password" id="adminNewPassword" class="settings-input w-full px-4 py-3 text-sm outline-none" autocomplete="new-password" required>@error('password')<div class="mt-2 text-xs font-medium text-red-600">{{ $message }}</div>@enderror</div>
                                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Confirm password</label><input type="password" name="password_confirmation" id="adminConfirmPassword" class="settings-input w-full px-4 py-3 text-sm outline-none" autocomplete="new-password" required></div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div><div class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Live password validation</div><div class="mt-1 text-sm text-slate-600">The requirements below update while you type.</div></div>
                                    <div id="passwordMatchBadge" class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">Waiting for input</div>
                                </div>
                                <div class="mt-4 space-y-3" id="passwordRuleList" data-min-length="{{ $passwordPolicy['min_length'] }}" data-require-uppercase="{{ $passwordPolicy['require_uppercase'] ? '1' : '0' }}" data-require-lowercase="{{ $passwordPolicy['require_lowercase'] ? '1' : '0' }}" data-require-number="{{ $passwordPolicy['require_number'] ? '1' : '0' }}" data-require-symbol="{{ $passwordPolicy['require_symbol'] ? '1' : '0' }}">
                                    <div class="rule-row fail rounded-2xl border px-4 py-3 flex items-center justify-between" data-rule="length"><span>Minimum {{ $passwordPolicy['min_length'] }} characters</span><span class="text-xs font-bold">Pending</span></div>
                                    <div class="rule-row fail rounded-2xl border px-4 py-3 flex items-center justify-between" data-rule="uppercase"><span>Uppercase required</span><span class="text-xs font-bold">Pending</span></div>
                                    <div class="rule-row fail rounded-2xl border px-4 py-3 flex items-center justify-between" data-rule="lowercase"><span>Lowercase required</span><span class="text-xs font-bold">Pending</span></div>
                                    <div class="rule-row fail rounded-2xl border px-4 py-3 flex items-center justify-between" data-rule="number"><span>Number required</span><span class="text-xs font-bold">Pending</span></div>
                                    <div class="rule-row fail rounded-2xl border px-4 py-3 flex items-center justify-between" data-rule="symbol"><span>Symbol required</span><span class="text-xs font-bold">Pending</span></div>
                                    <div class="rule-row fail rounded-2xl border px-4 py-3 flex items-center justify-between" data-rule="match"><span>Passwords match</span><span class="text-xs font-bold">Pending</span></div>
                                </div>
                                <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-xs text-slate-500" id="passwordPolicySummary">Password policy will update as you type.</div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3"><button type="submit" class="settings-action-btn inline-flex items-center gap-2 bg-slate-900 px-5 py-3 text-sm text-white hover:bg-slate-800"><i class="bi bi-shield-lock"></i>Update Password</button></div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = Array.from(document.querySelectorAll('.settings-tab-btn'));
    const panes = {
        department: document.getElementById('department-pane'),
        profile: document.getElementById('profile-pane'),
        security: document.getElementById('security-pane'),
        notifications: document.getElementById('notifications-pane'),
        info: document.getElementById('info-pane'),
        password: document.getElementById('password-pane'),
    };

    const showTab = (tab) => {
        tabButtons.forEach((button) => button.classList.toggle('active', button.dataset.tab === tab));
        Object.entries(panes).forEach(([key, pane]) => pane && pane.classList.toggle('hidden', key !== tab));
    };

    tabButtons.forEach((button) => button.addEventListener('click', () => showTab(button.dataset.tab)));
    showTab('department');

    @if($statusMessage === 'Profile updated successfully.')
        showTab('profile');
    @elseif($errors->hasAny(['name', 'username', 'email', 'phone', 'department', 'bio', 'photo']))
        showTab('profile');
    @elseif($errors->has('current_password') || $errors->has('password'))
        showTab('password');
    @elseif($errors->has('notification_email_enabled') || $errors->has('notification_email_exam') || $errors->has('notification_email_attendance') || $errors->has('notification_email_student') || $errors->has('notification_email_assignment') || $errors->has('notification_email_result'))
        showTab('notifications');
    @elseif($errors->has('security_password_min_length') || $errors->has('security_two_factor_expiry_minutes') || $errors->has('security_two_factor_roles'))
        showTab('security');
    @endif

    const ruleList = document.getElementById('passwordRuleList');
    const newPassword = document.getElementById('adminNewPassword');
    const confirmPassword = document.getElementById('adminConfirmPassword');
    const passwordMatchBadge = document.getElementById('passwordMatchBadge');
    const passwordPolicySummary = document.getElementById('passwordPolicySummary');

    if (ruleList && newPassword && confirmPassword && passwordMatchBadge && passwordPolicySummary) {
        const minLength = parseInt(ruleList.dataset.minLength || '10', 10);
        const requireUppercase = ruleList.dataset.requireUppercase === '1';
        const requireLowercase = ruleList.dataset.requireLowercase === '1';
        const requireNumber = ruleList.dataset.requireNumber === '1';
        const requireSymbol = ruleList.dataset.requireSymbol === '1';
        const rows = {
            length: ruleList.querySelector('[data-rule="length"]'),
            uppercase: ruleList.querySelector('[data-rule="uppercase"]'),
            lowercase: ruleList.querySelector('[data-rule="lowercase"]'),
            number: ruleList.querySelector('[data-rule="number"]'),
            symbol: ruleList.querySelector('[data-rule="symbol"]'),
            match: ruleList.querySelector('[data-rule="match"]'),
        };

        const setRowState = (row, passed) => {
            if (!row) return;
            row.classList.toggle('pass', passed);
            row.classList.toggle('fail', !passed);
            const state = row.querySelector('span.text-xs');
            if (state) {
                state.textContent = passed ? 'Met' : 'Pending';
            }
        };

        const sync = () => {
            const value = newPassword.value || '';
            const confirmValue = confirmPassword.value || '';
            const hasLength = value.length >= minLength;
            const hasUppercase = /[A-Z]/.test(value);
            const hasLowercase = /[a-z]/.test(value);
            const hasNumber = /\d/.test(value);
            const hasSymbol = /[^A-Za-z0-9]/.test(value);
            const isMatch = value.length > 0 && confirmValue.length > 0 && value === confirmValue;

            setRowState(rows.length, hasLength);
            setRowState(rows.uppercase, !requireUppercase || hasUppercase);
            setRowState(rows.lowercase, !requireLowercase || hasLowercase);
            setRowState(rows.number, !requireNumber || hasNumber);
            setRowState(rows.symbol, !requireSymbol || hasSymbol);
            setRowState(rows.match, isMatch);

            const missing = [];
            if (!hasLength) missing.push(`minimum ${minLength} characters`);
            if (requireUppercase && !hasUppercase) missing.push('uppercase');
            if (requireLowercase && !hasLowercase) missing.push('lowercase');
            if (requireNumber && !hasNumber) missing.push('number');
            if (requireSymbol && !hasSymbol) missing.push('symbol');
            if (!isMatch) missing.push('matching confirmation');

            const passed = [hasLength, !requireUppercase || hasUppercase, !requireLowercase || hasLowercase, !requireNumber || hasNumber, !requireSymbol || hasSymbol, isMatch].filter(Boolean).length;
            passwordPolicySummary.textContent = missing.length === 0 ? 'Password meets all current requirements.' : 'Still need: ' + missing.join(', ');

            if (!value && !confirmValue) {
                passwordMatchBadge.textContent = 'Waiting for input';
                passwordMatchBadge.className = 'rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600';
            } else if (isMatch && passed === 6) {
                passwordMatchBadge.textContent = 'All checks passed';
                passwordMatchBadge.className = 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
            } else if (isMatch) {
                passwordMatchBadge.textContent = 'Passwords match';
                passwordMatchBadge.className = 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
            } else {
                passwordMatchBadge.textContent = 'Passwords do not match';
                passwordMatchBadge.className = 'rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700';
            }
        };

        newPassword.addEventListener('input', sync);
        confirmPassword.addEventListener('input', sync);
        sync();
    }
});
</script>
@endsection
