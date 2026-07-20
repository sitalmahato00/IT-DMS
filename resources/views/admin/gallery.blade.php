@extends('admin.layouts.app')

@section('title', 'Gallery Management')

@section('styles')
<script>
    document.documentElement.classList.add('gallery-ui-enhanced');
</script>
<style>
    html.gallery-ui-enhanced:not(.dark) .gallery-page {
        color: #0f172a;
    }

    html.gallery-ui-enhanced:not(.dark) .gallery-stats > * > div {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        border-color: #d9e4f3;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(246, 250, 255, 0.96));
        box-shadow: 0 24px 48px -34px rgba(37, 99, 235, 0.28);
    }

    html.gallery-ui-enhanced:not(.dark) .gallery-filter-panel > *,
    html.gallery-ui-enhanced:not(.dark) .gallery-panel {
        border-radius: 28px;
        border-color: rgba(215, 227, 243, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 252, 255, 0.97));
        box-shadow: 0 28px 56px -40px rgba(30, 64, 175, 0.28);
    }

    html.gallery-ui-enhanced:not(.dark) .gallery-panel-header {
        background: linear-gradient(180deg, #f6faff, #fbfdff);
    }

    html.gallery-ui-enhanced:not(.dark) .gallery-tile {
        border-radius: 26px;
        border: 1px solid rgba(219, 234, 254, 0.9);
        box-shadow: 0 24px 48px -34px rgba(37, 99, 235, 0.28);
    }

    html.gallery-ui-enhanced:not(.dark) .gallery-tile-overlay {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.04), rgba(15, 23, 42, 0.58));
    }

    html.gallery-ui-enhanced:not(.dark) .gallery-icon-btn {
        box-shadow: 0 16px 30px -18px rgba(15, 23, 42, 0.42);
    }

    html.gallery-ui-enhanced:not(.dark) .gallery-chip,
    html.gallery-ui-enhanced:not(.dark) .gallery-state-chip {
        border-radius: 999px;
        padding: 0.4rem 0.8rem;
        font-weight: 700;
        box-shadow: 0 14px 24px -20px rgba(15, 23, 42, 0.26);
    }

    html.gallery-ui-enhanced:not(.dark) #confirmModal > div,
    html.gallery-ui-enhanced:not(.dark) #createGalleryModal > div,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal > div {
        border-radius: 30px;
        border: 1px solid rgba(215, 227, 243, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(247, 251, 255, 0.98));
        box-shadow: 0 34px 70px -38px rgba(15, 23, 42, 0.42);
        overflow: hidden;
    }

    html.gallery-ui-enhanced:not(.dark) #confirmHeader,
    html.gallery-ui-enhanced:not(.dark) #createGalleryModal .bg-gradient-to-r,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal .bg-gradient-to-r {
        border-bottom: none;
    }

    html.gallery-ui-enhanced:not(.dark) #createGalleryModal input,
    html.gallery-ui-enhanced:not(.dark) #createGalleryModal textarea,
    html.gallery-ui-enhanced:not(.dark) #createGalleryModal select,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal input,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal textarea,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal select {
        border-radius: 16px;
        border-color: #d8e4f5;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    html.gallery-ui-enhanced:not(.dark) #createGalleryModal input:focus,
    html.gallery-ui-enhanced:not(.dark) #createGalleryModal textarea:focus,
    html.gallery-ui-enhanced:not(.dark) #createGalleryModal select:focus,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal input:focus,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal textarea:focus,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    html.gallery-ui-enhanced:not(.dark) #confirmCancel,
    html.gallery-ui-enhanced:not(.dark) #confirmOk,
    html.gallery-ui-enhanced:not(.dark) #createGalleryModal button,
    html.gallery-ui-enhanced:not(.dark) #editGalleryModal button {
        border-radius: 999px;
    }
</style>
@endsection

@section('content')
{{-- Page Header - Using standardized component --}}
@include('admin.components.admin-page-header', [
    'title' => 'Gallery Management',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Gallery Management']
    ],
    'addButton' => [
        'label' => 'Add Photo',
        'onclick' => 'openCreateGalleryModal()'
    ]
])

<div class="gallery-page space-y-6">

    <!-- Success/Error Messages -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Toast Notification - Uses global toast system from layout -->

    <!-- Professional Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300 animate-fade-in">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all duration-300 animate-scale-up">
            <!-- Header with icon background -->
            <div id="confirmHeader" class="relative h-20 bg-gradient-to-r from-blue-50 to-blue-100 flex items-center justify-center">
                <div id="confirmIconContainer" class="absolute h-24 w-24 rounded-full flex items-center justify-center" style="transform: translateY(50%);">
                    <i id="confirmIcon" class="text-4xl"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="pt-16 px-6 pb-6 text-center">
                <h3 id="confirmTitle" class="text-xl font-bold text-gray-900 mb-2">Confirm Action</h3>
                <p id="confirmMessage" class="text-gray-600 text-sm leading-relaxed mb-8">Are you sure you want to proceed?</p>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-3">
                    <button id="confirmCancel" class="flex-1 px-4 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-150 active:scale-95">
                        <i class="bi bi-x-circle mr-1"></i>Cancel
                    </button>
                    <button id="confirmOk" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-150 active:scale-95 shadow-lg hover:shadow-xl">
                        <i id="confirmOkIcon" class="bi bi-check-circle mr-1"></i><span id="confirmOkText">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded text-xs">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-blue-700 px-4 py-2 rounded text-xs">
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Cards - Using standardized component --}}
<div class="gallery-stats">
@include('admin.components.admin-stats-cards', [
    'cards' => [
        ['title' => 'Total Photos', 'value' => $stats['total'] ?? 0, 'icon' => 'bi-images', 'color' => 'blue'],
        ['title' => 'Active', 'value' => $stats['active'] ?? 0, 'icon' => 'bi-check-circle', 'color' => 'green'],
        ['title' => 'Inactive', 'value' => $stats['inactive'] ?? 0, 'icon' => 'bi-dash-circle', 'color' => 'gray'],
        ['title' => 'Categories', 'value' => 6, 'icon' => 'bi-folder', 'color' => 'purple'],
    ]
])
</div>

    {{-- Filter Card - Using standardized component --}}
<div class="gallery-filter-panel">
@include('admin.components.admin-filter-card', [
    'formAction' => route('admin.gallery'),
    'filters' => [
        ['name' => 'search', 'type' => 'text', 'placeholder' => 'Photo title...', 'value' => request('search'), 'label' => 'Search'],
        ['name' => 'category', 'type' => 'select', 'options' => ['' => 'All Categories', 'campus' => 'Campus', 'events' => 'Events', 'activities' => 'Activities', 'students' => 'Students', 'faculty' => 'Faculty', 'facilities' => 'Facilities'], 'value' => request('category'), 'label' => 'Category'],
        ['name' => 'status', 'type' => 'select', 'options' => ['' => 'All Status', '1' => 'Active', '0' => 'Inactive'], 'value' => request('status'), 'label' => 'Status'],
    ],
    'showReset' => true,
    'resetRoute' => route('admin.gallery'),
    'hideFilterButton' => true
])
</div>

    <!-- Gallery Card -->
    <div class="gallery-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="gallery-panel-header px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Photo Gallery</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $galleries->total() }} total photos</span>
            </div>
        </div>

        @if($galleries->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
            @foreach($galleries as $gallery)
            <div class="gallery-tile relative group aspect-square bg-gray-100 rounded-lg overflow-hidden">
                @if($gallery->image_url)
                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                        <i class="bi bi-image text-4xl text-gray-400"></i>
                    </div>
                @endif

                <!-- Overlay -->
                <div class="gallery-tile-overlay absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                    <button onclick="openEditGalleryModal({{ $gallery->id }})" class="gallery-icon-btn p-2 bg-white rounded-full hover:bg-gray-100 transition" title="Edit">
                        <i class="bi bi-pencil text-gray-700 text-sm"></i>
                    </button>
                    <button onclick="toggleGalleryStatus({{ $gallery->id }}, {{ $gallery->is_active ? 'false' : 'true' }})" class="gallery-icon-btn p-2 {{ $gallery->is_active ? 'bg-green-500' : 'bg-gray-500' }} rounded-full hover:opacity-80 transition" title="{{ $gallery->is_active ? 'Deactivate' : 'Activate' }}">
                        <i class="bi {{ $gallery->is_active ? 'bi-eye-slash' : 'bi-eye' }} text-white text-sm"></i>
                    </button>
                    <button onclick="deleteGalleryPhoto({{ $gallery->id }})" class="gallery-icon-btn p-2 bg-blue-500 rounded-full hover:bg-blue-600 transition" title="Delete">
                        <i class="bi bi-trash text-white text-sm"></i>
                    </button>
                </div>

                <!-- Status Badge -->
                @if(!$gallery->is_active)
                <div class="absolute top-2 left-2">
                    <span class="gallery-state-chip inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-white">
                        <i class="bi bi-eye-slash text-xs"></i>
                    </span>
                </div>
                @endif

                <!-- Category Badge -->
                <div class="absolute bottom-2 left-2">
                    <span class="gallery-chip inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-white/90 text-gray-700">
                        {{ ucfirst($gallery->category) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800">
            @include('admin.components.admin-pagination', [
                'paginator' => $galleries,
                'route' => route('admin.gallery')
            ])
        </div>
    </div>
</div>

<!-- Create Gallery Modal -->
<div id="createGalleryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 flex items-center justify-between sticky top-0">
            <h2 class="text-white font-semibold text-sm">Add New Photo</h2>
            <button onclick="closeCreateGalleryModal()" class="text-white hover:text-gray-200 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
            @csrf
            
            <!-- Title -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Title *</label>
                <input type="text" name="title" placeholder="Enter photo title" required class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                @error('title')
                <p class="text-blue-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Optional description..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Photos *</label>
                <input type="file" name="images[]" id="galleryImage" accept="image/*" multiple required class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="previewGalleryImages(this, 'imagePreview')">
                <div id="imagePreview" class="mt-2 hidden grid grid-cols-2 gap-2 sm:grid-cols-3">
                </div>
                <p class="text-xs text-gray-500 mt-1">Select one or more images. Max size: 10MB each. Allowed: JPG, PNG, GIF, WebP</p>
                @error('images')
                <p class="text-blue-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                <p class="text-blue-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Category *</label>
                <select name="category" required class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Select Category</option>
                    <option value="campus">Campus</option>
                    <option value="events">Events</option>
                    <option value="activities">Activities</option>
                    <option value="students">Students</option>
                    <option value="faculty">Faculty</option>
                    <option value="facilities">Facilities</option>
                </select>
            </div>

            <!-- Order & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Display Order</label>
                    <input type="number" name="order" value="0" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Status</label>
                    <select name="is_active" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0">
                <button type="button" onclick="closeCreateGalleryModal()" class="px-3 py-1.5 border border-gray-300 rounded text-xs font-medium text-gray-900 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition">
                    Upload Photos
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Gallery Modal -->
<div id="editGalleryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 flex items-center justify-between sticky top-0">
            <h2 class="text-white font-semibold text-sm">Edit Photo</h2>
            <button onclick="closeEditGalleryModal()" class="text-white hover:text-gray-200 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form id="editGalleryForm" action="" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
            @csrf
            @method('PUT')
            
            <!-- Title -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Title *</label>
                <input type="text" name="title" id="editGalleryTitle" placeholder="Enter photo title" required class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Description</label>
                <textarea name="description" id="editGalleryDescription" rows="2" placeholder="Optional description..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
            </div>

            <!-- Current Image -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Current Photo</label>
                <div id="editGalleryCurrentImage" class="mt-2">
                    <!-- Image loaded via JS -->
                </div>
            </div>

            <!-- New Image Upload -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">New Photo (Optional)</label>
                <input type="file" name="image" id="editGalleryImage" accept="image/*" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="previewImage(this, 'editImagePreview')">
                <div id="editImagePreview" class="mt-2 hidden">
                    <img src="" alt="Preview" class="max-h-40 rounded-lg">
                </div>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Category *</label>
                <select name="category" id="editGalleryCategory" required class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="campus">Campus</option>
                    <option value="events">Events</option>
                    <option value="activities">Activities</option>
                    <option value="students">Students</option>
                    <option value="faculty">Faculty</option>
                    <option value="facilities">Facilities</option>
                </select>
            </div>

            <!-- Order & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Display Order</label>
                    <input type="number" name="order" id="editGalleryOrder" value="0" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Status</label>
                    <select name="is_active" id="editGalleryStatus" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0">
                <button type="button" onclick="closeEditGalleryModal()" class="px-3 py-1.5 border border-gray-300 rounded text-xs font-medium text-gray-900 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" onclick="document.getElementById('editGalleryForm').submit()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition">
                    Update Photo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateGalleryModal() {
        document.getElementById('createGalleryModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateGalleryModal() {
        document.getElementById('createGalleryModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        const form = document.querySelector('#createGalleryModal form');
        const preview = document.getElementById('imagePreview');
        if (form) {
            form.reset();
        }
        if (preview) {
            preview.innerHTML = '';
            preview.classList.add('hidden');
        }
    }

    function openEditGalleryModal(id) {
        // Load gallery details via AJAX
        fetch(`/admin/gallery/${id}`)
            .then(response => response.json())
            .then(data => {
                const gallery = data.gallery;
                
                document.getElementById('editGalleryTitle').value = gallery.title;
                document.getElementById('editGalleryDescription').value = gallery.description || '';
                document.getElementById('editGalleryCategory').value = gallery.category;
                document.getElementById('editGalleryOrder').value = gallery.order || 0;
                document.getElementById('editGalleryStatus').value = gallery.is_active ? '1' : '0';
                
                // Show current image
                const currentImageDiv = document.getElementById('editGalleryCurrentImage');
                if (gallery.image_url) {
                    currentImageDiv.innerHTML = `<img src="${gallery.image_url}" alt="${gallery.title}" class="max-h-40 rounded-lg">`;
                } else {
                    currentImageDiv.innerHTML = '<div class="w-40 h-40 bg-gray-200 rounded-lg flex items-center justify-center"><i class="bi bi-image text-2xl text-gray-400"></i></div>';
                }
                
                // Update form action
                document.getElementById('editGalleryForm').action = `/admin/gallery/${id}`;
                
                document.getElementById('editGalleryModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error loading gallery:', error);
                alert('Failed to load gallery details');
            });
    }

    function closeEditGalleryModal() {
        document.getElementById('editGalleryModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="max-h-40 rounded-lg">`;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewGalleryImages(input, previewId) {
        const preview = document.getElementById(previewId);
        const files = Array.from(input.files || []);

        preview.innerHTML = '';

        if (!files.length) {
            preview.classList.add('hidden');
            return;
        }

        files.forEach((file) => {
            if (!file.type.startsWith('image/')) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'overflow-hidden rounded-lg border border-gray-200 bg-gray-50';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}" class="h-24 w-full object-cover">
                    <div class="truncate px-2 py-1 text-[10px] text-gray-600">${file.name}</div>
                `;
                preview.appendChild(item);
            };
            reader.readAsDataURL(file);
        });

        preview.classList.remove('hidden');
    }

    function toggleGalleryStatus(id, status) {
        const showConfirmation = async () => {
            if (typeof showConfirm === 'function') {
                const confirmed = await showConfirm({
                    title: status === 'true' ? 'Activate Photo' : 'Deactivate Photo',
                    message: `Are you sure you want to ${status === 'true' ? 'activate' : 'deactivate'} this photo?`,
                    type: status === 'true' ? 'success' : 'warning',
                    okText: status === 'true' ? 'Activate' : 'Deactivate',
                    cancelText: 'Cancel'
                });
                if (!confirmed) return;
            } else {
                if (!confirm(`Are you sure you want to ${status === 'true' ? 'activate' : 'deactivate'} this photo?`)) {
                    return;
                }
            }
            
            showLoading(`${status === 'true' ? 'Activating' : 'Deactivating'} photo...`);
            
            try {
                const response = await fetch(`/admin/gallery/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: id, status: status })
                });
                
                const data = await response.json();
                if (data.success) {
                    showToast(`Photo ${status === 'true' ? 'activated' : 'deactivated'} successfully`, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    hideLoading();
                    showToast('Failed to update status', 'error');
                }
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                showToast('An error occurred', 'error');
            }
        };
        
        showConfirmation();
    }

    function deleteGalleryPhoto(id) {
        const confirmDelete = async () => {
            if (typeof showConfirm === 'function') {
                const confirmed = await showConfirm({
                    title: 'Delete Photo',
                    message: 'Are you sure you want to delete this photo? This action cannot be undone.',
                    type: 'delete',
                    okText: 'Delete',
                    cancelText: 'Cancel'
                });
                if (!confirmed) return;
            } else {
                if (!confirm('Are you sure you want to delete this photo?')) return;
            }
            
            showLoading('Deleting photo...');
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const response = await fetch(`/admin/gallery/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    showToast('Photo deleted successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    hideLoading();
                    showToast(data.message || 'Failed to delete photo', 'error');
                }
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                showToast('An error occurred while deleting photo', 'error');
            }
        };
        
        confirmDelete();
    }

    // Debounced search and Apply button for filters
    (function() {
        const searchInput = document.getElementById('gallerySearch');
        const applyBtn = document.getElementById('applyGalleryFiltersBtn');
        const filterForm = document.getElementById('galleryFiltersForm');
        let debounceTimer = null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filterForm.submit();
                }, 600);
            });
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                filterForm.submit();
            });
        }

        // Submit when filters change
        document.getElementById('filterCategory')?.addEventListener('change', function() {
            filterForm.submit();
        });
        document.getElementById('filterStatus')?.addEventListener('change', function() {
            filterForm.submit();
        });
    })();

    // Close modals when clicking outside
    document.getElementById('createGalleryModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeCreateGalleryModal();
        }
    });

    document.getElementById('editGalleryModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeEditGalleryModal();
        }
    });

    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateGalleryModal();
            closeEditGalleryModal();
        }
    });
</script>
</div>
@endsection

