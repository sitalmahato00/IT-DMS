@extends('admin.layouts.app')

@section('title', 'Edit Teacher')

@php
    $teacher = $teacher ?? new \App\Models\User();
    $teacherProfile = $teacherProfile ?? new \App\Models\Teacher();
    $initialSubjectIds = collect(old('subject_ids', $selectedSubjectIds ?? []))->map(fn ($id) => (string) $id)->values();
    $initialSemester = old('assignment_semester', $selectedSemester ?? '');
@endphp

@section('styles')
<style>
    html.teacher-form-page:not(.dark) .teacher-form-shell,
    html.teacher-form-page:not(.dark) .teacher-page-section,
    html.teacher-form-page:not(.dark) .teacher-side-card {
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
    }

    html.teacher-form-page:not(.dark) .teacher-page-grid {
        display: grid;
        grid-template-columns: minmax(0, 24rem) minmax(0, 1fr);
        gap: 1.5rem;
        align-items: stretch;
    }

    html.teacher-form-page:not(.dark) .teacher-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    html.teacher-form-page:not(.dark) .teacher-form-grid > div {
        min-width: 0;
    }

    html.teacher-form-page:not(.dark) .teacher-page-grid > div:first-child {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        align-self: start;
    }

    html.teacher-form-page:not(.dark) .teacher-page-grid > div:first-child > .teacher-side-card:last-child {
        flex: 0;
    }

    html.teacher-form-page:not(.dark) .teacher-side-card {
        min-height: 10.5rem;
    }

    html.teacher-form-page:not(.dark) .teacher-tab-bar {
        overflow-x: auto;
    }

    html.teacher-form-page:not(.dark) .teacher-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem;
        background: #ffffff;
        color: #475569;
        padding: 0.8rem 1rem;
        font-size: 0.9rem;
        font-weight: 700;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    html.teacher-form-page:not(.dark) .teacher-tab-btn:hover {
        border-color: #fb7185;
        color: #be123c;
        transform: translateY(-1px);
    }

    html.teacher-form-page:not(.dark) .teacher-tab-btn.active {
        border-color: #fb7185;
        background: #fff1f2;
        color: #be123c;
        box-shadow: 0 10px 20px -14px rgba(244, 63, 94, 0.35);
    }

    html.teacher-form-page:not(.dark) .teacher-label {
        display: block;
        margin-bottom: 0.45rem;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }

    html.teacher-form-page:not(.dark) .teacher-input,
    html.teacher-form-page:not(.dark) .teacher-select,
    html.teacher-form-page:not(.dark) .teacher-textarea,
    html.teacher-form-page:not(.dark) .teacher-file {
        width: 100%;
        min-height: 2.95rem;
        border: 2px solid #cbd5e1;
        border-radius: 0.9rem;
        background: #ffffff;
        padding: 0.75rem 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    html.teacher-form-page:not(.dark) .teacher-textarea {
        min-height: 5.75rem;
        resize: vertical;
    }

    html.teacher-form-page:not(.dark) .teacher-input:focus,
    html.teacher-form-page:not(.dark) .teacher-select:focus,
    html.teacher-form-page:not(.dark) .teacher-textarea:focus,
    html.teacher-form-page:not(.dark) .teacher-file:focus {
        outline: none;
        border-color: #e11d48;
        box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.12);
    }

    html.teacher-form-page:not(.dark) .teacher-photo-dropzone {
        border: 1.5px dashed #fecdd3;
        border-radius: 1.25rem;
        background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%);
        transition: border-color 0.2s ease, transform 0.2s ease;
    }

    html.teacher-form-page:not(.dark) .teacher-photo-dropzone:hover {
        border-color: #fb7185;
        transform: translateY(-1px);
    }

    html.teacher-form-page:not(.dark) .teacher-photo-frame {
        width: 12.5rem;
        height: 12.5rem;
        overflow: hidden;
        border-radius: 9999px;
        background: #fff;
    }

    html.teacher-form-page:not(.dark) .teacher-btn-soft,
    html.teacher-form-page:not(.dark) .teacher-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-radius: 0.9rem;
        padding: 0.8rem 1rem;
        font-weight: 700;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    html.teacher-form-page:not(.dark) .teacher-btn-soft {
        border: 1px solid #fecdd3;
        background: #fff1f2;
        color: #be123c;
    }

    html.teacher-form-page:not(.dark) .teacher-btn-secondary {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    html.teacher-form-page:not(.dark) .teacher-btn-soft:hover,
    html.teacher-form-page:not(.dark) .teacher-btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.28);
    }

    html.teacher-form-page:not(.dark) .teacher-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.35rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    html.teacher-form-page:not(.dark) .teacher-chip.active {
        background: #dcfce7;
        color: #166534;
    }

    html.teacher-form-page:not(.dark) .teacher-chip.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    html.teacher-form-page:not(.dark) .teacher-error-text {
        margin-top: 0.35rem;
        min-height: 1rem;
        font-size: 0.75rem;
        color: #dc2626;
    }

    html.teacher-form-page:not(.dark) .teacher-form-actions {
        position: sticky;
        bottom: 1rem;
        z-index: 10;
        margin-top: 1.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
    }

    @media (max-width: 1024px) {
        html.teacher-form-page:not(.dark) .teacher-page-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        html.teacher-form-page:not(.dark) .teacher-form-grid {
            grid-template-columns: 1fr;
        }

        html.teacher-form-page:not(.dark) .teacher-tab-btn {
            padding: 0.72rem 0.85rem;
            font-size: 0.84rem;
        }

        html.teacher-form-page:not(.dark) .teacher-photo-frame {
            width: 10.5rem;
            height: 10.5rem;
        }
    }
</style>
<script>document.documentElement.classList.add('teacher-form-page');</script>
@endsection

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Edit Teacher',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Teachers', 'url' => route('admin.teachers')],
        ['label' => $teacher->name ?? 'Teacher', 'url' => route('admin.teachers.show', $teacher->id)],
        ['label' => 'Edit Teacher']
    ]
])

<div class="space-y-6">
    <div class="teacher-form-shell overflow-hidden p-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-500">Teacher Management</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Update teacher profile</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Edit login, subject assignments, payroll, and personal details without leaving the page flow.
                </p>
            </div>
            <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <i class="bi bi-eye"></i>
                View Teacher
            </a>
        </div>
    </div>

    <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.teachers.partials.form', [
            'teacher' => $teacher,
            'teacherProfile' => $teacherProfile,
            'mode' => 'edit',
        ])

        <div class="teacher-form-actions flex flex-col-reverse gap-3 p-4 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('admin.teachers') }}" class="teacher-btn-secondary w-full sm:w-auto">Cancel</a>
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3 font-semibold text-white transition hover:bg-red-700 sm:w-auto">
                <i class="bi bi-check2-circle"></i>
                Update Teacher
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const teacherSubjectOptions = @json($teacherSubjectOptions ?? []);
    const semesterSelect = document.getElementById('assignment_semester');
    const subjectPicker = document.getElementById('teacher_subject_picker');
    const selectedSubjectsContainer = document.getElementById('teacherSelectedSubjects');
    const selectedSubjectInputs = document.getElementById('teacherSelectedSubjectInputs');
    const photoInput = document.getElementById('profile_photo_input');
    const photoPreview = document.getElementById('teacherPhotoPreview');
    const photoPlaceholder = document.getElementById('teacherPhotoPlaceholder');
    const dropzone = document.getElementById('teacherPhotoDropzone');
    const chooseButton = document.getElementById('choosePhotoButton');
    const removeButton = document.getElementById('removePhotoButton');
    const selectedSubjects = new Set((@json($initialSubjectIds) || []).map(String));
    const initialSemester = @json($initialSemester);
    const currentPhoto = photoPreview?.dataset.current || '';
    const tabButtons = Array.from(document.querySelectorAll('.teacher-tab-btn'));
    const tabSections = Array.from(document.querySelectorAll('[id^="teacher-"][id$="-section"]'));

    function showSection(sectionId) {
        tabSections.forEach(section => {
            section.classList.toggle('hidden', section.id !== sectionId);
        });

        tabButtons.forEach(button => {
            button.classList.toggle('active', button.dataset.teacherTab === sectionId);
        });
    }

    function renderSubjects(semesterValue) {
        if (!subjectPicker) return;

        const semester = String(semesterValue || '');
        const selected = new Set([...selectedSubjects].map(String));
        const filtered = teacherSubjectOptions.filter(subject => !semester || String(subject.semester || '') === semester);

        subjectPicker.innerHTML = '<option value="">Select subject</option>';

        if (!filtered.length) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No subjects available for this semester';
            option.disabled = true;
            subjectPicker.appendChild(option);
            return;
        }

        filtered.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = subject.semester ? `${subject.label} (${subject.semester_label})` : subject.label;
            option.disabled = selected.has(String(subject.id));
            if (option.disabled) {
                option.textContent += ' (Selected)';
            }
            subjectPicker.appendChild(option);
        });
    }

    function renderSelectedSubjects() {
        if (!selectedSubjectsContainer || !selectedSubjectInputs) return;

        selectedSubjectsContainer.innerHTML = '';
        selectedSubjectInputs.innerHTML = '';

        if (!selectedSubjects.size) {
            selectedSubjectsContainer.innerHTML = '<span class="text-sm text-slate-500">No subjects selected yet.</span>';
            return;
        }

        Array.from(selectedSubjects).forEach(id => {
            const subject = teacherSubjectOptions.find(item => String(item.id) === String(id));
            if (!subject) return;

            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700';
            chip.innerHTML = `${subject.label}<i class="bi bi-x-lg"></i>`;
            chip.addEventListener('click', () => {
                selectedSubjects.delete(String(id));
                renderSubjects(semesterSelect?.value || initialSemester);
                renderSelectedSubjects();
            });
            selectedSubjectsContainer.appendChild(chip);

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'subject_ids[]';
            hidden.value = String(id);
            selectedSubjectInputs.appendChild(hidden);
        });
    }

    function showSelectedPreview(file) {
        if (!photoPreview || !photoPlaceholder || !file) return;

        const reader = new FileReader();
        reader.onload = event => {
            photoPreview.src = event.target.result;
            photoPreview.classList.remove('hidden');
            photoPlaceholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    function resetPhotoPreview() {
        if (!photoPreview || !photoPlaceholder || !photoInput) return;

        photoInput.value = '';
        if (currentPhoto) {
            photoPreview.src = currentPhoto;
            photoPreview.classList.remove('hidden');
            photoPlaceholder.classList.add('hidden');
            return;
        }

        photoPreview.src = '';
        photoPreview.classList.add('hidden');
        photoPlaceholder.classList.remove('hidden');
    }

    if (semesterSelect && subjectPicker) {
        renderSubjects(semesterSelect.value || initialSemester);
        semesterSelect.addEventListener('change', function () {
            renderSubjects(this.value);
        });

        subjectPicker.addEventListener('change', function () {
            const value = String(this.value || '');
            if (value) {
                selectedSubjects.add(value);
                this.value = '';
                renderSubjects(semesterSelect.value || initialSemester);
                renderSelectedSubjects();
            }
        });
    }

    if (tabButtons.length && tabSections.length) {
        showSection('teacher-basic-section');
        tabButtons.forEach(button => {
            button.addEventListener('click', () => showSection(button.dataset.teacherTab));
        });
    }

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const [file] = this.files || [];
            if (file) {
                showSelectedPreview(file);
            }
        });
    }

    if (chooseButton && photoInput) {
        chooseButton.addEventListener('click', () => photoInput.click());
    }

    if (dropzone && photoInput) {
        dropzone.addEventListener('click', () => photoInput.click());
        dropzone.addEventListener('dragover', event => {
            event.preventDefault();
            dropzone.classList.add('ring-2', 'ring-rose-400');
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('ring-2', 'ring-rose-400');
        });
        dropzone.addEventListener('drop', event => {
            event.preventDefault();
            dropzone.classList.remove('ring-2', 'ring-rose-400');
            const file = event.dataTransfer.files?.[0];
            if (file && photoInput) {
                photoInput.files = event.dataTransfer.files;
                showSelectedPreview(file);
            }
        });
    }

    if (removeButton) {
        removeButton.addEventListener('click', resetPhotoPreview);
    }

    renderSelectedSubjects();
})();
</script>
@endsection
