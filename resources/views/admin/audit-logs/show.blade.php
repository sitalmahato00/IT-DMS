@extends('admin.layouts.app')

@section('title', 'Audit Log Detail')

@section('styles')
<script>
    document.documentElement.classList.add('audit-logs-ui-enhanced');
</script>
<style>
    html.audit-logs-ui-enhanced:not(.dark) .audit-detail-page {
        color: #0f172a;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-surface,
    html.audit-logs-ui-enhanced:not(.dark) .audit-panel {
        border-radius: 28px;
        border-color: rgba(226, 232, 240, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 250, 251, 0.98));
        box-shadow: 0 24px 52px -40px rgba(15, 23, 42, 0.22);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-avatar {
        width: 5.5rem;
        height: 5.5rem;
        border-radius: 999px;
        overflow: hidden;
        background: linear-gradient(180deg, #e2e8f0, #cbd5e1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        color: #334155;
        flex: 0 0 auto;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-chip,
    html.audit-logs-ui-enhanced:not(.dark) .audit-toolbar-btn {
        border-radius: 999px;
        font-weight: 700;
        transition: all .2s ease;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .7rem;
        background: #eef2ff;
        color: #4338ca;
        font-size: .72rem;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-kv {
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.96));
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-value {
        word-break: break-word;
        white-space: pre-wrap;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-compare thead th {
        background: linear-gradient(180deg, #f7fafc, #edf2f7);
        color: #334155;
        font-size: .72rem;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
@php
    $old = $log->old_values ?? [];
    $new = $log->new_values ?? [];
    $changeCount = is_countable($log->changes ?? null) ? count($log->changes) : 0;
    $userPhoto = $log->user_photo_url ?? null;
    $userName = $log->user_name ?? 'System';
    $userEmail = $log->user_email ?? 'System generated';
    $userRole = $log->user_role_label ?? 'System';
    $initials = $log->user_initials ?? 'S';
    $rawUserAgent = $log->raw_user_agent ?? '';
    $backUrl = route('admin.audit-logs.index', collect($filters)->except('user_id')->filter(fn ($value) => $value !== '' && $value !== null)->all());
@endphp

@include('admin.components.admin-page-header', [
    'title' => 'Audit Log Detail',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Audit Logs', 'url' => route('admin.audit-logs.index')],
        ['label' => 'Audit Log Detail']
    ],
    'rightContent' => '<a href="' . $backUrl . '" class="audit-toolbar-btn inline-flex items-center justify-center gap-2 border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"><i class="bi bi-arrow-left"></i>Back to logs</a>'
])

<div class="audit-detail-page space-y-6">
    <div class="audit-surface overflow-hidden border border-slate-200">
        <div class="border-b border-slate-200/80 px-5 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex min-w-0 gap-4">
                    <div class="audit-avatar border border-slate-200 shadow-sm">
                        @if(!empty($userPhoto))
                            <img src="{{ $userPhoto }}" alt="{{ $userName }}">
                        @else
                            <span class="text-2xl">{{ $initials }}</span>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Audit Record</div>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">{{ $log->details ?? ($log->entity_name ?? 'Audit log') }}</h2>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-600">
                            <span class="font-semibold text-slate-900">{{ $userName }}</span>
                            <span>•</span>
                            <span>{{ $userEmail }}</span>
                            <span>•</span>
                            <span>{{ $userRole }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="audit-chip"><i class="bi bi-activity"></i>{{ $log->action_label }}</span>
                            <span class="audit-chip"><i class="bi bi-layers"></i>{{ $log->entity_name ?? 'Record' }}</span>
                            @if(!empty($log->model_id))
                                <span class="audit-chip"><i class="bi bi-hash"></i>ID {{ $log->model_id }}</span>
                            @endif
                            @if($changeCount)
                                <span class="audit-chip"><i class="bi bi-list-check"></i>{{ $changeCount }} changes</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:w-auto">
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Timestamp</div>
                        <div class="mt-1 text-sm font-black text-slate-900">{{ $log->timestamp_label ?? '—' }}</div>
                        <div class="text-xs text-slate-500">{{ $log->timestamp_human ?? '' }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">IP</div>
                        <div class="mt-1 text-sm font-black text-slate-900">{{ $log->ip_address ?: 'Hidden' }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Browser</div>
                        <div class="mt-1 text-sm font-black text-slate-900">{{ $log->browser_name }}</div>
                        <div class="text-xs text-slate-500">{{ $log->browser_platform }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">User Agent</div>
                        <div class="mt-1 line-clamp-3 text-xs font-medium text-slate-600">{{ $rawUserAgent ?: 'Not captured' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 py-5">
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                <div class="audit-panel border border-slate-200 p-5 xl:col-span-1">
                    <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">User Profile</div>
                    <div class="mt-4 flex items-start gap-4">
                        <div class="audit-avatar border border-slate-200 shadow-sm">
                            @if(!empty($userPhoto))
                                <img src="{{ $userPhoto }}" alt="{{ $userName }}">
                            @else
                                <span class="text-2xl">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xl font-black text-slate-900">{{ $userName }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ $userEmail }}</div>
                            <div class="mt-2 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $userRole }}</div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3">
                        <div class="audit-kv px-4 py-3">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Action</div>
                            <div class="audit-value mt-1 text-sm font-semibold text-slate-900">{{ $log->action_label }}</div>
                        </div>
                        <div class="audit-kv px-4 py-3">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Module / Entity</div>
                            <div class="audit-value mt-1 text-sm font-semibold text-slate-900">{{ $log->entity_name ?? 'Record' }}</div>
                        </div>
                        <div class="audit-kv px-4 py-3">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Record ID</div>
                            <div class="audit-value mt-1 text-sm font-semibold text-slate-900">{{ $log->model_id ?? '—' }}</div>
                        </div>
                        <div class="audit-kv px-4 py-3">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Source</div>
                            <div class="audit-value mt-1 text-sm font-semibold text-slate-900">{{ $log->module ?? 'System' }}</div>
                        </div>
                    </div>
                </div>

                <div class="audit-panel border border-slate-200 p-5 xl:col-span-2">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Change History</div>
                            <div class="mt-1 text-lg font-black text-slate-900">Full before / after values</div>
                        </div>
                        <div class="text-xs font-medium text-slate-500">{{ $changeCount }} tracked field{{ $changeCount === 1 ? '' : 's' }}</div>
                    </div>

                    @if($changeCount > 0)
                        <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="audit-compare min-w-full divide-y divide-slate-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left">Field</th>
                                            <th class="px-4 py-3 text-left">Before</th>
                                            <th class="px-4 py-3 text-left">After</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @foreach($log->changes as $change)
                                            @php
                                                $beforeValue = $change['before'] ?? null;
                                                $afterValue = $change['after'] ?? null;
                                                $beforeDisplay = is_array($beforeValue)
                                                    ? json_encode($beforeValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                                    : ($beforeValue === null || $beforeValue === '' ? '—' : (is_bool($beforeValue) ? ($beforeValue ? 'true' : 'false') : (string) $beforeValue));
                                                $afterDisplay = is_array($afterValue)
                                                    ? json_encode($afterValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                                    : ($afterValue === null || $afterValue === '' ? '—' : (is_bool($afterValue) ? ($afterValue ? 'true' : 'false') : (string) $afterValue));
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-4">
                                                    <div class="text-sm font-semibold text-slate-900">{{ $change['label'] ?? $change['field'] }}</div>
                                                    <div class="text-xs text-slate-500">{{ $change['field'] }}</div>
                                                </td>
                                                <td class="px-4 py-4 align-top">
                                                    <div class="audit-value whitespace-pre-wrap break-words text-sm text-slate-700">{{ $beforeDisplay }}</div>
                                                </td>
                                                <td class="px-4 py-4 align-top">
                                                    <div class="audit-value whitespace-pre-wrap break-words text-sm font-semibold text-slate-900">{{ $afterDisplay }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl text-slate-400 shadow-sm">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <h3 class="mt-4 text-lg font-bold text-slate-900">No field-level changes captured</h3>
                            <p class="mt-2 text-sm text-slate-500">This entry only has a summary action. The raw before/after payloads are still shown below.</p>
                        </div>
                    @endif

                    <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="audit-kv px-4 py-4">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Summary</div>
                            <div class="audit-value mt-2 text-sm font-semibold text-slate-900">{{ $log->details ?? '—' }}</div>
                        </div>
                        <div class="audit-kv px-4 py-4">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Timestamp</div>
                            <div class="audit-value mt-2 text-sm font-semibold text-slate-900">{{ $log->timestamp_label ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $log->timestamp_human ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div class="audit-panel border border-slate-200 p-5">
                    <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Before Snapshot</div>
                    <div class="mt-3 space-y-3">
                        @if(!empty($old))
                            @foreach($old as $key => $value)
                                <div class="audit-kv px-4 py-3">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ \Illuminate\Support\Str::headline(str_replace(['_', '-'], ' ', $key)) }}</div>
                                    <div class="audit-value mt-1 text-sm text-slate-700">{{ is_scalar($value) || $value === null ? ($value === null || $value === '' ? '—' : $value) : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">No previous values recorded.</div>
                        @endif
                    </div>
                </div>

                <div class="audit-panel border border-slate-200 p-5">
                    <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">After Snapshot</div>
                    <div class="mt-3 space-y-3">
                        @if(!empty($new))
                            @foreach($new as $key => $value)
                                <div class="audit-kv px-4 py-3">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ \Illuminate\Support\Str::headline(str_replace(['_', '-'], ' ', $key)) }}</div>
                                    <div class="audit-value mt-1 text-sm text-slate-700">{{ is_scalar($value) || $value === null ? ($value === null || $value === '' ? '—' : $value) : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">No new values recorded.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-3">
                <div class="audit-panel border border-slate-200 p-5 xl:col-span-2">
                    <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Request Metadata</div>
                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="audit-kv px-4 py-3">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">User Agent</div>
                            <div class="audit-value mt-1 text-sm text-slate-700">{{ $rawUserAgent ?: 'Not captured' }}</div>
                        </div>
                        <div class="audit-kv px-4 py-3">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Browser</div>
                            <div class="audit-value mt-1 text-sm font-semibold text-slate-900">{{ $log->browser_name }}</div>
                            <div class="text-xs text-slate-500">{{ $log->browser_platform }}</div>
                        </div>
                    </div>
                </div>

                <div class="audit-panel border border-slate-200 p-5">
                    <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Quick Actions</div>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('admin.audit-logs.index') }}" class="audit-toolbar-btn flex w-full items-center justify-center gap-2 border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            <i class="bi bi-arrow-left"></i> Back to logs
                        </a>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <div class="font-semibold text-slate-900">Summary</div>
                            <div class="mt-1">{{ $log->details ?? 'No summary available' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

