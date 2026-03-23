@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-container" class="fixed top-4 right-4 z-[1001] space-y-2"></div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: College Details & Profile -->
        <div class="lg:col-span-2 space-y-6">
            <!-- College Details Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-building text-gray-500"></i>
                        College Details
                    </h3>
                </div>
                <div class="p-6">
                    <form id="collegeForm" method="POST" action="{{ route('admin.settings.college.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <!-- Logo Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">College Logo</label>
                                <div class="relative">
                                    <div id="logoPreview" class="w-full h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50 overflow-hidden cursor-pointer hover:bg-gray-100 transition" onclick="document.getElementById('logoInput').click()">
                                        @if($college && $college->logo_path)
                                            <img src="{{ $college->getLogoUrl() }}" alt="College Logo" class="h-full w-full object-contain p-2">
                                        @else
                                            <div class="text-center">
                                                <i class="bi bi-image text-3xl text-gray-400"></i>
                                                <p class="text-sm text-gray-500 mt-2">Click to upload logo</p>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" id="logoInput" name="logo" class="hidden" accept="image/*" onchange="handleLogoUpload(event)">
                                    @if($college && $college->logo_path)
                                        <button type="button" class="absolute top-2 right-2 bg-blue-500 text-white rounded-full p-1 hover:bg-blue-600 transition" onclick="deleteLogo()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">College Name</label>
                                    <input type="text" name="name" value="{{ $college->name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">College Name (Nepali)</label>
                                    <input type="text" name="name_nepali" value="{{ $college->name_nepali ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Short Name</label>
                                    <input type="text" name="short_name" value="{{ $college->short_name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Contact Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Phone</label>
                                    <input type="tel" name="phone" value="{{ $college->phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ $college->email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Website</label>
                                    <input type="url" name="website" value="{{ $college->website ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Address</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Address</label>
                                    <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $college->address ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Address (Nepali)</label>
                                    <textarea name="address_nepali" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $college->address_nepali ?? '' }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-1">City</label>
                                        <input type="text" name="city" value="{{ $college->city ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-1">District</label>
                                        <input type="text" name="district" value="{{ $college->district ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-1">Province</label>
                                        <input type="text" name="province" value="{{ $college->province ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Principal Information -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Principal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Principal Name</label>
                                    <input type="text" name="principal_name" value="{{ $college->principal_name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Principal Phone</label>
                                    <input type="tel" name="principal_phone" value="{{ $college->principal_phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Principal Email</label>
                                    <input type="email" name="principal_email" value="{{ $college->principal_email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Additional Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Established Year</label>
                                    <input type="number" name="established_year" value="{{ $college->established_year ?? '' }}" min="1900" max="{{ date('Y') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Registration Number</label>
                                    <input type="text" name="registration_number" value="{{ $college->registration_number ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-900 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $college->description ?? '' }}</textarea>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-900 mb-1">Description (Nepali)</label>
                                <textarea name="description_nepali" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $college->description_nepali ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="border-t pt-4">
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                <i class="bi bi-check-circle mr-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Admin Profile Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-person-circle text-gray-500"></i>
                        Admin Profile
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex items-start gap-4">
                        @php
                            $user = Auth::user();
                            $photoPath = $user->profile_photo_path;
                            $hasFile = !empty($photoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath);
                        @endphp
                        @if($hasFile)
                            <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300 flex-shrink-0">
                        @else
                            <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-orange-600 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                                {{ substr($user->name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</h4>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            <p class="text-xs text-gray-500 mt-1 capitalize">Role: {{ Auth::user()->role }}</p>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                                    <i class="bi bi-pencil mr-1"></i>
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-shield-lock text-gray-500"></i>
                        Account Security
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i class="bi bi-key text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Password</p>
                                <p class="text-xs text-gray-500">Last changed: Never</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Change</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: System Info & Quick Links -->
        <div class="space-y-6">
            <!-- System Information -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-info-circle text-gray-500"></i>
                        System Info
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">Laravel Version</span>
                        <span class="text-xs font-medium text-gray-900">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">PHP Version</span>
                        <span class="text-xs font-medium text-gray-900">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-600">Environment</span>
                        <span class="text-xs font-medium text-green-600">Local</span>
                    </div>
                </div>
            </div>

            <!-- Logout -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium transition">
                            <i class="bi bi-box-arrow-right"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle Logo Preview
    function handleLogoUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const logoPreview = document.getElementById('logoPreview');
            logoPreview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="h-full w-full object-contain p-2">`;
        };
        reader.readAsDataURL(file);
    }

    // Delete Logo
    function deleteLogo() {
        if (!confirm('Are you sure you want to delete the logo?')) return;

        fetch('{{ route("admin.settings.college.logo.delete") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('logoPreview').innerHTML = `
                    <div class="text-center">
                        <i class="bi bi-image text-3xl text-gray-400"></i>
                        <p class="text-sm text-gray-500 mt-2">Click to upload logo</p>
                    </div>
                `;
                document.getElementById('logoInput').value = '';
                showToast('Logo deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 1500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting logo', 'error');
        });
    }

    // Form Submission
    document.getElementById('collegeForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        
        // Remove logo field if no file is selected
        const logoInput = document.getElementById('logoInput');
        if (!logoInput || !logoInput.files || logoInput.files.length === 0) {
            formData.delete('logo');
        }
        
        showLoader(true, 'Saving college details...');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const submitUrl = form.action;
            
            const response = await fetch(submitUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                showToast('Server error. Please check console for details.', 'error');
                showLoader(false);
                return;
            }

            const data = await response.json();

            if (data.success) {
                showToast(data.message || 'College details saved successfully', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                let errorMessage = data.message || 'Error saving college details';
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (firstError && firstError[0]) {
                        errorMessage = firstError[0];
                    }
                }
                showToast(errorMessage, 'error');
                showLoader(false);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error saving college details: ' + error.message, 'error');
            showLoader(false);
        }
    });

    // Show Toast Notification
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-blue-500' : 'bg-blue-500';
        const icon = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-circle' : 'bi-info-circle';

        const toast = document.createElement('div');
        toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2`;
        toast.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;

        container.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 4000);
    }

    // Show/Hide Global Loader
    function showLoader(show, message = 'Loading...') {
        const loader = document.getElementById('globalLoader');
        const text = document.getElementById('loaderText');
        
        if (show) {
            text.textContent = message;
            loader.classList.remove('hidden');
        } else {
            loader.classList.add('hidden');
        }
    }
</script>
@endsection


