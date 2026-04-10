@extends('admin.layouts.app')

@section('title', 'Add Student')

@section('styles')
<script>document.documentElement.classList.add('students-ui-enhanced');</script>
<style>
    .student-page-shell{max-width:96rem;margin:0 auto;padding-inline:clamp(.5rem,1vw,1rem);width:100%;max-width:100%;overflow-x:hidden}
    .student-page-card,.student-page-section,.student-side-card,.student-sticky-bar{border:1px solid #e2e8f0;border-radius:1.5rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:0 24px 45px -34px rgba(15,23,42,.24)}
    .student-page-hero{position:relative;overflow:hidden;border:1px solid #fecdd3;border-radius:1.5rem;background:linear-gradient(135deg,#fff1f2 0%,#fff 50%,#eff6ff 100%);box-shadow:0 24px 45px -34px rgba(15,23,42,.24)}
    .student-page-hero:after{content:'';position:absolute;right:-3rem;bottom:-4rem;width:13rem;height:13rem;border-radius:999px;background:radial-gradient(circle,rgba(244,63,94,.16),rgba(244,63,94,0) 72%)}
    .student-page-grid{display:grid;grid-template-columns:minmax(0,24rem) minmax(0,1fr);gap:1.5rem;align-items:stretch}
    .student-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
    .student-page-grid > div,
    .student-form-grid > div,
    .student-tab-panel,
    .student-page-card,
    .student-page-section,
    .student-side-card,
    .student-sticky-bar{min-width:0}
    .student-page-grid > div:first-child{display:flex;flex-direction:column;gap:1.25rem;align-self:start}
    .student-page-grid > div:first-child > .student-side-card:last-child{flex:0}
    .student-side-card{min-height:10.5rem}
    .student-label{display:block;margin-bottom:.45rem;font-size:.76rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
    .student-input,.student-select,.student-textarea,.student-file{width:100%;border:1px solid #cbd5e1;border-radius:1rem;background:#fff;color:#0f172a;transition:border-color .2s ease,box-shadow .2s ease}
    .student-input,.student-select,.student-file{min-height:3rem;padding:.82rem 1rem}
    .student-textarea{padding:.9rem 1rem;min-height:7rem}
    .student-input:focus,.student-select:focus,.student-textarea:focus,.student-file:focus{outline:none;border-color:#fb7185;box-shadow:0 0 0 4px rgba(251,113,133,.12)}
    .student-error-text{min-height:1rem;font-size:.78rem;font-weight:600;color:#e11d48}
    .student-input.error,.student-select.error,.student-textarea.error,.student-file.error{border-color:#fb7185}
    .student-photo-dropzone{border:1px dashed #fda4af;border-radius:1.25rem;background:linear-gradient(180deg,#fff7f8 0%,#fff 100%);transition:border-color .2s ease,transform .2s ease,box-shadow .2s ease}
    .student-photo-dropzone:hover,.student-photo-dropzone.is-dragover{border-color:#f43f5e;transform:translateY(-1px);box-shadow:0 18px 32px -26px rgba(244,63,94,.35)}
    .student-photo-frame{width:10rem;height:10rem;border-radius:999px;border:4px solid #fff;background:linear-gradient(135deg,#ffe4e6 0%,#fff1f2 100%);overflow:hidden;box-shadow:0 18px 34px -24px rgba(244,63,94,.45)}
    .student-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem .8rem;border-radius:999px;font-size:.78rem;font-weight:700}
    .student-chip.active{background:#dcfce7;color:#166534}
    .student-chip.pending{background:#fee2e2;color:#be123c}
    .student-btn-primary,.student-btn-secondary,.student-btn-soft{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;border-radius:999px;font-weight:700;transition:transform .2s ease,box-shadow .2s ease}
    .student-btn-primary:hover,.student-btn-secondary:hover,.student-btn-soft:hover{transform:translateY(-1px)}
    .student-btn-primary{padding:.9rem 1.4rem;background:linear-gradient(135deg,#e11d48 0%,#fb7185 100%);color:#fff;box-shadow:0 18px 34px -24px rgba(225,29,72,.7)}
    .student-btn-secondary{padding:.9rem 1.35rem;border:1px solid #cbd5e1;background:#fff;color:#334155}
    .student-btn-soft{padding:.78rem 1rem;border:1px solid #fecdd3;background:#fff1f2;color:#be123c}
    .student-file-pill{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem .78rem;border:1px solid #e2e8f0;border-radius:999px;background:#fff;font-size:.82rem;font-weight:600;color:#334155}
    .student-toggle{position:relative;display:inline-flex;width:3.4rem;height:1.9rem;padding:.2rem;border-radius:999px;background:#cbd5e1;transition:background-color .2s ease}
    .student-toggle:after{content:'';width:1.45rem;height:1.45rem;border-radius:999px;background:#fff;box-shadow:0 8px 14px -10px rgba(15,23,42,.55);transition:transform .2s ease}
    #isActiveToggle:checked + .student-toggle{background:#22c55e}
    #isActiveToggle:checked + .student-toggle:after{transform:translateX(1.48rem)}
    .student-tab-bar{display:flex;gap:.6rem;overflow-x:auto;padding:.35rem;border:1px solid #e2e8f0;border-radius:1.2rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%)}
    .student-tab-button{display:inline-flex;align-items:center;gap:.55rem;border:0;border-radius:1rem;background:transparent;color:#475569;padding:.85rem 1rem;font-size:.92rem;font-weight:700;white-space:nowrap;transition:background-color .2s ease,color .2s ease,box-shadow .2s ease}
    .student-tab-button:hover{background:#fff1f2;color:#be123c}
    .student-tab-button.is-active{background:#fff;border:1px solid #fecdd3;color:#be123c;box-shadow:0 14px 28px -24px rgba(225,29,72,.45)}
    .student-tab-panel{display:none}
    .student-tab-panel.is-active{display:block}
    .student-parent-status{border:1px dashed #f59e0b;border-radius:1rem;background:#fffbeb;padding:1rem 1.1rem;font-size:.9rem;font-weight:600;color:#92400e}
    .student-parent-status.is-success{border-color:#86efac;background:#f0fdf4;color:#166534}
    .student-parent-status.is-error{border-color:#fda4af;background:#fff1f2;color:#be123c}
    .student-parent-status.is-muted{border-color:#cbd5e1;background:#f8fafc;color:#475569}
    .student-sticky-bar{position:sticky;bottom:0;z-index:5;padding:1rem 1.2rem;background:rgba(255,255,255,.94);backdrop-filter:blur(10px)}
    .student-footer-actions{display:flex;flex-direction:column;gap:.75rem;width:100%}
    .student-footer-actions > *{width:100%}
    @media (min-width:640px){.student-footer-actions{width:auto;flex-direction:row}.student-footer-actions > *{width:auto;min-width:9rem}}
    @media (max-width:1024px){.student-page-grid{grid-template-columns:1fr}}
    @media (max-width:640px){
        .student-form-grid{grid-template-columns:1fr}
        .student-page-hero{padding:1rem !important}
        .student-side-card,.student-page-section{padding:.9rem !important}
        .student-photo-dropzone{padding:1rem !important}
        .student-photo-frame{width:6.75rem;height:6.75rem}
        .student-photo-frame i{font-size:3.5rem}
        .student-sticky-bar{position:static;bottom:auto;padding:1rem}
        .student-tab-bar{padding:.3rem}
        .student-tab-button{padding:.68rem .8rem;font-size:.85rem}
        .student-btn-primary,.student-btn-secondary,.student-btn-soft{width:100%}
    }
</style>
@endsection

@section('content')
@php
    $departmentOptions = $departmentOptions ?? [];
    $programOptions = $programOptions ?? [];
    $sectionOptions = $sectionOptions ?? [];
    $academicYears = $academicYears ?? [];
    $semesterOptions = $semesterOptions ?? [];
    $statusValue = old('status', 'active');
    $isActive = old('is_active', '1') === '1';
@endphp

@include('admin.components.admin-page-header', [
    'title' => 'Add Student',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Students', 'url' => route('admin.students')],
        ['label' => 'Add Student']
    ]
])

<div class="student-page-shell space-y-6">
    <div class="student-page-hero p-6 sm:p-7">
        <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="student-label !mb-2">Student Management</p>
                <h2 class="text-2xl font-bold text-slate-900">Create a new student profile</h2>
                <p class="mt-2 max-w-3xl text-sm text-slate-600">Fill in the core profile, academic record, emergency details, and uploads. The page saves as a normal record screen instead of opening over the list.</p>
            </div>
            <span class="student-chip pending"><i class="bi bi-plus-circle"></i> New record</span>
        </div>
    </div>

    @if($errors->any())
        <div class="student-page-card p-4 text-sm text-rose-700">
            <div class="mb-2 font-semibold">Please fix the highlighted fields before saving.</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="studentForm" action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="is_active" id="is_active" value="{{ $isActive ? '1' : '0' }}">
        <input type="hidden" name="remove_profile_photo" id="remove_profile_photo" value="0">
        <input type="hidden" name="remove_id_document" id="remove_id_document" value="0">
        <input type="hidden" name="remove_certificates" id="remove_certificates" value="0">

        <datalist id="department-list">@foreach($departmentOptions as $option)<option value="{{ $option }}"></option>@endforeach</datalist>
        <datalist id="program-list">@foreach($programOptions as $option)<option value="{{ $option }}"></option>@endforeach</datalist>
        <datalist id="section-list">@foreach($sectionOptions as $option)<option value="{{ $option }}"></option>@endforeach</datalist>
        <datalist id="academic-year-list">@foreach($academicYears as $option)<option value="{{ $option }}"></option>@endforeach</datalist>

        <div class="student-page-grid">
            <div class="space-y-6 min-w-0">
                <div class="student-side-card p-5">
                    <p class="student-label">Profile Photo</p>
                    <div id="studentPhotoDropzone" class="student-photo-dropzone mt-4 cursor-pointer p-5 text-center">
                        <div class="mx-auto flex w-full flex-col items-center gap-4">
                            <div class="student-photo-frame flex items-center justify-center">
                                <img id="studentPhotoPreview" class="hidden h-full w-full object-cover" alt="Student photo preview">
                                <div id="studentPhotoPlaceholder" class="text-rose-300"><i class="bi bi-person-circle text-7xl"></i></div>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-base font-semibold text-slate-900">Choose a profile photo</h3>
                                <p class="text-sm text-slate-500">Click or drag an image here. JPG, PNG, or WEBP up to 4MB.</p>
                            </div>
                        </div>
                    </div>
                    <input id="profile_photo_input" name="profile_photo" type="file" accept="image/*" class="hidden">
                    <div class="mt-4 flex gap-3">
                        <button type="button" id="choosePhotoButton" class="student-btn-soft flex-1"><i class="bi bi-upload"></i>Choose Photo</button>
                        <button type="button" id="removePhotoButton" class="student-btn-secondary flex-1"><i class="bi bi-trash3"></i>Remove</button>
                    </div>
                    @error('profile_photo')<p class="student-error-text mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="student-side-card p-5">
                    <p class="student-label">Account Snapshot</p>
                    <div class="mt-4 space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Account Activation</p>
                                    <p id="activationLabel" class="mt-1 text-sm text-slate-500">{{ $isActive ? 'Account enabled' : 'Account disabled' }}</p>
                                </div>
                                <label class="cursor-pointer">
                                    <input id="isActiveToggle" type="checkbox" class="hidden" {{ $isActive ? 'checked' : '' }}>
                                    <span class="student-toggle"></span>
                                </label>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm font-semibold text-slate-900">Save Flow</p>
                            <p class="mt-1 text-sm text-slate-500">The record opens as a full page and returns to the student list after saving, so admins can review details without losing context.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6 min-w-0">
                <div class="student-tab-bar" role="tablist" aria-label="Student form sections">
                    <button type="button" class="student-tab-button is-active" data-student-tab="basic" role="tab" aria-selected="true"><i class="bi bi-file-earmark-text"></i>Basic Info</button>
                    <button type="button" class="student-tab-button" data-student-tab="contact" role="tab" aria-selected="false"><i class="bi bi-telephone"></i>Contact</button>
                    <button type="button" class="student-tab-button" data-student-tab="guardian" role="tab" aria-selected="false"><i class="bi bi-people"></i>Parent</button>
                    <button type="button" class="student-tab-button" data-student-tab="location" role="tab" aria-selected="false"><i class="bi bi-geo-alt"></i>Location</button>
                    <button type="button" class="student-tab-button" data-student-tab="health" role="tab" aria-selected="false"><i class="bi bi-heart-pulse"></i>Health</button>
                    <button type="button" class="student-tab-button" data-student-tab="documents" role="tab" aria-selected="false"><i class="bi bi-folder2-open"></i>Documents</button>
                    <button type="button" class="student-tab-button" data-student-tab="details" role="tab" aria-selected="false"><i class="bi bi-info-circle"></i>Details</button>
                </div>

                <div class="student-tab-panel is-active space-y-6" data-student-tab-panel="basic">
                <div class="student-page-section p-5">
                    <p class="student-label">Basic Information</p>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="name">Full Name *</label><input class="student-input @error('name') error @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required><p class="student-error-text">@error('name'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="email">Email *</label><input class="student-input @error('email') error @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="student@example.com" data-validate="email" required><p class="student-error-text">@error('email'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="phone">Phone (Primary) *</label><input class="student-input @error('phone') error @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="98XXXXXXXX" inputmode="numeric" maxlength="10" data-validate="phone" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" required><p class="student-error-text">@error('phone'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="student_id">Student ID *</label><input class="student-input @error('student_id') error @enderror" id="student_id" name="student_id" value="{{ old('student_id') }}" placeholder="Enter student ID" required><p class="student-error-text">@error('student_id'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="username">Username</label><input class="student-input @error('username') error @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Optional username"><p class="student-error-text">@error('username'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                <div class="student-page-section p-5">
                    <p class="student-label">Academic Information</p>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="department">Department *</label><input class="student-input @error('department') error @enderror" id="department" name="department" list="department-list" value="{{ old('department') }}" placeholder="Information Technology" required><p class="student-error-text">@error('department'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="program">Program / Course</label><input class="student-input @error('program') error @enderror" id="program" name="program" list="program-list" value="{{ old('program') }}" placeholder="Diploma in IT"><p class="student-error-text">@error('program'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="semester">Semester *</label><select class="student-select @error('semester') error @enderror" id="semester" name="semester" required><option value="">Select semester</option>@foreach($semesterOptions as $semesterOption)<option value="{{ $semesterOption['value'] }}" @selected(old('semester') == (string) $semesterOption['value'])>{{ $semesterOption['label'] }}</option>@endforeach</select><p class="student-error-text">@error('semester'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="section">Section / Group</label><input class="student-input @error('section') error @enderror" id="section" name="section" list="section-list" value="{{ old('section') }}" placeholder="A / Morning"><p class="student-error-text">@error('section'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="academic_year">Academic Year *</label><input class="student-input @error('academic_year') error @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year') }}" placeholder="2026" required><p class="student-error-text">@error('academic_year'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="academic_year_bs">Academic Year (BS)</label><input class="student-input @error('academic_year_bs') error @enderror" id="academic_year_bs" name="academic_year_bs" list="academic-year-list" value="{{ old('academic_year_bs') }}" placeholder="2083"><p class="student-error-text">@error('academic_year_bs'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="enrollment_date">Enrollment Date</label><input class="student-input @error('enrollment_date') error @enderror" id="enrollment_date" name="enrollment_date" type="date" value="{{ old('enrollment_date') }}"><p class="student-error-text">@error('enrollment_date'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="expected_graduation_year">Expected Graduation Year</label><input class="student-input @error('expected_graduation_year') error @enderror" id="expected_graduation_year" name="expected_graduation_year" value="{{ old('expected_graduation_year') }}" placeholder="2030" data-validate="year"><p class="student-error-text">@error('expected_graduation_year'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                <div class="student-page-section p-5">
                    <p class="student-label">Personal Information</p>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="date_of_birth">Date of Birth *</label><input class="student-input @error('date_of_birth') error @enderror" id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required><p class="student-error-text">@error('date_of_birth'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="date_of_birth_bs">Date of Birth (BS)</label><input class="student-input @error('date_of_birth_bs') error @enderror" id="date_of_birth_bs" name="date_of_birth_bs" value="{{ old('date_of_birth_bs') }}" placeholder="YYYY-MM-DD"><p class="student-error-text">@error('date_of_birth_bs'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="gender">Gender</label><select class="student-select @error('gender') error @enderror" id="gender" name="gender"><option value="">Select gender</option><option value="male" @selected(old('gender') === 'male')>Male</option><option value="female" @selected(old('gender') === 'female')>Female</option><option value="other" @selected(old('gender') === 'other')>Other</option></select><p class="student-error-text">@error('gender'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="national_id_number">National ID / Citizenship Number</label><input class="student-input @error('national_id_number') error @enderror" id="national_id_number" name="national_id_number" value="{{ old('national_id_number') }}" placeholder="Enter ID number"><p class="student-error-text">@error('national_id_number'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                </div>

                <div class="student-tab-panel space-y-6" data-student-tab-panel="contact">

                <div class="student-page-section p-5">
                    <p class="student-label">Contact & Emergency</p>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="secondary_phone">Secondary Phone</label><input class="student-input @error('secondary_phone') error @enderror" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone') }}" placeholder="98XXXXXXXX" inputmode="numeric" maxlength="10" data-validate="phone" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"><p class="student-error-text">@error('secondary_phone'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="emergency_contact">Emergency Contact</label><input class="student-input @error('emergency_contact') error @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" placeholder="98XXXXXXXX" inputmode="numeric" maxlength="10" data-validate="phone" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"><p class="student-error-text">@error('emergency_contact'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="emergency_contact_name">Emergency Contact Name</label><input class="student-input @error('emergency_contact_name') error @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Guardian name"><p class="student-error-text">@error('emergency_contact_name'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="emergency_relationship">Emergency Relationship</label><input class="student-input @error('emergency_relationship') error @enderror" id="emergency_relationship" name="emergency_relationship" value="{{ old('emergency_relationship') }}" placeholder="Mother / Father / Guardian"><p class="student-error-text">@error('emergency_relationship'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                </div>

                <div class="student-tab-panel space-y-6" data-student-tab-panel="guardian">
                <div class="student-page-section p-5">
                    <p class="student-label">Parent / Guardian</p>
                    <div id="parentLookupStatus" class="student-parent-status is-muted mt-4" aria-live="polite">
                        Enter the parent email to load an existing parent profile. If no match is found, a new parent account will be created and login details will be emailed automatically.
                    </div>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="parent_name">Parent Name *</label><input class="student-input @error('parent_name') error @enderror" id="parent_name" name="parent_name" value="{{ old('parent_name') }}" placeholder="Parent or guardian name" required><p class="student-error-text">@error('parent_name'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="parent_email">Parent Email *</label><input class="student-input @error('parent_email') error @enderror" id="parent_email" name="parent_email" type="email" value="{{ old('parent_email') }}" placeholder="parent@example.com" data-validate="email" required><p class="student-error-text">@error('parent_email'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="parent_phone">Parent Phone *</label><input class="student-input @error('parent_phone') error @enderror" id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}" placeholder="98XXXXXXXX" inputmode="numeric" maxlength="10" data-validate="phone" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" required><p class="student-error-text">@error('parent_phone'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="parent_relationship">Relationship</label><input class="student-input @error('parent_relationship') error @enderror" id="parent_relationship" name="parent_relationship" value="{{ old('parent_relationship') }}" placeholder="Father / Mother / Guardian"><p class="student-error-text">@error('parent_relationship'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="parent_gender">Gender</label><select class="student-select @error('parent_gender') error @enderror" id="parent_gender" name="parent_gender"><option value="">Select gender</option><option value="male" @selected(old('parent_gender') === 'male')>Male</option><option value="female" @selected(old('parent_gender') === 'female')>Female</option><option value="other" @selected(old('parent_gender') === 'other')>Other</option></select><p class="student-error-text">@error('parent_gender'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="parent_status">Parent Status</label><select class="student-select @error('parent_status') error @enderror" id="parent_status" name="parent_status"><option value="active" @selected(old('parent_status', 'active') === 'active')>Active</option><option value="pending" @selected(old('parent_status') === 'pending')>Pending</option><option value="inactive" @selected(old('parent_status') === 'inactive')>Inactive</option></select><p class="student-error-text">@error('parent_status'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="parent_occupation">Occupation</label><input class="student-input @error('parent_occupation') error @enderror" id="parent_occupation" name="parent_occupation" value="{{ old('parent_occupation') }}" placeholder="Engineer, Teacher, etc."><p class="student-error-text">@error('parent_occupation'){{ $message }}@enderror</p></div>
                        <div class="sm:col-span-2"><label class="student-label" for="parent_address">Parent Address</label><textarea class="student-textarea @error('parent_address') error @enderror" id="parent_address" name="parent_address" placeholder="Parent or guardian address">{{ old('parent_address') }}</textarea><p class="student-error-text">@error('parent_address'){{ $message }}@enderror</p></div>
                        <div class="sm:col-span-2"><label class="student-label" for="parent_bio">Parent Notes / Bio</label><textarea class="student-textarea @error('parent_bio') error @enderror" id="parent_bio" name="parent_bio" placeholder="Optional parent notes or important contact context">{{ old('parent_bio') }}</textarea><p class="student-error-text">@error('parent_bio'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                </div>

                <div class="student-tab-panel space-y-6" data-student-tab-panel="location">

                <div class="student-page-section p-5">
                    <p class="student-label">Address</p>
                    <div class="student-form-grid mt-4">
                        <div class="sm:col-span-2"><label class="student-label" for="address">Address *</label><textarea class="student-textarea @error('address') error @enderror" id="address" name="address" placeholder="Enter complete address" required>{{ old('address') }}</textarea><p class="student-error-text">@error('address'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="city">City</label><input class="student-input @error('city') error @enderror" id="city" name="city" value="{{ old('city') }}" placeholder="City"><p class="student-error-text">@error('city'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="state_province">State / Province</label><input class="student-input @error('state_province') error @enderror" id="state_province" name="state_province" value="{{ old('state_province') }}" placeholder="Province"><p class="student-error-text">@error('state_province'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="postal_code">Postal Code</label><input class="student-input @error('postal_code') error @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="Postal code"><p class="student-error-text">@error('postal_code'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="country">Country</label><input class="student-input @error('country') error @enderror" id="country" name="country" value="{{ old('country') }}" placeholder="Country"><p class="student-error-text">@error('country'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                </div>

                <div class="student-tab-panel space-y-6" data-student-tab-panel="health">

                <div class="student-page-section p-5">
                    <p class="student-label">Health Information</p>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="blood_group">Blood Group</label><select class="student-select @error('blood_group') error @enderror" id="blood_group" name="blood_group"><option value="">Select blood group</option>@foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)<option value="{{ $group }}" @selected(old('blood_group') === $group)>{{ $group }}</option>@endforeach</select><p class="student-error-text">@error('blood_group'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="disability_status">Disability Status</label><input class="student-input @error('disability_status') error @enderror" id="disability_status" name="disability_status" value="{{ old('disability_status') }}" placeholder="Optional"><p class="student-error-text">@error('disability_status'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="medical_conditions">Medical Conditions</label><textarea class="student-textarea @error('medical_conditions') error @enderror" id="medical_conditions" name="medical_conditions" placeholder="Medical conditions">{{ old('medical_conditions') }}</textarea><p class="student-error-text">@error('medical_conditions'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="allergies">Allergies</label><textarea class="student-textarea @error('allergies') error @enderror" id="allergies" name="allergies" placeholder="Allergies">{{ old('allergies') }}</textarea><p class="student-error-text">@error('allergies'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                </div>

                <div class="student-tab-panel space-y-6" data-student-tab-panel="documents">

                <div class="student-page-section p-5">
                    <p class="student-label">Account & Status</p>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="status">Status</label><select class="student-select @error('status') error @enderror" id="status" name="status"><option value="active" @selected($statusValue === 'active')>Active</option><option value="inactive" @selected($statusValue === 'inactive')>Inactive</option><option value="suspended" @selected($statusValue === 'suspended')>Suspended</option></select><p class="student-error-text">@error('status'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="role">Role</label><select class="student-select @error('role') error @enderror" id="role" name="role"><option value="student" selected>Student</option></select><p class="student-error-text">@error('role'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                <div class="student-page-section p-5">
                    <p class="student-label">Document Uploads</p>
                    <div class="student-form-grid mt-4">
                        <div><label class="student-label" for="id_document">ID Document Upload</label><input class="student-file @error('id_document') error @enderror" id="id_document" name="id_document" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"><div id="idDocumentFiles" class="mt-3 flex flex-wrap gap-2 text-sm text-slate-500">No file selected.</div><p class="student-error-text">@error('id_document'){{ $message }}@enderror</p></div>
                        <div><label class="student-label" for="certificates">Certificates Upload</label><input class="student-file @error('certificates') error @enderror @error('certificates.*') error @enderror" id="certificates" name="certificates[]" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple><div id="certificateFiles" class="mt-3 flex flex-wrap gap-2 text-sm text-slate-500">No files selected.</div><p class="student-error-text">@error('certificates'){{ $message }}@enderror @error('certificates.*'){{ $message }}@enderror</p></div>
                    </div>
                </div>

                </div>

                <div class="student-tab-panel space-y-6" data-student-tab-panel="details">

                <div class="student-page-section p-5">
                    <p class="student-label">Additional Notes</p>
                    <div class="mt-4">
                        <label class="student-label" for="notes">Notes / Details</label>
                        <textarea class="student-textarea @error('notes') error @enderror" id="notes" name="notes" placeholder="Add any additional details about the student">{{ old('notes') }}</textarea>
                        <p class="student-error-text">@error('notes'){{ $message }}@enderror</p>
                    </div>
                </div>

                </div>

                <div class="student-sticky-bar flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Move through the student profile one step at a time. Save is available on the final section.</p>
                    <div class="student-footer-actions">
                        <a href="{{ route('admin.students') }}" class="student-btn-secondary">Cancel</a>
                        <button type="button" id="previousStepButton" class="student-btn-secondary"><i class="bi bi-arrow-left"></i>Previous</button>
                        <button type="button" id="nextStepButton" class="student-btn-primary"><i class="bi bi-arrow-right"></i>Next</button>
                        <button type="submit" id="saveStudentButton" class="student-btn-primary hidden"><i class="bi bi-check2-circle"></i>Save Student</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const form = document.getElementById('studentForm');
        if (!form) return;

        const photoInput = document.getElementById('profile_photo_input');
        const choosePhotoButton = document.getElementById('choosePhotoButton');
        const removePhotoButton = document.getElementById('removePhotoButton');
        const photoPreview = document.getElementById('studentPhotoPreview');
        const photoPlaceholder = document.getElementById('studentPhotoPlaceholder');
        const dropzone = document.getElementById('studentPhotoDropzone');
        const removePhotoInput = document.getElementById('remove_profile_photo');
        const isActiveToggle = document.getElementById('isActiveToggle');
        const isActiveInput = document.getElementById('is_active');
        const activationLabel = document.getElementById('activationLabel');
        const idDocumentInput = document.getElementById('id_document');
        const certificatesInput = document.getElementById('certificates');
        const idDocumentFiles = document.getElementById('idDocumentFiles');
        const certificateFiles = document.getElementById('certificateFiles');
        const dobInput = document.getElementById('date_of_birth');
        const dobBsInput = document.getElementById('date_of_birth_bs');
        const parentEmailInput = document.getElementById('parent_email');
        const parentNameInput = document.getElementById('parent_name');
        const parentPhoneInput = document.getElementById('parent_phone');
        const parentGenderInput = document.getElementById('parent_gender');
        const parentStatusInput = document.getElementById('parent_status');
        const parentOccupationInput = document.getElementById('parent_occupation');
        const parentAddressInput = document.getElementById('parent_address');
        const parentBioInput = document.getElementById('parent_bio');
        const parentRelationshipInput = document.getElementById('parent_relationship');
        const parentLookupStatus = document.getElementById('parentLookupStatus');
        const previousStepButton = document.getElementById('previousStepButton');
        const nextStepButton = document.getElementById('nextStepButton');
        const saveStudentButton = document.getElementById('saveStudentButton');
        const tabButtons = Array.from(document.querySelectorAll('[data-student-tab]'));
        const tabPanels = Array.from(document.querySelectorAll('[data-student-tab-panel]'));
        const tabOrder = tabButtons.map(button => button.dataset.studentTab);
        let isDirty = false;
        let parentLookupTimer = null;
        let parentLookupToken = 0;

        function markDirty() { isDirty = true; }
        function escapeHtml(value) { return value.replace(/[&<>"']/g, function (match) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[match]; }); }
        function setPhoto(src) {
            if (src) {
                photoPreview.src = src;
                photoPreview.classList.remove('hidden');
                photoPlaceholder.classList.add('hidden');
            } else {
                photoPreview.src = '';
                photoPreview.classList.add('hidden');
                photoPlaceholder.classList.remove('hidden');
            }
        }
        function renderFiles(target, files, emptyText) {
            if (!files || !files.length) {
                target.innerHTML = emptyText;
                return;
            }
            target.innerHTML = Array.from(files).map(file => '<span class="student-file-pill"><i class="bi bi-paperclip"></i>' + escapeHtml(file.name) + '</span>').join('');
        }
        function openTab(tabName) {
            tabButtons.forEach(button => {
                const isActive = button.dataset.studentTab === tabName;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            tabPanels.forEach(panel => {
                panel.classList.toggle('is-active', panel.dataset.studentTabPanel === tabName);
            });
            updateStepButtons(tabName);
        }
        function updateStepButtons(tabName) {
            const index = tabOrder.indexOf(tabName);
            const isFirst = index <= 0;
            const isLast = index === tabOrder.length - 1;

            if (previousStepButton) {
                previousStepButton.classList.toggle('hidden', isFirst);
            }
            if (nextStepButton) {
                nextStepButton.classList.toggle('hidden', isLast);
            }
            if (saveStudentButton) {
                saveStudentButton.classList.toggle('hidden', !isLast);
            }
        }
        function validateField(field) {
            const type = (field.type || '').toLowerCase();
            if (['hidden', 'checkbox', 'file'].includes(type)) return true;
            const errorTarget = field.parentElement.querySelector('.student-error-text');
            if (!errorTarget) return true;
            let message = '';
            if (field.required && !field.value.trim()) message = 'This field is required.';
            if (!message && field.dataset.validate === 'email' && field.value && !/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(field.value)) message = 'Enter a valid email address.';
            if (!message && field.dataset.validate === 'phone' && field.value && !/^\\d{10}$/.test(field.value)) message = 'Enter a 10-digit phone number.';
            if (!message && field.dataset.validate === 'year' && field.value && !/^\\d{4}$/.test(field.value)) message = 'Enter a valid 4-digit year.';
            errorTarget.textContent = message;
            field.classList.toggle('error', !!message);
            return !message;
        }
        async function convertAdToBs(adDate) {
            if (!adDate) return;
            try {
                const response = await fetch(@json(route('util.convert.ad-to-bs')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ date: adDate })
                });
                if (!response.ok) return;
                const data = await response.json();
                if (data.bs) dobBsInput.value = data.bs;
            } catch (error) {
                console.warn('Date conversion failed', error);
            }
        }
        function setParentLookupStatus(message, tone) {
            if (!parentLookupStatus) return;
            parentLookupStatus.textContent = message;
            parentLookupStatus.classList.remove('is-success', 'is-error', 'is-muted');
            parentLookupStatus.classList.add(tone === 'success' ? 'is-success' : tone === 'error' ? 'is-error' : 'is-muted');
        }
        function fillParentFields(parent) {
            if (!parent) return;
            if (parent.name !== undefined) parentNameInput.value = parent.name ?? '';
            if (parent.email !== undefined) parentEmailInput.value = parent.email ?? '';
            if (parent.phone !== undefined) parentPhoneInput.value = parent.phone ?? '';
            if (parent.gender !== undefined) parentGenderInput.value = parent.gender ?? '';
            if (parent.status !== undefined) parentStatusInput.value = parent.status ?? 'active';
            if (parent.occupation !== undefined) parentOccupationInput.value = parent.occupation ?? '';
            if (parent.address !== undefined) parentAddressInput.value = parent.address ?? '';
            if (parent.bio !== undefined) parentBioInput.value = parent.bio ?? '';
            if (parent.relationship && !parentRelationshipInput.value) {
                parentRelationshipInput.value = parent.relationship;
            }
        }
        async function lookupParentByEmail() {
            const email = (parentEmailInput?.value || '').trim();
            if (!email) {
                setParentLookupStatus('Enter the parent email to load an existing profile or create a new linked account.', 'muted');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                setParentLookupStatus('Enter a valid parent email to check for an existing profile.', 'error');
                return;
            }

            const currentToken = ++parentLookupToken;
            setParentLookupStatus('Checking for an existing parent profile...', 'muted');

            try {
                const url = new URL(@json(route('admin.parents.lookup')));
                url.searchParams.set('email', email);
                const response = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (currentToken !== parentLookupToken) return;

                if (!response.ok) {
                    setParentLookupStatus('Unable to look up the parent right now. The student can still be saved, but the parent profile was not loaded.', 'error');
                    return;
                }

                const data = await response.json();
                if (currentToken !== parentLookupToken) return;

                if (data.found && data.parent) {
                    fillParentFields(data.parent);
                    const countText = data.parent.children_count > 0
                        ? `${data.parent.children_count} child account(s) already linked.`
                        : 'No linked student records yet.';
                    setParentLookupStatus(`Existing parent profile loaded. ${countText}`, 'success');
                } else {
                    setParentLookupStatus('No parent profile found. A new parent account will be created on save and login details will be emailed.', 'muted');
                }
            } catch (error) {
                console.warn('Parent lookup failed', error);
                setParentLookupStatus('Parent lookup failed temporarily. You can still save the student and create the linked parent account.', 'error');
            }
        }
        function scheduleParentLookup() {
            clearTimeout(parentLookupTimer);
            parentLookupTimer = setTimeout(() => lookupParentByEmail(), 400);
        }

        choosePhotoButton.addEventListener('click', () => photoInput.click());
        tabButtons.forEach(button => button.addEventListener('click', () => openTab(button.dataset.studentTab)));
        previousStepButton.addEventListener('click', () => {
            const activeTab = tabButtons.find(button => button.classList.contains('is-active'))?.dataset.studentTab ?? tabOrder[0];
            const index = tabOrder.indexOf(activeTab);
            if (index > 0) {
                openTab(tabOrder[index - 1]);
            }
        });
        nextStepButton.addEventListener('click', () => {
            const activeTab = tabButtons.find(button => button.classList.contains('is-active'))?.dataset.studentTab ?? tabOrder[0];
            const index = tabOrder.indexOf(activeTab);
            if (index > -1 && index < tabOrder.length - 1) {
                openTab(tabOrder[index + 1]);
            }
        });
        removePhotoButton.addEventListener('click', () => { markDirty(); photoInput.value = ''; removePhotoInput.value = '1'; setPhoto(''); });
        photoInput.addEventListener('change', function () {
            markDirty();
            removePhotoInput.value = '0';
            const file = this.files && this.files[0];
            if (!file) { setPhoto(''); return; }
            const reader = new FileReader();
            reader.onload = e => setPhoto(e.target.result);
            reader.readAsDataURL(file);
        });
        dropzone.addEventListener('click', e => { if (!e.target.closest('button')) photoInput.click(); });
        ['dragenter', 'dragover'].forEach(eventName => dropzone.addEventListener(eventName, e => { e.preventDefault(); dropzone.classList.add('is-dragover'); }));
        ['dragleave', 'drop'].forEach(eventName => dropzone.addEventListener(eventName, e => { e.preventDefault(); dropzone.classList.remove('is-dragover'); }));
        dropzone.addEventListener('drop', function (event) {
            const files = event.dataTransfer.files;
            if (!files.length) return;
            photoInput.files = files;
            photoInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
        isActiveToggle.addEventListener('change', function () {
            markDirty();
            isActiveInput.value = this.checked ? '1' : '0';
            activationLabel.textContent = this.checked ? 'Account enabled' : 'Account disabled';
        });
        if (parentEmailInput) {
            parentEmailInput.addEventListener('input', () => {
                markDirty();
                scheduleParentLookup();
            });
            parentEmailInput.addEventListener('blur', () => lookupParentByEmail());
        }
        idDocumentInput.addEventListener('change', function () { markDirty(); renderFiles(idDocumentFiles, this.files, 'No file selected.'); });
        certificatesInput.addEventListener('change', function () { markDirty(); renderFiles(certificateFiles, this.files, 'No files selected.'); });
        dobInput.addEventListener('change', function () { if (this.value) convertAdToBs(this.value); });

        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', () => { markDirty(); validateField(field); });
            field.addEventListener('blur', () => validateField(field));
        });

        form.addEventListener('submit', function (event) {
            let valid = true;
            form.querySelectorAll('input, select, textarea').forEach(field => { valid = validateField(field) && valid; });
            if (!valid) {
                event.preventDefault();
                const firstErrorField = form.querySelector('.student-input.error, .student-select.error, .student-textarea.error');
                const tabPanel = firstErrorField?.closest('[data-student-tab-panel]');
                if (tabPanel) openTab(tabPanel.dataset.studentTabPanel);
            } else {
                isDirty = false;
            }
        });

        window.addEventListener('beforeunload', function (event) {
            if (!isDirty) return;
            event.preventDefault();
            event.returnValue = '';
        });

        const initialErrorField = form.querySelector('.student-input.error, .student-select.error, .student-textarea.error');
        const initialTabPanel = initialErrorField?.closest('[data-student-tab-panel]');
        if (initialTabPanel) openTab(initialTabPanel.dataset.studentTabPanel);
        else openTab(tabOrder[0]);
        if (parentEmailInput && parentEmailInput.value.trim()) {
            lookupParentByEmail();
        }
    })();
</script>
@endsection

