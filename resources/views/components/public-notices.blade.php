@props(['notices' => collect([]), 'audience' => 'all', 'counts' => []])

<section id="notices" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <span class="inline-block px-3 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded-full mb-3">
                {{ __('Stay Updated') }}
            </span>
            <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ __('Latest Notices') }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                {{ __('Stay informed with the latest announcements, events, and important updates from the institution.') }}
            </p>
        </div>

        <!-- Audience Filter Tabs -->
        <div class="flex flex-wrap justify-center gap-2 mb-8" id="audienceTabs">
            <button data-audience="all" class="audience-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $audience === 'all' ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                {{ __('All Notices') }}
                <span class="ml-1 text-xs opacity-75">({{ $counts['all'] ?? 0 }})</span>
            </button>
            <button data-audience="students" class="audience-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $audience === 'students' ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                <i class="bi bi-graduation-cap mr-1"></i>{{ __('Students') }}
                <span class="ml-1 text-xs opacity-75">({{ $counts['students'] ?? 0 }})</span>
            </button>
            <button data-audience="faculty" class="audience-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $audience === 'faculty' ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                <i class="bi bi-person-workspace mr-1"></i>{{ __('Faculty') }}
                <span class="ml-1 text-xs opacity-75">({{ $counts['faculty'] ?? 0 }})</span>
            </button>
            <button data-audience="parents" class="audience-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $audience === 'parents' ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                <i class="bi bi-people mr-1"></i>{{ __('Parents') }}
                <span class="ml-1 text-xs opacity-75">({{ $counts['parents'] ?? 0 }})</span>
            </button>
        </div>

        {{-- <!-- New Layout: Simple Notice Banner -->
        <div class="flex flex-col items-center justify-center mb-8">
            <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-6 flex flex-col md:flex-row items-center gap-6">
                <div class="flex-shrink-0">
                    <i class="bi bi-megaphone-fill text-5xl text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Professional Notice Portal</h3>
                    <p class="text-gray-700 mb-2">Access the latest professional and institutional notices, updates, and announcements curated for you.</p>
                    <a href="#noticesGrid" class="inline-block px-5 py-2 bg-red-600 text-white rounded-lg font-semibold shadow hover:bg-red-700 transition">View Notices</a>
                </div>
            </div>
        </div> --}}

        <!-- Notices Grid -->
        <div id="noticesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @include('components.public-notices-grid', ['notices' => $notices])
        </div>

        <!-- Loading Indicator -->
        <div id="noticesLoading" class="hidden text-center py-8">
            <div class="inline-flex items-center justify-center w-12 h-12 border-4 border-red-200 border-t-red-600 rounded-full animate-spin"></div>
            <p class="mt-3 text-gray-600">{{ __('Loading') }}...</p>
        </div>

        @if($notices->hasPages())
            <div class="mt-8 flex justify-center gap-2" id="paginationContainer">
                <x-pagination :paginator="$notices" />
            </div>
        @endif
    </div>
</section>

<!-- View Notice Modal -->
<div id="publicNoticeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" onclick="closePublicNoticeModal(event)">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <i class="bi bi-bell-fill text-white text-xl"></i>
                <h2 class="text-white font-bold text-lg">{{ __('Notice Details') }}</h2>
            </div>
            <button onclick="closePublicNoticeModal()" class="text-white hover:text-gray-100 transition hover:scale-110">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="publicNoticeContent">
            <!-- Content will be loaded dynamically -->
            <div class="text-center py-12">
                <i class="bi bi-hourglass-split text-5xl text-gray-400 animate-spin mb-3"></i>
                <p class="text-gray-500 text-sm">{{ __('Loading') }}...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 flex justify-between items-center gap-3 flex-shrink-0">
                <!-- Meta info will be populated -->
            </div>
            <div class="flex gap-2">
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <a href="{{ route('admin.notice-board') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition">
                        <i class="bi bi-pencil mr-1"></i>{{ __('Edit') }} {{ __('in') }} {{ __('Admin') }}
                    </a>
                @endif
                <button type="button" onclick="closePublicNoticeModal()" class="px-4 py-2 border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-100 transition">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for AJAX Filtering and Modal -->
<script>
    // Store notices data globally for modal functionality
    window.publicNoticesData = @json($notices->items());

    // Current filter state
    let currentAudience = '{{ $audience }}';
    let currentPage = {{ $notices->currentPage() }};

    // Audience tab click handler
    document.querySelectorAll('.audience-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const audience = this.dataset.audience;
            
            // Update active state
            document.querySelectorAll('.audience-btn').forEach(b => {
                b.classList.remove('bg-red-600', 'text-white', 'shadow-lg');
                b.classList.add('bg-white', 'text-gray-600', 'hover:bg-gray-100');
            });
            this.classList.remove('bg-white', 'text-gray-600', 'hover:bg-gray-100');
            this.classList.add('bg-red-600', 'text-white', 'shadow-lg');
            
            // Fetch new notices
            fetchNotices(audience, 1);
        });
    });

    // Fetch notices via AJAX
    function fetchNotices(audience, page = 1) {
        const loadingEl = document.getElementById('noticesLoading');
        const gridEl = document.getElementById('noticesGrid');
        
        // Show loading
        loadingEl.classList.remove('hidden');
        gridEl.classList.add('opacity-50');
        
        // Build URL with query params
        const url = new URL('/notices/fetch', window.location.origin);
        url.searchParams.set('audience', audience);
        url.searchParams.set('page', page);
        
        fetch(url.toString())
            .then(response => response.json())
            .then(data => {
                // Update the grid
                gridEl.innerHTML = data.notices.map(notice => createNoticeCard(notice)).join('');
                
                // Update global data for modal
                window.publicNoticesData = data.notices;
                
                // Update pagination
                updatePagination(data, audience);
                
                currentAudience = audience;
                currentPage = data.current_page;
            })
            .catch(error => {
                console.error('Error fetching notices:', error);
                gridEl.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                            <i class="bi bi-exclamation-triangle text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Error Loading Notices') }}</h3>
                        <p class="text-gray-600">{{ __('Please try again') }}</p>
                    </div>
                `;
            })
            .finally(() => {
                loadingEl.classList.add('hidden');
                gridEl.classList.remove('opacity-50');
            });
    }

    // Create notice card HTML
    function createNoticeCard(notice) {
        const isImportant = notice.is_important;
        const creatorName = notice.creator ? notice.creator.name : 'Admin';
        const creatorInitials = creatorName.substring(0, 2).toUpperCase();
        const date = notice.published_at_bs || 'N/A';
        const audienceText = {
            'all': 'All',
            'students': 'Students',
            'faculty': 'Faculty',
            'parents': 'Parents'
        }[notice.audience] || notice.audience;

        return `
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 notice-card"
                 data-notice-id="${notice.id}"
                 data-audience="${notice.audience}">
                <div class="h-2 ${isImportant ? 'bg-gradient-to-r from-red-500 to-orange-500' : 'bg-gradient-to-r from-blue-500 to-cyan-500'}"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                            <i class="bi bi-calendar3"></i>
                            ${date}
                        </span>
                        ${isImportant ? `
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-semibold">
                                <i class="bi bi-star-fill"></i>{{ __('Important') }}
                            </span>
                        ` : ''}
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                        ${notice.title}
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        ${notice.message.substring(0, 120)}${notice.message.length > 120 ? '...' : ''}
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                ${creatorInitials}
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">${creatorName}</p>
                                <p class="text-xs text-gray-500">${audienceText}</p>
                            </div>
                        </div>
                        <button onclick="openPublicNoticeModal(${notice.id})"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="bi bi-eye"></i>
                            {{ __('View') }}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Update pagination
    function updatePagination(data, audience) {
        const container = document.getElementById('paginationContainer');
        if (!container) return;
        
        if (data.last_page > 1) {
            let paginationHtml = '<div class="flex gap-2">';
            
            // Previous button
            if (data.current_page > 1) {
                paginationHtml += `<button onclick="fetchNotices('${audience}', ${data.current_page - 1})" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">{{ __('Previous') }}</button>`;
            }
            
            // Page numbers
            for (let i = 1; i <= data.last_page; i++) {
                paginationHtml += `<button onclick="fetchNotices('${audience}', ${i})" class="px-3 py-1 rounded ${i === data.current_page ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'} transition">${i}</button>`;
            }
            
            // Next button
            if (data.has_more) {
                paginationHtml += `<button onclick="fetchNotices('${audience}', ${data.current_page + 1})" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">{{ __('Next') }}</button>`;
            }
            
            paginationHtml += '</div>';
            container.innerHTML = paginationHtml;
        } else {
            container.innerHTML = '';
        }
    }

    // Open Notice Modal
    function openPublicNoticeModal(noticeId) {
        const modal = document.getElementById('publicNoticeModal');
        const content = document.getElementById('publicNoticeContent');
        const footer = document.getElementById('publicNoticeMetaFooter');
        
        // Find notice data
        const notice = window.publicNoticesData.find(n => n.id === noticeId);
        
        if (!notice) {
            // Fetch notice data via AJAX if not in memory
            fetch(`/notices/${noticeId}`)
                .then(response => response.json())
                .then(data => {
                    displayNoticeInModal(data.notice);
                })
                .catch(error => {
                    console.error('Error loading notice:', error);
                    content.innerHTML = `
                        <div class="text-center py-12">
                            <i class="bi bi-exclamation-triangle text-5xl text-red-400 mb-3"></i>
                            <p class="text-red-600 font-semibold mb-1">{{ __('Failed to Load Notice') }}</p>
                            <p class="text-gray-600 text-sm">{{ __('There was an error loading the notice details. Please try again.') }}</p>
                        </div>
                    `;
                });
        } else {
            displayNoticeInModal(notice);
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Display Notice in Modal
    function displayNoticeInModal(notice) {
        const content = document.getElementById('publicNoticeContent');
        const footer = document.getElementById('publicNoticeMetaFooter');
        
        // Format semester text
        let semesterText = notice.semester ? `${notice.semester}<sup>th</sup> {{ __('Semester') }}` : '{{ __('All Semesters') }}';
        
        // Get subject info
        let subjectText = notice.subject ? notice.subject.subject_name : '{{ __('General') }}';
        let subjectCode = notice.subject ? notice.subject.subject_code : '';
        let subjectDisplay = subjectCode ? `<strong>${subjectText}</strong> <span class="text-gray-600">(${subjectCode})</span>` : `<strong>${subjectText}</strong>`;
        
        // Format importance flag
        let importanceDisplay = notice.is_important ? 
            '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-red-100 text-red-700 font-semibold"><i class="bi bi-exclamation-circle-fill"></i> {{ __("Important") }}</span>' :
            '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700 font-medium"><i class="bi bi-info-circle"></i> {{ __("Standard") }}</span>';
        
        // Format audience text
        let audienceDisplay = '';
        const audienceText = {
            'all': '<span class="inline-flex items-center gap-1"><i class="bi bi-people-fill"></i> {{ __("All") }}</span>',
            'students': '<span class="inline-flex items-center gap-1 text-blue-600"><i class="bi bi-graduation-cap"></i> {{ __("Students") }}</span>',
            'faculty': '<span class="inline-flex items-center gap-1 text-green-600"><i class="bi bi-person-workspace"></i> {{ __("Faculty") }}</span>',
            'parents': '<span class="inline-flex items-center gap-1 text-orange-600"><i class="bi bi-people"></i> {{ __("Parents") }}</span>'
        };
        audienceDisplay = audienceText[notice.audience] || notice.audience;
        
        // Format file attachment
        let fileDisplay = '';
        if (notice.file_name && notice.file_path) {
            const downloadUrl = `/storage/${notice.file_path}`;
            const fileIcon = getFileIcon(notice.file_name);
            fileDisplay = `
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi ${fileIcon} text-blue-600 text-lg"></i>
                        <p class="text-xs font-semibold text-blue-900 uppercase tracking-wide">{{ __('Attachment') }}</p>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-3 bg-white rounded border border-blue-200">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <i class="bi ${fileIcon} text-blue-600 text-sm"></i>
                            <a href="${downloadUrl}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-600 hover:text-blue-800 underline font-medium truncate">
                                ${notice.file_name}
                            </a>
                        </div>
                        <a href="${downloadUrl}" download class="text-blue-600 hover:text-blue-800 text-lg hover:scale-110 transition flex-shrink-0" title="{{ __('Download') }}">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            `;
        }

        // Get formatted dates
        const createdDate = new Date(notice.created_at);
        const createdDateStr = createdDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const createdTimeStr = createdDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        
        // Update footer meta
        footer.innerHTML = `
            <div class="space-y-1">
                <p class="text-gray-600"><span class="font-medium">{{ __('Published') }}:</span> ${createdDateStr} at ${createdTimeStr}</p>
                <p class="text-gray-600"><span class="font-medium">{{ __('By') }}:</span> ${notice.creator ? notice.creator.name : '{{ __('Admin') }}'}</p>
            </div>
        `;
        
        content.innerHTML = `
            <div class="flex flex-wrap items-center gap-2">
                ${importanceDisplay}
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    <i class="bi bi-check-circle-fill mr-1"></i>{{ __('Published') }}
                </span>
            </div>

            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Title') }}</p>
                <p class="text-xl font-bold text-gray-900 leading-snug">${notice.title}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">{{ __('Course') }}</p>
                    <p class="text-sm text-gray-900">${subjectDisplay}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">{{ __('Semester') }}</p>
                    <p class="text-sm text-gray-900">${semesterText}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">{{ __('Audience') }}</p>
                    <p class="text-sm text-gray-900 font-medium">${audienceDisplay}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">{{ __('Published') }} ({{ __('Date') }})</p>
                    <p class="text-sm text-gray-900 font-medium">${notice.published_at_bs || 'N/A'}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-3">{{ __('Content') }}</p>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${notice.message}</p>
            </div>

            ${fileDisplay}

            ${notice.is_important ? `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-700 text-sm font-medium">
                        <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                        {{ __('This is an important notice. Please take note of the information provided above.') }}
                    </p>
                </div>
            ` : ''}
        `;
    }

    // Get file icon based on extension
    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            'pdf': 'bi-file-earmark-pdf-fill',
            'doc': 'bi-file-earmark-word-fill',
            'docx': 'bi-file-earmark-word-fill',
            'xls': 'bi-file-earmark-excel-fill',
            'xlsx': 'bi-file-earmark-excel-fill',
            'ppt': 'bi-file-earmark-ppt-fill',
            'pptx': 'bi-file-earmark-ppt-fill',
            'jpg': 'bi-file-earmark-image-fill',
            'jpeg': 'bi-file-earmark-image-fill',
            'png': 'bi-file-earmark-image-fill',
            'gif': 'bi-file-earmark-image-fill',
            'zip': 'bi-file-earmark-zip-fill',
            'rar': 'bi-file-earmark-zip-fill'
        };
        return icons[ext] || 'bi-file-earmark-fill';
    }

    // Close Notice Modal
    function closePublicNoticeModal(event) {
        if (event && event.target !== event.currentTarget) return;
        
        const modal = document.getElementById('publicNoticeModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('publicNoticeModal');
            if (!modal.classList.contains('hidden')) {
                closePublicNoticeModal();
            }
        }
    });
</script>


