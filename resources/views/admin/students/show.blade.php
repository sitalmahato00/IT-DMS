@extends('admin.layouts.app')

@section('title', 'View Student')

@section('styles')
<script>document.documentElement.classList.add('students-ui-enhanced');</script>
<style>
    .student-view-shell{max-width:96rem;margin:0 auto}
    .student-view-card,.student-view-section{border:1px solid #e2e8f0;border-radius:1.5rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:0 24px 45px -34px rgba(15,23,42,.24)}
    .student-view-hero{position:relative;overflow:hidden;border:1px solid #fecdd3;border-radius:1.5rem;background:linear-gradient(135deg,#fff1f2 0%,#fff 50%,#eff6ff 100%);box-shadow:0 24px 45px -34px rgba(15,23,42,.24)}
    .student-view-hero:after{content:'';position:absolute;right:-3rem;bottom:-4rem;width:13rem;height:13rem;border-radius:999px;background:radial-gradient(circle,rgba(244,63,94,.16),rgba(244,63,94,0) 72%)}
    .student-view-grid{display:grid;grid-template-columns:minmax(0,22rem) minmax(0,1fr);gap:1.5rem;align-items:start}
    .student-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
    .student-detail-box{padding:1rem 1.05rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .student-label{display:block;margin-bottom:.38rem;font-size:.76rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
    .student-photo-frame{width:10.5rem;height:10.5rem;border-radius:999px;border:4px solid #fff;background:linear-gradient(135deg,#ffe4e6 0%,#fff1f2 100%);overflow:hidden;box-shadow:0 18px 34px -24px rgba(244,63,94,.45)}
    .student-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem .82rem;border-radius:999px;font-size:.78rem;font-weight:700}
    .student-chip.active{background:#dcfce7;color:#166534}
    .student-chip.inactive{background:#fee2e2;color:#be123c}
    .student-chip.suspended{background:#fef3c7;color:#b45309}
    .student-chip.soft{background:#eef2ff;color:#4338ca}
    .student-btn-primary,.student-btn-secondary{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;border-radius:999px;font-weight:700;transition:transform .2s ease}
    .student-btn-primary:hover,.student-btn-secondary:hover{transform:translateY(-1px)}
    .student-btn-primary{padding:.9rem 1.4rem;background:linear-gradient(135deg,#e11d48 0%,#fb7185 100%);color:#fff;box-shadow:0 18px 34px -24px rgba(225,29,72,.7)}
    .student-btn-secondary{padding:.9rem 1.35rem;border:1px solid #cbd5e1;background:#fff;color:#334155}
    .student-file-pill{display:inline-flex;align-items:center;gap:.45rem;padding:.55rem .82rem;border:1px solid #e2e8f0;border-radius:999px;background:#fff;font-size:.82rem;font-weight:600;color:#334155}
    @media (max-width:1024px){.student-view-grid{grid-template-columns:1fr}}
    @media (max-width:640px){.student-detail-grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
@php
    $studentProfile = $student->student ?? new \App\Models\Student();
    $photoUrl = $studentProfile?->profile_photo_path ? asset('storage/' . $studentProfile->profile_photo_path) : null;
    $idDocument = $studentProfile?->id_document_path;
    $certificates = collect($studentProfile?->certificate_paths ?? [])->filter()->values();
    $statusValue = $studentProfile->status ?? 'active';
    $notesValue = $studentProfile->notes ?? $studentProfile->bio ?? $student->bio;
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
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('admin.students') }}" class="student-btn-secondary">Back to Students</a>
                <a href="{{ route('admin.students.print-detail', $student->id) }}" target="_blank" rel="noreferrer" class="student-btn-secondary"><i class="bi bi-printer"></i>Print</a>
                <a href="{{ route('admin.students.edit', $student->id) }}" class="student-btn-primary"><i class="bi bi-pencil-square"></i>Edit Student</a>
            </div>
        </div>
    </div>

    <div class="student-view-grid">
        <div class="space-y-6">
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

        <div class="space-y-6">
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
                    <div class="student-detail-box"><label class="student-label">ID Document</label><div>@if($idDocument)<a href="{{ asset('storage/' . $idDocument) }}" target="_blank" rel="noreferrer" class="student-file-pill"><i class="bi bi-file-earmark-text"></i>{{ basename($idDocument) }}</a>@else Not uploaded @endif</div></div>
                    <div class="student-detail-box"><label class="student-label">Certificates</label><div class="flex flex-wrap gap-2">@forelse($certificates as $path)<a href="{{ asset('storage/' . $path) }}" target="_blank" rel="noreferrer" class="student-file-pill"><i class="bi bi-file-earmark-check"></i>{{ basename($path) }}</a>@empty<span>Not uploaded</span>@endforelse</div></div>
                </div>
            </div>

            <div class="student-view-section p-5">
                <p class="student-label">Notes / Details</p>
                <div class="student-detail-box mt-4">
                    <p>{{ $notesValue ?: 'No additional notes provided.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
