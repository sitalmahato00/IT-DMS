@php
    $editExamComponentDefinitions = [
        'ctevt' => [
            ['key' => 'theory_internal', 'label' => 'Theory Internal'],
            ['key' => 'theory_external', 'label' => 'Theory External'],
            ['key' => 'practical_internal', 'label' => 'Practical Internal'],
            ['key' => 'practical_external', 'label' => 'Practical External'],
        ],
    ];

    $editExamComponentFields = [
        'ctevt' => [
            'theory_internal' => ['max' => 'theory_internal_max_marks', 'pass' => 'theory_internal_pass_marks'],
            'theory_external' => ['max' => 'theory_external_max_marks', 'pass' => 'theory_external_pass_marks'],
            'practical_internal' => ['max' => 'practical_internal_max_marks', 'pass' => 'practical_internal_pass_marks'],
            'practical_external' => ['max' => 'practical_external_max_marks', 'pass' => 'practical_external_pass_marks'],
        ],
    ];

    $editExamDefaultCategory = 'assessment';
    $editSemesterOptions = $activeSemesters ?? $semesters ?? [];
@endphp

<!-- Edit Exam Modal -->
<div id="editExamModal" class="hidden fixed inset-0 z-50">
    <!-- Modal Background Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeEditExamModal()"></div>

    <!-- Modal Content -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-3 flex items-center justify-between z-10">
            <div>
                <h2 class="text-sm font-bold">Edit Exam</h2>
                <p class="text-red-100 text-xs mt-0.5">Update exam details and settings</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="editLoadingSpinner" class="hidden">
                    <i class="bi bi-arrow-repeat text-white text-lg animate-spin"></i>
                </div>
                <button onclick="closeEditExamModal()" class="text-red-200 hover:text-white">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
        </div>
        <div class="overflow-y-auto flex-1 p-6">
            <!-- Error display -->
            <div id="editExamErrors" class="hidden bg-red-100 border border-red-400 text-blue-700 px-4 py-2 rounded mb-3 text-xs"></div>
            
            <form id="editExamForm" action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @csrf
                @method('PUT')

                <!-- Academic Information Section -->
                <div class="lg:col-span-3">
                    <h4 class="text-xs font-semibold text-gray-900 bg-gray-100 p-2 rounded mb-2">Academic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                            <select name="semester" id="editSemester" required onchange="loadSubjectsForEditExam()" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                                <option value="">Select Semester</option>
                                <option value="all">All Semesters</option>
                                @foreach($editSemesterOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject *</label>
                            <select name="subject_id" id="editSubject" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                                <option value="">Select Subject</option>
                                <option value="all">All Subjects</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Exam Details Section -->
                <div class="lg:col-span-3">
                    <h4 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Exam Details</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div id="editAssessmentNumberField">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Assessment Number</label>
                            <input type="number" name="assessment_number" id="editAssessmentNumber" min="1" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" placeholder="Auto-generated">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Category *</label>
                            <select name="exam_category" id="editExamCategory" required class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" onchange="handleEditExamCategoryChange()">
                                <option value="assessment">Assessment</option>
                                <option value="ctevt">CTEVT</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name *</label>
                        <input type="text" name="exam_name" id="editExamName" placeholder="Enter exam name" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                    </div>
                    <input type="hidden" name="full_marks" id="editFullMarks">
                    <input type="hidden" name="passing_marks" id="editPassingMarks">
                    <div id="assessmentEditFields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks *</label>
                            <input type="number" id="editAssessmentFullMarks" placeholder="Enter total marks" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Passing Marks *</label>
                            <input type="number" id="editAssessmentPassingMarks" placeholder="Enter passing marks" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                    </div>
                    <div id="ctevtEditComponentFields" class="space-y-3 pt-4 hidden">
                        <p class="text-[11px] text-gray-500 italic">Component totals feed into the full and passing marks above.</p>
                        <div id="editExamComponentSection" class="space-y-3">
                            @foreach($editExamComponentDefinitions['ctevt'] as $component)
                            @php
                                $fields = $editExamComponentFields['ctevt'][$component['key']] ?? ['max' => '', 'pass' => ''];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white border border-dashed border-gray-200 rounded p-3" data-component="{{ $component['key'] }}">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ $component['label'] }} Full Marks</label>
                                    <input type="number" name="{{ $fields['max'] }}" min="0" step="0.5" data-component="{{ $component['key'] }}" data-component-category="ctevt" data-value-type="max" data-field-name="{{ $fields['max'] }}" class="subject-component-input w-full px-3 py-2 border border-gray-200 rounded-md text-sm" placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ $component['label'] }} Pass Marks</label>
                                    <input type="number" name="{{ $fields['pass'] }}" min="0" step="0.5" data-component="{{ $component['key'] }}" data-component-category="ctevt" data-value-type="pass" data-field-name="{{ $fields['pass'] }}" class="subject-component-input w-full px-3 py-2 border border-gray-200 rounded-md text-sm" placeholder="0">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (AD) *</label>
                            <input type="date" id="editExamDate" name="exam_date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required onchange="convertEditExamDateToBs()">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (BS)</label>
                            <input type="text" name="exam_date_bs" id="editExamDateBs" placeholder="YYYY-MM-DD" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" onchange="convertEditExamDateToAd()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" id="editStatus" required class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="lg:col-span-3">
                    <h4 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Additional Information</h4>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description/Instructions</label>
                        <textarea name="description" id="editDescription" placeholder="Enter assessment description and instructions..." class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs h-16"></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="lg:col-span-3 flex justify-end gap-2 mt-2">
                    <button type="submit" id="editSubmitBtn" class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs rounded">
                        <i class="bi bi-check text-xs mr-1"></i>Update
                    </button>
                    <button type="button" onclick="closeEditExamModal()" class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded">
                        <i class="bi bi-x text-xs mr-1"></i>Cancel
                    </button>
                </div>

<script>
    function closeEditExamModal() {
        document.getElementById('editExamModal').classList.add('hidden');
    }
</script>
            </form>
        </div>
    </div>
</div>

<script>
function el(id) { return document.getElementById(id); }

// Global loading helpers
function showEditLoading(){ const sp = el('editLoadingSpinner'); if(sp) sp.classList.remove('hidden'); }
function hideEditLoading(){ const sp = el('editLoadingSpinner'); if(sp) sp.classList.add('hidden'); }

const EDIT_EXAM_COMPONENT_DEFINITIONS = @json($editExamComponentDefinitions);
const EDIT_EXAM_DEFAULT_CATEGORY = '{{ $editExamDefaultCategory }}';
const EDIT_EXAM_DATA_URL_TEMPLATE = @json(route('admin.exam.edit-data', ['exam' => '__EXAM__']));
const EDIT_EXAM_UPDATE_URL_TEMPLATE = @json(route('admin.exam.update', ['exam' => '__EXAM__']));
const EDIT_EXAM_SUBJECTS_URL = @json(route('admin.exam.subjects'));

function normalizeSemesterValue(raw) {
    const v = String(raw ?? '').trim().toLowerCase();
    const map = {
        '1': 'first',
        '2': 'second',
        '3': 'third',
        '4': 'fourth',
        '5': 'fifth',
        '6': 'sixth',
    };
    return map[v] || v;
}

function ensureEditSemesterOption(select, rawValue) {
    if (!select) return;

    const value = normalizeSemesterValue(rawValue);
    if (!value || value === 'all') return;

    const existing = Array.from(select.options).find(option => option.value === value);
    if (existing) return;

    const labelMap = {
        first: 'First',
        second: 'Second',
        third: 'Third',
        fourth: 'Fourth',
        fifth: 'Fifth',
        sixth: 'Sixth',
        seventh: 'Seventh',
        eighth: 'Eighth',
    };

    const option = document.createElement('option');
    option.value = value;
    option.textContent = labelMap[value] || value;
    select.appendChild(option);
}

    function handleEditExamCategoryChange() {
        const select = el('editExamCategory');
        const category = select?.value || EDIT_EXAM_DEFAULT_CATEGORY;
        const isAssessment = category === 'assessment';

        el('assessmentEditFields').classList.toggle('hidden', !isAssessment);
        el('ctevtEditComponentFields').classList.toggle('hidden', isAssessment);
        el('editAssessmentNumberField')?.classList.toggle('hidden', !isAssessment);

        if (!isAssessment) {
            // Update hidden full/pass marks from component totals
            updateEditExamComponentTotals();
            // Clear the assessment number so it doesn't get saved accidentally
            const numInput = el('editAssessmentNumber');
            if (numInput) numInput.value = '';
        }

        // Adjust practical component visibility based on selected subject lab flag
        updateExamPracticalFieldsVisibility();
    }

    function updateExamPracticalFieldsVisibility() {
        const category = el('editExamCategory')?.value || EDIT_EXAM_DEFAULT_CATEGORY;
        const subjectSelect = el('editSubject');
        const selectedOption = subjectSelect?.selectedOptions?.[0];
        const hasLab = selectedOption ? (selectedOption.dataset.hasLab === '1') : false;
        const showPractical = category === 'ctevt' && hasLab;

        document.querySelectorAll('[data-component="pi"], [data-component="pe"]').forEach((element) => {
            const wrapper = element.closest('[data-component]') || element;
            if (wrapper) {
                wrapper.style.display = showPractical ? '' : 'none';
            }
        });
    }

function updateEditExamComponentTotals(force = false) {
    const category = el('editExamCategory')?.value || EDIT_EXAM_DEFAULT_CATEGORY;
    if (category !== 'ctevt') {
        return;
    }
    const fullMarksInput = el('editFullMarks');
    const passMarksInput = el('editPassingMarks');
    if (!fullMarksInput && !passMarksInput) return; // No targets

    const maxInputs = document.querySelectorAll(`#editExamComponentSection [data-component-category="ctevt"][data-value-type="max"]`);
    const passInputs = document.querySelectorAll(`#editExamComponentSection [data-component-category="ctevt"][data-value-type="pass"]`);

    const totalMax = Array.from(maxInputs).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
    const totalPass = Array.from(passInputs).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);

    const hasMaxValue = Array.from(maxInputs).some(input => input.value.trim() !== '');
    const hasPassValue = Array.from(passInputs).some(input => input.value.trim() !== '');

    if (fullMarksInput && (force || hasMaxValue)) {
        fullMarksInput.value = totalMax || 0;
    }
    if (passMarksInput && (force || hasPassValue)) {
        passMarksInput.value = totalPass || 0;
    }
}

function populateEditComponentInputs(exam) {
    document.querySelectorAll('#editExamComponentSection .subject-component-input').forEach(input => {
        const fieldName = input.dataset.fieldName;
        if (!fieldName) return;
        input.value = exam[fieldName] ?? '';
    });
    updateEditExamComponentTotals(true);
}

function registerEditExamComponentListeners() {
    const select = el('editExamCategory');
    const subjectSelect = el('editSubject');
    const assessmentFull = el('editAssessmentFullMarks');
    const assessmentPass = el('editAssessmentPassingMarks');
    if (select) {
        select.addEventListener('change', function() {
            handleEditExamCategoryChange();
        });
    }
    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            updateExamPracticalFieldsVisibility();
        });
    }
    if (assessmentFull) {
        assessmentFull.addEventListener('input', function() {
            el('editFullMarks').value = this.value;
        });
    }
    if (assessmentPass) {
        assessmentPass.addEventListener('input', function() {
            el('editPassingMarks').value = this.value;
        });
    }
    document.addEventListener('input', function(e) {
        if (e.target.matches('#editExamComponentSection .subject-component-input')) {
            updateEditExamComponentTotals();
        }
    });
    handleEditExamCategoryChange();
}

function openEditExamModal(examId) {
    // Ensure modal listeners are registered (safe to call multiple times)
    registerEditExamComponentListeners();
    showEditLoading();
    
    // Fetch exam data - use the named route
    const url = EDIT_EXAM_DATA_URL_TEMPLATE.replace('__EXAM__', encodeURIComponent(examId));
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        cache: 'no-store',
    })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                throw new Error(`HTTP ${response.status} ${response.statusText}: ${text.slice(0, 300)}`);
            }
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Expected JSON but got "${contentType}": ${text.slice(0, 300)}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.exam) {
                const exam = data.exam;
                
                // Set form action
                el('editExamForm').action = EDIT_EXAM_UPDATE_URL_TEMPLATE.replace('__EXAM__', encodeURIComponent(examId));
                
                try {
                    // Populate form fields with null safety
                    const nameEl = el('editExamName'); if (nameEl) nameEl.value = exam.exam_name || '';
                    const assNumEl = el('editAssessmentNumber'); if (assNumEl) assNumEl.value = exam.assessment_number || '';

                    const categoryInput = el('editExamCategory');
                    if (categoryInput) {
                        categoryInput.value = exam.exam_category || EDIT_EXAM_DEFAULT_CATEGORY;
                    }

                    const fullMarksInput = el('editFullMarks');
                    if (fullMarksInput) {
                        fullMarksInput.value = exam.full_marks ?? '';
                    }
                    const passMarksInput = el('editPassingMarks');
                    if (passMarksInput) {
                        passMarksInput.value = exam.passing_marks ?? '';
                    }
                    const assessmentFull = el('editAssessmentFullMarks');
                    if (assessmentFull) {
                        assessmentFull.value = exam.full_marks ?? '';
                    }
                    const assessmentPass = el('editAssessmentPassingMarks');
                    if (assessmentPass) {
                        assessmentPass.value = exam.passing_marks ?? '';
                    }
                    const dateEl = el('editExamDate'); if (dateEl) dateEl.value = exam.exam_date || '';
                    const dateBsEl = el('editExamDateBs'); if (dateBsEl) dateBsEl.value = exam.exam_date_bs || '';
                    const statusEl = el('editStatus'); if (statusEl) statusEl.value = exam.status || 'draft';
                    const descEl = el('editDescription'); if (descEl) descEl.value = exam.description || '';

                    populateEditComponentInputs(exam);
                    handleEditExamCategoryChange();
                } catch (populationErr) {
                    console.error('Error populating edit modal fields:', populationErr);
                    const errorsDiv = el('editExamErrors');
                    if (errorsDiv) {
                        errorsDiv.textContent = 'Error loading data. Please try again.';
                        errorsDiv.classList.remove('hidden');
                    }
                }
                
                // Set semester
                const semester = normalizeSemesterValue(exam.semester || 'all') || 'all';
                ensureEditSemesterOption(el('editSemester'), semester);
                el('editSemester').value = semester;
                
                // Load subjects and select the exam's subject
                loadSubjectsForEditExam(exam.subject_id || 'all');
                
                // Show modal
                el('editExamModal').classList.remove('hidden');
            } else {
                const msg = data?.message || 'Failed to load exam data';
                console.error('Failed to load exam data:', data);
                if (window.showToast) showToast(msg, 'error');
                const errorsDiv = el('editExamErrors');
                errorsDiv.textContent = msg;
                errorsDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const msg = 'Error loading exam data. ' + (error?.message || '');
            if (window.showToast) showToast(msg, 'error', 6000);
            const errorsDiv = el('editExamErrors');
            errorsDiv.textContent = msg;
            errorsDiv.classList.remove('hidden');
        })
        .finally(() => {
            hideEditLoading();
        });
}

function closeEditExamModal() {
    el('editExamModal').classList.add('hidden');
    hideEditLoading();
}

function loadSubjectsForEditExam(selectedSubjectId) {
    const semester = el('editSemester').value;
    const subjectSelect = el('editSubject');
    
    // If no semester selected show placeholder and disable
    if (!semester || semester === '') {
        subjectSelect.innerHTML = '<option value="">Select semester first</option>';
        subjectSelect.disabled = true;
        return;
    }
    
    subjectSelect.innerHTML = '<option value="">Loading...</option>';
    showEditLoading();
    
    const url = `${EDIT_EXAM_SUBJECTS_URL}?semester=${encodeURIComponent(semester)}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            subjectSelect.innerHTML = '<option value="">Select Subject</option><option value="all">All Subjects</option>';
            let hasSubjects = false;
            
            if (data.grouped) {
                Object.keys(data.grouped).forEach(group => {
                    const sublist = data.grouped[group];
                    if (!sublist || sublist.length === 0) return;
                    hasSubjects = true;
                    const optgrp = document.createElement('optgroup');
                    optgrp.label = group;
                    sublist.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                        option.dataset.hasLab = subject.has_lab ? '1' : '0';
                        optgrp.appendChild(option);
                    });
                    subjectSelect.appendChild(optgrp);
                });
            } else if (data.subjects && data.subjects.length > 0) {
                hasSubjects = true;
                data.subjects.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                    option.dataset.hasLab = subject.has_lab ? '1' : '0';
                    subjectSelect.appendChild(option);
                });
            }
            
            // If no subjects found, show message
            if (!hasSubjects) {
                subjectSelect.innerHTML = '<option value="">No subjects found</option><option value="all">All Subjects</option>';
            }
            
            subjectSelect.disabled = false;
            
            // Select the exam's subject
            if (selectedSubjectId && selectedSubjectId !== 'all') {
                const option = subjectSelect.querySelector(`option[value="${selectedSubjectId}"]`);
                if (option) {
                    option.selected = true;
                }
            } else if (selectedSubjectId === 'all') {
                const option = subjectSelect.querySelector('option[value="all"]');
                if (option) option.selected = true;
            }

            // Update practical component visibility based on selected subject
            updateExamPracticalFieldsVisibility();
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
            subjectSelect.innerHTML = '<option value="">No subjects found</option><option value="all">All Subjects</option>';
        })
        .finally(() => {
            hideEditLoading();
        });
}

// Handle form submission
el('editExamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const url = form.action;
    
    // Collect form data manually - normalize semester
    let semesterRaw = el('editSemester')?.value || '';
    let semester = normalizeSemesterValue(semesterRaw); // Normalize '1' → 'first'
    
    const data = {
        _method: 'PUT',
        exam_name: (el('editExamName')?.value || ''),
        full_marks: (el('editFullMarks')?.value || ''),
        passing_marks: (el('editPassingMarks')?.value || ''),
        exam_date: (el('editExamDate')?.value || ''),
        exam_date_bs: el('editExamDateBs')?.value || '',
        status: el('editStatus')?.value || '',
        semester: semester,
        subject_id: el('editSubject')?.value || '',
        exam_category: el('editExamCategory')?.value || 'assessment',
        assessment_number: el('editAssessmentNumber')?.value || '',
        description: el('editDescription')?.value || '',
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    };
    
    console.log('Normalized data sent:', {semesterRaw, semester, data});
    
    // Debug: Log data being sent
    console.log('Submitting data:', data);
    console.log('Form action URL:', url);
    
    // Show loading
    const submitBtn = el('editSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat text-xs mr-1 animate-spin"></i>Updating...';
    showEditLoading();
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok && response.status === 422) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success toast notification before reloading
            if (window.showToast) {
                showToast(data.message || 'Exam updated successfully!', 'success');
            } else {
                alert(data.message || 'Exam updated successfully!');
            }
            closeEditExamModal();
            // Delay reload slightly to allow toast to show
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            const errorsDiv = el('editExamErrors');
            let errorMsg = data.message || 'Error updating exam';
            if (data.errors && typeof data.errors === 'object') {
                // Show field-specific errors
                errorMsg = Object.entries(data.errors).map(([field, msgs]) => 
                    `${field}: ${Array.isArray(msgs) ? msgs[0] : msgs}`
                ).join('\\n');
            }
            errorsDiv.innerHTML = errorMsg.replace(/\\n/g, '<br>');
            errorsDiv.classList.remove('hidden');
            errorsDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (window.showToast) {
            showToast('An error occurred. Please try again.', 'error');
        } else {
            alert('An error occurred. Please try again.');
        }
        const errorsDiv = el('editExamErrors');
        errorsDiv.textContent = 'An error occurred. Please try again.';
        errorsDiv.classList.remove('hidden');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check text-xs mr-1"></i>Update';
        hideEditLoading();
    });
});

// Close modal on outside click
document.addEventListener('click', function(e) {
    const modal = el('editExamModal');
    if (modal && !modal.classList.contains('hidden') && e.target === modal.querySelector('.fixed.inset-0.bg-black')) {
        closeEditExamModal();
    }
});

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditExamModal();
    }
});

// Ensure listeners are registered immediately (not only on DOMContentLoaded)
registerEditExamComponentListeners();
</script>
