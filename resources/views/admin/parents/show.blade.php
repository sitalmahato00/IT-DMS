@extends('admin.layouts.app')

@section('title', 'View Parent')

@php
    $parentProfile = $parentProfile ?? $parent->parent ?? new \App\Models\ParentModel();
    $assignedChildren = collect($assignedChildren ?? []);
    $primaryChildUserId = $primaryChildUserId ?? $parentProfile->primary_child_user_id ?? null;
    $notificationPreferences = collect(array_filter(array_map('trim', explode(',', (string) ($parentProfile->notification_preferences ?? '')))));
    $docs = [
        'id' => $parentProfile->id_proof_url ?? null,
        'address' => $parentProfile->address_proof_url ?? null,
    ];
@endphp

@section('styles')
<style>
    .parent-view-shell{max-width:96rem;margin:0 auto;width:100%;overflow-x:hidden}
    .parent-view-hero,.parent-view-card,.parent-view-section{border:1px solid #e2e8f0;border-radius:1.25rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:0 22px 42px -34px rgba(15,23,42,.22)}
    .parent-view-grid{display:grid;grid-template-columns:minmax(0,22rem) minmax(0,1fr);gap:1.25rem;align-items:start}
    .parent-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
    .parent-view-tabs{display:flex;gap:.5rem;overflow-x:auto;padding:.4rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .parent-view-tab{display:inline-flex;align-items:center;gap:.45rem;border:0;border-radius:.9rem;background:transparent;color:#475569;padding:.75rem 1rem;font-size:.9rem;font-weight:700;white-space:nowrap}
    .parent-view-tab.is-active{background:#fff1f2;border:1px solid #fecdd3;color:#be123c;box-shadow:0 14px 28px -24px rgba(225,29,72,.45)}
    .parent-view-panel{display:none}
    .parent-view-panel.is-active{display:block}
    .parent-label{display:block;margin-bottom:.35rem;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
    .parent-photo-frame{width:10.5rem;height:10.5rem;border-radius:999px;border:4px solid #fff;background:linear-gradient(135deg,#ffe4e6 0%,#fff1f2 100%);overflow:hidden;box-shadow:0 18px 34px -24px rgba(244,63,94,.45)}
    .parent-chip{display:inline-flex;align-items:center;gap:.35rem;padding:.45rem .8rem;border-radius:999px;font-size:.78rem;font-weight:700}
    .parent-chip.active{background:#dcfce7;color:#166534}
    .parent-chip.inactive{background:#fee2e2;color:#b91c1c}
    .parent-chip.pending{background:#fef3c7;color:#b45309}
    .parent-summary{padding:1rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .parent-child-card{padding:1rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .parent-btn-secondary,.parent-btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:.45rem;border-radius:999px;padding:.85rem 1.25rem;font-weight:700}
    .parent-btn-secondary{border:1px solid #cbd5e1;background:#fff;color:#334155}
    .parent-btn-primary{background:linear-gradient(135deg,#e11d48 0%,#fb7185 100%);color:#fff}
    @media (max-width:1024px){.parent-view-grid{grid-template-columns:1fr}.parent-detail-grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'View Parent',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Parents', 'url' => route('admin.parents')],
        ['label' => $parent->name ?? 'Parent Details']
    ],
    'rightContent' => '<div class="flex flex-wrap gap-2"><a href="'.route('admin.parents').'" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700"><i class="bi bi-arrow-left"></i>Back</a><button type="button" onclick="adminOpenPrintPreview(\''.route('admin.parents.print', $parent->id).'\', { title: \'Parent Print Preview\' })" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-700"><i class="bi bi-printer"></i>Print</button><a href="'.route('admin.parents.download', $parent->id).'" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white"><i class="bi bi-download"></i>PDF</a><a href="'.route('admin.parents.edit', $parent->id).'" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white"><i class="bi bi-pencil"></i>Edit</a></div>'
])

<div class="parent-view-shell space-y-6">
    <section class="parent-view-hero p-5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                <div class="parent-photo-frame relative">
                    @if(!empty($parentProfile->profile_photo_url))
                        <img src="{{ $parentProfile->profile_photo_url }}" alt="{{ $parent->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-6xl font-black text-rose-300">{{ strtoupper(substr($parent->name ?? 'P', 0, 2)) }}</div>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-500">Parent Record</p>
                    <h1 class="mt-1 text-3xl font-bold text-slate-900">{{ $parent->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $parent->email }} · {{ $parentProfile->phone ?? $parent->phone ?? 'No phone' }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="parent-chip {{ $parentProfile->status ?? 'pending' }}">{{ ucfirst($parentProfile->status ?? 'pending') }}</span>
                        <span class="parent-chip pending">{{ ucfirst($parentProfile->relationship ?? 'Guardian') }}</span>
                        <span class="parent-chip active">{{ $assignedChildren->count() }} Children</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="parent-summary"><div class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Children</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ $assignedChildren->count() }}</div></div>
        <div class="parent-summary"><div class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Access Level</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ strtoupper($parentProfile->access_level ?? 'view_only') }}</div></div>
        <div class="parent-summary"><div class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Portal</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ $parentProfile->portal_access ? 'Enabled' : 'Disabled' }}</div></div>
        <div class="parent-summary"><div class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Primary Child</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ optional(optional($assignedChildren->firstWhere('user_id', $primaryChildUserId))->user)->name ?? 'Not set' }}</div></div>
    </div>

    <div class="parent-view-tabs">
        <button type="button" data-parent-tab="overview" class="parent-view-tab is-active"><i class="bi bi-person-lines-fill"></i>Overview</button>
        <button type="button" data-parent-tab="children" class="parent-view-tab"><i class="bi bi-people"></i>Children</button>
        <button type="button" data-parent-tab="contact" class="parent-view-tab"><i class="bi bi-telephone"></i>Contact</button>
        <button type="button" data-parent-tab="work" class="parent-view-tab"><i class="bi bi-briefcase"></i>Work</button>
        <button type="button" data-parent-tab="access" class="parent-view-tab"><i class="bi bi-shield-lock"></i>Access</button>
        <button type="button" data-parent-tab="documents" class="parent-view-tab"><i class="bi bi-folder2-open"></i>Documents</button>
        <button type="button" data-parent-tab="notes" class="parent-view-tab"><i class="bi bi-journal-text"></i>Notes</button>
    </div>

    <section data-parent-panel="overview" class="parent-view-panel is-active">
        <div class="parent-view-grid">
            <div class="parent-view-card p-5 text-center">
                <div class="parent-photo-frame mx-auto relative">
                    @if(!empty($parentProfile->profile_photo_url))
                        <img src="{{ $parentProfile->profile_photo_url }}" alt="{{ $parent->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-6xl font-black text-rose-300">{{ strtoupper(substr($parent->name ?? 'P', 0, 2)) }}</div>
                    @endif
                </div>
                <div class="mt-4">
                    <h2 class="text-xl font-bold text-slate-900">{{ $parent->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $parent->email }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $parentProfile->parent_code ?? 'Parent ID not set' }}</p>
                </div>
            </div>
            <div class="parent-view-section p-5">
                <div class="parent-title">Identity Snapshot</div>
                <div class="mt-4 parent-detail-grid">
                    <div class="parent-child-card"><span class="parent-label">Username</span><div class="text-sm font-semibold text-slate-900">{{ $parent->username ?? '—' }}</div></div>
                    <div class="parent-child-card"><span class="parent-label">Phone</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->phone ?? $parent->phone ?? '—' }}</div></div>
                    <div class="parent-child-card"><span class="parent-label">National ID</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->national_id_number ?? '—' }}</div></div>
                    <div class="parent-child-card"><span class="parent-label">Date of Birth</span><div class="text-sm font-semibold text-slate-900">{{ optional($parentProfile->date_of_birth)->format('d M Y') ?? '—' }}</div></div>
                    <div class="parent-child-card"><span class="parent-label">Relationship</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->relationship ?? '—' }}</div></div>
                    <div class="parent-child-card"><span class="parent-label">Gender</span><div class="text-sm font-semibold text-slate-900">{{ ucfirst($parentProfile->gender ?? '—') }}</div></div>
                </div>
            </div>
            </div>
        </div>
    </section>

    <section data-parent-panel="children" class="parent-view-panel">
        <div class="parent-view-section p-5">
            <div class="parent-title">Children Detail</div>
            <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
                @forelse($assignedChildren as $child)
                    <div class="parent-child-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-base font-bold text-slate-900">{{ $child->user->name ?? 'Student' }}</div>
                                <div class="text-sm text-slate-500">{{ $child->user->email ?? '—' }}</div>
                            </div>
                            @if($primaryChildUserId && $primaryChildUserId == $child->user_id)
                                <span class="parent-chip active">Primary</span>
                            @endif
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div><span class="parent-label">Roll No</span><div class="text-sm font-semibold text-slate-900">{{ $child->roll_no ?? '—' }}</div></div>
                            <div><span class="parent-label">Semester</span><div class="text-sm font-semibold text-slate-900">{{ $child->semester ?? '—' }}</div></div>
                            <div><span class="parent-label">Program</span><div class="text-sm font-semibold text-slate-900">{{ $child->program ?? '—' }}</div></div>
                            <div><span class="parent-label">Section</span><div class="text-sm font-semibold text-slate-900">{{ $child->section ?? '—' }}</div></div>
                            <div><span class="parent-label">Academic Year</span><div class="text-sm font-semibold text-slate-900">{{ $child->academic_year_bs ?? $child->academic_year ?? '—' }}</div></div>
                            <div><span class="parent-label">Status</span><div class="text-sm font-semibold text-slate-900">{{ ucfirst($child->status ?? 'active') }}</div></div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('admin.students.show', $child->user_id) }}" class="parent-btn-secondary text-sm">View Student</a>
                        </div>
                    </div>
                @empty
                    <div class="parent-child-card text-slate-500">No children assigned yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section data-parent-panel="contact" class="parent-view-panel">
        <div class="parent-view-section p-5">
            <div class="parent-title">Contact & Address</div>
            <div class="mt-4 parent-detail-grid">
                <div class="parent-child-card"><span class="parent-label">Secondary Phone</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->secondary_phone ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Alternate Email</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->alternate_email ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">WhatsApp Number</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->whatsapp_number ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Preferred Contact</span><div class="text-sm font-semibold text-slate-900">{{ ucfirst($parentProfile->preferred_contact_method ?? '—') }}</div></div>
                <div class="parent-child-card md:col-span-2"><span class="parent-label">Address</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->address ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">City</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->city ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">State / Province</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->state_province ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Postal Code</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->postal_code ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Country</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->country ?? '—' }}</div></div>
            </div>
        </div>
    </section>

    <section data-parent-panel="work" class="parent-view-panel">
        <div class="parent-view-section p-5">
            <div class="parent-title">Work & Health</div>
            <div class="mt-4 parent-detail-grid">
                <div class="parent-child-card"><span class="parent-label">Employer Name</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->employer_name ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Work Phone</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->work_phone_number ?? '—' }}</div></div>
                <div class="parent-child-card md:col-span-2"><span class="parent-label">Work Address</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->work_address ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Income Range</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->income_range ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Blood Group</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->blood_group ?? '—' }}</div></div>
                <div class="parent-child-card md:col-span-2"><span class="parent-label">Medical Conditions</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->medical_conditions ?? '—' }}</div></div>
                <div class="parent-child-card md:col-span-2"><span class="parent-label">Emergency Notes</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->emergency_notes ?? '—' }}</div></div>
            </div>
        </div>
    </section>

    <section data-parent-panel="access" class="parent-view-panel">
        <div class="parent-view-section p-5">
            <div class="parent-title">Access & Security</div>
            <div class="mt-4 parent-detail-grid">
                <div class="parent-child-card"><span class="parent-label">Status</span><div class="text-sm font-semibold text-slate-900">{{ ucfirst($parentProfile->status ?? 'active') }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Access Level</span><div class="text-sm font-semibold text-slate-900">{{ strtoupper($parentProfile->access_level ?? 'view_only') }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Portal Access</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->portal_access ? 'Enabled' : 'Disabled' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Profile Visibility</span><div class="text-sm font-semibold text-slate-900">{{ ucfirst($parentProfile->profile_visibility ?? 'public') }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Preferred Language</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->preferred_language ?? '—' }}</div></div>
                <div class="parent-child-card"><span class="parent-label">Notifications</span><div class="text-sm font-semibold text-slate-900">{{ $notificationPreferences->isNotEmpty() ? $notificationPreferences->join(', ') : '—' }}</div></div>
            </div>
        </div>
    </section>

    <section data-parent-panel="documents" class="parent-view-panel">
        <div class="parent-view-section p-5">
            <div class="parent-title">Documents</div>
            <div class="mt-4 parent-detail-grid">
                <div class="parent-child-card"><span class="parent-label">ID Proof</span>@if($docs['id'])<a href="{{ $docs['id'] }}" target="_blank" class="text-sm font-semibold text-rose-600 hover:underline">{{ basename($parentProfile->id_proof_path) }}</a>@else<div class="text-sm font-semibold text-slate-900">Not uploaded</div>@endif</div>
                <div class="parent-child-card"><span class="parent-label">Address Proof</span>@if($docs['address'])<a href="{{ $docs['address'] }}" target="_blank" class="text-sm font-semibold text-rose-600 hover:underline">{{ basename($parentProfile->address_proof_path) }}</a>@else<div class="text-sm font-semibold text-slate-900">Not uploaded</div>@endif</div>
            </div>
        </div>
    </section>

    <section data-parent-panel="notes" class="parent-view-panel">
        <div class="parent-view-section p-5">
            <div class="parent-title">Additional Notes</div>
            <div class="mt-4 parent-detail-grid">
                <div class="parent-child-card md:col-span-2"><span class="parent-label">Short Bio</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->bio ?? '—' }}</div></div>
                <div class="parent-child-card md:col-span-2"><span class="parent-label">Notes / Remarks</span><div class="text-sm font-semibold text-slate-900">{{ $parentProfile->notes ?? '—' }}</div></div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('[data-parent-tab]');
    const panels = document.querySelectorAll('[data-parent-panel]');
    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.parentTab;
            tabs.forEach((item) => item.classList.toggle('is-active', item.dataset.parentTab === key));
            panels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.parentPanel === key));
        });
    });
});
</script>
@endsection

