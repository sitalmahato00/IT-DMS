@php
    $teacher = $teacher ?? new \App\Models\User();
    $teacherProfile = $teacherProfile ?? new \App\Models\Teacher();
    $mode = $mode ?? 'create';
    $semesterOptions = $semesterOptions ?? [];
    $teacherSubjectOptions = $teacherSubjectOptions ?? [];
    $selectedSubjectIds = collect(old('subject_ids', $selectedSubjectIds ?? []))->filter()->map(fn ($id) => (string) $id)->values();
    $currentAccessLevel = old('access_level', $teacherProfile->access_level ?? 'teacher');
    if (!in_array($currentAccessLevel, ['teacher', 'admin'], true)) {
        $currentAccessLevel = 'teacher';
    }
    $currentPhotoUrl = $currentPhotoUrl ?? ($teacherProfile?->profile_photo_path ? asset('storage/' . $teacherProfile->profile_photo_path) : null);
    $currentResume = $currentResume ?? ($teacherProfile?->resume_path ? basename($teacherProfile->resume_path) : null);
    $currentCertificates = collect($currentCertificates ?? ($teacherProfile?->certificate_paths ?? []))->filter()->values();
    $currentIdProof = $currentIdProof ?? ($teacherProfile?->id_proof_path ? basename($teacherProfile->id_proof_path) : null);
@endphp

<div class="teacher-page-grid">
    <div class="space-y-6 min-w-0">
        <div class="teacher-side-card p-5">
            <p class="teacher-label">Profile Photo</p>
            <div id="teacherPhotoDropzone" class="teacher-photo-dropzone mt-4 cursor-pointer p-5 text-center">
                <div class="mx-auto flex w-full flex-col items-center gap-4">
                    <div class="teacher-photo-frame flex items-center justify-center">
                        <img id="teacherPhotoPreview" data-current="{{ $currentPhotoUrl }}" src="{{ $currentPhotoUrl }}" class="{{ $currentPhotoUrl ? '' : 'hidden' }} h-full w-full object-cover" alt="Teacher photo preview">
                        <div id="teacherPhotoPlaceholder" class="{{ $currentPhotoUrl ? 'hidden' : '' }} text-rose-300"><i class="bi bi-person-circle text-7xl"></i></div>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-base font-semibold text-slate-900">{{ $mode === 'edit' ? 'Update profile photo' : 'Choose a profile photo' }}</h3>
                        <p class="text-sm text-slate-500">Click or drag an image here. JPG, PNG, or WEBP up to 4MB.</p>
                    </div>
                </div>
            </div>
            <input id="profile_photo_input" name="profile_photo" type="file" accept="image/*" class="hidden">
            <div class="mt-4 flex gap-3">
                <button type="button" id="choosePhotoButton" class="teacher-btn-soft flex-1"><i class="bi bi-upload"></i>{{ $mode === 'edit' ? 'Change Photo' : 'Choose Photo' }}</button>
                <button type="button" id="removePhotoButton" class="teacher-btn-secondary flex-1"><i class="bi bi-trash3"></i>Remove</button>
            </div>
            @error('profile_photo')<p class="teacher-error-text mt-2">{{ $message }}</p>@enderror
        </div>

        <div class="teacher-side-card p-5">
            <p class="teacher-label">Account Snapshot</p>
            <div class="mt-4 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-sm font-semibold text-slate-900">{{ old('name', $teacher->name ?? '') ?: 'Teacher Name' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ old('email', $teacher->email ?? '') ?: 'Teacher email' }}</p>
                    <p class="mt-2 text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Teacher ID</p>
                    <p class="mt-1 text-sm text-slate-700">{{ old('teacher_id', $teacherProfile->teacher_code ?? '') ?: 'Not assigned' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Profile Visibility</p>
                            <p class="mt-1 text-sm text-slate-500">Set public or private access for the profile card.</p>
                        </div>
                        <span class="teacher-chip {{ (old('profile_visibility', $teacherProfile->profile_visibility ?? 'public') === 'private') ? 'inactive' : 'active' }}">
                            {{ ucfirst(old('profile_visibility', $teacherProfile->profile_visibility ?? 'public')) }}
                        </span>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-sm font-semibold text-slate-900">Assignment Summary</p>
                    <p class="mt-1 text-sm text-slate-500">Choose a semester and subject set to keep teacher assignment aligned with the academic structure.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6 min-w-0">
        <div class="teacher-tab-bar flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2">
            <button type="button" class="teacher-tab-btn active" data-teacher-tab="teacher-basic-section">
                <i class="bi bi-person-badge"></i>
                <span>Basic Info</span>
            </button>
            <button type="button" class="teacher-tab-btn" data-teacher-tab="teacher-assignment-section">
                <i class="bi bi-journal-text"></i>
                <span>Assignment</span>
            </button>
            <button type="button" class="teacher-tab-btn" data-teacher-tab="teacher-contact-section">
                <i class="bi bi-telephone"></i>
                <span>Contact</span>
            </button>
            <button type="button" class="teacher-tab-btn" data-teacher-tab="teacher-documents-section">
                <i class="bi bi-folder2-open"></i>
                <span>Documents</span>
            </button>
        </div>

        <div class="teacher-page-section p-5" id="teacher-basic-section">
            <p class="teacher-label">Basic & Professional Information</p>
            <div class="teacher-form-grid mt-4">
                <div><label class="teacher-label" for="name">Full Name *</label><input class="teacher-input @error('name') error @enderror" id="name" name="name" value="{{ old('name', $teacher->name ?? '') }}" placeholder="Enter full name" required><p class="teacher-error-text">@error('name'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="teacher_id">Teacher ID *</label><input class="teacher-input @error('teacher_id') error @enderror" id="teacher_id" name="teacher_id" value="{{ old('teacher_id', $teacherProfile->teacher_code ?? '') }}" placeholder="Enter teacher ID" required><p class="teacher-error-text">@error('teacher_id'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="email">Email *</label><input class="teacher-input @error('email') error @enderror" id="email" name="email" type="email" value="{{ old('email', $teacher->email ?? '') }}" placeholder="teacher@example.com" required><p class="teacher-error-text">@error('email'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="username">Username</label><input class="teacher-input @error('username') error @enderror" id="username" name="username" value="{{ old('username', $teacher->username ?? '') }}" placeholder="System login username"><p class="teacher-error-text">@error('username'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="phone">Phone *</label><input class="teacher-input @error('phone') error @enderror" id="phone" name="phone" value="{{ old('phone', $teacherProfile->phone ?? $teacher->phone ?? '') }}" placeholder="98XXXXXXXX" maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" required><p class="teacher-error-text">@error('phone'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="secondary_phone">Secondary Phone</label><input class="teacher-input @error('secondary_phone') error @enderror" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $teacherProfile->secondary_phone ?? '') }}" placeholder="Secondary phone" maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"><p class="teacher-error-text">@error('secondary_phone'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="alternate_email">Alternate Email</label><input class="teacher-input @error('alternate_email') error @enderror" id="alternate_email" name="alternate_email" type="email" value="{{ old('alternate_email', $teacherProfile->alternate_email ?? '') }}" placeholder="alternate@example.com"><p class="teacher-error-text">@error('alternate_email'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="gender">Gender</label><select class="teacher-select @error('gender') error @enderror" id="gender" name="gender"><option value="">Prefer not to say</option><option value="male" @selected(old('gender', $teacherProfile->gender ?? '') === 'male')>Male</option><option value="female" @selected(old('gender', $teacherProfile->gender ?? '') === 'female')>Female</option><option value="other" @selected(old('gender', $teacherProfile->gender ?? '') === 'other')>Other</option></select><p class="teacher-error-text">@error('gender'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="status">Status *</label><select class="teacher-select @error('status') error @enderror" id="status" name="status" required><option value="active" @selected(old('status', $teacherProfile->status ?? 'active') === 'active')>Active</option><option value="inactive" @selected(old('status', $teacherProfile->status ?? '') === 'inactive')>Inactive</option><option value="suspended" @selected(old('status', $teacherProfile->status ?? '') === 'suspended')>Suspended</option><option value="On Leave" @selected(old('status', $teacherProfile->status ?? '') === 'On Leave')>On Leave</option><option value="Retired" @selected(old('status', $teacherProfile->status ?? '') === 'Retired')>Retired</option></select><p class="teacher-error-text">@error('status'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="department">Department *</label><input class="teacher-input @error('department') error @enderror" id="department" name="department" value="{{ old('department', $teacherProfile->department ?? $teacher->department ?? '') }}" placeholder="Department or subject area" required><p class="teacher-error-text">@error('department'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="qualification">Qualification</label><input class="teacher-input @error('qualification') error @enderror" id="qualification" name="qualification" value="{{ old('qualification', $teacherProfile->qualification ?? '') }}" placeholder="M.Sc, B.Ed, etc."><p class="teacher-error-text">@error('qualification'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="specialization">Specialization</label><input class="teacher-input @error('specialization') error @enderror" id="specialization" name="specialization" value="{{ old('specialization', $teacherProfile->specialization ?? '') }}" placeholder="Subject expertise"><p class="teacher-error-text">@error('specialization'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="years_of_experience">Years of Experience</label><input class="teacher-input @error('years_of_experience') error @enderror" id="years_of_experience" name="years_of_experience" type="number" min="0" max="80" value="{{ old('years_of_experience', $teacherProfile->years_of_experience ?? '') }}" placeholder="0"><p class="teacher-error-text">@error('years_of_experience'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="date_of_birth">Date of Birth</label><input class="teacher-input @error('date_of_birth') error @enderror" id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', optional($teacherProfile->date_of_birth)->format('Y-m-d')) }}"><p class="teacher-error-text">@error('date_of_birth'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="joining_date">Joining Date *</label><input class="teacher-input @error('joining_date') error @enderror" id="joining_date" name="joining_date" type="date" value="{{ old('joining_date', optional($teacherProfile->joining_date)->format('Y-m-d')) }}" required><p class="teacher-error-text">@error('joining_date'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="national_id_number">National ID / Citizenship</label><input class="teacher-input @error('national_id_number') error @enderror" id="national_id_number" name="national_id_number" value="{{ old('national_id_number', $teacherProfile->national_id_number ?? '') }}" placeholder="National ID number"><p class="teacher-error-text">@error('national_id_number'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="employment_type">Employment Type</label><select class="teacher-select @error('employment_type') error @enderror" id="employment_type" name="employment_type"><option value="">Select type</option><option value="full-time" @selected(old('employment_type', $teacherProfile->employment_type ?? '') === 'full-time')>Full-time</option><option value="part-time" @selected(old('employment_type', $teacherProfile->employment_type ?? '') === 'part-time')>Part-time</option><option value="contract" @selected(old('employment_type', $teacherProfile->employment_type ?? '') === 'contract')>Contract</option></select><p class="teacher-error-text">@error('employment_type'){{ $message }}@enderror</p></div>
                <div class="sm:col-span-2"><label class="teacher-label" for="previous_institution">Previous Institution</label><input class="teacher-input @error('previous_institution') error @enderror" id="previous_institution" name="previous_institution" value="{{ old('previous_institution', $teacherProfile->previous_institution ?? '') }}" placeholder="Previous school or college"><p class="teacher-error-text">@error('previous_institution'){{ $message }}@enderror</p></div>
                <div class="sm:col-span-2"><label class="teacher-label" for="certifications_text">Certifications</label><textarea class="teacher-textarea @error('certifications_text') error @enderror" id="certifications_text" name="certifications_text" placeholder="List certifications separated by commas">{{ old('certifications_text', is_array($teacherProfile->certifications ?? null) ? implode(', ', $teacherProfile->certifications) : ($teacherProfile->certifications ?? '')) }}</textarea><p class="teacher-error-text">@error('certifications_text'){{ $message }}@enderror</p></div>
            </div>
        </div>

        <div class="teacher-page-section p-5" id="teacher-assignment-section">
            <p class="teacher-label">Teaching Assignment</p>
            <div class="teacher-form-grid mt-4">
                <div>
                    <label class="teacher-label" for="assignment_semester">Assign Semester</label>
                    <select class="teacher-select @error('assignment_semester') error @enderror" id="assignment_semester" name="assignment_semester">
                        <option value="">Select semester</option>
                        @foreach($semesterOptions as $semesterOption)
                            <option value="{{ $semesterOption['value'] }}" @selected(old('assignment_semester', $teacherProfile->assigned_semesters?->first() ?? '') == $semesterOption['value'])>{{ $semesterOption['label'] }}</option>
                        @endforeach
                    </select>
                    <p class="teacher-error-text">@error('assignment_semester'){{ $message }}@enderror</p>
                </div>
                <div>
                    <label class="teacher-label" for="teacher_subject_picker">Subjects Assigned</label>
                    <select class="teacher-select @error('subject_ids') error @enderror" id="teacher_subject_picker">
                        <option value="">Select subject</option>
                    </select>
                    <p class="teacher-error-text">@error('subject_ids'){{ $message }}@enderror</p>
                    <div class="mt-3 flex flex-wrap gap-2" id="teacherSelectedSubjects"></div>
                    <div id="teacherSelectedSubjectInputs"></div>
                </div>
                <div><label class="teacher-label" for="staff_room_location">Staff Room / Office</label><input class="teacher-input @error('staff_room_location') error @enderror" id="staff_room_location" name="staff_room_location" value="{{ old('staff_room_location', $teacherProfile->staff_room_location ?? '') }}" placeholder="Office location"><p class="teacher-error-text">@error('staff_room_location'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="employee_type">Employee Type</label><select class="teacher-select @error('employee_type') error @enderror" id="employee_type" name="employee_type"><option value="">Select employee type</option><option value="permanent" @selected(old('employee_type', $teacherProfile->employee_type ?? '') === 'permanent')>Permanent</option><option value="temporary" @selected(old('employee_type', $teacherProfile->employee_type ?? '') === 'temporary')>Temporary</option></select><p class="teacher-error-text">@error('employee_type'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="work_shift">Work Shift</label><select class="teacher-select @error('work_shift') error @enderror" id="work_shift" name="work_shift"><option value="">Select shift</option><option value="morning" @selected(old('work_shift', $teacherProfile->work_shift ?? '') === 'morning')>Morning</option><option value="day" @selected(old('work_shift', $teacherProfile->work_shift ?? '') === 'day')>Day</option><option value="evening" @selected(old('work_shift', $teacherProfile->work_shift ?? '') === 'evening')>Evening</option></select><p class="teacher-error-text">@error('work_shift'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="timetable_assignment">Timetable Assignment</label><input class="teacher-input @error('timetable_assignment') error @enderror" id="timetable_assignment" name="timetable_assignment" value="{{ old('timetable_assignment', $teacherProfile->timetable_assignment ?? '') }}" placeholder="Optional timetable link or code"><p class="teacher-error-text">@error('timetable_assignment'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="access_level">Access Level</label><select class="teacher-select @error('access_level') error @enderror" id="access_level" name="access_level"><option value="">Select level</option><option value="teacher" @selected($currentAccessLevel === 'teacher')>Teacher</option><option value="admin" @selected($currentAccessLevel === 'admin')>Admin</option></select><p class="teacher-error-text">@error('access_level'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="profile_visibility">Profile Visibility</label><select class="teacher-select @error('profile_visibility') error @enderror" id="profile_visibility" name="profile_visibility"><option value="public" @selected(old('profile_visibility', $teacherProfile->profile_visibility ?? 'public') === 'public')>Public</option><option value="private" @selected(old('profile_visibility', $teacherProfile->profile_visibility ?? '') === 'private')>Private</option></select><p class="teacher-error-text">@error('profile_visibility'){{ $message }}@enderror</p></div>
            </div>
        </div>

        <div class="teacher-page-section p-5" id="teacher-contact-section">
            <p class="teacher-label">Contact, Payroll & Health</p>
            <div class="teacher-form-grid mt-4">
                <div><label class="teacher-label" for="emergency_contact_name">Emergency Contact Name</label><input class="teacher-input @error('emergency_contact_name') error @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $teacherProfile->emergency_contact_name ?? '') }}" placeholder="Emergency contact name"><p class="teacher-error-text">@error('emergency_contact_name'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="emergency_contact_phone">Emergency Contact Phone</label><input class="teacher-input @error('emergency_contact_phone') error @enderror" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $teacherProfile->emergency_contact_phone ?? '') }}" placeholder="Emergency phone" maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"><p class="teacher-error-text">@error('emergency_contact_phone'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="emergency_relationship">Emergency Relationship</label><input class="teacher-input @error('emergency_relationship') error @enderror" id="emergency_relationship" name="emergency_relationship" value="{{ old('emergency_relationship', $teacherProfile->emergency_relationship ?? '') }}" placeholder="Father / Mother / Guardian"><p class="teacher-error-text">@error('emergency_relationship'){{ $message }}@enderror</p></div>
                <div class="sm:col-span-2"><label class="teacher-label" for="address">Address</label><textarea class="teacher-textarea @error('address') error @enderror" id="address" name="address" placeholder="Street, city, postal code">{{ old('address', $teacherProfile->address ?? '') }}</textarea><p class="teacher-error-text">@error('address'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="salary">Salary</label><input class="teacher-input @error('salary') error @enderror" id="salary" name="salary" type="number" step="0.01" min="0" value="{{ old('salary', $teacherProfile->salary ?? '') }}" placeholder="0.00"><p class="teacher-error-text">@error('salary'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="bank_name">Bank Name</label><input class="teacher-input @error('bank_name') error @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $teacherProfile->bank_name ?? '') }}" placeholder="Bank name"><p class="teacher-error-text">@error('bank_name'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="bank_account_number">Bank Account Number</label><input class="teacher-input @error('bank_account_number') error @enderror" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $teacherProfile->bank_account_number ?? '') }}" placeholder="Account number"><p class="teacher-error-text">@error('bank_account_number'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="tax_identification_number">Tax ID Number</label><input class="teacher-input @error('tax_identification_number') error @enderror" id="tax_identification_number" name="tax_identification_number" value="{{ old('tax_identification_number', $teacherProfile->tax_identification_number ?? '') }}" placeholder="Tax ID"><p class="teacher-error-text">@error('tax_identification_number'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="blood_group">Blood Group</label><select class="teacher-select @error('blood_group') error @enderror" id="blood_group" name="blood_group"><option value="">Select blood group</option>@foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)<option value="{{ $group }}" @selected(old('blood_group', $teacherProfile->blood_group ?? '') === $group)>{{ $group }}</option>@endforeach</select><p class="teacher-error-text">@error('blood_group'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="medical_conditions">Medical Conditions</label><textarea class="teacher-textarea @error('medical_conditions') error @enderror" id="medical_conditions" name="medical_conditions" placeholder="Optional medical conditions">{{ old('medical_conditions', $teacherProfile->medical_conditions ?? '') }}</textarea><p class="teacher-error-text">@error('medical_conditions'){{ $message }}@enderror</p></div>
                <div class="sm:col-span-2"><label class="teacher-label" for="emergency_notes">Emergency Notes</label><textarea class="teacher-textarea @error('emergency_notes') error @enderror" id="emergency_notes" name="emergency_notes" placeholder="Emergency remarks or instructions">{{ old('emergency_notes', $teacherProfile->emergency_notes ?? '') }}</textarea><p class="teacher-error-text">@error('emergency_notes'){{ $message }}@enderror</p></div>
            </div>
        </div>

        <div class="teacher-page-section p-5" id="teacher-documents-section">
            <p class="teacher-label">Documents</p>
            <div class="teacher-form-grid mt-4">
                <div><label class="teacher-label" for="resume">Resume / CV</label><input class="teacher-file @error('resume') error @enderror" id="resume" name="resume" type="file" accept=".pdf,.doc,.docx"><div class="mt-2 text-xs text-slate-500">{{ $currentResume ? 'Current file: ' . $currentResume : 'No resume uploaded.' }}</div><p class="teacher-error-text">@error('resume'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="certificates">Certificates</label><input class="teacher-file @error('certificates') error @enderror @error('certificates.*') error @enderror" id="certificates" name="certificates[]" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple><div class="mt-2 text-xs text-slate-500">{{ $currentCertificates->count() ? 'Current files: ' . $currentCertificates->map(fn ($path) => basename($path))->implode(', ') : 'No certificate files uploaded.' }}</div><p class="teacher-error-text">@error('certificates'){{ $message }}@enderror @error('certificates.*'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="id_proof">ID Proof</label><input class="teacher-file @error('id_proof') error @enderror" id="id_proof" name="id_proof" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"><div class="mt-2 text-xs text-slate-500">{{ $currentIdProof ? 'Current file: ' . $currentIdProof : 'No ID proof uploaded.' }}</div><p class="teacher-error-text">@error('id_proof'){{ $message }}@enderror</p></div>
                <div><label class="teacher-label" for="social_links">Social Links</label><textarea class="teacher-textarea @error('social_links') error @enderror" id="social_links" name="social_links" placeholder="LinkedIn, portfolio, website">{{ old('social_links', $teacherProfile->social_links ? (is_array($teacherProfile->social_links) ? implode("\n", $teacherProfile->social_links) : $teacherProfile->social_links) : '') }}</textarea><p class="teacher-error-text">@error('social_links'){{ $message }}@enderror</p></div>
                <div class="sm:col-span-2"><label class="teacher-label" for="notes">Notes</label><textarea class="teacher-textarea @error('notes') error @enderror" id="notes" name="notes" placeholder="Internal remarks">{{ old('notes', $teacherProfile->notes ?? $teacherProfile->bio ?? '') }}</textarea><p class="teacher-error-text">@error('notes'){{ $message }}@enderror</p></div>
                <div class="sm:col-span-2"><label class="teacher-label" for="bio">Bio</label><textarea class="teacher-textarea @error('bio') error @enderror" id="bio" name="bio" placeholder="Short professional bio">{{ old('bio', $teacherProfile->bio ?? '') }}</textarea><p class="teacher-error-text">@error('bio'){{ $message }}@enderror</p></div>
            </div>
        </div>
    </div>
</div>
