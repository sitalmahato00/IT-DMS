@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="space-y-6 max-w-none">
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
                        @php
                            $photoPath = $user->profile_photo_url;
                            $hasFile = !empty($photoPath);
                        @endphp
                        @if($hasFile)
                            <img id="profilePhotoPreview" src="{{ $photoPath }}" alt="Profile photo" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm" />
                        @else
                            <div id="profilePhotoPreview" class="w-32 h-32 rounded-full bg-gradient-to-br from-red-600 to-orange-600 flex items-center justify-center text-white text-5xl font-bold border-4 border-gray-100 shadow-sm">
                                {{ substr($user->name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <div class="mt-3 flex flex-col items-center w-full">
                            <label for="photo" class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium cursor-pointer transition">
                                <i class="bi bi-cloud-upload mr-2"></i>
                                <span id="photoButtonText">Choose Photo</span>
                            </label>
                            <input id="photo" name="photo" type="file" accept="image/*" class="hidden" />
                            <p id="photoFileName" class="text-xs text-gray-600 mt-2 text-center"></p>
                        </div>
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
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input type="text" id="username" name="username" value="{{ old('username', $user->username ?? '') }}" autocomplete="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Optional username" />
                                @error('username')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Role</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ ucfirst($user->role ?? 'user') }}</div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email Status</div>
                                <div class="mt-1 text-sm font-semibold {{ $user->hasVerifiedEmail() ? 'text-green-600' : 'text-amber-600' }}">
                                    {{ $user->hasVerifiedEmail() ? 'Verified' : 'Unverified' }}
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Member Since</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ optional($user->created_at)->format('M d, Y') ?? 'Not available' }}
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Last Updated</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ optional($user->updated_at)->format('M d, Y h:i A') ?? 'Not available' }}
                                </div>
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
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
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
        <div class="px-4 py-3 border-b-2 border-red-700 bg-red-600 text-white">
            <h3 class="font-semibold text-sm">Confirm Account Deletion</h3>
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
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('profilePhotoPreview');
    const photoButton = document.querySelector('label[for="photo"]');
    const photoFileName = document.getElementById('photoFileName');
    const photoButtonText = document.getElementById('photoButtonText');

    // Check for duplicate email error and show toast
    @if(session('error') === 'duplicate_email')
        if (typeof showToast === 'function') {
            showToast('This email address is already in use by another account.', 'error', 'Please use a different email');
        } else {
            alert('This email address is already in use by another account. Please use a different email.');
        }
    @endif

    if (photoInput && photoButton) {
        // Make the button clickable to open file input
        photoButton.addEventListener('click', function(e) {
            e.preventDefault();
            photoInput.click();
        });

        // Handle file selection
        photoInput.addEventListener('change', function(e) {
            const file = this.files && this.files[0];
            
            if (!file) {
                photoFileName.textContent = '';
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                photoFileName.textContent = 'Please select an image file';
                photoFileName.classList.add('text-red-600');
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                photoFileName.textContent = 'File size must be less than 5MB';
                photoFileName.classList.add('text-red-600');
                return;
            }

            // Show file name
            photoFileName.textContent = 'Selected: ' + file.name;
            photoFileName.classList.remove('text-red-600');
            photoFileName.classList.add('text-green-600');

            // Update preview
            const reader = new FileReader();
            reader.onload = function(ev) {
                // If preview is a div (avatar), replace with img
                if (photoPreview && photoPreview.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.id = 'profilePhotoPreview';
                    img.alt = 'Profile photo';
                    img.className = 'w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm';
                    img.src = ev.target.result;
                    photoPreview.parentNode.replaceChild(img, photoPreview);
                } else if (photoPreview) {
                    // If it's an img, just update src
                    photoPreview.src = ev.target.result;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // Form submission validation
    if (form) {
        form.addEventListener('submit', function(e) {
            // If a file is selected but invalid, prevent submission
            if (photoInput.files.length > 0) {
                const file = photoInput.files[0];
                if (!file.type.startsWith('image/')) {
                    e.preventDefault();
                    alert('Please select a valid image file');
                    return false;
                }
                if (file.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('File size must be less than 5MB');
                    return false;
                }
            }
        });
    }
});
</script>
@endsection
