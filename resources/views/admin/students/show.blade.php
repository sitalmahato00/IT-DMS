@extends('admin.layouts.app')

@section('title', 'View Student')

@section('styles')
<script>document.documentElement.classList.add('students-ui-enhanced');</script>
<style>
    .student-view-shell{max-width:96rem;margin:0 auto;padding-inline:clamp(.5rem,1vw,1rem);width:100%;max-width:100%;overflow-x:hidden}
    .student-view-card,.student-view-section{border:1px solid #e2e8f0;border-radius:1.5rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:0 24px 45px -34px rgba(15,23,42,.24)}
    .student-view-hero{position:relative;overflow:hidden;border:1px solid #fecdd3;border-radius:1.5rem;background:linear-gradient(135deg,#fff1f2 0%,#fff 50%,#eff6ff 100%);box-shadow:0 24px 45px -34px rgba(15,23,42,.24)}
    .student-view-hero:after{content:'';position:absolute;right:-3rem;bottom:-4rem;width:13rem;height:13rem;border-radius:999px;background:radial-gradient(circle,rgba(244,63,94,.16),rgba(244,63,94,0) 72%)}
    .student-view-grid{display:grid;grid-template-columns:minmax(0,24rem) minmax(0,1fr);gap:1.5rem;align-items:stretch}
    .student-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
    .student-view-grid > div,
    .student-detail-grid > div,
    .student-view-card,
    .student-view-section{min-width:0}
    .student-view-grid > div:first-child{display:flex;flex-direction:column;gap:1.25rem;align-self:start}
    .student-view-grid > div:first-child > .student-view-card:last-child{flex:0}
    .student-view-card{min-height:10.5rem}
    .student-detail-box{padding:1rem 1.05rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .student-label{display:block;margin-bottom:.38rem;font-size:.76rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
    .student-photo-frame{width:10.5rem;height:10.5rem;border-radius:999px;border:4px solid #fff;background:linear-gradient(135deg,#ffe4e6 0%,#fff1f2 100%);overflow:hidden;box-shadow:0 18px 34px -24px rgba(244,63,94,.45)}
    .student-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem .82rem;border-radius:999px;font-size:.78rem;font-weight:700}
    .student-chip.active{background:#dcfce7;color:#166534}
    .student-chip.inactive{background:#fee2e2;color:#be123c}
    .student-chip.suspended{background:#fef3c7;color:#b45309}
    .student-chip.soft{background:#eef2ff;color:#4338ca}
    .student-chip.pass{background:#dcfce7;color:#166534}
    .student-chip.fail{background:#fee2e2;color:#be123c}
    .student-chip.absent{background:#fef3c7;color:#b45309}
    .student-btn-primary,.student-btn-secondary{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;border-radius:999px;font-weight:700;transition:transform .2s ease}
    .student-btn-primary:hover,.student-btn-secondary:hover{transform:translateY(-1px)}
    .student-btn-primary{padding:.9rem 1.4rem;background:linear-gradient(135deg,#e11d48 0%,#fb7185 100%);color:#fff;box-shadow:0 18px 34px -24px rgba(225,29,72,.7)}
    .student-btn-secondary{padding:.9rem 1.35rem;border:1px solid #cbd5e1;background:#fff;color:#334155}
    .student-file-pill{display:inline-flex;align-items:center;gap:.45rem;padding:.55rem .82rem;border:1px solid #e2e8f0;border-radius:999px;background:#fff;font-size:.82rem;font-weight:600;color:#334155}
    .student-stat-card{padding:1rem 1.05rem;border:1px solid #e2e8f0;border-radius:1rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%)}
    .student-stat-label{font-size:.74rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
    .student-stat-value{margin-top:.35rem;font-size:1.4rem;font-weight:800;color:#0f172a}
    .student-view-tabs{display:flex;gap:.55rem;overflow-x:auto;padding:.4rem;border:1px solid #e2e8f0;border-radius:1.15rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%)}
    .student-view-tab{display:inline-flex;align-items:center;gap:.5rem;border:0;border-radius:1rem;background:transparent;color:#475569;padding:.82rem 1rem;font-size:.92rem;font-weight:700;white-space:nowrap;transition:background-color .2s ease,color .2s ease,box-shadow .2s ease}
    .student-view-tab:hover{background:#fff1f2;color:#be123c}
    .student-view-tab.is-active{background:#fff;border:1px solid #fecdd3;color:#be123c;box-shadow:0 14px 28px -24px rgba(225,29,72,.45)}
    .student-view-panel{display:none}
    .student-view-panel.is-active{display:block}
    .student-empty-state{padding:1rem 1.05rem;border:1px dashed #cbd5e1;border-radius:1rem;background:#fff;font-size:.92rem;color:#64748b}
    .student-list-card{padding:1rem 1.05rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .student-list-card + .student-list-card{margin-top:.85rem}
    .student-table{width:100%;border-collapse:separate;border-spacing:0}
    .student-table th{padding:.85rem 1rem;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b;text-align:left}
    .student-table td{padding:.95rem 1rem;border-bottom:1px solid #eef2f7;vertical-align:top}
    .student-table tbody tr:hover td{background:#fff7f8}
    .student-exam-accordion{overflow:hidden;border:1px solid #e2e8f0;border-radius:1.25rem;background:#fff;box-shadow:0 14px 28px -28px rgba(15,23,42,.22)}
    .student-exam-accordion + .student-exam-accordion{margin-top:.9rem}
    .student-exam-summary{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.05rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);cursor:pointer;list-style:none}
    .student-exam-summary:hover{background:#fff7f8}
    .student-exam-summary::-webkit-details-marker{display:none}
    .student-exam-summary::marker{content:''}
    .student-exam-title{font-size:1rem;font-weight:800;color:#0f172a}
    .student-exam-meta{margin-top:.18rem;font-size:.82rem;color:#64748b}
    .student-exam-body{border-top:1px solid #e2e8f0;background:#fff}
    .student-exam-table-wrap{overflow-x:auto}
    .student-exam-footer td{background:#f8fafc !important;font-size:.8rem;font-weight:800;color:#0f172a}
    .student-exam-footer td:first-child{border-bottom-left-radius:1rem}
    .student-exam-footer td:last-child{border-bottom-right-radius:1rem}
    .student-view-actions{display:flex;flex-direction:column;gap:.75rem;width:100%}
    .student-view-actions > *{width:100%}
    @media (min-width:640px){.student-view-actions{width:auto;flex-direction:row}.student-view-actions > *{width:auto;min-width:9rem}}
    @media (max-width:1024px){.student-view-grid{grid-template-columns:1fr}}
    @media (max-width:640px){
        .student-view-hero{padding:1rem !important}
        .student-view-card,.student-view-section{padding:.9rem !important}
        .student-detail-grid{grid-template-columns:1fr}
        .student-photo-frame{width:6.75rem;height:6.75rem}
        .student-stat-value{font-size:1.2rem}
        .student-view-tab{padding:.72rem .85rem;font-size:.86rem}
        .student-view-actions > *{width:100%}
    }
</style>
@endsection

@section('content')
@php
    $studentProfile = $studentProfile ?? $student->student ?? new \App\Models\Student();
    $photoUrl = $photoUrl ?? ($studentProfile?->profile_photo_url ?: $student->profile_photo_url ?? null);
    $documents = $documents ?? [];
    $idDocument = $documents['id_document'] ?? ($studentProfile?->id_document_url ? [
        'name' => basename($studentProfile->id_document_path),
        'path' => $studentProfile->id_document_path,
        'url' => $studentProfile->id_document_url,
    ] : null);
    $certificates = collect($documents['certificates'] ?? collect($studentProfile?->certificate_urls ?? []))
        ->filter()
        ->map(function ($certificate) {
            if (is_array($certificate)) {
                return [
                    'name' => $certificate['name'] ?? basename($certificate['path'] ?? ''),
                    'path' => $certificate['path'] ?? null,
                    'url' => $certificate['url'] ?? null,
                ];
            }

            return [
                'name' => basename($certificate),
                'path' => $certificate,
                'url' => $certificate,
            ];
        })
        ->values();
    $attendanceSummary = $attendanceSummary ?? [
        'total' => 0,
        'present' => 0,
        'absent' => 0,
        'late' => 0,
        'leave' => 0,
        'percentage' => 100,
    ];
    $markSummary = $markSummary ?? [
        'total' => 0,
        'average' => 0,
        'pass' => 0,
        'fail' => 0,
        'absent' => 0,
        'latest' => null,
    ];
    $attendanceRecords = collect($attendanceRecords ?? []);
    $attendanceBySubject = collect($attendanceBySubject ?? []);
    $markTimeline = collect($markTimeline ?? []);
    $subjectPerformance = collect($subjectPerformance ?? []);
    $parentInfo = $parentInfo ?? [];
    $quickStats = $quickStats ?? [
        'attendance' => $attendanceSummary['percentage'],
        'marks' => $markSummary['average'],
        'subjects' => $subjectPerformance->count(),
        'documents' => ($idDocument ? 1 : 0) + $certificates->count(),
    ];
    $basicInfo = $basicInfo ?? [
        'name' => $student->name,
        'email' => $student->email,
        'username' => $student->username,
        'phone' => $studentProfile?->phone ?? $student->phone,
        'student_id' => $studentProfile?->roll_no,
        'department' => $studentProfile?->department ?? $student->department,
        'program' => $studentProfile?->program,
        'semester' => $studentProfile?->semester,
        'section' => $studentProfile?->section,
        'academic_year' => $studentProfile?->academic_year,
        'academic_year_bs' => $studentProfile?->academic_year_bs,
        'date_of_birth' => optional($studentProfile?->date_of_birth)->format('Y-m-d'),
        'date_of_birth_bs' => $studentProfile?->date_of_birth_bs,
        'gender' => $studentProfile?->gender,
        'blood_group' => $studentProfile?->blood_group,
        'national_id_number' => $studentProfile?->national_id_number,
        'secondary_phone' => $studentProfile?->secondary_phone,
        'emergency_contact' => $studentProfile?->emergency_contact,
        'emergency_contact_name' => $studentProfile?->emergency_contact_name,
        'emergency_relationship' => $studentProfile?->emergency_relationship,
        'address' => $studentProfile?->address,
        'city' => $studentProfile?->city,
        'state_province' => $studentProfile?->state_province,
        'postal_code' => $studentProfile?->postal_code,
        'country' => $studentProfile?->country,
        'medical_conditions' => $studentProfile?->medical_conditions,
        'allergies' => $studentProfile?->allergies,
        'disability_status' => $studentProfile?->disability_status,
        'status' => $studentProfile?->status ?? 'active',
        'is_active' => (bool) ($studentProfile?->is_active ?? true),
        'is_alumni' => (bool) ($studentProfile?->is_alumni ?? false),
        'enrollment_date' => optional($studentProfile?->enrollment_date)->format('Y-m-d'),
        'expected_graduation_year' => $studentProfile?->expected_graduation_year,
    ];
    $statusValue = $basicInfo['status'] ?? 'active';
    $notesValue = $notesValue ?? $studentProfile->notes ?? $studentProfile->bio ?? $student->bio;
@endphp

@include('admin.components.admin-page-header', [
    'title' => 'View Student',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Students', 'url' => route('admin.students')],
        ['label' => 'View Student']
    ]
])

<div class="student-view-shell space-y-6">
    <div class="student-view-hero p-6 sm:p-7">
        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="student-label !mb-2">Student Record</p>
                <h2 class="text-2xl font-bold text-slate-900">{{ $student->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $student->email }}@if($studentProfile?->roll_no) · Student ID {{ $studentProfile->roll_no }}@endif</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="student-chip {{ $statusValue }}"><i class="bi bi-shield-check"></i>{{ ucfirst($statusValue) }}</span>
                    <span class="student-chip soft"><i class="bi bi-person-badge"></i>{{ $studentProfile?->department ?? $student->department ?? 'Department not set' }}</span>
                    <span class="student-chip soft"><i class="bi bi-toggle-on"></i>{{ ($studentProfile?->is_active ?? true) ? 'Account Active' : 'Account Disabled' }}</span>
                </div>
            </div>
            <div class="student-view-actions">
                <a href="{{ route('admin.students') }}" class="student-btn-secondary">Back to Students</a>
                <a href="{{ route('admin.students.print-detail', $student->id) }}" target="_blank" rel="noreferrer" class="student-btn-secondary"><i class="bi bi-printer"></i>Print</a>
                <a href="{{ route('admin.students.edit', $student->id) }}" class="student-btn-primary"><i class="bi bi-pencil-square"></i>Edit Student</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="student-stat-card">
            <div class="student-stat-label">Attendance</div>
            <div class="student-stat-value">{{ $quickStats['attendance'] ?? 0 }}%</div>
            <p class="mt-1 text-sm text-slate-500">{{ $attendanceSummary['present'] ?? 0 }} present of {{ $attendanceSummary['total'] ?? 0 }}</p>
        </div>
        <div class="student-stat-card">
            <div class="student-stat-label">Average Marks</div>
            <div class="student-stat-value">{{ $quickStats['marks'] ?? 0 }}%</div>
            <p class="mt-1 text-sm text-slate-500">{{ $markSummary['pass'] ?? 0 }} passed, {{ $markSummary['fail'] ?? 0 }} need attention</p>
        </div>
        <div class="student-stat-card">
            <div class="student-stat-label">Subjects</div>
            <div class="student-stat-value">{{ $quickStats['subjects'] ?? 0 }}</div>
            <p class="mt-1 text-sm text-slate-500">Current subject load for the enrolled semester</p>
        </div>
        <div class="student-stat-card">
            <div class="student-stat-label">Documents</div>
            <div class="student-stat-value">{{ $quickStats['documents'] ?? 0 }}</div>
            <p class="mt-1 text-sm text-slate-500">Uploaded ID and certificate files</p>
        </div>
    </div>

    <div class="student-view-tabs" role="tablist" aria-label="Student detail sections">
        <button type="button" class="student-view-tab is-active" data-student-view-tab="overview" role="tab" aria-selected="true"><i class="bi bi-grid-1x2"></i>Overview</button>
        <button type="button" class="student-view-tab" data-student-view-tab="attendance" role="tab" aria-selected="false"><i class="bi bi-calendar-check"></i>Attendance</button>
        <button type="button" class="student-view-tab" data-student-view-tab="marks" role="tab" aria-selected="false"><i class="bi bi-graph-up"></i>Marks</button>
        <button type="button" class="student-view-tab" data-student-view-tab="guardian" role="tab" aria-selected="false"><i class="bi bi-people"></i>Guardian</button>
        <button type="button" class="student-view-tab" data-student-view-tab="documents" role="tab" aria-selected="false"><i class="bi bi-folder2-open"></i>Documents</button>
    </div>

    <div class="student-view-grid">
        <div class="space-y-6 min-w-0">
            <div class="student-view-card p-5">
                <p class="student-label">Profile Photo</p>
                <div class="mt-4 flex flex-col items-center gap-4 text-center">
                    <div class="student-photo-frame flex items-center justify-center">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="text-rose-300"><i class="bi bi-person-circle text-7xl"></i></div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $student->name }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $student->username ?: 'No username assigned' }}</p>
                    </div>
                </div>
            </div>

            <div class="student-view-card p-5">
                <p class="student-label">Quick Details</p>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <div class="student-detail-box"><label class="student-label">Program</label><p>{{ $studentProfile?->program ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Semester</label><p>{{ $studentProfile?->semester ?: 'Not assigned' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Section</label><p>{{ $studentProfile?->section ?: 'Not assigned' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Academic Year</label><p>{{ $studentProfile?->academic_year ?: 'Not provided' }}</p></div>
                </div>
            </div>
        </div>

        <div class="space-y-6 min-w-0">
            <div class="student-view-panel is-active space-y-6" data-student-view-panel="overview">
            <div class="student-view-section p-5">
                <p class="student-label">Basic Information</p>
                <div class="student-detail-grid mt-4">
                    <div class="student-detail-box"><label class="student-label">Full Name</label><p>{{ $student->name }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Email</label><p>{{ $student->email }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Phone</label><p>{{ $studentProfile?->phone ?? $student->phone ?? 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Student ID</label><p>{{ $studentProfile?->roll_no ?: 'Not assigned' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Username</label><p>{{ $student->username ?: 'Not provided' }}</p></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Academic Information</p>
                <div class="student-detail-grid mt-4">
                    <div class="student-detail-box"><label class="student-label">Department</label><p>{{ $studentProfile?->department ?? $student->department ?? 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Program / Course</label><p>{{ $studentProfile?->program ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Semester</label><p>{{ $studentProfile?->semester ?: 'Not assigned' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Section / Group</label><p>{{ $studentProfile?->section ?: 'Not assigned' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Academic Year</label><p>{{ $studentProfile?->academic_year ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Academic Year (BS)</label><p>{{ $studentProfile?->academic_year_bs ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Enrollment Date</label><p>{{ optional($studentProfile?->enrollment_date)->format('Y-m-d') ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Expected Graduation Year</label><p>{{ $studentProfile?->expected_graduation_year ?: 'Not provided' }}</p></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Personal Information</p>
                <div class="student-detail-grid mt-4">
                    <div class="student-detail-box"><label class="student-label">Date of Birth</label><p>{{ optional($studentProfile?->date_of_birth)->format('Y-m-d') ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Date of Birth (BS)</label><p>{{ $studentProfile?->date_of_birth_bs ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Gender</label><p>{{ $studentProfile?->gender ? ucfirst($studentProfile->gender) : 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">National ID / Citizenship</label><p>{{ $studentProfile?->national_id_number ?: 'Not provided' }}</p></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Contact & Emergency</p>
                <div class="student-detail-grid mt-4">
                    <div class="student-detail-box"><label class="student-label">Primary Phone</label><p>{{ $studentProfile?->phone ?? $student->phone ?? 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Secondary Phone</label><p>{{ $studentProfile?->secondary_phone ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Emergency Contact</label><p>{{ $studentProfile?->emergency_contact ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Emergency Contact Name</label><p>{{ $studentProfile?->emergency_contact_name ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Emergency Relationship</label><p>{{ $studentProfile?->emergency_relationship ?: 'Not provided' }}</p></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Address</p>
                <div class="student-detail-grid mt-4">
                    <div class="student-detail-box"><label class="student-label">Address</label><p>{{ $studentProfile?->address ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">City</label><p>{{ $studentProfile?->city ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">State / Province</label><p>{{ $studentProfile?->state_province ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Postal Code</label><p>{{ $studentProfile?->postal_code ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Country</label><p>{{ $studentProfile?->country ?: 'Not provided' }}</p></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Health Information</p>
                <div class="student-detail-grid mt-4">
                    <div class="student-detail-box"><label class="student-label">Blood Group</label><p>{{ $studentProfile?->blood_group ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Disability Status</label><p>{{ $studentProfile?->disability_status ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Medical Conditions</label><p>{{ $studentProfile?->medical_conditions ?: 'Not provided' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">Allergies</label><p>{{ $studentProfile?->allergies ?: 'Not provided' }}</p></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Account & Documents</p>
                <div class="student-detail-grid mt-4">
                    <div class="student-detail-box"><label class="student-label">Role</label><p>Student</p></div>
                    <div class="student-detail-box"><label class="student-label">Account Activation</label><p>{{ ($studentProfile?->is_active ?? true) ? 'Enabled' : 'Disabled' }}</p></div>
                    <div class="student-detail-box"><label class="student-label">ID Document</label><div>@if($idDocument)<a href="{{ $idDocument['url'] ?? '#' }}" target="_blank" rel="noreferrer" class="student-file-pill"><i class="bi bi-file-earmark-text"></i>{{ $idDocument['name'] ?? basename($idDocument['path'] ?? '') }}</a>@else Not uploaded @endif</div></div>
                    <div class="student-detail-box"><label class="student-label">Certificates</label><div class="flex flex-wrap gap-2">@forelse($certificates as $certificate)<a href="{{ $certificate['url'] ?? '#' }}" target="_blank" rel="noreferrer" class="student-file-pill"><i class="bi bi-file-earmark-check"></i>{{ $certificate['name'] ?? basename($certificate['path'] ?? '') }}</a>@empty<span>Not uploaded</span>@endforelse</div></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Notes / Details</p>
                <div class="student-detail-box mt-4">
                    <p>{{ $notesValue ?: 'No additional notes provided.' }}</p>
                </div>
            </div>
            </div>

            <div class="student-view-panel space-y-6" data-student-view-panel="attendance">
                <div class="student-view-section p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="student-label">Attendance Summary</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $attendanceSummary['percentage'] ?? 0 }}%</h3>
                            <p class="mt-1 text-sm text-slate-500">Overall class attendance from recorded attendance entries.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="student-chip soft"><i class="bi bi-check2-circle"></i>{{ $attendanceSummary['present'] ?? 0 }} Present</span>
                            <span class="student-chip soft"><i class="bi bi-x-circle"></i>{{ $attendanceSummary['absent'] ?? 0 }} Absent</span>
                            <span class="student-chip soft"><i class="bi bi-clock-history"></i>{{ $attendanceSummary['late'] ?? 0 }} Late</span>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="student-stat-card"><div class="student-stat-label">Total Records</div><div class="student-stat-value">{{ $attendanceSummary['total'] ?? 0 }}</div></div>
                        <div class="student-stat-card"><div class="student-stat-label">Present</div><div class="student-stat-value text-emerald-600">{{ $attendanceSummary['present'] ?? 0 }}</div></div>
                        <div class="student-stat-card"><div class="student-stat-label">Absent</div><div class="student-stat-value text-rose-600">{{ $attendanceSummary['absent'] ?? 0 }}</div></div>
                        <div class="student-stat-card"><div class="student-stat-label">Leave / Excused</div><div class="student-stat-value text-amber-600">{{ $attendanceSummary['leave'] ?? 0 }}</div></div>
                    </div>
                </div>

                <div class="student-view-section p-5">
                    <p class="student-label">Attendance by Subject</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse($attendanceBySubject as $subject)
                            <div class="student-list-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-base font-semibold text-slate-900">{{ $subject['subject_name'] ?? 'Subject' }}</p>
                                        <p class="text-sm text-slate-500">{{ $subject['subject_code'] ?? 'No code' }}</p>
                                    </div>
                                    <span class="student-chip soft">{{ $subject['percentage'] ?? 0 }}%</span>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                    <div><span class="student-label !mb-1">Total</span><p class="font-semibold text-slate-900">{{ $subject['total'] ?? 0 }}</p></div>
                                    <div><span class="student-label !mb-1">Present</span><p class="font-semibold text-slate-900">{{ $subject['present'] ?? 0 }}</p></div>
                                    <div><span class="student-label !mb-1">Absent</span><p class="font-semibold text-slate-900">{{ $subject['absent'] ?? 0 }}</p></div>
                                    <div><span class="student-label !mb-1">Latest</span><p class="font-semibold text-slate-900">{{ $subject['latest_status_label'] ?? 'N/A' }}</p></div>
                                </div>
                            </div>
                        @empty
                            <div class="student-empty-state md:col-span-2 xl:col-span-3">Attendance subject summaries will appear here once attendance records are entered.</div>
                        @endforelse
                    </div>
                </div>

                <div class="student-view-section p-5">
                    <p class="student-label">Recent Attendance</p>
                    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceRecords as $record)
                                    <tr>
                                        <td>
                                            <p class="font-semibold text-slate-900">{{ $record['date_label'] ?? 'Date pending' }}</p>
                                        </td>
                                        <td>
                                            <p class="font-semibold text-slate-900">{{ $record['subject_name'] ?? 'Subject' }}</p>
                                            <p class="text-sm text-slate-500">{{ $record['subject_code'] ?? '' }}</p>
                                        </td>
                                        <td><span class="student-chip soft">{{ ucfirst((string) ($record['status'] ?? '')) }}</span></td>
                                        <td class="text-sm text-slate-600">{{ $record['remarks'] ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4"><div class="student-empty-state">No attendance records found for this student yet.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="student-view-panel space-y-6" data-student-view-panel="marks">
                <div class="student-view-section p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="student-label">Academic Performance</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $markSummary['average'] ?? 0 }}%</h3>
                            <p class="mt-1 text-sm text-slate-500">Average of recorded exam and mark entries.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="student-chip soft"><i class="bi bi-check2-circle"></i>{{ $markSummary['pass'] ?? 0 }} Pass</span>
                            <span class="student-chip soft"><i class="bi bi-exclamation-triangle"></i>{{ $markSummary['fail'] ?? 0 }} Needs Attention</span>
                            <span class="student-chip soft"><i class="bi bi-dash-circle"></i>{{ $markSummary['absent'] ?? 0 }} Absent</span>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="student-stat-card"><div class="student-stat-label">Total Entries</div><div class="student-stat-value">{{ $markSummary['total'] ?? 0 }}</div></div>
                        <div class="student-stat-card"><div class="student-stat-label">Average</div><div class="student-stat-value text-emerald-600">{{ $markSummary['average'] ?? 0 }}%</div></div>
                        <div class="student-stat-card"><div class="student-stat-label">Passed</div><div class="student-stat-value text-emerald-600">{{ $markSummary['pass'] ?? 0 }}</div></div>
                        <div class="student-stat-card"><div class="student-stat-label">Needs Attention</div><div class="student-stat-value text-rose-600">{{ $markSummary['fail'] ?? 0 }}</div></div>
                    </div>
                </div>

                <div class="student-view-section p-5">
                    <p class="student-label">Subject Performance</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse($subjectPerformance as $subject)
                            <div class="student-list-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-base font-semibold text-slate-900">{{ $subject['name'] ?? 'Subject' }}</p>
                                        <p class="text-sm text-slate-500">{{ $subject['code'] ?? 'No code' }}</p>
                                    </div>
                                    <span class="student-chip soft">{{ $subject['latest_percentage'] !== null ? $subject['latest_percentage'] . '%' : 'No marks' }}</span>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                    <div><span class="student-label !mb-1">Attendance</span><p class="font-semibold text-slate-900">{{ $subject['attendance_percentage'] ?? 0 }}%</p></div>
                                    <div><span class="student-label !mb-1">Marks</span><p class="font-semibold text-slate-900">{{ $subject['latest_mark'] ?? 'No marks' }}</p><p class="text-xs text-slate-500">{{ $subject['latest_exam_name'] ?? 'Latest exam score' }}</p></div>
                                    <div class="col-span-2"><span class="student-label !mb-1">Teacher</span><p class="font-semibold text-slate-900">{{ $subject['teacher_name'] ?: 'Not assigned' }}</p></div>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                                    <span class="student-chip soft">{{ $subject['marks_count'] ?? 0 }} exam{{ ($subject['marks_count'] ?? 0) === 1 ? '' : 's' }}</span>
                                    @if($subject['marks_average'] !== null)
                                        <span class="student-chip soft">Avg {{ $subject['marks_average'] }}%</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="student-empty-state md:col-span-2 xl:col-span-3">Subject performance will appear once the student is enrolled in subjects with attendance and marks.</div>
                        @endforelse
                    </div>
                </div>

                <div class="student-view-section p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="student-label">Exam Marks</p>
                            <h3 class="text-xl font-bold text-slate-900">Marks by exam</h3>
                        </div>
                        <p class="text-sm text-slate-500">Each exam expands to show subject-wise marks, totals, grades, and result status.</p>
                    </div>

                    <div class="mt-4">
                        @forelse($examGroups as $examGroup)
                            <details class="student-exam-accordion" @if($loop->first) open @endif>
                                <summary class="student-exam-summary">
                                    <div class="min-w-0">
                                        <p class="student-exam-title truncate">{{ $examGroup['exam_name'] ?? 'Exam' }}</p>
                                        <p class="student-exam-meta truncate">{{ $examGroup['date_label'] ?? 'Date pending' }} · {{ $examGroup['category_label'] ?? 'Exam' }} · {{ $examGroup['total_subjects'] ?? 0 }} subject{{ ($examGroup['total_subjects'] ?? 0) === 1 ? '' : 's' }}</p>
                                    </div>
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <span class="student-chip soft">{{ number_format((float) ($examGroup['percentage'] ?? 0), 1) }}%</span>
                                        <span class="student-chip soft">{{ number_format((float) ($examGroup['total_obtained'] ?? 0), 2) }} / {{ number_format((float) ($examGroup['total_full'] ?? 0), 2) }}</span>
                                        <span class="student-chip {{ $examGroup['result_status'] ?? 'soft' }}">{{ $examGroup['result_label'] ?? 'Pending' }}</span>
                                        @if(!empty($examGroup['exam_id']))
                                            @php
                                                $marksheetQuery = [
                                                    'student_id' => $studentProfile?->id ?? $student->id,
                                                    'academic_year' => $basicInfo['academic_year_bs'] ?? '',
                                                    'semester' => $basicInfo['semester'] ?? '',
                                                    'exam_id' => $examGroup['exam_id'],
                                                    'exam_category' => $examGroup['exam_category'] ?? '',
                                                ];

                                                if (($examGroup['exam_category'] ?? '') === 'assessment' && !empty($examGroup['assessment_number'])) {
                                                    $marksheetQuery['assessment_number'] = $examGroup['assessment_number'];
                                                }
                                            @endphp
                                            <a href="{{ route('admin.marksheet.print', $marksheetQuery) }}" target="_blank" rel="noreferrer" class="student-btn-secondary !px-3 !py-2 !text-xs" onclick="event.stopPropagation();">
                                                <i class="bi bi-journal-text"></i>Marksheet
                                            </a>
                                        @endif
                                    </div>
                                </summary>

                                <div class="student-exam-body">
                                    <div class="student-exam-table-wrap">
                                        <table class="student-table min-w-[760px]">
                                            <thead>
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Max Marks</th>
                                                    <th>Pass Mark</th>
                                                    <th>Marks Obtained</th>
                                                    <th>Grade</th>
                                                    <th>Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($examGroup['marks'] ?? [] as $mark)
                                                    <tr>
                                                        <td>
                                                            <p class="font-semibold text-slate-900">{{ $mark['subject_name'] ?? 'Subject' }}</p>
                                                            <p class="text-sm text-slate-500">{{ $mark['subject_code'] ?? '' }}</p>
                                                        </td>
                                                        <td class="font-semibold text-slate-900">{{ number_format((float) ($mark['full_marks'] ?? 0), 2) }}</td>
                                                        <td class="font-semibold text-slate-900">{{ number_format((float) ($mark['passing_marks'] ?? 0), 2) }}</td>
                                                        <td>
                                                            <p class="font-semibold text-slate-900">{{ number_format((float) ($mark['obtained_marks'] ?? 0), 2) }}</p>
                                                            <p class="text-sm text-slate-500">{{ $mark['percentage'] !== null ? number_format((float) $mark['percentage'], 1) . '%' : 'No percentage' }}</p>
                                                        </td>
                                                        <td class="font-semibold text-slate-900">{{ $mark['grade'] ?? '-' }}</td>
                                                        <td>
                                                            <span class="student-chip {{ $mark['status'] ?? 'soft' }}">{{ $mark['status_label'] ?? 'Pending' }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6"><div class="student-empty-state">No subject marks were found for this exam.</div></td></tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr class="student-exam-footer">
                                                    <td>Subjects: {{ $examGroup['total_subjects'] ?? 0 }}</td>
                                                    <td>Total: {{ number_format((float) ($examGroup['total_full'] ?? 0), 2) }}</td>
                                                    <td>Pass Mark: {{ number_format((float) collect($examGroup['marks'] ?? [])->sum(fn ($mark) => (float) ($mark['passing_marks'] ?? 0)), 2) }}</td>
                                                    <td>Total Obtained: {{ number_format((float) ($examGroup['total_obtained'] ?? 0), 2) }}</td>
                                                    <td>Grade: {{ $examGroup['grade'] ?? '-' }}</td>
                                                    <td>
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span>Result: {{ $examGroup['result_label'] ?? 'Pending' }}</span>
                                                            @if(!empty($examGroup['exam_id']))
                                                                <a href="{{ route('admin.marksheet.print', $marksheetQuery) }}" target="_blank" rel="noreferrer" class="student-btn-secondary !px-3 !py-2 !text-xs" onclick="event.stopPropagation();">
                                                                    <i class="bi bi-arrow-right-circle"></i>Open
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </details>
                        @empty
                            <div class="student-empty-state">No marks have been recorded for this student yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="student-view-panel space-y-6" data-student-view-panel="guardian">
                <div class="student-view-section p-5">
                    <p class="student-label">Parent / Guardian Detail</p>
                    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,18rem)_1fr]">
                        <div class="student-list-card">
                            <div class="flex items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-rose-500">
                                    <i class="bi bi-person-heart text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-slate-900">{{ $parentInfo['name'] ?? 'Parent not linked' }}</p>
                                    <p class="text-sm text-slate-500">{{ $parentInfo['parent_code'] ?? 'Parent account' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="student-chip soft">{{ ucfirst((string) ($parentInfo['status'] ?? 'active')) }}</span>
                                @if(!empty($parentInfo['children_count']))
                                    <span class="student-chip soft">{{ $parentInfo['children_count'] }} Child(ren)</span>
                                @endif
                            </div>
                        </div>

                        <div class="student-detail-grid">
                            <div class="student-detail-box"><label class="student-label">Email</label><p>{{ $parentInfo['email'] ?? 'Not provided' }}</p></div>
                            <div class="student-detail-box"><label class="student-label">Phone</label><p>{{ $parentInfo['phone'] ?? 'Not provided' }}</p></div>
                            <div class="student-detail-box"><label class="student-label">Occupation</label><p>{{ $parentInfo['occupation'] ?? 'Not provided' }}</p></div>
                            <div class="student-detail-box"><label class="student-label">Gender</label><p>{{ $parentInfo['gender'] ? ucfirst($parentInfo['gender']) : 'Not provided' }}</p></div>
                            <div class="student-detail-box sm:col-span-2"><label class="student-label">Address</label><p>{{ $parentInfo['address'] ?? 'Not provided' }}</p></div>
                            <div class="student-detail-box sm:col-span-2"><label class="student-label">Bio / Notes</label><p>{{ $parentInfo['bio'] ?? 'No parent notes provided.' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="student-view-panel space-y-6" data-student-view-panel="documents">
                <div class="student-view-section p-5">
                    <p class="student-label">Account & Documents</p>
                    <div class="student-detail-grid mt-4">
                        <div class="student-detail-box"><label class="student-label">Role</label><p>Student</p></div>
                        <div class="student-detail-box"><label class="student-label">Account Activation</label><p>{{ $basicInfo['is_active'] ? 'Enabled' : 'Disabled' }}</p></div>
                        <div class="student-detail-box"><label class="student-label">Student Status</label><p>{{ ucfirst((string) ($basicInfo['status'] ?? 'active')) }}</p></div>
                        <div class="student-detail-box"><label class="student-label">Alumni</label><p>{{ !empty($basicInfo['is_alumni']) ? 'Yes' : 'No' }}</p></div>
                    </div>
                </div>

                <div class="student-view-section p-5">
                    <p class="student-label">Uploaded Files</p>
                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        <div class="student-list-card">
                            <label class="student-label">ID Document</label>
                            @if($idDocument)
                                <a href="{{ $idDocument['url'] ?? '#' }}" target="_blank" rel="noreferrer" class="student-file-pill mt-2"><i class="bi bi-file-earmark-text"></i>{{ $idDocument['name'] ?? basename($idDocument['path'] ?? '') }}</a>
                            @else
                                <div class="student-empty-state mt-2">No ID document uploaded.</div>
                            @endif
                        </div>
                        <div class="student-list-card">
                            <label class="student-label">Certificates</label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @forelse($certificates as $certificate)
                                    <a href="{{ $certificate['url'] ?? '#' }}" target="_blank" rel="noreferrer" class="student-file-pill"><i class="bi bi-file-earmark-check"></i>{{ $certificate['name'] ?? basename($certificate['path'] ?? '') }}</a>
                                @empty
                                    <div class="student-empty-state">No certificate files uploaded.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const buttons = Array.from(document.querySelectorAll('[data-student-view-tab]'));
        const panels = Array.from(document.querySelectorAll('[data-student-view-panel]'));
        const examAccordions = Array.from(document.querySelectorAll('.student-exam-accordion'));

        function openTab(tabName) {
            buttons.forEach(button => {
                const active = button.dataset.studentViewTab === tabName;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach(panel => {
                panel.classList.toggle('is-active', panel.dataset.studentViewPanel === tabName);
            });
        }

        buttons.forEach(button => button.addEventListener('click', () => openTab(button.dataset.studentViewTab)));
        examAccordions.forEach(accordion => {
            accordion.addEventListener('toggle', () => {
                if (!accordion.open) {
                    return;
                }

                examAccordions.forEach(other => {
                    if (other !== accordion) {
                        other.open = false;
                    }
                });
            });
        });
        openTab('overview');
    })();
</script>
@endsection

