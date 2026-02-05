@extends('admin.layouts.app')

@section('title', 'Exam')

@section('content')
<div class="space-y-4">
    <!-- Exam Page (Single View) -->
    <div>
        <!-- Stats Cards -->
        <div id="examsStats">
            @include('admin.partials.exams_stats', ['stats' => $stats])
        </div>

        <!-- Filters & Search -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Search & Filter Exams</h3>
            <form id="assessmentFilterForm" method="GET" action="{{ route('admin.assessment') }}">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year</label>
                    <select name="academic_year" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                    <select name="semester" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Semesters</option>
                        @foreach($semesters as $key => $label)
                            <option value="{{ $key }}" {{ request('semester') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Course</label>
                    <select name="course_id" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-2">
                <button type="submit" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Filter</button>
                <a href="{{ route('admin.assessment') }}" class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">Reset</a>
            </div>
            </form>
        </x-card>

        <!-- Exam Type Tabs -->
        <div class="flex gap-2 mb-4">
            <button class="exam-type-tab active px-4 py-2 text-xs font-medium text-gray-900 border-b-2 border-red-600 hover:text-red-600 transition" onclick="filterExamType('all', this)">All</button>
            <button class="exam-type-tab px-4 py-2 text-xs font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 transition" onclick="filterExamType('internal', this)">Internal</button>
            <button class="exam-type-tab px-4 py-2 text-xs font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 transition" onclick="filterExamType('practical', this)">Practical</button>
            <button class="exam-type-tab px-4 py-2 text-xs font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 transition" onclick="filterExamType('assessment', this)">Assessment</button>
            <button class="exam-type-tab px-4 py-2 text-xs font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 transition" onclick="filterExamType('viva', this)">Viva</button>
        </div>

        <!-- Assessment List Table -->
        <script>
        function filterExamType(type, btn) {
            // Remove active class from all tab buttons
            document.querySelectorAll('.exam-type-tab').forEach(function(tab) {
                tab.classList.remove('active', 'text-gray-900', 'border-red-600');
                tab.classList.add('text-gray-600', 'border-transparent');
            });
            // Add active class to clicked button
            btn.classList.add('active', 'text-gray-900', 'border-red-600');
            btn.classList.remove('text-gray-600', 'border-transparent');
            // Filter table rows by exam type
            document.querySelectorAll('table tbody tr').forEach(function(row) {
                if (type === 'all') {
                    row.style.display = '';
                } else {
                    var examType = row.querySelector('td:nth-child(5)');
                    if (examType && examType.textContent.trim().toLowerCase().includes(type)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
        </script>
        <div class="bg-white rounded shadow-sm border border-gray-200">
            <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Exam List</h3>
                <div class="flex gap-2">
                    <button class="flex items-center gap-1 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100 rounded transition">
                        <i class="bi bi-download text-xs"></i>
                        <span>Export</span>
                    </button>
                    <button id="btnAddNewExam" class="flex items-center gap-1 px-2 py-1 text-xs text-white bg-red-600 hover:bg-red-700 rounded transition">
                        <i class="bi bi-plus-circle text-xs"></i>
                        <span>Add New Exam</span>
                    </button>
                </div>
            </div>
            
            <x-table>
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Exam Name</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Year</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Semester</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Course</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Type</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Total Marks</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Status</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="examsTableBody">
                    @include('admin.partials.exams_table_rows', ['exams' => $exams])
                </tbody>
            </x-table>
            
            <div id="examsTableFooter">
                @include('admin.partials.exams_table_footer', ['exams' => $exams])
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('assessmentFilterForm');
    const ajaxBase = "{{ route('admin.assessment.data') }}";

    function serializeForm(f) {
        return new URLSearchParams(new FormData(f)).toString();
    }

    async function loadExams(url, push = true) {
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Network response was not ok');
            const data = await res.json();
            if (data.success) {
                document.getElementById('examsTableBody').innerHTML = data.table_rows;
                document.getElementById('examsTableFooter').innerHTML = data.table_footer;
                document.getElementById('examsStats').innerHTML = data.stats;
                bindPaginationLinks();
                bindPerPage();
                if (push) {
                    // Update visible URL (use main assessment route)
                    const displayUrl = url.replace('{{ route('admin.assessment.data') }}', '{{ route('admin.assessment') }}');
                    history.pushState(null, '', displayUrl);
                }
            }
        } catch (err) {
            console.error('Failed to load exams via AJAX', err);
        }
    }

    function bindPaginationLinks() {
        document.querySelectorAll('#examsTableFooter a').forEach(function(a) {
            if (a.dataset.bound) return;
            a.addEventListener('click', function(e) {
                e.preventDefault();
                loadExams(this.href);
            });
            a.dataset.bound = '1';
        });
    }

    function bindPerPage() {
        const per = document.getElementById('perPageSelect');
        if (per) {
            per.addEventListener('change', function() {
                const params = serializeForm(form);
                const url = ajaxBase + '?' + params;
                loadExams(url);
            });
        }
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const params = serializeForm(form);
            const url = ajaxBase + '?' + params;
            loadExams(url);
        });
    }

    // Expose a helper to refresh exams after create/update/delete
    window.refreshExams = function() {
        const params = serializeForm(document.getElementById('assessmentFilterForm'));
        const url = ajaxBase + '?' + params;
        loadExams(url);
    };

    // Add Exam modal handling
    var addExamBtn = document.getElementById('btnAddNewExam');
    function openAddExamModal() {
        document.getElementById('addExamModal').classList.remove('hidden');
        document.getElementById('addExamErrors').innerHTML = '';
    }
    function closeAddExamModal() {
        document.getElementById('addExamModal').classList.add('hidden');
        document.getElementById('addExamForm').reset();
        document.getElementById('addExamErrors').innerHTML = '';
    }

    if (addExamBtn) {
        addExamBtn.addEventListener('click', function() { openAddExamModal(); });
    }

    // Add Exam Modal - Close on backdrop/ESC
    const addExamModal = document.getElementById('addExamModal');
    if (addExamModal) {
        addExamModal.addEventListener('click', function(e) {
            if (e.target.id === 'addExamModal') closeAddExamModal();
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAddExamModal();
    });

    const addExamForm = document.getElementById('addExamForm');
    if (addExamForm) {
        addExamForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const url = this.action;
            const formData = new FormData(this);
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                if (res.status === 422) {
                    const data = await res.json();
                    let html = '';
                    if (data.errors) {
                        Object.values(data.errors).forEach(function(arr) { html += '<div>' + arr.join('<br>') + '</div>'; });
                    } else if (data.message) {
                        html = '<div>' + data.message + '</div>';
                    }
                    document.getElementById('addExamErrors').innerHTML = html;
                    return;
                }
                const data = await res.json();
                if (data.success) {
                    closeAddExamModal();
                    alert(data.message || 'Exam created');
                    // refresh exams list
                    window.refreshExams();
                } else {
                    document.getElementById('addExamErrors').innerHTML = '<div>' + (data.message || 'Failed') + '</div>';
                }
            } catch (err) {
                console.error(err);
                document.getElementById('addExamErrors').innerHTML = '<div>Failed to create exam. See console.</div>';
            }
        });
    }

    window.addEventListener('popstate', function() {
        const params = new URLSearchParams(window.location.search);
        const url = ajaxBase + '?' + params.toString();
        loadExams(url, false);
    });

    // Initial binding
    bindPaginationLinks();
    bindPerPage();
});
</script>

<!-- Add Exam Modal -->
<div id="addExamModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Add New Exam</h3>
            <button onclick="closeAddExamModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="addExamForm" method="POST" action="{{ route('admin.assessment.store') }}" class="px-5 py-4 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name *</label>
                    <input name="exam_name" type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name (Nepali)</label>
                    <input name="exam_name_ne" type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year *</label>
                    <select name="academic_year" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                    <select name="semester" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        @foreach($semesters as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Type *</label>
                    <select name="exam_type" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        <option value="internal">Internal</option>
                        <option value="final">Final</option>
                        <option value="midterm">Midterm</option>
                        <option value="practical">Practical</option>
                        <option value="viva">Viva</option>
                        <option value="assignment">Assignment</option>
                        <option value="assessment">Assessment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks *</label>
                    <input name="full_marks" type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Passing Marks *</label>
                    <input name="passing_marks" type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (AD) *</label>
                    <input name="exam_date" type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs h-20"></textarea>
            </div>
            <div id="addExamErrors" class="text-sm text-red-600"></div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeAddExamModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Create Exam</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Exam Modal -->
<div id="editExamModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Exam</h3>
            <button onclick="closeEditExamModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="editExamForm" method="POST" class="px-5 py-4 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="exam_id" id="editExamId">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name *</label>
                    <input name="exam_name" id="editExamName" type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name (Nepali)</label>
                    <input name="exam_name_ne" id="editExamNameNe" type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year *</label>
                    <select name="academic_year" id="editAcademicYear" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                    <select name="semester" id="editSemester" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        @foreach($semesters as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" id="editSubjectId" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Type *</label>
                    <select name="exam_type" id="editExamType" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        <option value="internal">Internal</option>
                        <option value="final">Final</option>
                        <option value="midterm">Midterm</option>
                        <option value="practical">Practical</option>
                        <option value="viva">Viva</option>
                        <option value="assignment">Assignment</option>
                        <option value="assessment">Assessment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks *</label>
                    <input name="full_marks" id="editFullMarks" type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Passing Marks *</label>
                    <input name="passing_marks" id="editPassingMarks" type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (AD) *</label>
                    <input name="exam_date" id="editExamDate" type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" id="editStatus" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="editDescription" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs h-20"></textarea>
            </div>
            <div id="editExamErrors" class="text-sm text-red-600"></div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeEditExamModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Update Exam</button>
            </div>
        </form>
    </div>
</div>

<script>
// Edit Exam Modal Functions
async function openEditExamModal(examId) {
    try {
        const response = await fetch(`/admin/assessment/${examId}/edit-data`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        
        if (data.success) {
            const exam = data.exam;
            document.getElementById('editExamId').value = exam.id;
            document.getElementById('editExamName').value = exam.exam_name;
            document.getElementById('editExamNameNe').value = exam.exam_name_ne || '';
            document.getElementById('editAcademicYear').value = exam.academic_year;
            document.getElementById('editSemester').value = exam.semester;
            document.getElementById('editSubjectId').value = exam.subject_id || '';
            document.getElementById('editExamType').value = exam.exam_type;
            document.getElementById('editFullMarks').value = exam.full_marks;
            document.getElementById('editPassingMarks').value = exam.passing_marks;
            document.getElementById('editExamDate').value = exam.exam_date;
            document.getElementById('editStatus').value = exam.status;
            document.getElementById('editDescription').value = exam.description || '';
            
            document.getElementById('editExamForm').action = `/admin/assessment/${examId}`;
            document.getElementById('editExamModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error fetching exam data:', error);
    }
}

function closeEditExamModal() {
    document.getElementById('editExamModal').classList.add('hidden');
    document.getElementById('editExamForm').reset();
}

// Close modal on outside click
const editExamModal = document.getElementById('editExamModal');
if (editExamModal) {
    editExamModal.addEventListener('click', function(e) {
        if (e.target.id === 'editExamModal') closeEditExamModal();
    });
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEditExamModal();
});

// Handle Edit Exam Form submission
document.getElementById('editExamForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const url = this.action;
    const formData = new FormData(this);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        if (res.ok || res.status === 302) {
            closeEditExamModal();
            window.refreshExams();
        } else {
            const data = await res.json();
            document.getElementById('editExamErrors').innerHTML = data.message || 'Error updating exam';
        }
    } catch (error) {
        console.error(error);
    }
});
</script>
</div>

<div id="sectionMarks" class="hidden">
    <!-- Mark Management content here -->
</div>
</div>
@endsection

@section('scripts')
<script>
    // Add event listener for Add New Exam button when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        var addExamBtn = document.getElementById('btnAddNewExam');
        if (addExamBtn) {
            addExamBtn.addEventListener('click', function() {
                document.getElementById('addExamModal').classList.remove('hidden');
            });
        }
        
        var addExamModal = document.getElementById('addExamModal');
        if (addExamModal) {
            addExamModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAddExamModal();
                }
            });
        }
    });
    
    function openAddAssessmentModal() {
        document.getElementById('addAssessmentModal').classList.remove('hidden');
    }
    
    function closeAddAssessmentModal() {
        document.getElementById('addAssessmentModal').classList.add('hidden');
    }

    function openViewAssessmentModal(name, year, semester, course, type, marks, passing, status) {
        document.getElementById('viewAssessmentName').textContent = name;
        document.getElementById('viewAssessmentType').textContent = type;
        document.getElementById('viewAssessmentYear').textContent = year;
        document.getElementById('viewAssessmentSemester').textContent = semester;
        document.getElementById('viewAssessmentCourse').textContent = course;
        document.getElementById('viewAssessmentMarks').textContent = marks;
        document.getElementById('viewAssessmentPassing').textContent = passing;
        document.getElementById('viewAssessmentStatus').textContent = status;
        
        const badge = document.getElementById('viewAssessmentStatus');
        if (status === 'Published') {
            badge.className = 'inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium';
        } else if (status === 'Draft') {
            badge.className = 'inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium';
        } else {
            badge.className = 'inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-medium';
        }
        
        document.getElementById('viewAssessmentModal').classList.remove('hidden');
    }

    function closeViewAssessmentModal() {
        document.getElementById('viewAssessmentModal').classList.add('hidden');
    }

    function resetAssessmentFilters() {
        // TODO: Implement filter reset
    }
</script>
@endsection

