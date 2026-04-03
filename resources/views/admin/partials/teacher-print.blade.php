@extends('admin.print.layout')

@section('title', 'Teacher Profile Print')

@section('styles')
    <style>
        .teacher-print-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .teacher-print-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .teacher-print-logo {
            width: 58px;
            height: 58px;
            border-radius: 999px;
            overflow: hidden;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #be123c;
        }

        .teacher-print-title {
            font-size: 20pt;
            font-weight: 800;
            color: #111827;
            margin-bottom: 2px;
        }

        .teacher-print-subtitle {
            font-size: 10pt;
            color: #6b7280;
        }

        .teacher-print-section {
            margin-top: 18px;
        }

        .teacher-print-section h2 {
            font-size: 12pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #be123c;
            border-bottom: 2px solid #fecdd3;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .teacher-print-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .teacher-print-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            background: #fff;
        }

        .teacher-print-label {
            display: block;
            font-size: 8.5pt;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }

        .teacher-print-value {
            font-size: 10.5pt;
            color: #111827;
            font-weight: 600;
        }

        .teacher-print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        .teacher-print-table th,
        .teacher-print-table td {
            border: 1px solid #111827;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }

        .teacher-print-table th {
            background: #f8fafc;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .teacher-print-chip {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            font-size: 8.5pt;
            font-weight: 700;
            margin: 0 6px 6px 0;
        }

        .teacher-print-photo {
            width: 90px;
            height: 90px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid #fecdd3;
            background: #fff1f2;
        }

        @media print {
            .print-btn {
                display: none !important;
            }

            .teacher-print-card {
                break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
@php
    $photoUrl = $teacher->teacher->profile_photo_url ?? $teacher->profile_photo_url ?? null;
    $certificatePaths = collect($teacher->teacher->certificate_urls ?? [])->filter()->values();
    $assignedSubjects = collect($teacher->teacher->subjects ?? collect());
@endphp

<div class="print-preview">
    <div class="teacher-print-header">
        <div class="teacher-print-brand">
            <div class="teacher-print-logo">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $teacher->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="bi bi-person-badge"></i>
                @endif
            </div>
            <div>
                <div class="teacher-print-title">{{ $teacher->name }}</div>
                <div class="teacher-print-subtitle">Teacher Profile Summary · Printed {{ now()->format('F d, Y') }}</div>
            </div>
        </div>
        <div class="text-right">
            <div class="teacher-print-subtitle">Teacher ID</div>
            <div class="teacher-print-value">{{ $teacher->teacher->teacher_code ?? '—' }}</div>
        </div>
    </div>

    <div class="teacher-print-section">
        <h2>Overview</h2>
        <div class="teacher-print-grid">
            <div class="teacher-print-card"><span class="teacher-print-label">Email</span><div class="teacher-print-value">{{ $teacher->email }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Phone</span><div class="teacher-print-value">{{ $teacher->teacher->phone ?? $teacher->phone ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Department</span><div class="teacher-print-value">{{ $teacher->teacher->department ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Status</span><div class="teacher-print-value">{{ ucfirst($teacher->teacher->status ?? 'active') }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Qualification</span><div class="teacher-print-value">{{ $teacher->teacher->qualification ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Specialization</span><div class="teacher-print-value">{{ $teacher->teacher->specialization ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Joining Date</span><div class="teacher-print-value">{{ optional($teacher->teacher->joining_date)->format('d M Y') ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Experience</span><div class="teacher-print-value">{{ $teacher->teacher->years_of_experience ?? 0 }} years</div></div>
        </div>
    </div>

    <div class="teacher-print-section">
        <h2>Assigned Subjects</h2>
        @if($assignedSubjects->isNotEmpty())
            @foreach($assignedSubjects as $subject)
                <span class="teacher-print-chip">{{ $subject->subject_code ? $subject->subject_code . ' - ' : '' }}{{ $subject->subject_name }}{{ $subject->pivot->semester ? ' (Sem ' . $subject->pivot->semester . ')' : '' }}</span>
            @endforeach
        @else
            <div class="teacher-print-card">No subjects assigned.</div>
        @endif
    </div>

    <div class="teacher-print-section">
        <h2>Contact & Access</h2>
        <div class="teacher-print-grid">
            <div class="teacher-print-card"><span class="teacher-print-label">Alternate Email</span><div class="teacher-print-value">{{ $teacher->teacher->alternate_email ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Secondary Phone</span><div class="teacher-print-value">{{ $teacher->teacher->secondary_phone ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Emergency Contact</span><div class="teacher-print-value">{{ $teacher->teacher->emergency_contact_name ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Emergency Phone</span><div class="teacher-print-value">{{ $teacher->teacher->emergency_contact_phone ?? '—' }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Access Level</span><div class="teacher-print-value">{{ ucfirst($teacher->teacher->access_level ?? '—') }}</div></div>
            <div class="teacher-print-card"><span class="teacher-print-label">Profile Visibility</span><div class="teacher-print-value">{{ ucfirst($teacher->teacher->profile_visibility ?? 'public') }}</div></div>
        </div>
    </div>

    <div class="teacher-print-section">
        <h2>Documents</h2>
        <table class="teacher-print-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Resume</td>
                    <td>{{ $teacher->teacher->resume_path ? basename($teacher->teacher->resume_path) : '—' }}</td>
                </tr>
                <tr>
                    <td>ID Proof</td>
                    <td>{{ $teacher->teacher->id_proof_path ? basename($teacher->teacher->id_proof_path) : '—' }}</td>
                </tr>
                <tr>
                    <td>Certificates</td>
                    <td>{{ $certificatePaths->isNotEmpty() ? $certificatePaths->map(fn ($path) => basename($path))->implode(', ') : '—' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="teacher-print-section">
        <h2>Notes</h2>
        <div class="teacher-print-card">
            <div class="teacher-print-label">Bio</div>
            <div class="teacher-print-value" style="font-weight:400;">{{ $teacher->teacher->bio ?? '—' }}</div>
            <div class="teacher-print-label" style="margin-top:12px;">Internal Notes</div>
            <div class="teacher-print-value" style="font-weight:400;">{{ $teacher->teacher->notes ?? '—' }}</div>
        </div>
    </div>
</div>
@endsection
