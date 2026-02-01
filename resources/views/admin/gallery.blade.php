@extends('admin.layouts.app')

@section('title', 'Gallery Management')

@section('content')
<div class="space-y-4">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded text-xs">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded text-xs">
        {{ session('error') }}
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Total Photos -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Total Photos</p>
                    <p class="text-xl font-bold text-gray-900 mt-0.5">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-2 rounded-lg">
                    <i class="bi bi-images text-lg text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Active Photos -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Active</p>
                    <p class="text-xl font-bold text-green-600 mt-0.5">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-lg">
                    <i class="bi bi-check-circle text-lg text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Inactive Photos -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Inactive</p>
                    <p class="text-xl font-bold text-gray-600 mt-0.5">{{ $stats['inactive'] ?? 0 }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded-lg">
                    <i class="bi bi-dash-circle text-lg text-gray-600"></i>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Categories</p>
                    <p class="text-xl font-bold text-purple-600 mt-0.5">6</p>
                </div>
                <div class="bg-purple-100 p-2 rounded-lg">
                    <i class="bi bi-folder text-lg text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <form id="galleryFiltersForm" action="{{ route('admin.gallery') }}" method="GET" class="flex items-center gap-2">
            <div class="flex-1 relative min-w-48">
                <i class="bi bi-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="gallerySearch" name="search" placeholder="Search photos..." value="{{ request('search') }}" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <select name="category" id="filterCategory" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All Categories</option>
                <option value="campus" {{ request('category') == 'campus' ? 'selected' : '' }}>Campus</option>
                <option value="events" {{ request('category') == 'events' ? 'selected' : '' }}>Events</option>
                <option value="activities" {{ request('category') == 'activities' ? 'selected' : '' }}>Activities</option>
                <option value="students" {{ request('category') == 'students' ? 'selected' : '' }}>Students</option>
                <option value="faculty" {{ request('category') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                <option value="facilities" {{ request('category') == 'facilities' ? 'selected' : '' }}>Facilities</option>
            </select>
            <select name="status" id="filterStatus" class="w-32 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="button" id="applyGalleryFiltersBtn" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium">Filter</button>
            @if(request('search') || request('category') || request('status') !== '')
            <a href="{{ route('admin.gallery') }}" class="px-3 py-2 border border-gray-300 rounded text-xs hover:bg-gray-50 font-medium">Clear</a>
            @endif
        </form>

        <button onclick="openCreateGalleryModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 font-medium">
            <i class="bi bi-plus-lg"></i>
            <span>Add Photo</span>
        </button>
    </div>

    <!-- Gallery Grid -->
    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        @if($galleries->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
            @foreach($galleries as $gallery)
            <div class="relative group aspect-square bg-gray-100 rounded-lg overflow-hidden">
                @if($gallery->image_url)
                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                        <i class="bi bi-image text-4xl text-gray-400"></i>
                    </div>
                @endif

                <!-- Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                    <button onclick="openEditGalleryModal({{ $gallery->id }})" class="p-2 bg-white rounded-full hover:bg-gray-100 transition" title="Edit">
                        <i class="bi bi-pencil text-gray-700 text-sm"></i>
                    </button>
                    <button onclick="toggleGalleryStatus({{ $gallery->id }}, {{ $gallery->is_active ? 'false' : 'true' }})" class="p-2 {{ $gallery->is_active ? 'bg-green-500' : 'bg-gray-500' }} rounded-full hover:opacity-80 transition" title="{{ $gallery->is_active ? 'Deactivate' : 'Activate' }}">
                        <i class="bi {{ $gallery->is_active ? 'bi-eye-slash' : 'bi-eye' }} text-white text-sm"></i>
                    </button>
                    <form action="{{ route('admin.gallery.destroy', $gallery->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this photo?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-red-500 rounded-full hover:bg-red-600 transition" title="Delete">
                            <i class="bi bi-trash text-white text-sm"></i>
                        </button>
                    </form>
                </div>

                <!-- Status Badge -->
                @if(!$gallery->is_active)
                <div class="absolute top-2 left-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-white">
                        <i class="bi bi-eye-slash text-xs"></i>
                    </span>
                </div>
                @endif

                <!-- Category Badge -->
                <div class="absolute bottom-2 left-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-white/90 text-gray-700">
                        {{ ucfirst($gallery->category) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                <i class="bi bi-images text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Photos Found</h3>
            <p class="text-gray-500 text-sm mb-4">There are no photos in the gallery yet. Add your first photo!</p>
            <button onclick="openCreateGalleryModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 font-medium">
                <i class="bi bi-plus-lg"></i>
                <span>Add Photo</span>
            </button>
        </div>
        @endif

        <!-- Pagination -->
        @if($galleries->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
            <p class="text-xs text-gray-600">Showing {{ $galleries->firstItem() ?? 0 }}-{{ $galleries->lastItem() ?? 0 }} of {{ $galleries->total() }} photos</p>
            <div class="flex gap-1">
                {{ $galleries->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
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
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Optional description..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Photo *</label>
                <input type="file" name="image" id="galleryImage" accept="image/*" required class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="previewImage(this, 'imagePreview')">
                <div id="imagePreview" class="mt-2 hidden">
                    <img src="" alt="Preview" class="max-h-40 rounded-lg">
                </div>
                <p class="text-xs text-gray-500 mt-1">Max size: 10MB. Allowed: JPG, PNG, GIF, WebP</p>
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
            <div class="grid grid-cols-2 gap-3">
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
                    Upload Photo
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
            <div class="grid grid-cols-2 gap-3">
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

    function toggleGalleryStatus(id, status) {
        if (!confirm(`Are you sure you want to ${status === 'true' ? 'activate' : 'deactivate'} this photo?`)) {
            return;
        }
        
        fetch(`/admin/gallery/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: id, status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
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
@endsection
