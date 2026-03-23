@extends('admin.layouts.app')

@section('title', 'Department Details')

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

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                <i class="bi bi-building text-gray-500"></i>
                Department Details
            </h3>
            <a href="{{ route('admin.settings') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Back to Settings</a>
        </div>

        <div class="p-6">
            <form id="departmentForm" method="POST" action="{{ route('admin.department.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Logo + Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Department Logo</label>
                        <div class="relative">
                            <div id="logoPreview" class="w-full h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50 overflow-hidden cursor-pointer hover:bg-gray-100 transition" onclick="document.getElementById('logoInput').click()">
                                @if($department && $department->logo_path)
                                    <img src="{{ $department->getLogoUrl() }}" alt="Department Logo" class="h-full w-full object-contain p-2">
                                @else
                                    <div class="text-center">
                                        <i class="bi bi-image text-3xl text-gray-400"></i>
                                        <p class="text-sm text-gray-500 mt-2">Click to upload logo</p>
                                    </div>
                                @endif
                            </div>
                            <input type="file" id="logoInput" name="logo" class="hidden" accept="image/*" onchange="handleLogoUpload(event)">
                            @if($department && $department->logo_path)
                                <button type="button" class="absolute top-2 right-2 bg-blue-500 text-white rounded-full p-1 hover:bg-blue-600 transition" onclick="deleteLogo()">
                                    <i class="bi bi-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Department Name</label>
                            <input type="text" name="name" value="{{ $department->name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Department Name (Nepali)</label>
                            <input type="text" name="name_nepali" value="{{ $department->name_nepali ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Short Name</label>
                            <input type="text" name="short_name" value="{{ $department->short_name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Contact Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ $department->phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Email</label>
                            <input type="email" name="email" value="{{ $department->email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Website</label>
                            <input type="text" name="website" value="{{ $department->website ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Address</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Address</label>
                            <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $department->address ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Address (Nepali)</label>
                            <textarea name="address_nepali" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $department->address_nepali ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">City</label>
                            <input type="text" name="city" value="{{ $department->city ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">District</label>
                            <input type="text" name="district" value="{{ $department->district ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Province</label>
                            <input type="text" name="province" value="{{ $department->province ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Map & Location</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Latitude</label>
                            <input type="text" name="latitude" value="{{ $department->latitude ?? '' }}" placeholder="e.g. 27.7172"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Longitude</label>
                            <input type="text" name="longitude" value="{{ $department->longitude ?? '' }}" placeholder="e.g. 85.3240"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Map Embed URL (optional)</label>
                        <input type="text" name="map_embed_url" value="{{ $department->map_embed_url ?? '' }}" placeholder="Paste Google Maps embed URL or any iframe src URL"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Map Label (optional)</label>
                        <input type="text" name="map_label" value="{{ $department->map_label ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                </div>

                <!-- Principal -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">HOD / Principal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Name</label>
                            <input type="text" name="principal_name" value="{{ $department->principal_name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Phone</label>
                            <input type="text" name="principal_phone" value="{{ $department->principal_phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Email</label>
                            <input type="email" name="principal_email" value="{{ $department->principal_email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>
                </div>

                <!-- About -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">About</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Established Year</label>
                            <input type="number" name="established_year" value="{{ $department->established_year ?? '' }}" min="1900" max="{{ date('Y') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Registration Number</label>
                            <input type="text" name="registration_number" value="{{ $department->registration_number ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $department->description ?? '' }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Description (Nepali)</label>
                        <textarea name="description_nepali" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $department->description_nepali ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Landing -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Landing Page</h4>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Hero Photos (multiple)</label>
                        <input type="file" name="hero_images[]" multiple accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700" />
                        <label class="mt-3 inline-flex items-center gap-2 text-xs font-medium text-gray-700">
                            <input type="checkbox" name="replace_hero_images" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Replace existing hero photos (deletes old)
                        </label>
                        @if($department && !empty($department->hero_images))
                            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach((array) $department->hero_images as $img)
                                    @if(!empty($img))
                                        <div class="aspect-[4/3] overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                            <img src="{{ asset('storage/' . ltrim($img, '/')) }}" alt="Hero photo" class="h-full w-full object-cover" />
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Programs Title</label>
                        <input type="text" name="programs_title" value="{{ $department->programs_title ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Programs Title (Nepali)</label>
                        <input type="text" name="programs_title_nepali" value="{{ $department->programs_title_nepali ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Programs Content</label>
                        <textarea name="programs_content" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $department->programs_content ?? '' }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Programs Content (Nepali)</label>
                        <textarea name="programs_content_nepali" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">{{ $department->programs_content_nepali ?? '' }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Programs Photo</label>
                        <input type="file" name="programs_image" accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700" />
                        @if($department && !empty($department->programs_image_path))
                            <div class="mt-3 aspect-[16/9] overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                <img src="{{ asset('storage/' . ltrim($department->programs_image_path, '/')) }}" alt="Programs photo" class="h-full w-full object-cover" />
                            </div>
                        @endif
                    </div>
                </div>

                <div class="border-t pt-4">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        <i class="bi bi-check-circle mr-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showLoader(show, text = 'Loading...') {
        const loader = document.getElementById('globalLoader');
        const loaderText = document.getElementById('loaderText');
        if (loaderText) loaderText.textContent = text;
        if (loader) loader.classList.toggle('hidden', !show);
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const colors = {
            success: 'bg-green-600',
            error: 'bg-red-600',
            info: 'bg-blue-600',
        };

        const toast = document.createElement('div');
        toast.className = `${colors[type] || colors.info} text-white px-4 py-3 rounded-lg shadow-lg text-sm`;
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(() => toast.remove(), 2500);
    }

    function handleLogoUpload(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            if (preview) preview.innerHTML = `<img src="${e.target.result}" alt="Logo preview" class="h-full w-full object-contain p-2">`;
        };
        reader.readAsDataURL(file);
    }

    async function deleteLogo() {
        if (!confirm('Delete logo?')) return;
        showLoader(true, 'Deleting logo...');
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch('{{ route("admin.department.logo.delete") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast('Logo deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Error deleting logo', 'error');
                showLoader(false);
            }
        } catch (e) {
            showToast('Error deleting logo', 'error');
            showLoader(false);
        }
    }

    document.getElementById('departmentForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const logoInput = document.getElementById('logoInput');
        if (!logoInput || !logoInput.files || logoInput.files.length === 0) {
            formData.delete('logo');
        }

        showLoader(true, 'Saving department details...');

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
                showToast('Server error. Please check console for details.', 'error');
                showLoader(false);
                return;
            }

            const data = await response.json();

            if (data.success) {
                showToast(data.message || 'Department details saved successfully', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                let errorMessage = data.message || 'Error saving department details';
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (firstError && firstError[0]) errorMessage = firstError[0];
                }
                showToast(errorMessage, 'error');
                showLoader(false);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error saving department details: ' + error.message, 'error');
            showLoader(false);
        }
    });
</script>
@endsection
