{{-- Subject Detail View Modal --}}
{{-- Displays complete subject information in a professional, accessible modal dialog --}}

<div id="subjectDetailModal" 
     class="hidden fixed inset-0 z-50" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="subjectDetailModalTitle"
     aria-hidden="true">
    
    <!-- Modal Background Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
         onclick="closeSubjectDetailModal()" 
         aria-hidden="true"></div>

    <!-- Modal Content Container -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all"
             role="document"
             id="subjectDetailModalContent">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="bi bi-book-fill text-xl"></i>
                    </div>
                    <div>
                        <h2 id="subjectDetailModalTitle" class="text-lg font-bold" tabindex="-1">Course Details</h2>
                        <p id="subjectDetailModalSubtitle" class="text-red-100 text-xs mt-0.5">View complete course information</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Loading Spinner -->
                    <div id="subjectDetailLoadingSpinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <!-- Close Button -->
                    <button onclick="closeSubjectDetailModal()" 
                            class="text-red-200 hover:text-white hover:bg-white/10 rounded-lg p-2 transition-colors"
                            aria-label="Close modal">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto flex-1 p-6">
                
                <!-- Error Display -->
                <div id="subjectDetailError" 
                     class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="subjectDetailErrorMessage">Failed to load subject details. Please try again.</span>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="subjectDetailLoading" class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mb-4"></div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Loading subject details...</p>
                </div>

                <!-- Subject Details Content -->
                <div id="subjectDetailContent" class="hidden space-y-6">
                    
                    <!-- Basic Information Section -->
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4 pb-2 border-b border-gray-200 dark:border-slate-600">
                            <i class="bi bi-info-circle text-red-600"></i>
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Subject Name -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Course Name</label>
                                <p id="detailSubjectName" class="text-gray-900 dark:text-white font-medium">—</p>
                            </div>
                            
                            <!-- Subject Code -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Course Code</label>
                                <p id="detailSubjectCode" class="text-gray-900 dark:text-white">—</p>
                            </div>
                            
                            <!-- Credits -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Credit Hours</label>
                                <p id="detailCredits" class="text-gray-900 dark:text-white">—</p>
                            </div>

                            <!-- Semester -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Semester</label>
                                <p id="detailSemester" class="text-gray-900 dark:text-white">—</p>
                            </div>

                            <!-- Assigned Teacher -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Assigned Teacher</label>
                                <p id="detailTeacher" class="text-gray-900 dark:text-white">—</p>
                            </div>
                        </div>
                    </div>

                    <!-- Elective Settings -->
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4 pb-2 border-b border-gray-200 dark:border-slate-600">
                            <i class="bi bi-people text-red-600"></i>
                            Elective Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Subject Type</label>
                                <p id="detailSubjectTypeText" class="text-gray-900 dark:text-white">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Has Lab</label>
                                <p id="detailHasLab" class="text-gray-900 dark:text-white">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lab Technician</label>
                                <p id="detailLabTech" class="text-gray-900 dark:text-white">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lab Document</label>
                                <p id="detailLabDocument" class="text-gray-900 dark:text-white text-sm">—</p>
                            </div>
                        </div>

                        <div id="electiveSettingsSection" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-slate-600">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Max Students (for Electives)</label>
                                    <p id="detailMaxStudents" class="text-gray-900 dark:text-white">—</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Min Students (for Electives)</label>
                                    <p id="detailMinStudents" class="text-gray-900 dark:text-white">—</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Elective Group</label>
                                    <p id="detailElectiveGroup" class="text-gray-900 dark:text-white">—</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Elective Enrollment Open</label>
                                    <p id="detailElectiveOpenText" class="text-gray-900 dark:text-white">—</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4 pb-2 border-b border-gray-200 dark:border-slate-600">
                            <i class="bi bi-card-text text-red-600"></i>
                            Additional Details
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                                <p id="detailDescription" class="text-gray-900 dark:text-white text-sm whitespace-pre-line">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Prerequisite</label>
                                <p id="detailPrerequisite" class="text-gray-900 dark:text-white text-sm whitespace-pre-line">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Remarks</label>
                                <p id="detailRemarks" class="text-gray-900 dark:text-white text-sm whitespace-pre-line">—</p>
                            </div>
                        </div>
                    </div>

 <!-- Teaching Hours Section -->
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4 pb-2 border-b border-gray-200 dark:border-slate-600">
                            <i class="bi bi-clock text-red-600"></i>
                            Teaching Hours Per Week
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-3 bg-white dark:bg-slate-800 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lecture</p>
                                <p id="detailLectureHours" class="text-xl font-bold text-gray-900 dark:text-white">— hrs</p>
                            </div>
                            <div class="text-center p-3 bg-white dark:bg-slate-800 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Practical</p>
                                <p id="detailPracticalHours" class="text-xl font-bold text-gray-900 dark:text-white">— hrs</p>
                            </div>
                            <div class="text-center p-3 bg-white dark:bg-slate-800 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tutorial</p>
                                <p id="detailTutorialHours" class="text-xl font-bold text-gray-900 dark:text-white">— hrs</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4 pb-2 border-b border-gray-200 dark:border-slate-600">
                            <i class="bi bi-check-circle text-red-600"></i>
                            Status
                        </h3>
                        <span id="detailStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">—</span>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 dark:bg-slate-700/50 px-6 py-4 flex items-center justify-end flex-shrink-0 border-t border-gray-200 dark:border-slate-600">
                <div class="flex items-center gap-3">
                    <button onclick="closeSubjectDetailModal()" 
                            class="px-4 py-2 border border-gray-300 dark:border-slate-500 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-600 font-medium text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-slate-500">
                        <i class="bi bi-x-lg mr-1"></i>
                        Close
                    </button>
                    <button id="editSubjectBtn" 
                            onclick="editSubjectFromDetail()"
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-300 flex items-center gap-2">
                        <i class="bi bi-pencil"></i>
                        Edit Course
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Store current subject ID for edit functionality
    let currentSubjectId = null;

    /**
     * Open the subject detail modal and load data
     */
    function openSubjectDetailModal(subjectId) {
        currentSubjectId = subjectId;
        
        const modal = document.getElementById('subjectDetailModal');
        const loading = document.getElementById('subjectDetailLoading');
        const content = document.getElementById('subjectDetailContent');
        const error = document.getElementById('subjectDetailError');
        const spinner = document.getElementById('subjectDetailLoadingSpinner');
        
        // Reset state
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        error.classList.add('hidden');
        spinner.classList.add('hidden');
        
        // Update ARIA state
        modal.setAttribute('aria-hidden', 'false');
        
        // Focus management - focus on modal
        setTimeout(() => {
            document.getElementById('subjectDetailModalTitle').focus();
        }, 100);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Load subject data via AJAX
        loadSubjectDetails(subjectId);
    }

    /**
     * Close the subject detail modal
     */
    function closeSubjectDetailModal() {
        const modal = document.getElementById('subjectDetailModal');
        
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        
        // Restore body scroll
        document.body.style.overflow = '';
        
        // Reset current subject ID
        currentSubjectId = null;
    }

    /**
     * Load subject details via AJAX
     */
    async function loadSubjectDetails(subjectId) {
        const spinner = document.getElementById('subjectDetailLoadingSpinner');
        const loading = document.getElementById('subjectDetailLoading');
        const content = document.getElementById('subjectDetailContent');
        const error = document.getElementById('subjectDetailError');
        const errorMessage = document.getElementById('subjectDetailErrorMessage');
        
        spinner.classList.remove('hidden');
        
        try {
            const response = await fetch(`{{ route('admin.courses.detail', ['id' => '__ID__']) }}`.replace('__ID__', subjectId), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                populateSubjectDetails(data.subject);
                loading.classList.add('hidden');
                content.classList.remove('hidden');
            } else {
                throw new Error(data.message || 'Failed to load subject details');
            }
            
        } catch (err) {
            console.error('Error loading subject details:', err);
            errorMessage.textContent = err.message || 'Failed to load subject details. Please try again.';
            error.classList.remove('hidden');
            loading.classList.add('hidden');
        } finally {
            spinner.classList.add('hidden');
        }
    }

    /**
      * Populate the modal with subject data
      */
    function populateSubjectDetails(subject) {
        const safeText = (value, fallback = '—') => {
            const v = (value ?? '').toString().trim();
            return v !== '' ? v : fallback;
        };

        const formatSemester = (value) => {
            const v = (value ?? '').toString().trim();
            if (!v) return 'All Semesters';
            const num = parseInt(v, 10);
            if (!Number.isNaN(num) && num >= 1 && num <= 6) {
                const suffix = (num % 10 === 1 && num !== 11) ? 'st'
                    : (num % 10 === 2 && num !== 12) ? 'nd'
                    : (num % 10 === 3 && num !== 13) ? 'rd'
                    : 'th';
                return `${num}${suffix} Semester`;
            }
            return v;
        };

        const formatSubjectType = (value) => {
            const v = (value ?? '').toString().trim().toLowerCase();
            const map = { core: 'Core Subject', elective: 'Elective Subject', optional: 'Optional Subject' };
            return map[v] || (v ? (v.charAt(0).toUpperCase() + v.slice(1)) : '—');
        };

        // Basic Information
        document.getElementById('detailSubjectName').textContent = safeText(subject.subject_name);
        document.getElementById('detailSubjectCode').textContent = safeText(subject.subject_code);
        document.getElementById('detailCredits').textContent = safeText(subject.credits);
        document.getElementById('detailSemester').textContent = formatSemester(subject.semester);
        document.getElementById('detailTeacher').textContent = safeText(subject.teacher_name, 'Not Assigned');

        // Elective & Lab Settings
        document.getElementById('detailSubjectTypeText').textContent = formatSubjectType(subject.subject_type);
        document.getElementById('detailHasLab').textContent = (subject.has_lab ?? false) ? 'Yes' : 'No';
        document.getElementById('detailLabTech').textContent = safeText(subject.lab_technician_name, 'Not Assigned');

        const labDocumentEl = document.getElementById('detailLabDocument');
        if (subject.lab_document) {
            const url = subject.lab_document;
            labDocumentEl.innerHTML = `<a href="${url}" target="_blank" class="text-blue-600 hover:underline">Download</a>`;
        } else {
            labDocumentEl.textContent = '—';
        }

        const electiveSettingsSection = document.getElementById('electiveSettingsSection');
        const isElective = (subject.subject_type ?? '').toString().trim().toLowerCase() === 'elective';
        electiveSettingsSection?.classList.toggle('hidden', !isElective);
        if (isElective) {
            document.getElementById('detailMaxStudents').textContent = safeText(subject.max_students, 'Not Set');
            document.getElementById('detailMinStudents').textContent = safeText(subject.min_students, 'Not Set');
            document.getElementById('detailElectiveGroup').textContent = safeText(subject.elective_group, 'Not Set');
            document.getElementById('detailElectiveOpenText').textContent = subject.is_elective_open ? 'Yes' : 'No';
        }

        // Additional Details
        document.getElementById('detailDescription').textContent = safeText(subject.description);
        document.getElementById('detailPrerequisite').textContent = safeText(subject.prerequisite);
        document.getElementById('detailRemarks').textContent = safeText(subject.remarks);

        // Teaching Hours
        document.getElementById('detailLectureHours').textContent = (subject.lecture_hours ?? 0) + ' hrs';
        document.getElementById('detailPracticalHours').textContent = (subject.practical_hours ?? 0) + ' hrs';
        document.getElementById('detailTutorialHours').textContent = (subject.tutorial_hours ?? 0) + ' hrs';

        // Status Badge
        const statusEl = document.getElementById('detailStatus');
        const statusValue = (subject.status ?? '').toString().trim().toLowerCase();
        const isActive = statusValue === 'active';
        statusEl.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'}`;
        statusEl.textContent = safeText(statusValue ? (statusValue.charAt(0).toUpperCase() + statusValue.slice(1)) : '');
    }

    /**
     * Edit subject from detail modal - triggers the existing edit modal
     */
    function editSubjectFromDetail() {
        if (currentSubjectId) {
            closeSubjectDetailModal();
            // Call the existing editCourse function from the main page
            if (typeof editCourse === 'function') {
                editCourse(currentSubjectId);
            }
        }
    }

    // Keyboard navigation support
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('subjectDetailModal');
        if (!modal || modal.classList.contains('hidden')) return;
        
        if (e.key === 'Escape') {
            closeSubjectDetailModal();
        }
        
        // Trap focus within modal
        if (e.key === 'Tab') {
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    });

    // Close modal on background click
    document.getElementById('subjectDetailModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeSubjectDetailModal();
        }
    });
</script>
@endpush
