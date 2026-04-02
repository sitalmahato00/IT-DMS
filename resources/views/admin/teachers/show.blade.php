@extends('admin.layouts.app')

@section('title', 'Teacher Details')

@php
    $teacher = $teacher ?? new \App\Models\User();
    $teacherProfile = $teacherProfile ?? $teacher->teacher ?? new \App\Models\Teacher();
    $assignedSubjects = collect($assignedSubjects ?? ($teacherProfile->subjects ?? collect()));
    $photoPath = $teacherProfile->profile_photo_path ?? $teacher->profile_photo_path ?? null;
    $photoUrl = $photoPath ? (\Illuminate\Support\Str::startsWith($photoPath, 'storage/') ? asset($photoPath) : asset('storage/' . $photoPath)) : null;
    $resumeUrl = $teacherProfile->resume_path ? asset('storage/' . $teacherProfile->resume_path) : null;
    $idProofUrl = $teacherProfile->id_proof_path ? asset('storage/' . $teacherProfile->id_proof_path) : null;
    $certificatePaths = collect($teacherProfile->certificate_paths ?? [])->filter()->values();
    $subjectSemesters = $assignedSubjects->pluck('pivot.semester')->filter()->unique()->values();
    $socialLinks = is_array($teacherProfile->social_links ?? null)
        ? $teacherProfile->social_links
        : array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) ($teacherProfile->social_links ?? '')))));
@endphp

@section('styles')
<style>
    .teacher-show-shell,
    .teacher-show-card {
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
    }

    .teacher-show-nav {
        overflow-x: auto;
        scrollbar-width: thin;
    }

    .teacher-show-nav a {
        white-space: nowrap;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.55rem 0.95rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
        background: #ffffff;
        transition: all 0.2s ease;
    }

    .teacher-show-nav a:hover {
        border-color: #fb7185;
        color: #be123c;
        transform: translateY(-1px);
    }

    .teacher-detail-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .teacher-detail-grid > div,
    .teacher-detail-block {
        padding: 0.95rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem;
        background: #ffffff;
    }

    .teacher-detail-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }

    .teacher-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.35rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .teacher-chip-active { background: #dcfce7; color: #166534; }
    .teacher-chip-inactive { background: #fee2e2; color: #b91c1c; }
    .teacher-chip-muted { background: #f8fafc; color: #475569; }

    .teacher-stat {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #ffffff;
        padding: 1rem;
    }

    .teacher-doc-item {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #ffffff;
        padding: 0.9rem 1rem;
    }

    .teacher-photo-frame {
        width: 13rem;
        height: 13rem;
        border-radius: 9999px;
        overflow: hidden;
        border: 1px solid #fecdd3;
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
    }

    @media (max-width: 1024px) {
        .teacher-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .teacher-photo-frame {
            width: 10.5rem;
            height: 10.5rem;
        }
    }
</style>
@endsection

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Teacher Details',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Teachers', 'url' => route('admin.teachers')],
        ['label' => $teacher->name ?? 'Teacher']
    ]
])

<div class="space-y-6">
    <div class="teacher-show-shell overflow-hidden p-6">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center">
                <div class="teacher-photo-frame flex items-center justify-center">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $teacher->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-5xl text-rose-300">
                            <i class="bi bi-person-circle"></i>
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-500">Teacher Profile</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $teacher->name }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $teacher->email }}</p>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="teacher-chip {{ strtolower($teacherProfile->status ?? 'active') === 'active' ? 'teacher-chip-active' : 'teacher-chip-inactive' }}">
                            {{ ucfirst($teacherProfile->status ?? 'active') }}
                        </span>
                        <span class="teacher-chip teacher-chip-muted">Teacher ID: {{ $teacherProfile->teacher_code ?? '—' }}</span>
                        <span class="teacher-chip teacher-chip-muted">{{ $teacherProfile->department ?? 'General' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="adminOpenPrintPreview('{{ route('admin.teachers.print', $teacher->id) }}', { title: 'Teacher Print Preview', subtitle: 'Use the preview controls to print or open in a new tab' })" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-50">
                    <i class="bi bi-printer"></i>
                    Print Preview
                </button>
                <a href="{{ route('admin.teachers.download', $teacher->id) }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                    <i class="bi bi-download"></i>
                    Download PDF
                </a>
                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                    <i class="bi bi-pencil"></i>
                    Edit
                </a>
            </div>
        </div>

        <div class="teacher-show-nav mt-6 flex gap-2 pb-1">
            <a href="#teacher-overview">Overview</a>
            <a href="#teacher-subjects">Subjects</a>
            <a href="#teacher-contact">Contact</a>
            <a href="#teacher-documents">Documents</a>
            <a href="#teacher-notes">Notes</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="teacher-stat">
            <p class="text-sm font-medium text-slate-500">Assigned Subjects</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $assignedSubjects->count() }}</p>
        </div>
        <div class="teacher-stat">
            <p class="text-sm font-medium text-slate-500">Semesters Covered</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $subjectSemesters->count() }}</p>
        </div>
        <div class="teacher-stat">
            <p class="text-sm font-medium text-slate-500">Experience</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $teacherProfile->years_of_experience ?? 0 }}</p>
            <p class="text-xs text-slate-500">Years</p>
        </div>
        <div class="teacher-stat">
            <p class="text-sm font-medium text-slate-500">Documents</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ collect([$teacherProfile->resume_path, $teacherProfile->id_proof_path])->filter()->count() + $certificatePaths->count() }}</p>
        </div>
    </div>

    <section id="teacher-overview" class="teacher-show-card p-6">
        <div class="mb-5">
            <h3 class="text-lg font-bold text-slate-900">Overview</h3>
            <p class="text-sm text-slate-500">Identity, academic, and work details.</p>
        </div>

        <div class="teacher-detail-grid">
            <div>
                <span class="teacher-detail-label">Full Name</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacher->name }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Email</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacher->email }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Username</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacher->username ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Phone</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->phone ?? $teacher->phone ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Qualification</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->qualification ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Specialization</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->specialization ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Joining Date</span>
                <p class="mt-1 font-semibold text-slate-900">{{ optional($teacherProfile->joining_date)->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Date of Birth</span>
                <p class="mt-1 font-semibold text-slate-900">{{ optional($teacherProfile->date_of_birth)->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Gender</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->gender ? ucfirst($teacherProfile->gender) : '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">National ID</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->national_id_number ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Employment Type</span>
                <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->employment_type ? ucfirst($teacherProfile->employment_type) : '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Status</span>
                <p class="mt-1 font-semibold text-slate-900">{{ ucfirst($teacherProfile->status ?? 'active') }}</p>
            </div>
        </div>
    </section>

    <section id="teacher-subjects" class="teacher-show-card p-6">
        <div class="mb-5">
            <h3 class="text-lg font-bold text-slate-900">Assigned Subjects</h3>
            <p class="text-sm text-slate-500">Semester and subject mapping for this teacher.</p>
        </div>

        @if($assignedSubjects->isNotEmpty())
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Subject</th>
                            <th class="px-4 py-3 text-left">Semester</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Assigned At</th>
                            <th class="px-4 py-3 text-left">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($assignedSubjects as $subject)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $subject->subject_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $subject->subject_code ?? 'No code' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $subject->pivot->semester ? 'Semester ' . $subject->pivot->semester : 'All Semesters' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ ucfirst($subject->pivot->role ?? 'primary') }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ optional($subject->pivot->assigned_at)->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $subject->pivot->notes ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-sm text-slate-500">
                No subjects have been assigned yet.
            </div>
        @endif
    </section>

    <div id="teacher-contact" class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="teacher-show-card p-6">
            <h3 class="text-lg font-bold text-slate-900">Contact & Emergency</h3>
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <span class="teacher-detail-label">Secondary Phone</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->secondary_phone ?? '—' }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Alternate Email</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->alternate_email ?? '—' }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Emergency Contact</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->emergency_contact_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Emergency Phone</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->emergency_contact_phone ?? '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <span class="teacher-detail-label">Emergency Relationship</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->emergency_relationship ?? '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <span class="teacher-detail-label">Address</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->address ?? '—' }}</p>
                </div>
            </div>
        </section>

        <section class="teacher-show-card p-6">
            <h3 class="text-lg font-bold text-slate-900">Payroll & Access</h3>
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <span class="teacher-detail-label">Salary</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->salary !== null ? number_format((float) $teacherProfile->salary, 2) : '—' }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Access Level</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->access_level ? ucfirst($teacherProfile->access_level) : '—' }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Profile Visibility</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ ucfirst($teacherProfile->profile_visibility ?? 'public') }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Work Shift</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->work_shift ? ucfirst($teacherProfile->work_shift) : '—' }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Employee Type</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->employee_type ? ucfirst($teacherProfile->employee_type) : '—' }}</p>
                </div>
                <div>
                    <span class="teacher-detail-label">Staff Room</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->staff_room_location ?? '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <span class="teacher-detail-label">Timetable Assignment</span>
                    <p class="mt-1 font-semibold text-slate-900">{{ $teacherProfile->timetable_assignment ?? '—' }}</p>
                </div>
            </div>
        </section>
    </div>

    <section id="teacher-documents" class="teacher-show-card p-6">
        <h3 class="text-lg font-bold text-slate-900">Documents</h3>
        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="teacher-doc-item">
                <span class="teacher-detail-label">Resume / CV</span>
                @if($resumeUrl)
                    <a href="{{ $resumeUrl }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-blue-600 hover:underline">{{ basename($teacherProfile->resume_path) }}</a>
                @else
                    <p class="mt-2 text-sm text-slate-500">No resume uploaded.</p>
                @endif
            </div>
            <div class="teacher-doc-item">
                <span class="teacher-detail-label">ID Proof</span>
                @if($idProofUrl)
                    <a href="{{ $idProofUrl }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-blue-600 hover:underline">{{ basename($teacherProfile->id_proof_path) }}</a>
                @else
                    <p class="mt-2 text-sm text-slate-500">No ID proof uploaded.</p>
                @endif
            </div>
            <div class="teacher-doc-item">
                <span class="teacher-detail-label">Certificates</span>
                @if($certificatePaths->isNotEmpty())
                    <div class="mt-2 space-y-2">
                        @foreach($certificatePaths as $path)
                            @php
                                $fileUrl = \Illuminate\Support\Str::startsWith($path, 'storage/') ? asset($path) : asset('storage/' . $path);
                            @endphp
                            <a href="{{ $fileUrl }}" target="_blank" class="block text-sm font-semibold text-blue-600 hover:underline">{{ basename($path) }}</a>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-slate-500">No certificates uploaded.</p>
                @endif
            </div>
        </div>
    </section>

    <section id="teacher-notes" class="teacher-show-card p-6">
        <h3 class="text-lg font-bold text-slate-900">Notes & Links</h3>
        <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <div>
                <span class="teacher-detail-label">Bio</span>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $teacherProfile->bio ?? '—' }}</p>
            </div>
            <div>
                <span class="teacher-detail-label">Internal Notes</span>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $teacherProfile->notes ?? '—' }}</p>
            </div>
            <div class="xl:col-span-2">
                <span class="teacher-detail-label">Social Links</span>
                @if(!empty($socialLinks))
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($socialLinks as $link)
                            <a href="{{ \Illuminate\Support\Str::startsWith($link, ['http://', 'https://']) ? $link : 'https://' . ltrim($link, '/') }}" target="_blank" class="teacher-chip teacher-chip-muted">
                                {{ $link }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-slate-500">No social links added.</p>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
