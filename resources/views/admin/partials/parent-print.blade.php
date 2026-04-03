@extends('admin.print.layout')

@section('title', 'Parent Profile Print')

@section('styles')
<style>
    .parent-print-section{margin-top:18px}
    .parent-print-section h2{font-size:12pt;text-transform:uppercase;letter-spacing:.08em;color:#be123c;border-bottom:2px solid #fecdd3;padding-bottom:6px;margin-bottom:12px}
    .parent-print-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .parent-print-card{border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;break-inside:avoid}
    .parent-print-label{display:block;font-size:8.5pt;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px}
    .parent-print-value{font-size:10.5pt;color:#111827;font-weight:600}
    .parent-print-chip{display:inline-block;padding:3px 8px;border-radius:999px;background:#f8fafc;color:#334155;font-size:8.5pt;font-weight:700;margin:0 6px 6px 0}
    .parent-print-table{width:100%;border-collapse:collapse;font-size:9.5pt}
    .parent-print-table th,.parent-print-table td{border:1px solid #111827;padding:6px 8px;vertical-align:top;text-align:left}
    .parent-print-table th{background:#f8fafc;font-size:8.5pt;text-transform:uppercase;letter-spacing:.08em}
    .parent-print-photo{width:90px;height:90px;border-radius:999px;overflow:hidden;border:1px solid #fecdd3;background:#fff1f2}
    @media print{.print-btn{display:none !important}.parent-print-card{break-inside:avoid}}
</style>
@endsection

@section('content')
@php
    $photoUrl = $parent->parent->profile_photo_url ?? $parent->profile_photo_url ?? null;
    $children = collect($children ?? []);
    $primaryChild = $children->firstWhere('user_id', $parent->parent->primary_child_user_id ?? null);
    $notificationPreferences = collect(array_filter(array_map('trim', explode(',', (string) ($parent->parent->notification_preferences ?? '')))));
@endphp

<div class="print-preview">
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="parent-print-photo">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $parent->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#be123c;font-size:30px;font-weight:800;">{{ strtoupper(substr($parent->name ?? 'P', 0, 2)) }}</div>
                @endif
            </div>
            <div>
                <div style="font-size:20pt;font-weight:800;color:#111827;">{{ $parent->name }}</div>
                <div style="font-size:10pt;color:#6b7280;">Parent Profile Summary · Printed {{ now()->format('F d, Y') }}</div>
            </div>
        </div>
        <div class="text-right">
            <div style="font-size:10pt;color:#6b7280;">Parent ID</div>
            <div style="font-size:11pt;font-weight:700;">{{ $parent->parent->parent_code ?? '—' }}</div>
        </div>
    </div>

    <div class="parent-print-section">
        <h2>Overview</h2>
        <div class="parent-print-grid">
            <div class="parent-print-card"><span class="parent-print-label">Email</span><div class="parent-print-value">{{ $parent->email }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Phone</span><div class="parent-print-value">{{ $parent->parent->phone ?? $parent->phone ?? '—' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Relationship</span><div class="parent-print-value">{{ $parent->parent->relationship ?? '—' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Status</span><div class="parent-print-value">{{ ucfirst($parent->parent->status ?? 'active') }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Access Level</span><div class="parent-print-value">{{ ucfirst($parent->parent->access_level ?? 'view_only') }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Portal Access</span><div class="parent-print-value">{{ !empty($parent->parent->portal_access) ? 'Enabled' : 'Disabled' }}</div></div>
        </div>
    </div>

    <div class="parent-print-section">
        <h2>Children</h2>
        @if($children->isNotEmpty())
            <table class="parent-print-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Roll / Semester</th>
                        <th>Program</th>
                        <th>Primary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($children as $child)
                        <tr>
                            <td>
                                <strong>{{ $child->user->name ?? 'Student' }}</strong><br>
                                {{ $child->user->email ?? '—' }}
                            </td>
                            <td>{{ ($child->roll_no ?? '—') . ' / ' . ($child->semester ?? '—') }}</td>
                            <td>{{ $child->program ?? '—' }}</td>
                            <td>{{ $primaryChild && $primaryChild->user_id === $child->user_id ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="parent-print-card">No children assigned.</div>
        @endif
    </div>

    <div class="parent-print-section">
        <h2>Contact & Address</h2>
        <div class="parent-print-grid">
            <div class="parent-print-card"><span class="parent-print-label">Secondary Phone</span><div class="parent-print-value">{{ $parent->parent->secondary_phone ?? '—' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Alternate Email</span><div class="parent-print-value">{{ $parent->parent->alternate_email ?? '—' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">WhatsApp</span><div class="parent-print-value">{{ $parent->parent->whatsapp_number ?? '—' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Preferred Contact</span><div class="parent-print-value">{{ ucfirst($parent->parent->preferred_contact_method ?? '—') }}</div></div>
            <div class="parent-print-card" style="grid-column:1 / -1;"><span class="parent-print-label">Address</span><div class="parent-print-value">{{ $parent->parent->address ?? '—' }}</div></div>
        </div>
    </div>

    <div class="parent-print-section">
        <h2>Access & Health</h2>
        <div class="parent-print-grid">
            <div class="parent-print-card"><span class="parent-print-label">Profile Visibility</span><div class="parent-print-value">{{ ucfirst($parent->parent->profile_visibility ?? 'public') }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Preferred Language</span><div class="parent-print-value">{{ $parent->parent->preferred_language ?? '—' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Blood Group</span><div class="parent-print-value">{{ $parent->parent->blood_group ?? '—' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Emergency Priority</span><div class="parent-print-value">{{ !empty($parent->parent->emergency_contact_priority) ? 'Yes' : 'No' }}</div></div>
        </div>
    </div>

    <div class="parent-print-section">
        <h2>Documents</h2>
        <div class="parent-print-grid">
            <div class="parent-print-card"><span class="parent-print-label">ID Proof</span><div class="parent-print-value">{{ $parent->parent->id_proof_path ? basename($parent->parent->id_proof_path) : 'Not uploaded' }}</div></div>
            <div class="parent-print-card"><span class="parent-print-label">Address Proof</span><div class="parent-print-value">{{ $parent->parent->address_proof_path ? basename($parent->parent->address_proof_path) : 'Not uploaded' }}</div></div>
        </div>
    </div>

    <div class="parent-print-section">
        <h2>Notes</h2>
        <div class="parent-print-card">
            <div class="parent-print-label">Notifications</div>
            <div class="parent-print-value">{{ $notificationPreferences->isNotEmpty() ? $notificationPreferences->join(', ') : '—' }}</div>
            <div class="parent-print-label" style="margin-top:12px;">Bio</div>
            <div class="parent-print-value">{{ $parent->parent->bio ?? '—' }}</div>
            <div class="parent-print-label" style="margin-top:12px;">Remarks</div>
            <div class="parent-print-value">{{ $parent->parent->notes ?? '—' }}</div>
        </div>
    </div>
</div>
@endsection
