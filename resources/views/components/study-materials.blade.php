@props(['materials' => collect([]), 'counts' => [], 'subjects' => collect([]), 'selectedSemester' => '', 'selectedSubject' => '', 'searchQuery' => ''])

<section id="study-materials" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <span class="inline-block px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full mb-3">
                Study Resources
            </span>
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Study Materials</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Access lecture notes, assignments, previous year papers, and other study resources to support your learning.
            </p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-gray-50 rounded-xl p-4 mb-8">
            <form id="materialsFilterForm" class="flex flex-wrap gap-4 items-end">
                <!-- Search Bar -->
                <div class="flex-1 min-w-[250px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search Materials</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="materialsSearch" name="search" value="{{ $searchQuery }}"
                            placeholder="Search by title, description..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <!-- Semester Filter -->
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                    <select id="semesterFilter" name="semester" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Semesters</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ $selectedSemester == $i ? 'selected' : '' }}>
                                {{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Subject Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                    <select id="subjectFilter" name="subject" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $selectedSubject == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_name }} ({{ $subject->subject_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Material Type Filter -->
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                    <select id="typeFilter" name="type" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Types</option>
                        <option value="lecture_notes">Notes</option>
                        <option value="assignment">Assignments</option>
                        <option value="assessment">Papers</option>
                        <option value="lab_report">Lab Reports</option>
                        <option value="study_guide">Study Guides</option>
                        <option value="syllabus">Syllabus</option>
                        <option value="project_material">Project Materials</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1"> </label>
                    <button type="button" id="resetMaterialsFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
                        <i class="bi bi-arrow-clockwise mr-1"></i>Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFilters" class="flex flex-wrap gap-2 mb-6">
            <!-- Will be populated by JavaScript -->
        </div>

        <!-- Material Type Quick Filter Tabs -->
        <div class="flex flex-wrap justify-center gap-2 mb-8" id="materialTypeTabs">
            <button data-type="all" class="material-type-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-green-600 text-white shadow-lg">
                All
                <span class="ml-1 text-xs opacity-75">({{ $counts['all'] ?? count($materials) }})</span>
            </button>
            <button data-type="lecture_notes" class="material-type-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100">
                <i class="bi bi-journal-text mr-1"></i>Notes
                <span class="ml-1 text-xs opacity-75">({{ $counts['lecture_notes'] ?? 0 }})</span>
            </button>
            <button data-type="assignment" class="material-type-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100">
                <i class="bi bi-pencil-square mr-1"></i>Assignments
                <span class="ml-1 text-xs opacity-75">({{ $counts['assignment'] ?? 0 }})</span>
            </button>
            <button data-type="assessment" class="material-type-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100">
                <i class="bi bi-file-earmark-ruled mr-1"></i>Papers
                <span class="ml-1 text-xs opacity-75">({{ $counts['assessment'] ?? 0 }})</span>
            </button>
            <button data-type="lab_report" class="material-type-btn px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100">
                <i class="bi bi-flask mr-1"></i>Lab Reports
                <span class="ml-1 text-xs opacity-75">({{ $counts['lab_report'] ?? 0 }})</span>
            </button>
        </div>

        <!-- Materials Grid -->
        <div id="materialsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @include('components.study-materials-grid', ['materials' => $materials])
        </div>

        <!-- Loading Indicator -->
        <div id="materialsLoading" class="hidden text-center py-8">
            <div class="inline-flex items-center justify-center w-12 h-12 border-4 border-green-200 border-t-green-600 rounded-full animate-spin"></div>
            <p class="mt-3 text-gray-600">Loading materials...</p>
        </div>

        <!-- No Results Message -->
        <div id="noMaterialsMessage" class="hidden text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="bi bi-search text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Materials Found</h3>
            <p class="text-gray-600">Try adjusting your filters or search terms.</p>
        </div>

        <!-- View All Link -->
        @if(count($materials) > 0)
        <div class="text-center mt-10">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition shadow-lg hover:shadow-xl">
                <i class="bi bi-arrow-right"></i>
                View All Study Materials
            </a>
        </div>
        @endif
    </div>
</section>

<!-- JavaScript for Filtering -->
<script>
    // Store materials data globally
    window.studyMaterialsData = @json($materials);
    window.allSubjects = @json($subjects);

    // Current filter state
    let currentFilters = {
        search: '{{ $searchQuery }}',
        semester: '{{ $selectedSemester }}',
        subject: '{{ $selectedSubject }}',
        type: ''
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateActiveFilters();
    });

    // Search input handler with debounce
    let searchTimeout;
    document.getElementById('materialsSearch').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = e.target.value;
            applyFilters();
        }, 300);
    });

    // Filter change handlers
    document.getElementById('semesterFilter').addEventListener('change', function(e) {
        currentFilters.semester = e.target.value;
        applyFilters();
    });

    document.getElementById('subjectFilter').addEventListener('change', function(e) {
        currentFilters.subject = e.target.value;
        applyFilters();
    });

    document.getElementById('typeFilter').addEventListener('change', function(e) {
        currentFilters.type = e.target.value;
        applyFilters();
    });

    // Reset button handler
    document.getElementById('resetMaterialsFilters').addEventListener('click', function() {
        currentFilters = { search: '', semester: '', subject: '', type: '' };
        document.getElementById('materialsSearch').value = '';
        document.getElementById('semesterFilter').value = '';
        document.getElementById('subjectFilter').value = '';
        document.getElementById('typeFilter').value = '';
        updateActiveFilters();
        renderMaterials(window.studyMaterialsData);
    });

    // Material type tab click handler
    document.querySelectorAll('.material-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Update active state
            document.querySelectorAll('.material-type-btn').forEach(b => {
                b.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                b.classList.add('bg-white', 'text-gray-600', 'hover:bg-gray-100');
            });
            this.classList.remove('bg-white', 'text-gray-600', 'hover:bg-gray-100');
            this.classList.add('bg-green-600', 'text-white', 'shadow-lg');
            
            currentFilters.type = type === 'all' ? '' : type;
            document.getElementById('typeFilter').value = currentFilters.type;
            applyFilters();
        });
    });

    // Apply all filters
    function applyFilters() {
        updateActiveFilters();
        
        let filtered = window.studyMaterialsData.filter(material => {
            // Search filter
            if (currentFilters.search) {
                const searchLower = currentFilters.search.toLowerCase();
                const matchesSearch = material.title.toLowerCase().includes(searchLower) ||
                    (material.description && material.description.toLowerCase().includes(searchLower));
                if (!matchesSearch) return false;
            }
            
            // Semester filter
            if (currentFilters.semester && material.semester != currentFilters.semester) {
                return false;
            }
            
            // Subject filter
            if (currentFilters.subject) {
                if (!material.subject || material.subject.id != currentFilters.subject) {
                    return false;
                }
            }
            
            // Type filter
            if (currentFilters.type && material.document_type !== currentFilters.type) {
                return false;
            }
            
            return true;
        });
        
        renderMaterials(filtered);
    }

    // Update active filters display
    function updateActiveFilters() {
        const container = document.getElementById('activeFilters');
        let filters = [];
        
        if (currentFilters.search) {
            filters.push(`<span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs"><i class="bi bi-search"></i> "${currentFilters.search}" <button onclick="removeFilter('search')" class="hover:text-blue-900"><i class="bi bi-x"></i></button></span>`);
        }
        
        if (currentFilters.semester) {
            const semesterText = currentFilters.semester + (currentFilters.semester == 1 ? 'st' : (currentFilters.semester == 2 ? 'nd' : (currentFilters.semester == 3 ? 'rd' : 'th'))) + ' Semester';
            filters.push(`<span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs"><i class="bi bi-calendar3"></i> ${semesterText} <button onclick="removeFilter('semester')" class="hover:text-green-900"><i class="bi bi-x"></i></button></span>`);
        }
        
        if (currentFilters.subject) {
            const subject = window.allSubjects.find(s => s.id == currentFilters.subject);
            const subjectName = subject ? subject.subject_name : 'Subject';
            filters.push(`<span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs"><i class="bi bi-book"></i> ${subjectName} <button onclick="removeFilter('subject')" class="hover:text-purple-900"><i class="bi bi-x"></i></button></span>`);
        }
        
        if (currentFilters.type) {
            const typeNames = {
                'lecture_notes': 'Notes',
                'assignment': 'Assignments',
                'assessment': 'Papers',
                'lab_report': 'Lab Reports',
                'study_guide': 'Study Guides',
                'syllabus': 'Syllabus',
                'project_material': 'Project Materials'
            };
            filters.push(`<span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs"><i class="bi bi-folder"></i> ${typeNames[currentFilters.type] || currentFilters.type} <button onclick="removeFilter('type')" class="hover:text-orange-900"><i class="bi bi-x"></i></button></span>`);
        }
        
        container.innerHTML = filters.join('');
    }

    // Remove individual filter
    function removeFilter(filterName) {
        currentFilters[filterName] = '';
        
        // Update UI
        if (filterName === 'search') {
            document.getElementById('materialsSearch').value = '';
        } else if (filterName === 'semester') {
            document.getElementById('semesterFilter').value = '';
        } else if (filterName === 'subject') {
            document.getElementById('subjectFilter').value = '';
        } else if (filterName === 'type') {
            document.getElementById('typeFilter').value = '';
            // Reset tab
            document.querySelectorAll('.material-type-btn').forEach(b => {
                b.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                b.classList.add('bg-white', 'text-gray-600', 'hover:bg-gray-100');
            });
            document.querySelector('[data-type="all"]').classList.remove('bg-white', 'text-gray-600', 'hover:bg-gray-100');
            document.querySelector('[data-type="all"]').classList.add('bg-green-600', 'text-white', 'shadow-lg');
        }
        
        applyFilters();
    }

    // Make removeFilter available globally
    window.removeFilter = removeFilter;

    // Render materials grid
    function renderMaterials(materials) {
        const grid = document.getElementById('materialsGrid');
        const loading = document.getElementById('materialsLoading');
        const noResults = document.getElementById('noMaterialsMessage');
        
        if (materials.length === 0) {
            grid.innerHTML = '';
            grid.classList.add('hidden');
            noResults.classList.remove('hidden');
            return;
        }
        
        noResults.classList.add('hidden');
        grid.classList.remove('hidden');
        
        grid.innerHTML = materials.map(material => {
            const typeColors = {
                'lecture_notes': 'from-blue-500 to-cyan-500',
                'assignment': 'from-green-500 to-emerald-500',
                'assessment': 'from-orange-500 to-yellow-500',
                'lab_report': 'from-purple-500 to-pink-500',
                'study_guide': 'from-indigo-500 to-purple-500',
                'syllabus': 'from-pink-500 to-rose-500',
                'project_material': 'from-gray-500 to-gray-700',
            };
            
            const colorClass = typeColors[material.document_type] || 'from-gray-500 to-gray-600';
            const typeText = {
                'lecture_notes': 'Notes',
                'assignment': 'Assignment',
                'assessment': 'Paper',
                'lab_report': 'Lab Report',
                'study_guide': 'Guide',
                'syllabus': 'Syllabus',
                'project_material': 'Project',
            }[material.document_type] || 'Material';
            
            return `
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 material-card"
                     data-type="${material.document_type}" data-semester="${material.semester}" data-subject="${material.subject?.id || ''}">
                    <div class="h-2 bg-gradient-to-r ${colorClass}"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                                <i class="bi bi-calendar3"></i>
                                ${material.semester || 'N/A'} Semester
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                                ${typeText}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">${material.title}</h3>
                        ${material.description ? `<p class="text-gray-600 text-sm mb-4 line-clamp-2">${material.description}</p>` : ''}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    ${(material.subject?.subject_name?.substring(0, 2) || 'SU').toUpperCase()}
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-900">${material.subject?.subject_name || 'General'}</p>
                                    <p class="text-xs text-gray-500">${material.formatted_size || 'N/A'}</p>
                                </div>
                            </div>
                            ${material.file_path ? `
                                <a href="/admin/study-material/download/${material.id}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    <i class="bi bi-download"></i>
                                    Download
                                </a>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
</script>
