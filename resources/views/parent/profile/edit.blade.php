@extends('parent.layouts.parentlayout')

@section('title', __('Profile Settings'))
@section('subtitle', __('Manage your guardian account details and password'))

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
            <form id="profileInfoForm" method="post" action="{{ route('parent.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Profile Photo -->
                    <div class="flex flex-col items-center">
                        <label class="block text-sm font-medium text-gray-900 mb-3">Profile Photo</label>
                        @php
                            $photoPath = $user->profile_photo_path;
                            $hasFile = !empty($photoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath);
                        @endphp
                        @if($hasFile)
                            <img id="profilePhotoPreview" src="{{ asset('storage/' . $photoPath) }}" alt="Profile photo" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm" />
                        @else
                            <div id="profilePhotoPreview" class="w-32 h-32 rounded-full bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center text-white text-5xl font-bold border-4 border-gray-100 shadow-sm">
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
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" autocomplete="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                                @error('phone')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <div class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600">
                                    Parent
                                </div>
                            </div>
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
                        <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                        @error('current_password', 'updatePassword')
                            <p class="text-xs text-red-600 mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                        @enderror
                    </div>
                    <div></div>
                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="update_password_password" name="password" autocomplete="new-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                        @error('password', 'updatePassword')
                            <p class="text-xs text-red-600 mt-1">{{ $errors->updatePassword->first('password') }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                        @error('password_confirmation', 'updatePassword')
                            <p class="text-xs text-red-600 mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileInfoForm');
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('profilePhotoPreview');
    const photoButton = document.querySelector('label[for="photo"]');
    const photoFileName = document.getElementById('photoFileName');
    const photoButtonText = document.getElementById('photoButtonText');

    @if(session('error') === 'duplicate_email')
        alert('This email address is already in use by another account. Please use a different email.');
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

