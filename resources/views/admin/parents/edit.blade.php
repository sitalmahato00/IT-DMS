@extends('admin.layouts.app')

@section('title', 'Edit Parent')

@php
    $parentUser = $parent ?? new \App\Models\User();
    $parentProfile = $parentProfile ?? new \App\Models\ParentModel();
    $fieldOptions = $fieldOptions ?? [];
    $availableStudents = collect($availableStudents ?? []);
    $selectedChildren = collect(old('children', $selectedChildren ?? []))->map(fn ($id) => (string) $id)->values();
@endphp

@section('styles')
<style>
    .parent-page-shell{max-width:96rem;margin:0 auto;width:100%;overflow-x:hidden}
    .parent-panel,.parent-card{border:1px solid #e2e8f0;border-radius:1.25rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:0 22px 42px -34px rgba(15,23,42,.22)}
    .parent-grid{display:grid;grid-template-columns:minmax(0,22rem) minmax(0,1fr);gap:1.25rem;align-items:start}
    .parent-card{padding:1.1rem}
    .parent-label{display:block;margin-bottom:.4rem;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
    .parent-input{width:100%;min-height:2.8rem;border:2px solid #cbd5e1;border-radius:.85rem;background:#fff;padding:.7rem .9rem;transition:border-color .2s ease,box-shadow .2s ease}
    .parent-input:focus{outline:none;border-color:#e11d48;box-shadow:0 0 0 4px rgba(225,29,72,.1)}
    .parent-section{padding:1rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .parent-section + .parent-section{margin-top:1rem}
    .parent-title{font-size:.9rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#64748b}
    .parent-photo-frame{width:10.5rem;height:10.5rem;border-radius:999px;border:4px solid #fff;background:linear-gradient(135deg,#ffe4e6 0%,#fff1f2 100%);overflow:hidden;box-shadow:0 18px 34px -24px rgba(244,63,94,.45)}
    .parent-btn-secondary{border:1px solid #cbd5e1;background:#fff;color:#334155;border-radius:999px;padding:.85rem 1.3rem;font-weight:700}
    .parent-btn-primary{border:0;background:linear-gradient(135deg,#e11d48 0%,#fb7185 100%);color:#fff;border-radius:999px;padding:.85rem 1.3rem;font-weight:700}
    .parent-children-menu{max-height:16rem;overflow-y:auto}
    .parent-children-option{display:flex;align-items:center;gap:.65rem;width:100%;padding:.7rem .85rem;border-radius:.75rem;cursor:pointer}
    .parent-children-option:hover{background:#f8fafc}
    .parent-children-summary{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    @media (max-width:1024px){.parent-grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Parents',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Parents', 'url' => route('admin.parents')],
        ['label' => 'Edit Parent']
    ],
    'rightContent' => '<a href="'.route('admin.parents.show', $parentUser->id).'" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700"><i class="bi bi-arrow-left"></i>Back</a>'
])

<div class="parent-page-shell space-y-6">
    <form action="{{ route('admin.parents.update', $parentUser->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="parent-grid">
            <aside class="space-y-4">
                <section class="parent-panel p-5 text-center">
                    <div class="mx-auto parent-photo-frame relative">
                        <img src="{{ $parentProfile?->profile_photo_url ?? '' }}" alt="Parent photo" data-parent-photo-preview class="absolute inset-0 h-full w-full object-cover {{ $parentProfile?->profile_photo_url ? '' : 'hidden' }}">
                        <div data-parent-photo-placeholder class="absolute inset-0 flex items-center justify-center {{ $parentProfile?->profile_photo_url ? 'hidden' : '' }}">
                            <i class="bi bi-person-fill text-6xl text-rose-300"></i>
                        </div>
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-slate-900">{{ old('name', $parentUser->name ?? '') ?: 'Parent Name' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ old('email', $parentUser->email ?? '') ?: 'parent@example.com' }}</p>
                    <label class="mt-4 inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                        <i class="bi bi-upload"></i>Choose Photo
                        <input type="file" name="profile_photo" accept="image/*" class="hidden" data-parent-photo-input>
                    </label>
                </section>

                <section class="parent-panel p-5">
                    <div class="parent-title">Quick Details</div>
                    <div class="mt-4 space-y-3">
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Parent ID</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900">{{ old('parent_code', $parentProfile->parent_code ?? '') ?: 'Auto generated' }}</div>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Children Linked</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedChildren->count() }}</div>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Access Level</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900">{{ strtoupper(old('access_level', $parentProfile->access_level ?? 'view_only')) }}</div>
                        </div>
                    </div>
                </section>
            </aside>

            <main class="parent-panel p-5">
                <div class="border-b border-slate-200 pb-4">
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-500">Parent Management</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900">Edit Parent</h1>
                    <p class="mt-1 text-sm text-slate-500">Update parent details, linked children, and portal access.</p>
                </div>

                <div class="space-y-4 pt-5">
                    <section class="parent-section">
                        <div class="parent-title">Identity & Verification</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div><label class="parent-label">Username</label><input name="username" value="{{ old('username', $parentUser->username ?? '') }}" class="parent-input" placeholder="System username"></div>
                            <div><label class="parent-label">Parent ID</label><input name="parent_code" value="{{ old('parent_code', $parentProfile->parent_code ?? '') }}" class="parent-input" placeholder="Auto generated if empty"></div>
                            <div><label class="parent-label">Full Name *</label><input name="name" value="{{ old('name', $parentUser->name ?? '') }}" required class="parent-input" placeholder="Full name"></div>
                            <div><label class="parent-label">Email *</label><input name="email" type="email" value="{{ old('email', $parentUser->email ?? '') }}" required class="parent-input" placeholder="parent@example.com"></div>
                            <div><label class="parent-label">Phone *</label><input name="phone" value="{{ old('phone', $parentProfile->phone ?? $parentUser->phone ?? '') }}" required class="parent-input" placeholder="Primary phone"></div>
                            <div><label class="parent-label">Occupation</label><input name="occupation" value="{{ old('occupation', $parentProfile->occupation ?? '') }}" class="parent-input" placeholder="Occupation"></div>
                            <div><label class="parent-label">National ID / Citizenship Number</label><input name="national_id_number" value="{{ old('national_id_number', $parentProfile->national_id_number ?? '') }}" class="parent-input" placeholder="National ID number"></div>
                            <div><label class="parent-label">Date of Birth</label><input name="date_of_birth" type="date" value="{{ old('date_of_birth', optional($parentProfile->date_of_birth)->format('Y-m-d')) }}" class="parent-input"></div>
                            <div><label class="parent-label">Relationship</label><select name="relationship" class="parent-input"><option value="">Select relationship</option>@foreach(($fieldOptions['relationships'] ?? []) as $rel)<option value="{{ $rel }}" @selected(old('relationship', $parentProfile->relationship ?? '')===$rel)>{{ $rel }}</option>@endforeach</select></div>
                            <div><label class="parent-label">Gender</label><select name="gender" class="parent-input"><option value="">Prefer not to say</option><option value="male" @selected(old('gender', $parentProfile->gender ?? '')==='male')>Male</option><option value="female" @selected(old('gender', $parentProfile->gender ?? '')==='female')>Female</option><option value="other" @selected(old('gender', $parentProfile->gender ?? '')==='other')>Other</option></select></div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Family & Student Link</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="parent-label">Multiple Children Selection</label>
                                <div class="relative">
                                    <button type="button" data-parent-children-toggle class="parent-input flex items-center justify-between text-left">
                                        <span data-parent-children-summary class="parent-children-summary">Choose one or more children</span>
                                        <i class="bi bi-chevron-down text-slate-400"></i>
                                    </button>
                                    <div data-parent-children-menu class="parent-children-menu parent-input absolute left-0 right-0 z-20 mt-2 hidden rounded-xl bg-white p-2 shadow-xl">
                                        @forelse($availableStudents as $student)
                                            @php($label = trim(($student['name'] ?? 'Student') . ($student['roll_no'] ? ' · Roll ' . $student['roll_no'] : '') . ($student['semester'] ? ' · Sem ' . $student['semester'] : '') . ($student['program'] ? ' · ' . $student['program'] : '')))
                                            <label class="parent-children-option">
                                                <input type="checkbox" name="children[]" value="{{ $student['id'] }}" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500" @checked($selectedChildren->contains((string) $student['id'])) data-parent-child-option>
                                                <span class="text-sm font-medium text-slate-800">{{ $label }}</span>
                                            </label>
                                        @empty
                                            <div class="px-3 py-2 text-sm text-slate-500">No students available</div>
                                        @endforelse
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Select one or more children from the dropdown.</p>
                            </div>
                            <div><label class="parent-label">Primary Child</label><select name="primary_child_user_id" class="parent-input"><option value="">Choose primary child</option>@foreach($availableStudents as $student)<option value="{{ $student['id'] }}" @selected((string) old('primary_child_user_id', $primaryChildUserId ?? '') === (string) $student['id'])>{{ $student['name'] ?? 'Student' }}</option>@endforeach</select></div>
                            <div class="flex items-end"><label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3"><input type="checkbox" name="emergency_contact_priority" value="1" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500" @checked(old('emergency_contact_priority', $parentProfile->emergency_contact_priority ?? false))><span><span class="block text-sm font-semibold text-slate-900">Emergency Contact Priority</span><span class="block text-xs text-slate-500">Mark this parent as the main emergency contact.</span></span></label></div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Contact Enhancement</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div><label class="parent-label">Secondary Phone Number</label><input name="secondary_phone" value="{{ old('secondary_phone', $parentProfile->secondary_phone ?? '') }}" class="parent-input" placeholder="Secondary phone"></div>
                            <div><label class="parent-label">Alternate Email</label><input name="alternate_email" type="email" value="{{ old('alternate_email', $parentProfile->alternate_email ?? '') }}" class="parent-input" placeholder="Alternate email"></div>
                            <div><label class="parent-label">WhatsApp Number</label><input name="whatsapp_number" value="{{ old('whatsapp_number', $parentProfile->whatsapp_number ?? '') }}" class="parent-input" placeholder="WhatsApp number"></div>
                            <div><label class="parent-label">Preferred Contact Method</label><select name="preferred_contact_method" class="parent-input"><option value="">Select method</option>@foreach(($fieldOptions['contactMethods'] ?? []) as $value => $label)<option value="{{ $value }}" @selected(old('preferred_contact_method', $parentProfile->preferred_contact_method ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Address Expansion</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2"><label class="parent-label">Address</label><textarea name="address" rows="3" class="parent-input" placeholder="Street address">{{ old('address', $parentProfile->address ?? '') }}</textarea></div>
                            <div><label class="parent-label">City</label><input name="city" value="{{ old('city', $parentProfile->city ?? '') }}" class="parent-input" placeholder="City"></div>
                            <div><label class="parent-label">State / Province</label><input name="state_province" value="{{ old('state_province', $parentProfile->state_province ?? '') }}" class="parent-input" placeholder="State / Province"></div>
                            <div><label class="parent-label">Postal Code</label><input name="postal_code" value="{{ old('postal_code', $parentProfile->postal_code ?? '') }}" class="parent-input" placeholder="Postal code"></div>
                            <div><label class="parent-label">Country</label><input name="country" value="{{ old('country', $parentProfile->country ?? '') }}" class="parent-input" placeholder="Country"></div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Work & Professional Info</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div><label class="parent-label">Employer Name</label><input name="employer_name" value="{{ old('employer_name', $parentProfile->employer_name ?? '') }}" class="parent-input" placeholder="Employer name"></div>
                            <div><label class="parent-label">Work Phone Number</label><input name="work_phone_number" value="{{ old('work_phone_number', $parentProfile->work_phone_number ?? '') }}" class="parent-input" placeholder="Work phone"></div>
                            <div class="md:col-span-2"><label class="parent-label">Work Address</label><textarea name="work_address" rows="3" class="parent-input" placeholder="Work address">{{ old('work_address', $parentProfile->work_address ?? '') }}</textarea></div>
                            <div><label class="parent-label">Income Range</label><input name="income_range" value="{{ old('income_range', $parentProfile->income_range ?? '') }}" class="parent-input" placeholder="Income range"></div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Health & Emergency</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div><label class="parent-label">Blood Group</label><select name="blood_group" class="parent-input"><option value="">Select blood group</option>@foreach(($fieldOptions['bloodGroups'] ?? []) as $bg)<option value="{{ $bg }}" @selected(old('blood_group', $parentProfile->blood_group ?? '') === $bg)>{{ $bg }}</option>@endforeach</select></div>
                            <div class="md:col-span-2"><label class="parent-label">Medical Conditions</label><textarea name="medical_conditions" rows="3" class="parent-input" placeholder="Medical conditions">{{ old('medical_conditions', $parentProfile->medical_conditions ?? '') }}</textarea></div>
                            <div class="md:col-span-2"><label class="parent-label">Emergency Notes</label><textarea name="emergency_notes" rows="3" class="parent-input" placeholder="Emergency notes">{{ old('emergency_notes', $parentProfile->emergency_notes ?? '') }}</textarea></div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Account & Access Control</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div><label class="parent-label">Status</label><select name="status" class="parent-input">@foreach(($fieldOptions['statuses'] ?? []) as $value => $label)<option value="{{ $value }}" @selected(old('status', $parentProfile->status ?? 'active') === $value)>{{ $label }}</option>@endforeach</select></div>
                            <div><label class="parent-label">Access Level</label><select name="access_level" class="parent-input">@foreach(($fieldOptions['accessLevels'] ?? []) as $value => $label)<option value="{{ $value }}" @selected(old('access_level', $parentProfile->access_level ?? 'view_only') === $value)>{{ $label }}</option>@endforeach</select></div>
                            <div class="md:col-span-2"><label class="parent-label">Notification Preferences</label><div class="mt-2 flex flex-wrap gap-3">@foreach(($fieldOptions['notificationPreferences'] ?? []) as $value => $label)<label class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700"><input type="checkbox" name="notification_preferences[]" value="{{ $value }}" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500" @checked(in_array($value, old('notification_preferences', array_filter(array_map('trim', explode(',', (string) ($parentProfile->notification_preferences ?? ''))))), true))><span>{{ $label }}</span></label>@endforeach</div></div>
                            <div><label class="parent-label">Portal Access</label><label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3"><input type="checkbox" name="portal_access" value="1" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500" @checked(old('portal_access', $parentProfile->portal_access ?? true))><span class="text-sm font-semibold text-slate-900">Enable Login</span></label></div>
                            <div><label class="parent-label">Profile Visibility</label><select name="profile_visibility" class="parent-input">@foreach(($fieldOptions['profileVisibilities'] ?? []) as $value => $label)<option value="{{ $value }}" @selected(old('profile_visibility', $parentProfile->profile_visibility ?? 'public') === $value)>{{ $label }}</option>@endforeach</select></div>
                            <div><label class="parent-label">Preferred Language</label><select name="preferred_language" class="parent-input"><option value="">Select language</option>@foreach(($fieldOptions['preferredLanguages'] ?? []) as $lang)<option value="{{ $lang }}" @selected(old('preferred_language', $parentProfile->preferred_language ?? '') === $lang)>{{ $lang }}</option>@endforeach</select></div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Documents Upload</div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div><label class="parent-label">ID Proof Upload</label><input type="file" name="id_proof_upload" accept=".jpg,.jpeg,.png,.webp,.pdf" class="parent-input p-2">@if(!empty($parentProfile->id_proof_url))<div class="mt-2 text-sm text-slate-600">Current file: <a href="{{ $parentProfile->id_proof_url }}" target="_blank" class="text-rose-600 hover:underline">{{ basename($parentProfile->id_proof_path) }}</a></div>@endif</div>
                            <div><label class="parent-label">Address Proof Upload</label><input type="file" name="address_proof_upload" accept=".jpg,.jpeg,.png,.webp,.pdf" class="parent-input p-2">@if(!empty($parentProfile->address_proof_url))<div class="mt-2 text-sm text-slate-600">Current file: <a href="{{ $parentProfile->address_proof_url }}" target="_blank" class="text-rose-600 hover:underline">{{ basename($parentProfile->address_proof_path) }}</a></div>@endif</div>
                        </div>
                    </section>

                    <section class="parent-section">
                        <div class="parent-title">Additional Info</div>
                        <div class="mt-4 grid grid-cols-1 gap-4">
                            <div><label class="parent-label">Short Bio</label><textarea name="bio" rows="3" class="parent-input" placeholder="Short bio">{{ old('bio', $parentProfile->bio ?? '') }}</textarea></div>
                            <div><label class="parent-label">Notes / Remarks</label><textarea name="notes" rows="4" class="parent-input" placeholder="Internal remarks">{{ old('notes', $parentProfile->notes ?? '') }}</textarea></div>
                        </div>
                    </section>
                </div>

                <div class="mt-5 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.parents.show', $parentUser->id) }}" class="parent-btn-secondary inline-flex items-center justify-center">Cancel</a>
                    <button type="submit" class="parent-btn-primary inline-flex items-center justify-center"><i class="bi bi-check2 mr-2"></i>Update Parent</button>
                </div>
            </main>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('[data-parent-photo-input]');
    const preview = document.querySelector('[data-parent-photo-preview]');
    const placeholder = document.querySelector('[data-parent-photo-placeholder]');
    const childrenToggle = document.querySelector('[data-parent-children-toggle]');
    const childrenMenu = document.querySelector('[data-parent-children-menu]');
    const childOptions = Array.from(document.querySelectorAll('[data-parent-child-option]'));
    const childrenSummary = document.querySelector('[data-parent-children-summary]');

    function updateChildrenSummary() {
        if (!childrenSummary) return;
        const checked = childOptions.filter((option) => option.checked);
        if (!checked.length) {
            childrenSummary.textContent = 'Choose one or more children';
            return;
        }

        const names = checked.map((input) => input.closest('label')?.innerText.trim().replace(/\s+/g, ' ') || '').filter(Boolean);
        childrenSummary.textContent = names.length <= 2 ? names.join(', ') : `${names.slice(0, 2).join(', ')} +${names.length - 2} more`;
    }

    if (childrenToggle && childrenMenu) {
        childrenToggle.addEventListener('click', () => {
            childrenMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!childrenMenu.contains(event.target) && !childrenToggle.contains(event.target)) {
                childrenMenu.classList.add('hidden');
            }
        });
    }

    childOptions.forEach((option) => option.addEventListener('change', updateChildrenSummary));
    updateChildrenSummary();

    if (!input || !preview || !placeholder) return;
    input.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection

