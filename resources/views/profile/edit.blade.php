@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="space-y-6">
    <!-- Profile Info Card -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-gray-900 font-semibold text-base flex items-center gap-2">
                <i class="bi bi-person-badge text-gray-500"></i>
                Profile Information
            </h3>
        </div>
        <div class="p-6">
            <form id="profileInfoForm" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Profile Photo -->
                    <div class="flex flex-col items-center">
                        <label class="block text-sm font-medium text-gray-900 mb-3">Profile Photo</label>
                        <img id="profilePhotoPreview" src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=150&background=random' }}" alt="Profile photo" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm" />
                        <input id="photo" name="photo" type="file" accept="image/*" class="mt-3 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100" />
                        @error('photo')
                            <p class="text-xs text-red-600 mt-2 text-center">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2 text-center">JPG/PNG up to 5 MB</p>
                    </div>

                    <!-- Profile Fields -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                                @error('name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                                @error('email')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" autocomplete="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                                @error('phone')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <input type="text" id="department" name="department" value="{{ old('department', $user->department ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                                @error('department')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea id="bio" name="bio" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Tell us about yourself...">{{ old('bio', $user->bio ?? '') }}</textarea>
                            @error('bio')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                                <i class="bi bi-check-lg mr-1"></i>
                                Save Changes
                            </button>
                            @if(session('status') === 'profile-updated')
                                <p class="text-sm text-green-600 flex items-center gap-1">
                                    <i class="bi bi-check-circle"></i>
                                    Saved successfully!
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-exclamation-triangle text-yellow-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Your email address is unverified.</p>
                            <p class="text-xs text-yellow-700 mt-1">Please verify your email address to access all features.</p>
                            <form method="post" action="{{ route('verification.send') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="text-xs text-yellow-800 hover:text-yellow-900 underline">Resend verification email</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Password Update Card -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                <i class="bi bi-key text-gray-500"></i>
                Update Password
            </h3>
        </div>
        <div class="p-6">
            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('put')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        @error('current_password', 'updatePassword')
                            <p class="text-xs text-red-600 mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                        @enderror
                    </div>
                    <div></div>
                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="update_password_password" name="password" autocomplete="new-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        @error('password', 'updatePassword')
                            <p class="text-xs text-red-600 mt-1">{{ $errors->updatePassword->first('password') }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        @error('password_confirmation', 'updatePassword')
                            <p class="text-xs text-red-600 mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="bi bi-shield-check mr-1"></i>
                        Update Password
                    </button>
                    @if(session('status') === 'password-updated')
                        <p class="text-sm text-green-600 flex items-center gap-1">
                            <i class="bi bi-check-circle"></i>
                            Password updated!
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Card -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                <i class="bi bi-exclamation-triangle text-gray-500"></i>
                Delete Account
            </h3>
        </div>
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">Permanently delete your account</p>
                    <p class="text-xs text-gray-600 mt-1">Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.</p>
                </div>
                <button type="button" onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium transition">
                    <i class="bi bi-trash mr-1"></i>
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-gray-900 font-semibold text-sm">Confirm Account Deletion</h3>
        </div>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')
            
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to delete your account? This action cannot be undone. Please enter your password to confirm.</p>
            
            <div class="mb-4">
                <input type="password" name="password" placeholder="Enter your password" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required />
                @error('password', 'userDeletion')
                    <p class="text-xs text-red-600 mt-1">{{ $errors->userDeletion->first('password') }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="bi bi-trash mr-1"></i>
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileInfoForm');
    const saveBtn = form ? form.querySelector('button[type="submit"]') : null;
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('profilePhotoPreview');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function(e) {
            const file = this.files && this.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                photoPreview.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endsection

