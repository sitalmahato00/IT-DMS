@extends('admin.layouts.app')

@section('title', 'Department Settings')

@section('styles')
<style>
    .department-settings-page {
        --department-brand: #FF0037;
        --department-brand-dark: #D90033;
        --department-brand-deep: #B8002B;
        --department-brand-soft: rgba(255, 0, 55, 0.08);
        --department-brand-soft-strong: rgba(255, 0, 55, 0.14);
        --department-brand-border: rgba(255, 0, 55, 0.24);
        --department-brand-ring: rgba(255, 0, 55, 0.16);
        margin: -0.75rem -1.5rem;
        min-height: calc(100vh - 4rem);
        background: #fff;
    }

    .department-settings-page .department-shell {
        width: 100%;
        max-width: none;
        padding-inline: 0;
        min-height: inherit;
    }

    .department-settings-page .department-card {
        min-height: inherit;
        background: transparent;
        border: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .department-settings-page .text-blue-600,
    .department-settings-page .text-blue-500,
    .department-settings-page .text-blue-400 {
        color: var(--department-brand);
    }

    .department-settings-page .border-blue-600,
    .department-settings-page .border-blue-500 {
        border-color: var(--department-brand);
    }

    .department-settings-page .border-blue-300 {
        border-color: var(--department-brand-border);
    }

    .department-settings-page .bg-blue-50 {
        background-color: var(--department-brand-soft);
    }

    .department-settings-page .bg-blue-600,
    .department-settings-page .bg-blue-500 {
        background-color: var(--department-brand);
    }

    .department-settings-page .bg-blue-200 {
        background-color: var(--department-brand-soft-strong);
    }

    .department-settings-page .hover\:bg-blue-100:hover {
        background-color: var(--department-brand-soft-strong);
    }

    .department-settings-page .hover\:text-blue-600:hover,
    .department-settings-page .hover\:text-blue-500:hover {
        color: var(--department-brand);
    }

    .department-settings-page .group:hover .group-hover\:text-blue-500 {
        color: var(--department-brand-dark);
    }

    .department-settings-page .from-blue-600 {
        --tw-gradient-from: var(--department-brand) var(--tw-gradient-from-position);
        --tw-gradient-to: rgb(255 0 55 / 0) var(--tw-gradient-to-position);
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
    }

    .department-settings-page .from-blue-500 {
        --tw-gradient-from: var(--department-brand) var(--tw-gradient-from-position);
        --tw-gradient-to: rgb(255 0 55 / 0) var(--tw-gradient-to-position);
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
    }

    .department-settings-page .to-blue-600 {
        --tw-gradient-to: var(--department-brand-dark) var(--tw-gradient-to-position);
    }

    .department-settings-page .to-blue-700 {
        --tw-gradient-to: var(--department-brand-dark) var(--tw-gradient-to-position);
    }

    .department-settings-page .from-blue-700 {
        --tw-gradient-from: var(--department-brand-dark) var(--tw-gradient-from-position);
        --tw-gradient-to: rgb(217 0 51 / 0) var(--tw-gradient-to-position);
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
    }

    .department-settings-page .to-blue-800 {
        --tw-gradient-to: var(--department-brand-deep) var(--tw-gradient-to-position);
    }

    .department-settings-page .field-input {
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .department-settings-page .field-input:focus {
        border-color: var(--department-brand);
        box-shadow: 0 0 0 4px var(--department-brand-ring);
    }

    .department-settings-page .field-input:hover {
        border-color: var(--department-brand-border);
    }

    .department-settings-page .file\:bg-blue-600::file-selector-button {
        background: linear-gradient(135deg, var(--department-brand) 0%, var(--department-brand-dark) 100%);
        color: #fff;
    }

    .department-settings-page .hover\:file\:bg-blue-700:hover::file-selector-button {
        background: linear-gradient(135deg, var(--department-brand-dark) 0%, var(--department-brand-deep) 100%);
    }

    .department-settings-page .field-input.border-blue-500 {
        background-color: rgba(255, 0, 55, 0.02);
    }
</style>
@endsection

@section('content')
<div class="department-settings-page min-h-screen bg-white">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[1001] space-y-2"></div>

    <div class="department-shell w-full">
        <div class="department-card overflow-hidden">
            <!-- Tab Navigation -->
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200 overflow-x-auto">
                <div class="flex max-w-full px-6">
                    <button class="tab-btn active px-6 py-4 font-semibold text-gray-700 border-b-2 border-blue-600 hover:text-blue-600 transition whitespace-nowrap" data-tab="basic">
                        <i class="bi bi-file-earmark-text mr-2"></i>Basic Info
                    </button>
                    <button class="tab-btn px-6 py-4 font-semibold text-gray-700 border-b-2 border-gray-200 hover:text-blue-600 transition whitespace-nowrap" data-tab="contact">
                        <i class="bi bi-telephone mr-2"></i>Contact
                    </button>
                    <button class="tab-btn px-6 py-4 font-semibold text-gray-700 border-b-2 border-gray-200 hover:text-blue-600 transition whitespace-nowrap" data-tab="location">
                        <i class="bi bi-geo-alt mr-2"></i>Location
                    </button>
                    <button class="tab-btn px-6 py-4 font-semibold text-gray-700 border-b-2 border-gray-200 hover:text-blue-600 transition whitespace-nowrap" data-tab="leader">
                        <i class="bi bi-person-badge mr-2"></i>Leadership
                    </button>
                    <button class="tab-btn px-6 py-4 font-semibold text-gray-700 border-b-2 border-gray-200 hover:text-blue-600 transition whitespace-nowrap" data-tab="details">
                        <i class="bi bi-info-circle mr-2"></i>Details
                    </button>
                    <button class="tab-btn px-6 py-4 font-semibold text-gray-700 border-b-2 border-gray-200 hover:text-blue-600 transition whitespace-nowrap" data-tab="landing">
                        <i class="bi bi-globe mr-2"></i>Landing Page
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form id="departmentForm" method="POST" action="{{ route('admin.department.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="p-8">

                    <!-- ============ BASIC INFO TAB ============ -->
                    <div class="tab-content active" id="basic-content">
                        <div class="space-y-6">
                            <!-- Logo Section -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Logo Upload -->
                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                        <i class="bi bi-image text-blue-600"></i>
                                        College Logo
                                    </label>
                                    <div class="relative">
                                        <div id="logoPreview" class="w-full h-40 border-2 border-dashed border-blue-300 rounded-xl flex items-center justify-center bg-blue-50 overflow-hidden cursor-pointer hover:bg-blue-100 transition group">
                                            @if($department && $department->logo_url)
                                                <img src="{{ $department->logo_url }}" alt="College Logo" class="h-full w-full object-contain p-2">
                                            @else
                                                <div class="text-center">
                                                    <i class="bi bi-cloud-arrow-up text-4xl text-blue-400 group-hover:text-blue-500 transition"></i>
                                                    <p class="text-sm text-blue-600 mt-2 font-medium">Click or drag to upload</p>
                                                    <p class="text-xs text-blue-500">PNG, JPG, GIF, SVG up to 2MB</p>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" id="logoInput" name="logo" class="hidden" accept="image/*" onchange="handleLogoUpload(event)">
                                        @if($department && $department->logo_url)
                                            <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 shadow-lg transition" onclick="deleteLogo()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Department Names -->
                                <div class="lg:col-span-2 space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                                            College Name (English) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name" value="{{ $department->name ?? '' }}" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="Enter college name">
                                        <p class="text-xs text-gray-500 mt-1">English name of your college</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                                            College Name (Nepali) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name_nepali" value="{{ $department->name_nepali ?? '' }}" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="नेपालीमा कलेज नाम लेख्नुहोस्">
                                        <p class="text-xs text-gray-500 mt-1">College name in Nepali</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                                            Department Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="short_name" value="{{ $department->short_name ?? '' }}" 
                                            maxlength="50"
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="Enter department name">
                                        <p class="text-xs text-gray-500 mt-1">Full department name</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ CONTACT INFO TAB ============ -->
                    <div class="tab-content hidden" id="contact-content">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        <i class="bi bi-telephone text-blue-600 mr-2"></i>Phone
                                    </label>
                                    <input type="tel" name="phone" value="{{ $department->phone ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="+977-1-something">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        <i class="bi bi-envelope text-blue-600 mr-2"></i>Email
                                    </label>
                                    <input type="email" name="email" value="{{ $department->email ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="info@department.edu.np">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        <i class="bi bi-globe text-blue-600 mr-2"></i>Website
                                    </label>
                                    <input type="url" name="website" value="{{ $department->website ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="https://department.edu.np">
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="bi bi-geo-alt text-blue-600"></i>
                                    Address
                                </h4>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Address (English)</label>
                                        <textarea name="address" rows="3" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                            placeholder="Street address, building name...">{{ $department->address ?? '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">ठेगाना (Nepali)</label>
                                        <textarea name="address_nepali" rows="3" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                            placeholder="नेपालीमा ठेगाना...">{{ $department->address_nepali ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- City, District, Province -->
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">City</label>
                                    <input type="text" name="city" value="{{ $department->city ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="Kathmandu">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">District</label>
                                    <input type="text" name="district" value="{{ $department->district ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="Kathmandu">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">Province</label>
                                    <input type="text" name="province" value="{{ $department->province ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="Bagmati">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ LOCATION & MAP TAB ============ -->
                    <div class="tab-content hidden" id="location-content">
                        <div class="space-y-6">
                            <!-- Coordinates -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="bi bi-compass text-blue-600"></i>
                                    Coordinates
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Latitude</label>
                                        <input type="number" name="latitude" value="{{ $department->latitude ?? '' }}" 
                                            step="0.00000001" min="-90" max="90"
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="e.g., 27.7172"
                                            onchange="updateMapPreview()">
                                        <p class="text-xs text-gray-500 mt-1">Range: -90 to 90</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Longitude</label>
                                        <input type="number" name="longitude" value="{{ $department->longitude ?? '' }}" 
                                            step="0.00000001" min="-180" max="180"
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="e.g., 85.3240"
                                            onchange="updateMapPreview()">
                                        <p class="text-xs text-gray-500 mt-1">Range: -180 to 180</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Map Embed -->
                            <div class="border-t pt-6">
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    <i class="bi bi-map text-blue-600 mr-2"></i>Map Embed URL <span class="text-xs font-normal text-gray-500">(optional)</span>
                                </label>
                                <input type="text" name="map_embed_url" value="{{ $department->map_embed_url ?? '' }}" 
                                    class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="Paste Google Maps iframe URL or embed code URL"
                                    onchange="updateMapPreview()">
                                <p class="text-xs text-gray-500 mt-1">Use Google Maps embed URL or any iframe src URL</p>
                            </div>

                            <!-- Map Label -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Map Label <span class="text-xs font-normal text-gray-500">(optional)</span></label>
                                <input type="text" name="map_label" value="{{ $department->map_label ?? '' }}" 
                                    class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="e.g., Kathmandu Campus">
                            </div>

                            <!-- Map Preview -->
                            <div class="border-t pt-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="bi bi-eye text-blue-600"></i>
                                    Map Preview
                                </h4>
                                <div id="mapPreview" class="w-full h-96 border-2 border-gray-200 rounded-lg bg-gray-50 overflow-hidden">
                                    <div class="w-full h-full flex items-center justify-center text-gray-500">
                                        <i class="bi bi-map text-4xl"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Map preview updates as you enter coordinates or embed URL</p>
                            </div>
                        </div>
                    </div>

                    <!-- ============ LEADERSHIP SECTION ============ -->
                    <div class="tab-content hidden" id="leader-content">
                        <div class="space-y-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                <i class="bi bi-person-badge text-blue-600"></i>
                                HOD / Principal Information
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">Name</label>
                                    <input type="text" name="principal_name" value="{{ $department->principal_name ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="Full name">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">Phone</label>
                                    <input type="tel" name="principal_phone" value="{{ $department->principal_phone ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="+977-1-xxx-xxxx">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">Email</label>
                                    <input type="email" name="principal_email" value="{{ $department->principal_email ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="hod@department.edu.np">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ DEPARTMENT DETAILS TAB ============ -->
                    <div class="tab-content hidden" id="details-content">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        <i class="bi bi-calendar text-blue-600 mr-2"></i>Established Year
                                    </label>
                                    <input type="number" name="established_year" value="{{ $department->established_year ?? '' }}" 
                                        min="1900" max="{{ date('Y') }}"
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="e.g., 2015">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        <i class="bi bi-hash text-blue-600 mr-2"></i>Registration Number
                                    </label>
                                    <input type="text" name="registration_number" value="{{ $department->registration_number ?? '' }}" 
                                        class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="e.g., REG-2015-001">
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Description (English)
                                </label>
                                <textarea name="description" rows="5" 
                                    class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                    placeholder="Write a brief description about your department...">{{ $department->description ?? '' }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Max 2000 characters. Supports plain text.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    विवरण (Nepali)
                                </label>
                                <textarea name="description_nepali" rows="5" 
                                    class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                    placeholder="नेपालीमा विवरण लेख्नुहोस्...">{{ $department->description_nepali ?? '' }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Max 2000 characters in Nepali</p>
                            </div>
                        </div>
                    </div>

                    <!-- ============ LANDING PAGE TAB ============ -->
                    <div class="tab-content hidden" id="landing-content">
                        <div class="space-y-6">
                            <!-- Hero Images -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="bi bi-images text-blue-600"></i>
                                    Hero Section (Multiple Images)
                                </h4>
                                <div class="mb-4">
                                    <label class="block text-sm text-gray-700 mb-3">Upload hero images for the landing page carousel</label>
                                    <div id="heroDropZone" class="relative w-full border-2 border-dashed border-blue-300 rounded-xl p-8 text-center bg-blue-50 hover:bg-blue-100 transition cursor-pointer">
                                        <input type="file" name="hero_images[]" multiple accept="image/*" id="heroImages" class="hidden">
                                        <div class="pointer-events-none">
                                            <i class="bi bi-cloud-arrow-up text-4xl text-blue-400"></i>
                                            <p class="text-sm text-blue-600 mt-3 font-medium">Drag & drop images or click to upload</p>
                                            <p class="text-xs text-blue-500 mt-1">PNG, JPG, GIF up to 4MB each. Multiple files accepted.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Existing Hero Images -->
                                @if($department && !empty($department->hero_images))
                                    <div class="mt-6">
                                        <p class="text-sm font-semibold text-gray-900 mb-3">Current Images</p>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                            @foreach((array) $department->hero_images as $index => $img)
                                                @if(!empty($img))
                                                    <div class="relative group">
                                                        <div class="aspect-[4/3] overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50 shadow-sm">
                                                            <img src="{{ \App\Support\Media::publicUrl($img) }}" alt="Hero photo" class="h-full w-full object-cover">
                                                        </div>
                                                        <button type="button"
                                                            class="absolute inset-0 m-auto w-10 h-10 rounded-full bg-red-500 text-white shadow-lg transition opacity-0 group-hover:opacity-100 flex items-center justify-center hover:bg-red-600"
                                                            onclick="deleteHeroImage({{ $index }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Programs Section -->
                            <div class="border-t pt-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="bi bi-bookmark text-blue-600"></i>
                                    Programs Section
                                </h4>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Programs Title (English)</label>
                                        <input type="text" name="programs_title" value="{{ $department->programs_title ?? '' }}" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="e.g., Our Programs">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">कार्यक्रम शीर्षक (Nepali)</label>
                                        <input type="text" name="programs_title_nepali" value="{{ $department->programs_title_nepali ?? '' }}" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="नेपालीमा शीर्षक">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Programs Content (English)</label>
                                        <textarea name="programs_content" rows="5" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                            placeholder="Describe your programs here...">{{ $department->programs_content ?? '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">कार्यक्रम विषयवस्तु (Nepali)</label>
                                        <textarea name="programs_content_nepali" rows="5" 
                                            class="field-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                            placeholder="नेपालीमा विषयवस्तु...">{{ $department->programs_content_nepali ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">Programs Photo</label>
                                    <input type="file" name="programs_image" accept="image/*" id="programsImageInput" 
                                        class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 transition"
                                        onchange="handleProgramsImageUpload(event)">
                                    @if($department && !empty($department->programs_image_path))
                                        <div class="mt-4 relative">
                                            <div class="aspect-[16/9] overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50">
                                                <img src="{{ $department->getProgramsImageUrl() }}" alt="Programs photo" class="h-full w-full object-cover">
                                            </div>
                                            <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 shadow-lg"
                                                onclick="deleteProgramsImage()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="border-t mt-8 pt-6 flex items-center gap-3 sticky bottom-0 bg-white">
                        <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-semibold transition shadow-lg flex items-center justify-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            Save Changes
                        </button>
                        <a href="{{ route('admin.settings') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition">
                            <i class="bi bi-x-circle"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ===================== TAB FUNCTIONALITY =====================
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Deactivate all tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-gray-200', 'text-gray-700');
        });

        // Show selected tab content
        const selectedContent = document.getElementById(tabName + '-content');
        if (selectedContent) selectedContent.classList.remove('hidden');

        // Activate selected tab button
        const selectedBtn = document.querySelector(`[data-tab="${tabName}"]`);
        if (selectedBtn) {
            selectedBtn.classList.remove('border-gray-200', 'text-gray-700');
            selectedBtn.classList.add('border-blue-600', 'text-blue-600');
        }

        // Scroll top on tab switch
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ===================== TOAST NOTIFICATIONS =====================
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const colors = {
            success: 'bg-gradient-to-r from-green-500 to-green-600',
            error: 'bg-gradient-to-r from-red-500 to-red-600',
            info: 'bg-gradient-to-r from-blue-500 to-blue-600',
            warning: 'bg-gradient-to-r from-yellow-500 to-yellow-600',
        };

        const icons = {
            success: 'bi-check-circle',
            error: 'bi-exclamation-circle',
            info: 'bi-info-circle',
            warning: 'bi-exclamation-triangle',
        };

        const toast = document.createElement('div');
        toast.className = `${colors[type] || colors.info} text-white px-6 py-4 rounded-lg shadow-lg text-sm font-medium flex items-center gap-3 animate-slide-in`;
        toast.innerHTML = `
            <i class="bi ${icons[type] || icons.info}"></i>
            <span>${message}</span>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('animate-slide-out');
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    // ===================== LOADER =====================
    function showLoader(show, text = 'Loading...') {
        const loader = document.getElementById('globalLoader');
        const loaderText = document.getElementById('loaderText');
        if (loaderText) loaderText.textContent = text;
        if (loader) loader.classList.toggle('hidden', !show);
    }

    // ===================== LOGO UPLOAD =====================
    const logoPreview = document.getElementById('logoPreview');
    const logoInput = document.getElementById('logoInput');

    if (logoPreview) {
        logoPreview.addEventListener('click', () => logoInput.click());
        logoPreview.addEventListener('dragover', (e) => {
            e.preventDefault();
            logoPreview.classList.add('bg-blue-200');
        });
        logoPreview.addEventListener('dragleave', () => {
            logoPreview.classList.remove('bg-blue-200');
        });
        logoPreview.addEventListener('drop', (e) => {
            e.preventDefault();
            logoPreview.classList.remove('bg-blue-200');
            if (e.dataTransfer.files.length) {
                logoInput.files = e.dataTransfer.files;
                handleLogoUpload({ target: { files: e.dataTransfer.files } });
            }
        });
    }

function handleLogoUpload(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml'];
        if (!validTypes.includes(file.type)) {
            showToast('Please select a valid image file (JPEG, PNG, GIF, SVG)', 'error');
            return;
        }

        if (file.size > 2048 * 1024) {
            showToast('File size must be less than 2MB', 'error');
            return;
        }

        showToast('Previewing image...', 'info');

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            if (preview) {
                preview.innerHTML = `
                    <div class="relative w-full h-full">
                        <img src="${e.target.result}" alt="Logo preview" class="h-full w-full object-contain p-2">
                        <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 shadow-lg transition text-xs" onclick="clearLogoPreview()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                showToast('Image preview loaded successfully', 'success');
            }
        };
        reader.onerror = function() {
            showToast('Error reading image file', 'error');
        };
        reader.readAsDataURL(file);
        // Reset input to allow same file re-selection
        event.target.value = '';
    }

    function clearLogoPreview() {
        const preview = document.getElementById('logoPreview');
        const input = document.getElementById('logoInput');
        if (preview && input) {
            preview.innerHTML = `
                <div class="text-center">
                    <i class="bi bi-cloud-arrow-up text-4xl text-blue-400 group-hover:text-blue-500 transition"></i>
                    <p class="text-sm text-blue-600 mt-2 font-medium">Click or drag to upload</p>
                    <p class="text-xs text-blue-500">PNG, JPG, GIF, SVG up to 2MB</p>
                </div>
            `;
            input.value = '';
        }
    }

    async function deleteLogo() {
        if (!confirm('Delete college logo?')) return;
        showLoader(true, 'Deleting logo...');
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch('{{ route("admin.department.logo.delete") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast('Logo deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Error deleting logo', 'error');
            }
        } catch (e) {
            console.error('Delete logo error:', e);
            showToast('Network error deleting logo', 'error');
        } finally {
            showLoader(false);
        }
    }

    // ===================== HERO IMAGES UPLOAD =====================
    const heroDropZone = document.getElementById('heroDropZone');
    const heroImages = document.getElementById('heroImages');

    if (heroDropZone && heroImages) {
        heroDropZone.addEventListener('click', () => heroImages.click());
        heroDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            heroDropZone.classList.add('bg-blue-200', 'border-blue-500');
        });
        heroDropZone.addEventListener('dragleave', () => {
            heroDropZone.classList.remove('bg-blue-200', 'border-blue-500');
        });
        heroDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            heroDropZone.classList.remove('bg-blue-200', 'border-blue-500');
            if (e.dataTransfer.files.length) {
                heroImages.files = e.dataTransfer.files;
            }
        });
    }

    async function deleteHeroImage(index) {
        if (!confirm('Delete this hero image?')) return;
        showLoader(true, 'Deleting image...');
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch(`{{ url('/admin/department/hero-images') }}/${index}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast('Hero image deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Error deleting image', 'error');
                showLoader(false);
            }
        } catch (e) {
            showToast('Error deleting image', 'error');
            showLoader(false);
        }
    }

    // ===================== PROGRAMS IMAGE UPLOAD =====================
    function handleProgramsImageUpload(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        if (file.size > 4096 * 1024) {
            showToast('File size must be less than 4MB', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.createElement('div');
            container.className = 'mt-4 relative';
            container.innerHTML = `
                <div class="aspect-[16/9] overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50">
                    <img src="${e.target.result}" alt="Programs photo preview" class="h-full w-full object-cover">
                </div>
                <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 shadow-lg"
                    onclick="this.parentElement.remove()">
                    <i class="bi bi-trash"></i>
                </button>
            `;

            // Remove previous preview if exists
            const oldPreview = document.querySelector('#programsImageInput').nextElementSibling;
            if (oldPreview && oldPreview.className.includes('mt-4')) {
                oldPreview.remove();
            }

            document.getElementById('programsImageInput').parentElement.appendChild(container);
        };
        reader.readAsDataURL(file);
    }

    async function deleteProgramsImage() {
        if (!confirm('Delete this programs image?')) return;
        showLoader(true, 'Deleting image...');
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch('{{ url("/admin/department/programs-image") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast('Programs image deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Error deleting image', 'error');
                showLoader(false);
            }
        } catch (e) {
            showToast('Error deleting image', 'error');
            showLoader(false);
        }
    }

    // ===================== MAP PREVIEW =====================
    function updateMapPreview() {
        const lat = document.querySelector('input[name="latitude"]').value;
        const lon = document.querySelector('input[name="longitude"]').value;
        const embedUrl = document.querySelector('input[name="map_embed_url"]').value;
        const mapLabel = document.querySelector('input[name="map_label"]').value;

        const preview = document.getElementById('mapPreview');
        if (!preview) return;

        if (embedUrl) {
            // Use embed URL if provided
            preview.innerHTML = `<iframe src="${embedUrl}" width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen="" loading="lazy"></iframe>`;
        } else if (lat && lon) {
            // Create OpenStreetMap embed from coordinates
            const mapZoom = 15;
            const osmEmbedUrl = `https://www.openstreetmap.org/export/embed.html?bbox=${(lon - 0.01).toFixed(4)},${(lat - 0.01).toFixed(4)},${(lon + 0.01).toFixed(4)},${(lat + 0.01).toFixed(4)}&layer=mapnik`;
            preview.innerHTML = `
                <div class="w-full h-full flex flex-col">
                    <iframe src="${osmEmbedUrl}" width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    ${mapLabel ? `<div class="bg-blue-600 text-white p-2 text-center text-sm font-semibold">${mapLabel}</div>` : ''}
                </div>
            `;
        } else {
            preview.innerHTML = `
                <div class="w-full h-full flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <i class="bi bi-map text-4xl"></i>
                        <p class="mt-2 text-sm">Enter coordinates or map embed URL to preview</p>
                    </div>
                </div>
            `;
        }
    }

    // Initialize map preview on page load
    document.addEventListener('DOMContentLoaded', updateMapPreview);

    // ===================== FORM SUBMISSION =====================
    document.getElementById('departmentForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Validate required fields
        const nameInput = form.querySelector('input[name="name"]');
        const nameNepaliInput = form.querySelector('input[name="name_nepali"]');
        const shortNameInput = form.querySelector('input[name="short_name"]');

        if (!nameInput.value.trim()) {
            showToast('Department name is required', 'error');
            switchTab('basic');
            nameInput.focus();
            return;
        }

        showLoader(true, 'Saving department details...');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                showToast('Server error. Please check console for details.', 'error');
                showLoader(false);
                return;
            }

            const data = await response.json();
            console.debug('Department update response:', data);

            if (data.success) {
                // Update form fields from returned department object so UI reflects changes immediately
                const dept = data.department || {};
                if (dept.name !== undefined) form.querySelector('input[name="name"]').value = dept.name;
                if (dept.name_nepali !== undefined) form.querySelector('input[name="name_nepali"]').value = dept.name_nepali;
                if (dept.short_name !== undefined) form.querySelector('input[name="short_name"]').value = dept.short_name;

                showToast(data.message || '✓ Department details saved successfully', 'success');
                showLoader(false);
                // Reload page to refresh preview with saved logo
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                let errorMessage = data.message || 'Error saving department details';
                if (data.errors) {
                    const errors = Object.values(data.errors).flat();
                    errorMessage = errors[0] || errorMessage;
                }
                showToast(errorMessage, 'error');
                showLoader(false);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error saving: ' + error.message, 'error');
            showLoader(false);
        }
    });

    // ===================== FIELD CHANGE TRACKING =====================
    const formFields = document.querySelectorAll('.field-input');
    formFields.forEach(field => {
        field.addEventListener('change', function() {
            // Add visual indicator that field was modified
            this.classList.add('border-blue-500');
        });
    });

    // Add CSS animations if not already in stylesheet
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(20px);
            }
        }
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        .animate-slide-out {
            animation: slideOut 0.3s ease-out;
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
