@props(['galleryItems', 'category' => 'all', 'counts' => []])

@php
$tabs = [
    'all' => ['label' => 'All Photos', 'icon' => 'bi-images', 'color' => 'blue'],
    'campus' => ['label' => 'Campus', 'icon' => 'bi-building', 'color' => 'green'],
    'events' => ['label' => 'Events', 'icon' => 'bi-calendar-event', 'color' => 'purple'],
    'activities' => ['label' => 'Activities', 'icon' => 'bi-activity', 'color' => 'red'],
    'students' => ['label' => 'Students', 'icon' => 'bi-mortarboard', 'color' => 'cyan'],
    'faculty' => ['label' => 'Faculty', 'icon' => 'bi-person-badge', 'color' => 'orange'],
    'facilities' => ['label' => 'Facilities', 'icon' => 'bi-gear', 'color' => 'teal'],
];

// Prepare gallery items JSON for JavaScript
$galleryItemsJson = [];
foreach ($galleryItems as $item) {
    $galleryItemsJson[] = [
        'id' => $item->id,
        'title' => $item->title,
        'description' => $item->description,
        'image_url' => $item->image_url,
        'category' => $item->category,
        'category_text' => $item->category_text,
    ];
}
@endphp

<div id="gallery" class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
                <i class="bi bi-images text-2xl text-blue-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ __('Photo Gallery') }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">{{ __('Explore moments from our campus life through our photo gallery.') }}</p>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            @foreach($tabs as $key => $tab)
                <button 
                    onclick="filterGallery('{{ $key }}')"
                    class="filter-gallery-btn inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                        {{ $category === $key 
                            ? 'bg-blue-600 text-white shadow-md' 
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    data-category="{{ $key }}">
                    <i class="bi {{ $tab['icon'] }}"></i>
                    <span>{{ __($tab['label']) }}</span>
                    @if(isset($counts[$key]) && $counts[$key] > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs
                            {{ $category === $key ? 'bg-blue-700 text-blue-100' : 'bg-gray-200 text-gray-600' }}">
                            {{ $counts[$key] }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        <!-- Gallery Grid -->
        <div id="gallery-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($galleryItems as $item)
                <div class="gallery-item group relative aspect-square overflow-hidden rounded-xl bg-gray-100 cursor-pointer"
                     onclick="openGalleryModal({{ $loop->index }})"
                     data-item-id="{{ $item->id }}">
                    
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" 
                             alt="{{ $item->title }}" 
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                             loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                            <i class="bi bi-image text-4xl text-gray-400"></i>
                        </div>
                    @endif

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-end">
                        <div class="p-4 w-full bg-gradient-to-t from-black/70 to-transparent transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <p class="text-white font-medium text-sm truncate">{{ $item->title }}</p>
                            <p class="text-gray-300 text-xs">{{ $item->category_text }}</p>
                        </div>
                    </div>

                    <!-- Zoom Icon -->
                    <div class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow-lg">
                        <i class="bi bi-zoom-in text-gray-700 text-sm"></i>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                            <i class="bi bi-images text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Photos Found') }}</h3>
                        <p class="text-gray-500">{{ __('There are no photos in this category yet.') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- View All Button -->
        <div class="text-center mt-10">
            <a href="{{ route('admin.gallery') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                <i class="bi bi-images"></i>
                <span>{{ __('View Full Gallery') }}</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Gallery Lightbox Modal -->
<div id="gallery-modal" class="hidden fixed inset-0 bg-black bg-opacity-95 flex items-center justify-center z-50 p-4" onclick="closeGalleryModal(event)">
    <button onclick="closeGalleryModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition z-10">
        <i class="bi bi-x-lg text-2xl"></i>
    </button>
    
    <!-- Navigation Arrows -->
    <button onclick="event.stopPropagation(); navigateGallery(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-blue-400 transition p-2">
        <i class="bi bi-chevron-left text-3xl"></i>
    </button>
    
    <button onclick="event.stopPropagation(); navigateGallery(1)" class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-blue-400 transition p-2">
        <i class="bi bi-chevron-right text-3xl"></i>
    </button>

    <!-- Image Container -->
    <div class="max-w-5xl max-h-[85vh] w-full" onclick="event.stopPropagation()">
        <div id="gallery-modal-content" class="text-center">
            <!-- Content loaded via JS -->
            <div class="text-center py-20">
                <i class="bi bi-hourglass-split text-5xl text-gray-500 animate-spin mb-3"></i>
                <p class="text-gray-400">{{ __('Loading image...') }}</p>
            </div>
        </div>
        
        <!-- Caption -->
        <div id="gallery-modal-caption" class="mt-4 text-center">
        </div>
        
        <!-- Counter -->
        <div id="gallery-modal-counter" class="mt-2 text-center text-gray-400 text-sm">
        </div>
    </div>
</div>

<script>
    let currentGalleryCategory = '{{ $category }}';
    let currentGalleryIndex = 0;
    let galleryItems = @json($galleryItemsJson);

    function filterGallery(category) {
        currentGalleryCategory = category;
        currentGalleryIndex = 0;
        
        // Update active tab styling
        document.querySelectorAll('.filter-gallery-btn').forEach(btn => {
            if (btn.dataset.category === category) {
                btn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                btn.classList.remove('bg-gray-100', 'text-gray-700');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            }
        });

        // Show loading indicator
        document.getElementById('gallery-container').innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="bi bi-hourglass-split text-5xl text-gray-400 animate-spin mb-3"></i>
                <p class="text-gray-500">{{ __('Loading gallery...') }}</p>
            </div>
        `;

        // Fetch gallery items via AJAX
        fetch(`/gallery/fetch?category=${encodeURIComponent(category)}`)
            .then(response => response.json())
            .then(data => {
                galleryItems = data.items;
                updateGalleryGrid();
                
                // Scroll to gallery section
                document.getElementById('gallery')?.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error fetching gallery:', error);
                document.getElementById('gallery-container').innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i class="bi bi-exclamation-triangle text-5xl text-red-400 mb-3"></i>
                        <p class="text-red-600">{{ __('Failed to load gallery.') }}</p>
                    </div>
                `;
            });
    }

    function updateGalleryGrid() {
        const container = document.getElementById('gallery-container');
        
        if (galleryItems.length === 0) {
            container.innerHTML = `
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                            <i class="bi bi-images text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Photos Found') }}</h3>
                        <p class="text-gray-500">{{ __('There are no photos in this category yet.') }}</p>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = galleryItems.map((item, index) => `
            <div class="gallery-item group relative aspect-square overflow-hidden rounded-xl bg-gray-100 cursor-pointer"
                 onclick="openGalleryModal(${index})"
                 data-item-id="${item.id}">
                
                ${item.image_url ? `
                    <img src="${item.image_url}" 
                         alt="${item.title}" 
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                         loading="lazy">
                ` : `
                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                        <i class="bi bi-image text-4xl text-gray-400"></i>
                    </div>
                `}

                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-end">
                    <div class="p-4 w-full bg-gradient-to-t from-black/70 to-transparent transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white font-medium text-sm truncate">${item.title}</p>
                        <p class="text-gray-300 text-xs">${item.category_text}</p>
                    </div>
                </div>

                <div class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow-lg">
                    <i class="bi bi-zoom-in text-gray-700 text-sm"></i>
                </div>
            </div>
        `).join('');
    }

    function openGalleryModal(index) {
        if (galleryItems.length === 0) return;
        
        currentGalleryIndex = index;
        document.getElementById('gallery-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        updateGalleryModal();
    }

    function updateGalleryModal() {
        const item = galleryItems[currentGalleryIndex];
        const modalContent = document.getElementById('gallery-modal-content');
        const modalCaption = document.getElementById('gallery-modal-caption');
        const modalCounter = document.getElementById('gallery-modal-counter');
        
        if (!item) return;
        
        modalContent.innerHTML = item.image_url ? `
            <img src="${item.image_url}" 
                 alt="${item.title}" 
                 class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-2xl">
        ` : `
            <div class="w-96 h-96 flex items-center justify-center bg-gray-800 rounded-lg">
                <i class="bi bi-image text-6xl text-gray-600"></i>
            </div>
        `;
        
        modalCaption.innerHTML = `
            <h3 class="text-white text-xl font-bold mb-2">${item.title}</h3>
            ${item.description ? `<p class="text-gray-300 text-sm max-w-2xl mx-auto">${item.description}</p>` : ''}
            <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium bg-blue-600 text-white">
                ${item.category_text}
            </span>
        `;
        
        modalCounter.innerHTML = `
            ${currentGalleryIndex + 1} / ${galleryItems.length}
        `;
    }

    function navigateGallery(direction) {
        if (galleryItems.length === 0) return;
        
        currentGalleryIndex += direction;
        
        if (currentGalleryIndex < 0) {
            currentGalleryIndex = galleryItems.length - 1;
        } else if (currentGalleryIndex >= galleryItems.length) {
            currentGalleryIndex = 0;
        }
        
        updateGalleryModal();
    }

    function closeGalleryModal(event) {
        if (event && event.target !== event.currentTarget) return;
        document.getElementById('gallery-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (document.getElementById('gallery-modal').classList.contains('hidden')) return;
        
        if (event.key === 'Escape') {
            closeGalleryModal();
        } else if (event.key === 'ArrowLeft') {
            navigateGallery(-1);
        } else if (event.key === 'ArrowRight') {
            navigateGallery(1);
        }
    });
</script>
