@php
    $genderOptions = [
        ['value' => '', 'label' => 'Select gender'],
        ['value' => 'male', 'label' => 'Male'],
        ['value' => 'female', 'label' => 'Female'],
        ['value' => 'other', 'label' => 'Other'],
    ];

    $bloodGroupOptions = ['', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $statusOptions = ['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'];
    $countryOptions = ['Nepal', 'India', 'Bangladesh', 'Bhutan', 'China', 'United States', 'United Kingdom', 'Australia', 'Canada'];
    $modalFieldNames = [
        'name', 'email', 'username', 'phone', 'student_id', 'department', 'program', 'semester', 'section',
        'academic_year', 'enrollment_date', 'expected_graduation_year', 'date_of_birth', 'gender', 'blood_group',
        'national_id_number', 'secondary_phone', 'emergency_contact', 'emergency_contact_name',
        'emergency_relationship', 'address', 'city', 'state_province', 'postal_code', 'country',
        'medical_conditions', 'allergies', 'disability_status', 'status', 'notes', 'is_active',
    ];

    $oldStudentPayload = [];
    foreach ($modalFieldNames as $fieldName) {
        $oldStudentPayload[$fieldName] = old($fieldName);
    }

    $suggestedAcademicYears = array_values(array_unique(array_filter(array_merge(
        $academicYears ?? [],
        [
            now()->format('Y') . '/' . now()->addYear()->format('y'),
            now()->subYear()->format('Y') . '/' . now()->format('y'),
            now()->addYear()->format('Y') . '/' . now()->addYears(2)->format('y'),
        ]
    ))));
@endphp

<style>
    @keyframes studentModalIn {
        from { opacity: 0; transform: translateY(18px) scale(0.985); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .student-modal-panel { animation: studentModalIn 0.22s ease-out; }
    .student-dropzone.is-dragging { border-color: #ef4444; background: linear-gradient(180deg, #fff7ed 0%, #fff1f2 100%); box-shadow: 0 20px 40px rgba(248, 113, 113, 0.14); }
    .student-field-error { display: none; }
    .student-field-error[data-visible="true"] { display: block; }
    .student-input-invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important; background: #fff8f8 !important; }
    .student-input-valid { border-color: #10b981 !important; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.08) !important; }
</style>

<datalist id="student-department-options">
    @foreach($departmentOptions ?? [] as $departmentOption)
        <option value="{{ $departmentOption }}"></option>
    @endforeach
</datalist>

<datalist id="student-program-options">
    @foreach($programOptions ?? [] as $programOption)
        <option value="{{ $programOption }}"></option>
    @endforeach
</datalist>

<datalist id="student-section-options">
    @foreach($sectionOptions ?? [] as $sectionOption)
        <option value="{{ $sectionOption }}"></option>
    @endforeach
</datalist>

<datalist id="student-academic-year-options">
    @foreach($suggestedAcademicYears as $yearOption)
        <option value="{{ $yearOption }}"></option>
    @endforeach
</datalist>

<div id="studentFormModal" class="hidden fixed inset-0 z-50 p-4 sm:p-6 lg:p-8" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-[2px]" data-form-close></div>
    <div class="student-modal-panel relative mx-auto flex h-[calc(100vh-2rem)] max-h-[940px] w-full max-w-6xl flex-col overflow-hidden rounded-[30px] border border-white/70 bg-slate-50 shadow-[0_32px_80px_rgba(15,23,42,0.32)]">
        <div class="sticky top-0 z-20 border-b border-slate-200/80 bg-gradient-to-r from-rose-50 via-white to-orange-50/70 px-6 py-5 backdrop-blur sm:px-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="studentFormModeBadge" class="inline-flex items-center rounded-full border border-rose-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-rose-600">Create profile</span>
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-500">Responsive dashboard modal</span>
                    </div>
                    <div>
                        <h3 id="studentFormTitle" class="text-2xl font-semibold tracking-tight text-slate-900">Add Student</h3>
                        <p id="studentFormSubtitle" class="mt-1 text-sm text-slate-500">Create a complete student profile with academic, health, and document details.</p>
                    </div>
                </div>
                <button type="button" id="studentFormCloseButton" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:-translate-y-0.5 hover:border-rose-200 hover:text-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
        </div>

        <div id="studentFormLoading" class="hidden border-b border-slate-200 bg-white/90 px-6 py-3 text-sm text-slate-500 sm:px-8">
            <span class="inline-flex items-center gap-2">
                <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-rose-500"></span>
                Loading student details...
            </span>
        </div>

        <form id="studentForm" action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
            @csrf
            <input type="hidden" id="studentFormMethod" name="_method" value="">
            <input type="hidden" id="studentFormModeInput" name="_modal_mode" value="add">
            <input type="hidden" id="studentFormUserId" name="_student_user_id" value="">
            <input type="hidden" id="studentRole" name="role" value="student">
            <input type="hidden" id="studentIsActive" name="is_active" value="1">
            <input type="hidden" id="studentRemovePhoto" name="remove_profile_photo" value="0">
            <input type="hidden" id="studentRemoveIdDocument" name="remove_id_document" value="0">
            <input type="hidden" id="studentRemoveCertificates" name="remove_certificates" value="0">
            <input type="hidden" id="studentReplaceCertificates" name="replace_certificates" value="0">

            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">
                <div id="studentFormAlert" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>

                <div class="mt-6 grid gap-6 lg:grid-cols-[280px,minmax(0,1fr)]">
                    <aside class="space-y-5">
                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Profile Photo</p>
                                <p class="mt-1 text-xs text-slate-500">Upload a clear square photo. Drag an image here or click to browse.</p>
                            </div>
                            <div class="px-5 py-5">
                                <div id="studentPhotoDropzone" class="student-dropzone group rounded-[28px] border border-dashed border-slate-300 bg-gradient-to-b from-slate-50 to-white p-5 text-center transition">
                                    <div class="mx-auto flex h-36 w-36 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-slate-100 shadow-[0_18px_40px_rgba(15,23,42,0.12)]">
                                        <img id="studentPhotoPreview" src="" alt="Student profile preview" class="hidden h-full w-full object-cover">
                                        <div id="studentPhotoPlaceholder" class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                                            <i class="bi bi-person-circle text-5xl"></i>
                                            <span class="mt-2 text-xs font-medium uppercase tracking-[0.22em]">No Photo</span>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="button" id="studentChoosePhotoButton" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:-translate-y-0.5 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                                            <i class="bi bi-camera2"></i>
                                            <span>Choose Photo</span>
                                        </button>
                                    </div>

                                    <div class="mt-3 flex flex-wrap justify-center gap-2">
                                        <button type="button" id="studentChangePhotoButton" class="hidden rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Change Photo</button>
                                        <button type="button" id="studentRemovePhotoButton" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-600 transition hover:border-rose-300 hover:bg-rose-100">Remove Photo</button>
                                    </div>

                                    <p class="mt-4 text-xs text-slate-500">PNG or JPG, up to 4MB. Best at 1:1 ratio.</p>
                                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden">
                                    <p class="student-field-error mt-3 text-left text-xs font-medium text-rose-600" data-error-for="profile_photo"></p>
                                </div>
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Quick Status</p>
                                <p class="mt-1 text-xs text-slate-500">Keep the account state visible while editing.</p>
                            </div>
                            <div class="space-y-4 px-5 py-5">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Account Active</p>
                                            <p class="mt-1 text-xs text-slate-500">Inactive accounts can be kept on record without enabling access.</p>
                                        </div>
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input type="checkbox" id="studentIsActiveToggle" class="peer sr-only" checked>
                                            <span class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500"></span>
                                            <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                                        </label>
                                    </div>
                                    <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-600">
                                        <span id="studentActiveDot" class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                        <span id="studentIsActiveLabel">Active</span>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Document Summary</p>
                                    <div id="studentDocumentSummary" class="mt-3 space-y-2 text-sm text-slate-600">
                                        <p>No uploaded documents yet.</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </aside>

                    <div class="space-y-5">
                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">Basic Information</p>
                                        <p class="mt-1 text-xs text-slate-500">Core identity and system access details.</p>
                                    </div>
                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                        2-column layout
                                    </span>
                                </div>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                @include('admin.students.partials.management-text-field', ['id' => 'name', 'label' => 'Full Name', 'required' => true, 'placeholder' => 'Enter full student name', 'icon' => 'bi-person'])
                                @include('admin.students.partials.management-text-field', ['id' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'student@school.edu', 'icon' => 'bi-envelope'])
                                @include('admin.students.partials.management-text-field', ['id' => 'student_id', 'label' => 'Student ID', 'required' => true, 'placeholder' => 'STU-2026-001', 'icon' => 'bi-credit-card-2-front'])
                                @include('admin.students.partials.management-text-field', ['id' => 'username', 'label' => 'Username', 'placeholder' => 'Optional username', 'icon' => 'bi-at'])
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Academic Information</p>
                                <p class="mt-1 text-xs text-slate-500">Placement, program context, and enrollment timeline.</p>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                @include('admin.students.partials.management-text-field', ['id' => 'department', 'label' => 'Department', 'required' => true, 'placeholder' => 'Computer Science', 'icon' => 'bi-building', 'list' => 'student-department-options'])
                                @include('admin.students.partials.management-text-field', ['id' => 'program', 'label' => 'Program / Course', 'placeholder' => 'BSc. CSIT', 'icon' => 'bi-journal-bookmark', 'list' => 'student-program-options'])
                                @include('admin.students.partials.management-select-field', ['id' => 'semester', 'label' => 'Semester', 'required' => true, 'icon' => 'bi-layers', 'options' => collect(range(1, 6))->map(fn ($n) => ['value' => (string) $n, 'label' => 'Semester ' . $n])->prepend(['value' => '', 'label' => 'Select semester'])->all()])
                                @include('admin.students.partials.management-text-field', ['id' => 'section', 'label' => 'Section / Group', 'placeholder' => 'Section A', 'icon' => 'bi-people', 'list' => 'student-section-options'])
                                @include('admin.students.partials.management-text-field', ['id' => 'academic_year', 'label' => 'Academic Year', 'required' => true, 'placeholder' => '2026/27', 'icon' => 'bi-calendar2-range', 'list' => 'student-academic-year-options'])
                                @include('admin.students.partials.management-date-field', ['id' => 'enrollment_date', 'label' => 'Enrollment Date', 'icon' => 'bi-calendar-plus'])
                                @include('admin.students.partials.management-text-field', ['id' => 'expected_graduation_year', 'label' => 'Expected Graduation Year', 'placeholder' => '2030', 'icon' => 'bi-calendar-check'])
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Personal Information</p>
                                <p class="mt-1 text-xs text-slate-500">Personal profile, compliance, and identity details.</p>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                @include('admin.students.partials.management-date-field', ['id' => 'date_of_birth', 'label' => 'Date of Birth', 'required' => true, 'icon' => 'bi-calendar-heart'])
                                @include('admin.students.partials.management-select-field', ['id' => 'gender', 'label' => 'Gender', 'icon' => 'bi-gender-ambiguous', 'options' => $genderOptions])
                                @include('admin.students.partials.management-select-field', ['id' => 'blood_group', 'label' => 'Blood Group', 'icon' => 'bi-droplet-half', 'options' => collect($bloodGroupOptions)->map(fn ($value) => ['value' => $value, 'label' => $value === '' ? 'Select blood group' : $value])->all()])
                                @include('admin.students.partials.management-text-field', ['id' => 'national_id_number', 'label' => 'National ID / Citizenship Number', 'placeholder' => 'Enter ID number', 'icon' => 'bi-shield-check'])
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Contact & Emergency</p>
                                <p class="mt-1 text-xs text-slate-500">Primary contact details and emergency fallback information.</p>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                @include('admin.students.partials.management-text-field', ['id' => 'phone', 'label' => 'Phone (Primary)', 'required' => true, 'placeholder' => '10-digit phone number', 'icon' => 'bi-telephone', 'validate' => 'phone'])
                                @include('admin.students.partials.management-text-field', ['id' => 'secondary_phone', 'label' => 'Secondary Phone', 'placeholder' => 'Optional secondary phone', 'icon' => 'bi-phone', 'validate' => 'phone'])
                                @include('admin.students.partials.management-text-field', ['id' => 'emergency_contact', 'label' => 'Emergency Contact', 'placeholder' => '10-digit emergency phone', 'icon' => 'bi-telephone-forward', 'validate' => 'phone'])
                                @include('admin.students.partials.management-text-field', ['id' => 'emergency_contact_name', 'label' => 'Emergency Contact Name', 'placeholder' => 'Guardian / family member', 'icon' => 'bi-person-hearts'])
                                @include('admin.students.partials.management-text-field', ['id' => 'emergency_relationship', 'label' => 'Emergency Relationship', 'placeholder' => 'Parent, sibling, guardian...', 'icon' => 'bi-diagram-3'])
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Address</p>
                                <p class="mt-1 text-xs text-slate-500">Store full location data in a structured format.</p>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                @include('admin.students.partials.management-textarea-field', ['id' => 'address', 'label' => 'Address', 'required' => true, 'placeholder' => 'Street, ward, municipality...', 'icon' => 'bi-geo-alt', 'span' => 'md:col-span-2', 'rows' => 3])
                                @include('admin.students.partials.management-text-field', ['id' => 'city', 'label' => 'City', 'placeholder' => 'City', 'icon' => 'bi-buildings'])
                                @include('admin.students.partials.management-text-field', ['id' => 'state_province', 'label' => 'State / Province', 'placeholder' => 'Province', 'icon' => 'bi-map'])
                                @include('admin.students.partials.management-text-field', ['id' => 'postal_code', 'label' => 'Postal Code', 'placeholder' => 'Postal code', 'icon' => 'bi-mailbox'])
                                @include('admin.students.partials.management-select-field', ['id' => 'country', 'label' => 'Country', 'icon' => 'bi-globe-central-south-asia', 'options' => collect($countryOptions)->map(fn ($value) => ['value' => $value, 'label' => $value])->prepend(['value' => '', 'label' => 'Select country'])->all()])
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Health Information</p>
                                <p class="mt-1 text-xs text-slate-500">Medical notes that may matter for day-to-day support.</p>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                @include('admin.students.partials.management-textarea-field', ['id' => 'medical_conditions', 'label' => 'Medical Conditions', 'placeholder' => 'Any known conditions', 'icon' => 'bi-heart-pulse', 'rows' => 3])
                                @include('admin.students.partials.management-textarea-field', ['id' => 'allergies', 'label' => 'Allergies', 'placeholder' => 'Food, medicine, environmental...', 'icon' => 'bi-exclamation-triangle', 'rows' => 3])
                                @include('admin.students.partials.management-text-field', ['id' => 'disability_status', 'label' => 'Disability Status', 'placeholder' => 'Optional accessibility note', 'icon' => 'bi-universal-access-circle', 'span' => 'md:col-span-2'])
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Account & Status</p>
                                <p class="mt-1 text-xs text-slate-500">Set record state and keep access aligned with student status.</p>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                @include('admin.students.partials.management-select-field', ['id' => 'status', 'label' => 'Status', 'required' => true, 'icon' => 'bi-activity', 'options' => collect($statusOptions)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->all()])
                                <div class="space-y-2">
                                    <label for="studentRoleDisplay" class="block text-sm font-medium text-slate-700">Role</label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                            <i class="bi bi-person-badge"></i>
                                        </span>
                                        <select id="studentRoleDisplay" class="block w-full appearance-none rounded-2xl border border-slate-200 bg-slate-100 py-3 pl-11 pr-10 text-sm text-slate-500 shadow-sm outline-none" disabled>
                                            <option selected>Student</option>
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Document Uploads</p>
                                <p class="mt-1 text-xs text-slate-500">Upload supporting files and keep current attachments visible in edit mode.</p>
                            </div>
                            <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                                <div class="space-y-2 md:col-span-2">
                                    <label for="id_document" class="block text-sm font-medium text-slate-700">ID Document Upload</label>
                                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label for="id_document" class="inline-flex cursor-pointer items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:text-slate-900">
                                                <i class="bi bi-file-earmark-arrow-up"></i>
                                                <span>Choose ID Document</span>
                                            </label>
                                            <button type="button" id="studentRemoveIdDocumentButton" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-600 transition hover:border-rose-300 hover:bg-rose-100">Remove Current File</button>
                                        </div>
                                        <input type="file" id="id_document" name="id_document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                                        <div id="studentIdDocumentList" class="mt-4 space-y-2 text-sm text-slate-600">
                                            <p>No ID document uploaded.</p>
                                        </div>
                                        <p class="student-field-error mt-3 text-xs font-medium text-rose-600" data-error-for="id_document"></p>
                                    </div>
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <label for="certificates" class="block text-sm font-medium text-slate-700">Certificates Upload</label>
                                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label for="certificates" class="inline-flex cursor-pointer items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:text-slate-900">
                                                <i class="bi bi-files"></i>
                                                <span>Select Certificates</span>
                                            </label>
                                            <button type="button" id="studentRemoveCertificatesButton" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-600 transition hover:border-rose-300 hover:bg-rose-100">Clear Current Files</button>
                                        </div>
                                        <input type="file" id="certificates" name="certificates[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                                        <div id="studentCertificatesList" class="mt-4 space-y-2 text-sm text-slate-600">
                                            <p>No certificates uploaded.</p>
                                        </div>
                                        <p class="mt-3 text-xs text-slate-500">Selecting new certificate files replaces the current set.</p>
                                        <p class="student-field-error mt-3 text-xs font-medium text-rose-600" data-error-for="certificates"></p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Additional Notes</p>
                                <p class="mt-1 text-xs text-slate-500">Use this space for details that do not fit other categories.</p>
                            </div>
                            <div class="px-5 py-5">
                                @include('admin.students.partials.management-textarea-field', ['id' => 'notes', 'label' => 'Notes / Details', 'placeholder' => 'Internal notes, learning support info, or context for staff...', 'icon' => 'bi-stickies', 'rows' => 4])
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="sticky bottom-0 z-20 border-t border-slate-200/90 bg-white/95 px-6 py-4 backdrop-blur sm:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">All required fields are marked clearly. Validation feedback appears inline before submit.</p>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" id="studentFormCancelButton" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <i class="bi bi-x-circle"></i>
                            <span>Cancel</span>
                        </button>
                        <button type="submit" id="studentFormSubmitButton" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_16px_35px_rgba(15,23,42,0.18)] transition hover:-translate-y-0.5 hover:bg-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <i class="bi bi-check2-circle"></i>
                            <span id="studentFormSubmitText">Save Student</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="studentViewModal" class="hidden fixed inset-0 z-50 p-4 sm:p-6 lg:p-8" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-[2px]" data-view-close></div>
    <div class="student-modal-panel relative mx-auto flex h-[calc(100vh-2rem)] max-h-[920px] w-full max-w-5xl flex-col overflow-hidden rounded-[30px] border border-white/70 bg-white shadow-[0_32px_80px_rgba(15,23,42,0.32)]">
        <div class="sticky top-0 z-20 border-b border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800 px-6 py-5 text-white sm:px-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-white/80">Student Profile</span>
                    <div>
                        <h3 id="studentViewTitle" class="text-2xl font-semibold tracking-tight">View Student</h3>
                        <p id="studentViewSubtitle" class="mt-1 text-sm text-slate-300">A complete, dashboard-style overview of the selected student record.</p>
                    </div>
                </div>
                <button type="button" id="studentViewCloseButton" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-slate-200 transition hover:-translate-y-0.5 hover:bg-white/15 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
        </div>

        <div id="studentViewLoading" class="hidden border-b border-slate-200 bg-white px-6 py-3 text-sm text-slate-500 sm:px-8">
            <span class="inline-flex items-center gap-2">
                <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-rose-500"></span>
                Loading student details...
            </span>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">
            <div class="grid gap-6 lg:grid-cols-[260px,minmax(0,1fr)]">
                <aside class="space-y-5">
                    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-slate-50 shadow-sm">
                        <div class="px-5 py-5 text-center">
                            <div class="mx-auto flex h-36 w-36 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-slate-100 shadow-[0_20px_40px_rgba(15,23,42,0.16)]">
                                <img id="studentViewPhoto" src="" alt="Student photo" class="hidden h-full w-full object-cover">
                                <div id="studentViewPlaceholder" class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                                    <i class="bi bi-person-circle text-5xl"></i>
                                    <span class="mt-2 text-xs font-medium uppercase tracking-[0.22em]">Profile</span>
                                </div>
                            </div>
                            <h4 id="studentViewName" class="mt-4 text-xl font-semibold text-slate-900">Student Name</h4>
                            <p id="studentViewEmail" class="mt-1 text-sm text-slate-500">student@email.com</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                <span id="studentViewStatusBadge" class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                                <span id="studentViewActiveBadge" class="inline-flex items-center gap-2 rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">Account Active</span>
                            </div>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">Quick Facts</p>
                        </div>
                        <div class="space-y-3 px-5 py-5 text-sm text-slate-600">
                            <div class="flex items-start justify-between gap-3"><span class="text-slate-400">Student ID</span><span id="studentViewStudentId" class="text-right font-medium text-slate-900">-</span></div>
                            <div class="flex items-start justify-between gap-3"><span class="text-slate-400">Username</span><span id="studentViewUsername" class="text-right font-medium text-slate-900">-</span></div>
                            <div class="flex items-start justify-between gap-3"><span class="text-slate-400">Department</span><span id="studentViewDepartment" class="text-right font-medium text-slate-900">-</span></div>
                            <div class="flex items-start justify-between gap-3"><span class="text-slate-400">Program</span><span id="studentViewProgram" class="text-right font-medium text-slate-900">-</span></div>
                            <div class="flex items-start justify-between gap-3"><span class="text-slate-400">Semester</span><span id="studentViewSemester" class="text-right font-medium text-slate-900">-</span></div>
                            <div class="flex items-start justify-between gap-3"><span class="text-slate-400">Section</span><span id="studentViewSection" class="text-right font-medium text-slate-900">-</span></div>
                        </div>
                    </section>
                </aside>

                <div class="space-y-5">
                    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">Academic & Personal Details</p>
                        </div>
                        <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Academic Year</p><p id="studentViewAcademicYear" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Enrollment Date</p><p id="studentViewEnrollmentDate" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Expected Graduation</p><p id="studentViewGraduationYear" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Role</p><p id="studentViewRole" class="mt-2 text-sm font-medium text-slate-900">Student</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Date of Birth</p><p id="studentViewDob" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Gender</p><p id="studentViewGender" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Blood Group</p><p id="studentViewBloodGroup" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">National ID</p><p id="studentViewNationalId" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">Contact & Address</p>
                        </div>
                        <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Primary Phone</p><p id="studentViewPhone" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Secondary Phone</p><p id="studentViewSecondaryPhone" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Emergency Contact</p><p id="studentViewEmergencyContact" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Emergency Name</p><p id="studentViewEmergencyName" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Relationship</p><p id="studentViewEmergencyRelationship" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Country</p><p id="studentViewCountry" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div class="md:col-span-2"><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Address</p><p id="studentViewAddress" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">City</p><p id="studentViewCity" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">State / Province</p><p id="studentViewState" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Postal Code</p><p id="studentViewPostalCode" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">Health & Documents</p>
                        </div>
                        <div class="grid gap-4 px-5 py-5 md:grid-cols-2">
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Medical Conditions</p><p id="studentViewMedicalConditions" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Allergies</p><p id="studentViewAllergies" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div class="md:col-span-2"><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Disability Status</p><p id="studentViewDisabilityStatus" class="mt-2 text-sm font-medium text-slate-900">-</p></div>
                            <div class="md:col-span-2"><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ID Document</p><div id="studentViewIdDocument" class="mt-2 text-sm text-slate-700">-</div></div>
                            <div class="md:col-span-2"><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Certificates</p><div id="studentViewCertificates" class="mt-2 text-sm text-slate-700">-</div></div>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">Notes</p>
                        </div>
                        <div class="px-5 py-5">
                            <p id="studentViewNotes" class="text-sm leading-6 text-slate-700">-</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="sticky bottom-0 z-20 border-t border-slate-200 bg-white/95 px-6 py-4 backdrop-blur sm:px-8">
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="studentViewEditButton" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit Student</span>
                </button>
                <button type="button" id="studentViewCloseFooterButton" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <i class="bi bi-x-circle"></i>
                    <span>Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const formModal = document.getElementById('studentFormModal');
        const viewModal = document.getElementById('studentViewModal');
        const studentForm = document.getElementById('studentForm');
        const studentBaseUrl = @json(url('/admin/students'));
        const studentStoreUrl = @json(route('admin.students.store'));
        const modalFieldNames = @json($modalFieldNames);
        const oldStudentPayload = @json($oldStudentPayload);
        const oldModalMode = @json(old('_modal_mode'));
        const oldStudentUserId = @json(old('_student_user_id'));
        const serverErrors = @json($errors->getMessages());

        const state = {
            dirty: false,
            populating: false,
            mode: 'add',
            currentStudentId: null,
            viewStudentId: null,
            currentPhotoUrl: null,
            currentIdDocument: null,
            currentCertificates: [],
        };

        const els = {
            body: document.body,
            formTitle: document.getElementById('studentFormTitle'),
            formSubtitle: document.getElementById('studentFormSubtitle'),
            formModeBadge: document.getElementById('studentFormModeBadge'),
            formCloseButton: document.getElementById('studentFormCloseButton'),
            formCancelButton: document.getElementById('studentFormCancelButton'),
            formSubmitText: document.getElementById('studentFormSubmitText'),
            formMethod: document.getElementById('studentFormMethod'),
            formModeInput: document.getElementById('studentFormModeInput'),
            formUserId: document.getElementById('studentFormUserId'),
            formLoading: document.getElementById('studentFormLoading'),
            formAlert: document.getElementById('studentFormAlert'),
            photoInput: document.getElementById('profile_photo'),
            photoDropzone: document.getElementById('studentPhotoDropzone'),
            photoPreview: document.getElementById('studentPhotoPreview'),
            photoPlaceholder: document.getElementById('studentPhotoPlaceholder'),
            choosePhotoButton: document.getElementById('studentChoosePhotoButton'),
            changePhotoButton: document.getElementById('studentChangePhotoButton'),
            removePhotoButton: document.getElementById('studentRemovePhotoButton'),
            removePhotoInput: document.getElementById('studentRemovePhoto'),
            isActiveInput: document.getElementById('studentIsActive'),
            isActiveToggle: document.getElementById('studentIsActiveToggle'),
            isActiveLabel: document.getElementById('studentIsActiveLabel'),
            activeDot: document.getElementById('studentActiveDot'),
            removeIdDocumentInput: document.getElementById('studentRemoveIdDocument'),
            removeCertificatesInput: document.getElementById('studentRemoveCertificates'),
            replaceCertificatesInput: document.getElementById('studentReplaceCertificates'),
            idDocumentInput: document.getElementById('id_document'),
            certificatesInput: document.getElementById('certificates'),
            idDocumentList: document.getElementById('studentIdDocumentList'),
            certificatesList: document.getElementById('studentCertificatesList'),
            removeIdDocumentButton: document.getElementById('studentRemoveIdDocumentButton'),
            removeCertificatesButton: document.getElementById('studentRemoveCertificatesButton'),
            documentSummary: document.getElementById('studentDocumentSummary'),
            viewLoading: document.getElementById('studentViewLoading'),
            viewCloseButton: document.getElementById('studentViewCloseButton'),
            viewCloseFooterButton: document.getElementById('studentViewCloseFooterButton'),
            viewEditButton: document.getElementById('studentViewEditButton'),
        };

        const safeText = (value, fallback = '—') => {
            if (value === null || value === undefined) return fallback;
            const stringValue = String(value).trim();
            return stringValue === '' ? fallback : stringValue;
        };

        const titleCase = (value) => {
            if (!value) return '—';
            return String(value)
                .replace(/[_-]+/g, ' ')
                .split(' ')
                .filter(Boolean)
                .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
                .join(' ');
        };

        const formatDate = (value) => {
            if (!value) return '—';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return safeText(value);
            return new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(date);
        };

        const updateBodyLock = () => {
            const anyOpen = !formModal.classList.contains('hidden') || !viewModal.classList.contains('hidden');
            els.body.style.overflow = anyOpen ? 'hidden' : 'auto';
        };

        const setLoading = (type, show) => {
            const target = type === 'form' ? els.formLoading : els.viewLoading;
            target.classList.toggle('hidden', !show);
        };

        const showAlert = (message) => {
            if (!message) {
                els.formAlert.classList.add('hidden');
                els.formAlert.textContent = '';
                return;
            }

            els.formAlert.textContent = message;
            els.formAlert.classList.remove('hidden');
        };

        const modalBackdropClose = (modal, forceClose) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal || event.target.hasAttribute('data-form-close') || event.target.hasAttribute('data-view-close')) {
                    forceClose();
                }
            });
        };

        const setActiveToggle = (active) => {
            const isActive = Boolean(active);
            els.isActiveInput.value = isActive ? '1' : '0';
            els.isActiveToggle.checked = isActive;
            els.isActiveLabel.textContent = isActive ? 'Active' : 'Inactive';
            els.activeDot.className = `h-2.5 w-2.5 rounded-full ${isActive ? 'bg-emerald-500' : 'bg-slate-400'}`;
        };

        const setPhotoPreview = (url = null) => {
            state.currentPhotoUrl = url || null;
            if (url) {
                els.photoPreview.src = url;
                els.photoPreview.classList.remove('hidden');
                els.photoPlaceholder.classList.add('hidden');
                els.changePhotoButton.classList.remove('hidden');
                els.removePhotoButton.classList.remove('hidden');
            } else {
                els.photoPreview.src = '';
                els.photoPreview.classList.add('hidden');
                els.photoPlaceholder.classList.remove('hidden');
                els.changePhotoButton.classList.add('hidden');
                els.removePhotoButton.classList.add('hidden');
            }
        };

        const renderLinkList = (container, items, emptyMessage) => {
            container.innerHTML = '';

            if (!Array.isArray(items) || items.length === 0) {
                const empty = document.createElement('p');
                empty.textContent = emptyMessage;
                container.appendChild(empty);
                return;
            }

            items.forEach((item) => {
                const row = document.createElement(item.url ? 'a' : 'div');
                row.className = 'flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700';
                if (item.url) {
                    row.href = item.url;
                    row.target = '_blank';
                    row.rel = 'noopener noreferrer';
                }

                const left = document.createElement('span');
                left.className = 'inline-flex min-w-0 items-center gap-2';
                left.innerHTML = `<i class="bi ${item.icon || 'bi-file-earmark'} text-slate-400"></i><span class="truncate">${item.name || 'Untitled file'}</span>`;
                row.appendChild(left);

                const right = document.createElement('span');
                right.className = 'text-xs font-medium text-slate-400';
                right.textContent = item.meta || (item.url ? 'Open' : '');
                row.appendChild(right);

                container.appendChild(row);
            });
        };

        const updateDocumentSummary = () => {
            const summary = [];
            summary.push(state.currentIdDocument ? 'ID document on file' : 'No ID document');
            summary.push(Array.isArray(state.currentCertificates) && state.currentCertificates.length > 0
                ? `${state.currentCertificates.length} certificate file${state.currentCertificates.length > 1 ? 's' : ''}`
                : 'No certificates');
            els.documentSummary.innerHTML = summary.map((item) => `<p>${item}</p>`).join('');
        };

        const resetFileFlags = () => {
            els.removePhotoInput.value = '0';
            els.removeIdDocumentInput.value = '0';
            els.removeCertificatesInput.value = '0';
            els.replaceCertificatesInput.value = '0';
            els.photoInput.value = '';
            els.idDocumentInput.value = '';
            els.certificatesInput.value = '';
        };

        const clearValidation = () => {
            studentForm.querySelectorAll('.student-field-error').forEach((errorEl) => {
                errorEl.textContent = '';
                errorEl.dataset.visible = 'false';
            });

            studentForm.querySelectorAll('.student-input-invalid, .student-input-valid').forEach((field) => {
                field.classList.remove('student-input-invalid', 'student-input-valid');
            });
        };

        const normalizeErrorKey = (name) => String(name || '').split('.')[0];
        const findField = (name) => studentForm.elements.namedItem(name);

        const setFieldError = (name, message = '') => {
            const normalized = normalizeErrorKey(name);
            const errorEl = studentForm.querySelector(`[data-error-for="${normalized}"]`);
            const field = findField(normalized);

            if (field && typeof field.classList !== 'undefined') {
                field.classList.remove('student-input-valid', 'student-input-invalid');
                if (message) {
                    field.classList.add('student-input-invalid');
                }
            }

            if (errorEl) {
                errorEl.textContent = message;
                errorEl.dataset.visible = message ? 'true' : 'false';
            }
        };

        const validateField = (field) => {
            if (!field || field.type === 'hidden' || field.type === 'file' || field.disabled) {
                return true;
            }

            const value = typeof field.value === 'string' ? field.value.trim() : field.value;
            let message = '';

            if (field.required && value === '') {
                message = 'This field is required.';
            } else if (field.type === 'email' && value && !/^\S+@\S+\.\S+$/.test(value)) {
                message = 'Enter a valid email address.';
            } else if (field.dataset.validate === 'phone' && value && !/^\d{10}$/.test(value)) {
                message = 'Enter exactly 10 digits.';
            } else if (field.name === 'username' && value && !/^[A-Za-z0-9._-]+$/.test(value)) {
                message = 'Use letters, numbers, dots, dashes, or underscores only.';
            } else if (field.name === 'expected_graduation_year' && value && !/^\d{4}$/.test(value)) {
                message = 'Use a 4-digit year.';
            }

            setFieldError(field.name, message);

            if (!message && value !== '') {
                field.classList.add('student-input-valid');
            } else {
                field.classList.remove('student-input-valid');
            }

            return message === '';
        };

        const validateForm = () => {
            let isValid = true;
            const fields = studentForm.querySelectorAll('input:not([type="hidden"]):not([type="file"]), select, textarea');
            fields.forEach((field) => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            return isValid;
        };

        const populateFields = (payload = {}) => {
            state.populating = true;
            modalFieldNames.forEach((fieldName) => {
                const field = findField(fieldName);
                if (!field) return;
                const value = payload[fieldName] ?? '';
                field.value = value === null ? '' : value;
            });

            setActiveToggle(payload.is_active === undefined || payload.is_active === null ? true : Boolean(Number(payload.is_active) || payload.is_active === true));
            state.populating = false;
        };

        const resetFormState = () => {
            state.populating = true;
            studentForm.reset();
            modalFieldNames.forEach((fieldName) => {
                const field = findField(fieldName);
                if (field) {
                    field.value = '';
                }
            });
            els.formMethod.value = '';
            els.formUserId.value = '';
            els.formModeInput.value = 'add';
            showAlert('');
            clearValidation();
            setActiveToggle(true);
            const statusField = findField('status');
            if (statusField) {
                statusField.value = 'active';
            }
            resetFileFlags();
            setPhotoPreview(null);
            state.currentStudentId = null;
            state.currentIdDocument = null;
            state.currentCertificates = [];
            renderLinkList(els.idDocumentList, [], 'No ID document uploaded.');
            renderLinkList(els.certificatesList, [], 'No certificates uploaded.');
            els.removeIdDocumentButton.classList.add('hidden');
            els.removeCertificatesButton.classList.add('hidden');
            updateDocumentSummary();
            state.populating = false;
            state.dirty = false;
        };

        const configureFormMode = (mode, studentId = null) => {
            state.mode = mode;
            state.currentStudentId = studentId;

            const isEdit = mode === 'edit';
            els.formTitle.textContent = isEdit ? 'Edit Student' : 'Add Student';
            els.formSubtitle.textContent = isEdit
                ? 'Update the student record, replace uploaded files, and keep the profile current.'
                : 'Create a complete student profile with academic, health, and document details.';
            els.formModeBadge.textContent = isEdit ? 'Editing record' : 'Create profile';
            els.formSubmitText.textContent = isEdit ? 'Update Student' : 'Save Student';
            els.formMethod.value = isEdit ? 'PUT' : '';
            els.formModeInput.value = mode;
            els.formUserId.value = studentId || '';
            studentForm.action = isEdit && studentId ? `${studentBaseUrl}/${studentId}` : studentStoreUrl;
        };

        const openModal = (modal) => {
            modal.classList.remove('hidden');
            updateBodyLock();
        };

        const closeStudentFormModal = (force = false) => {
            if (!force && state.dirty && !window.confirm('Discard unsaved changes?')) {
                return;
            }

            formModal.classList.add('hidden');
            state.dirty = false;
            showAlert('');
            updateBodyLock();
        };

        const closeStudentViewModal = () => {
            viewModal.classList.add('hidden');
            updateBodyLock();
        };

        const fetchStudent = async (id) => {
            const response = await fetch(`${studentBaseUrl}/${id}/json`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to load student details.');
            }

            const data = await response.json();
            return data.student;
        };

        const syncExistingDocuments = (student = {}) => {
            state.currentIdDocument = student.id_document || null;
            state.currentCertificates = Array.isArray(student.certificates) ? student.certificates : [];

            renderLinkList(els.idDocumentList, state.currentIdDocument ? [{ ...state.currentIdDocument, icon: 'bi-file-earmark-text' }] : [], 'No ID document uploaded.');
            renderLinkList(els.certificatesList, state.currentCertificates.map((certificate) => ({ ...certificate, icon: 'bi-file-earmark-medical' })), 'No certificates uploaded.');

            els.removeIdDocumentButton.classList.toggle('hidden', !state.currentIdDocument);
            els.removeCertificatesButton.classList.toggle('hidden', state.currentCertificates.length === 0);
            updateDocumentSummary();
        };

        const openStudentFormModal = async (mode = 'add', studentId = null, preset = null, mergeFetched = false) => {
            resetFormState();
            configureFormMode(mode, studentId);
            openModal(formModal);

            if (mode === 'add') {
                if (preset) {
                    populateFields(preset);
                }
                state.dirty = false;
                return;
            }

            setLoading('form', true);
            showAlert('');

            try {
                let payload = preset || null;

                if (studentId) {
                    const fetched = await fetchStudent(studentId);
                    payload = mergeFetched && payload ? { ...fetched, ...payload } : (payload || fetched);
                    syncExistingDocuments(fetched);
                    setPhotoPreview(fetched.photo_url || null);
                } else if (payload) {
                    syncExistingDocuments(payload);
                    setPhotoPreview(payload.photo_url || null);
                }

                populateFields(payload || {});

                if (!payload?.photo_url) {
                    setPhotoPreview(null);
                }
            } catch (error) {
                showAlert(error.message || 'Unable to load student data.');
            } finally {
                setLoading('form', false);
                state.dirty = false;
            }
        };

        const formatLink = (item) => {
            if (!item || !item.url) return '<span>-</span>';
            return `<a href="${item.url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:text-slate-900"><i class="bi bi-file-earmark-text"></i><span>${item.name || 'Open file'}</span></a>`;
        };

        const formatLinks = (items) => {
            if (!Array.isArray(items) || items.length === 0) return '<span>-</span>';
            return items.map((item) => `<a href="${item.url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:text-slate-900"><i class="bi bi-file-earmark-medical"></i><span>${item.name || 'Certificate'}</span></a>`).join(' ');
        };

        const statusBadgeClasses = (status) => {
            switch (status) {
                case 'inactive':
                    return ['bg-slate-200', 'text-slate-700', 'bg-slate-500'];
                case 'suspended':
                    return ['bg-amber-100', 'text-amber-700', 'bg-amber-500'];
                default:
                    return ['bg-emerald-100', 'text-emerald-700', 'bg-emerald-500'];
            }
        };

        const populateView = (student) => {
            state.viewStudentId = student.id;

            document.getElementById('studentViewName').textContent = safeText(student.name);
            document.getElementById('studentViewEmail').textContent = safeText(student.email);
            document.getElementById('studentViewTitle').textContent = safeText(student.name, 'View Student');
            document.getElementById('studentViewSubtitle').textContent = `Academic year ${safeText(student.academic_year, '—')} | ${safeText(student.department, 'No department')}`;
            document.getElementById('studentViewStudentId').textContent = safeText(student.student_id);
            document.getElementById('studentViewUsername').textContent = safeText(student.username);
            document.getElementById('studentViewDepartment').textContent = safeText(student.department);
            document.getElementById('studentViewProgram').textContent = safeText(student.program);
            document.getElementById('studentViewSemester').textContent = safeText(student.semester ? `Semester ${student.semester}` : '');
            document.getElementById('studentViewSection').textContent = safeText(student.section);
            document.getElementById('studentViewAcademicYear').textContent = safeText(student.academic_year);
            document.getElementById('studentViewEnrollmentDate').textContent = formatDate(student.enrollment_date);
            document.getElementById('studentViewGraduationYear').textContent = safeText(student.expected_graduation_year);
            document.getElementById('studentViewRole').textContent = titleCase(student.role || 'student');
            document.getElementById('studentViewDob').textContent = formatDate(student.date_of_birth);
            document.getElementById('studentViewGender').textContent = titleCase(student.gender);
            document.getElementById('studentViewBloodGroup').textContent = safeText(student.blood_group);
            document.getElementById('studentViewNationalId').textContent = safeText(student.national_id_number);
            document.getElementById('studentViewPhone').textContent = safeText(student.phone);
            document.getElementById('studentViewSecondaryPhone').textContent = safeText(student.secondary_phone);
            document.getElementById('studentViewEmergencyContact').textContent = safeText(student.emergency_contact);
            document.getElementById('studentViewEmergencyName').textContent = safeText(student.emergency_contact_name);
            document.getElementById('studentViewEmergencyRelationship').textContent = safeText(student.emergency_relationship);
            document.getElementById('studentViewCountry').textContent = safeText(student.country);
            document.getElementById('studentViewAddress').textContent = safeText(student.address);
            document.getElementById('studentViewCity').textContent = safeText(student.city);
            document.getElementById('studentViewState').textContent = safeText(student.state_province);
            document.getElementById('studentViewPostalCode').textContent = safeText(student.postal_code);
            document.getElementById('studentViewMedicalConditions').textContent = safeText(student.medical_conditions);
            document.getElementById('studentViewAllergies').textContent = safeText(student.allergies);
            document.getElementById('studentViewDisabilityStatus').textContent = safeText(student.disability_status);
            document.getElementById('studentViewNotes').textContent = safeText(student.notes);
            document.getElementById('studentViewIdDocument').innerHTML = formatLink(student.id_document);
            document.getElementById('studentViewCertificates').innerHTML = formatLinks(student.certificates);

            const [badgeBackground, badgeText, badgeDot] = statusBadgeClasses(student.status);
            const statusBadge = document.getElementById('studentViewStatusBadge');
            statusBadge.className = `inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ${badgeBackground} ${badgeText}`;
            statusBadge.innerHTML = `<span class="h-2.5 w-2.5 rounded-full ${badgeDot}"></span>${titleCase(student.status || 'active')}`;

            const activeBadge = document.getElementById('studentViewActiveBadge');
            const isActive = Boolean(student.is_active);
            activeBadge.textContent = isActive ? 'Account Active' : 'Account Inactive';
            activeBadge.className = `inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ${isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600'}`;

            const viewPhoto = document.getElementById('studentViewPhoto');
            const viewPlaceholder = document.getElementById('studentViewPlaceholder');
            if (student.photo_url) {
                viewPhoto.src = student.photo_url;
                viewPhoto.classList.remove('hidden');
                viewPlaceholder.classList.add('hidden');
            } else {
                viewPhoto.src = '';
                viewPhoto.classList.add('hidden');
                viewPlaceholder.classList.remove('hidden');
            }
        };

        const openStudentViewModal = async (studentId) => {
            openModal(viewModal);
            setLoading('view', true);

            try {
                const student = await fetchStudent(studentId);
                populateView(student);
            } catch (error) {
                window.alert(error.message || 'Unable to load student details.');
                closeStudentViewModal();
            } finally {
                setLoading('view', false);
            }
        };

        const handlePhotoInput = (file) => {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (event) => {
                setPhotoPreview(event.target?.result || null);
                els.removePhotoInput.value = '0';
            };
            reader.readAsDataURL(file);
        };

        const markDirty = () => {
            if (!state.populating) {
                state.dirty = true;
            }
        };

        els.choosePhotoButton.addEventListener('click', () => els.photoInput.click());
        els.changePhotoButton.addEventListener('click', () => els.photoInput.click());
        els.photoDropzone.addEventListener('click', (event) => {
            if (event.target.closest('button')) return;
            els.photoInput.click();
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            els.photoDropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                els.photoDropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            els.photoDropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                els.photoDropzone.classList.remove('is-dragging');
            });
        });

        els.photoDropzone.addEventListener('drop', (event) => {
            const [file] = event.dataTransfer?.files || [];
            if (!file) return;
            const transfer = new DataTransfer();
            transfer.items.add(file);
            els.photoInput.files = transfer.files;
            handlePhotoInput(file);
            markDirty();
        });

        els.photoInput.addEventListener('change', () => {
            const [file] = els.photoInput.files || [];
            handlePhotoInput(file);
            markDirty();
        });

        els.removePhotoButton.addEventListener('click', () => {
            els.photoInput.value = '';
            els.removePhotoInput.value = '1';
            setPhotoPreview(null);
            markDirty();
        });

        els.isActiveToggle.addEventListener('change', () => {
            setActiveToggle(els.isActiveToggle.checked);
            markDirty();
        });

        els.idDocumentInput.addEventListener('change', () => {
            const [file] = els.idDocumentInput.files || [];
            els.removeIdDocumentInput.value = '0';
            if (file) {
                renderLinkList(els.idDocumentList, [{ name: file.name, icon: 'bi-file-earmark-text', meta: 'Selected' }], 'No ID document uploaded.');
                markDirty();
            } else {
                syncExistingDocuments({ id_document: state.currentIdDocument, certificates: state.currentCertificates });
            }
        });

        els.certificatesInput.addEventListener('change', () => {
            const files = Array.from(els.certificatesInput.files || []);
            els.removeCertificatesInput.value = '0';
            els.replaceCertificatesInput.value = files.length > 0 ? '1' : '0';

            if (files.length > 0) {
                renderLinkList(els.certificatesList, files.map((file) => ({ name: file.name, icon: 'bi-file-earmark-medical', meta: 'Selected' })), 'No certificates uploaded.');
                markDirty();
            } else {
                syncExistingDocuments({ id_document: state.currentIdDocument, certificates: state.currentCertificates });
            }
        });

        els.removeIdDocumentButton.addEventListener('click', () => {
            els.idDocumentInput.value = '';
            els.removeIdDocumentInput.value = '1';
            state.currentIdDocument = null;
            renderLinkList(els.idDocumentList, [], 'No ID document uploaded.');
            els.removeIdDocumentButton.classList.add('hidden');
            updateDocumentSummary();
            markDirty();
        });

        els.removeCertificatesButton.addEventListener('click', () => {
            els.certificatesInput.value = '';
            els.removeCertificatesInput.value = '1';
            els.replaceCertificatesInput.value = '0';
            state.currentCertificates = [];
            renderLinkList(els.certificatesList, [], 'No certificates uploaded.');
            els.removeCertificatesButton.classList.add('hidden');
            updateDocumentSummary();
            markDirty();
        });

        studentForm.querySelectorAll('input:not([type="hidden"]):not([type="file"]), select, textarea').forEach((field) => {
            field.classList.add('transition', 'duration-200', 'focus:outline-none', 'focus:ring-4', 'focus:ring-rose-100');
            field.addEventListener('input', () => {
                validateField(field);
                markDirty();
            });
            field.addEventListener('change', () => {
                validateField(field);
                markDirty();
            });
            field.addEventListener('blur', () => validateField(field));
        });

        studentForm.addEventListener('submit', (event) => {
            showAlert('');
            clearValidation();

            if (!validateForm()) {
                event.preventDefault();
                showAlert('Please review the highlighted fields before saving.');
                const firstInvalid = studentForm.querySelector('.student-input-invalid');
                firstInvalid?.focus();
            }
        });

        studentForm.addEventListener('change', markDirty);
        studentForm.addEventListener('input', markDirty);

        const applyServerErrors = (errors) => {
            const flatErrors = Object.entries(errors || {});
            if (flatErrors.length === 0) return;

            showAlert('Please fix the validation errors and try again.');
            flatErrors.forEach(([field, messages]) => {
                setFieldError(field, Array.isArray(messages) ? messages[0] : messages);
            });
        };

        els.formCloseButton.addEventListener('click', () => closeStudentFormModal());
        els.formCancelButton.addEventListener('click', () => closeStudentFormModal());
        els.viewCloseButton.addEventListener('click', closeStudentViewModal);
        els.viewCloseFooterButton.addEventListener('click', closeStudentViewModal);
        els.viewEditButton.addEventListener('click', () => {
            if (!state.viewStudentId) return;
            closeStudentViewModal();
            openStudentFormModal('edit', state.viewStudentId);
        });

        modalBackdropClose(formModal, () => closeStudentFormModal());
        modalBackdropClose(viewModal, closeStudentViewModal);

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            if (!viewModal.classList.contains('hidden')) {
                closeStudentViewModal();
                return;
            }
            if (!formModal.classList.contains('hidden')) {
                closeStudentFormModal();
            }
        });

        window.openStudentFormModal = openStudentFormModal;
        window.openAddStudentModal = () => openStudentFormModal('add');
        window.openStudentViewModal = openStudentViewModal;
        window.closeAddStudentModal = () => closeStudentFormModal();
        window.closeEditStudentModal = () => closeStudentFormModal();
        window.closeViewStudentModal = closeStudentViewModal;
        window.editStudent = (student) => {
            const id = student?.id || student?.user_id || student?._student_user_id;
            if (id) {
                openStudentFormModal('edit', id, student || null, Boolean(student));
            }
        };
        window.viewStudent = (student) => {
            const id = student?.id || student?.user_id;
            if (id) {
                openStudentViewModal(id);
            }
        };

        if (oldModalMode === 'edit' && oldStudentUserId) {
            openStudentFormModal('edit', oldStudentUserId, oldStudentPayload, true).then(() => applyServerErrors(serverErrors));
        } else if (oldModalMode === 'add' && Object.keys(serverErrors || {}).length > 0) {
            openStudentFormModal('add', null, oldStudentPayload).then(() => applyServerErrors(serverErrors));
        }
    })();
</script>
