@extends('admin.layouts.app')

@section('title', 'Audit Logs')

@section('styles')
<script>
    document.documentElement.classList.add('audit-logs-ui-enhanced');
</script>
<style>
    html.audit-logs-ui-enhanced:not(.dark) .audit-logs-page {
        color: #0f172a;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-surface,
    html.audit-logs-ui-enhanced:not(.dark) .audit-panel {
        border-radius: 28px;
        border-color: rgba(226, 232, 240, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 250, 251, 0.98));
        box-shadow: 0 24px 52px -40px rgba(15, 23, 42, 0.22);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-user-tab,
    html.audit-logs-ui-enhanced:not(.dark) .audit-toolbar-btn {
        border-radius: 999px;
        font-weight: 700;
        transition: all .2s ease;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-user-tab {
        display: inline-flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1rem;
        border: 1px solid #dbe4ee;
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.96));
        box-shadow: 0 14px 28px -22px rgba(15, 23, 42, .28);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-user-tab.is-active {
        border-color: rgba(37, 99, 235, .35);
        background: linear-gradient(180deg, rgba(59, 130, 246, .12), rgba(59, 130, 246, .05));
        box-shadow: 0 18px 32px -22px rgba(37, 99, 235, .28);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-user-tab-avatar {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 999px;
        overflow: hidden;
        background: #e2e8f0;
        flex: 0 0 auto;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-user-tab-avatar img,
    html.audit-logs-ui-enhanced:not(.dark) .audit-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-table th,
    html.audit-logs-ui-enhanced:not(.dark) .audit-table td {
        vertical-align: top;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-table thead th {
        background: linear-gradient(180deg, #f7fafc, #edf2f7);
        color: #334155;
        font-size: .72rem;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-row:hover {
        background: rgba(59, 130, 246, .03);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-avatar {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 999px;
        overflow: hidden;
        background: linear-gradient(180deg, #e2e8f0, #cbd5e1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #334155;
        flex: 0 0 auto;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .65rem;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-size: .7rem;
        font-weight: 700;
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-search input,
    html.audit-logs-ui-enhanced:not(.dark) .audit-search select {
        border-radius: 16px;
        border-color: #d6dde8;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-search input:focus,
    html.audit-logs-ui-enhanced:not(.dark) .audit-search select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .12);
    }
</style>
@endsection

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Audit Logs',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Audit Logs']
    ],
    'rightContent' => '<div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200"><i class="bi bi-clock-history text-blue-600"></i><span>' . (method_exists($logs, 'total') ? $logs->total() : $logs->count()) . ' entries</span></div>'
])

@php
    $totalLogs = method_exists($logs, 'total') ? $logs->total() : $logs->count();
    $baseFilters = collect($filters)->except('user_id')->filter(fn ($value) => $value !== '' && $value !== null)->all();
    $selectedUser = collect($userTabs)->firstWhere('active', true);
    $selectedUserLabel = $selectedUser['name'] ?? 'All Users';
@endphp

<div class="audit-logs-page space-y-6">
    <div class="audit-surface overflow-hidden border border-slate-200">
        <div class="border-b border-slate-200/80 px-5 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Activity Stream</div>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900">Audit Logs</h2>
                    <p class="mt-1 max-w-2xl text-sm text-slate-500">Browse every action captured by the system with user profile photos, IP addresses, browser details, timestamps, and full change history.</p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:w-auto">
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Total</div>
                        <div class="mt-1 text-2xl font-black text-slate-900">{{ $totalLogs }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Users</div>
                        <div class="mt-1 text-2xl font-black text-slate-900">{{ $userTabs->count() }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Actions</div>
                        <div class="mt-1 text-2xl font-black text-slate-900">{{ count($actionOptions) }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Page</div>
                        <div class="mt-1 text-2xl font-black text-slate-900">{{ $perPage }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-b border-slate-200/80 px-4 py-4">
            <div class="flex flex-wrap gap-2">
                @foreach($userTabs as $tab)
                    @php
                        $tabQuery = $baseFilters;
                        if (!is_null($tab['id'])) {
                            $tabQuery['user_id'] = $tab['id'];
                        }
                    @endphp
                    <a href="{{ route('admin.audit-logs.index', $tabQuery) }}" class="audit-user-tab {{ $tab['active'] ? 'is-active' : '' }}">
                        <span class="audit-user-tab-avatar">
                            @if(!empty($tab['photo_url']))
                                <img src="{{ $tab['photo_url'] }}" alt="{{ $tab['name'] }}">
                            @else
                                <span class="flex h-full w-full items-center justify-center bg-slate-100 text-sm font-black text-slate-500">{{ strtoupper(mb_substr($tab['name'], 0, 1)) }}</span>
                            @endif
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold text-slate-900">{{ $tab['name'] }}</span>
                            <span class="block text-xs font-medium text-slate-500">{{ $tab['count'] }} logs</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <form method="GET" class="audit-search border-b border-slate-200/80 px-5 py-5">
            @if(!empty($filters['user_id']))
                <input type="hidden" name="user_id" value="{{ $filters['user_id'] }}">
            @endif

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Search actions, modules, emails, IPs..." class="w-full border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                </div>

                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Action</label>
                    <select name="action" class="w-full border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        <option value="">All Actions</option>
                        @foreach($actionOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['action'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Show</label>
                    <select name="per_page" class="w-full border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        @foreach([10, 20, 50] as $size)
                            <option value="{{ $size }}" @selected((int) $perPage === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2 lg:col-span-2">
                    <button type="submit" class="audit-toolbar-btn inline-flex flex-1 items-center justify-center gap-2 bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        <i class="bi bi-funnel"></i> Search
                    </button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="audit-toolbar-btn inline-flex items-center justify-center gap-2 border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="px-4 py-4">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">All Users</div>
                    <div class="text-sm font-semibold text-slate-700">{{ $selectedUserLabel }} · {{ $totalLogs }} total entries</div>
                </div>
                <div class="text-xs font-medium text-slate-500">Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $totalLogs }} logs</div>
            </div>

            @if($logs->count() > 0)
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="audit-table min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left">Action</th>
                                    <th class="px-4 py-3 text-left">Details</th>
                                    <th class="px-4 py-3 text-left">User</th>
                                    <th class="px-4 py-3 text-left">IP Address</th>
                                    <th class="px-4 py-3 text-left">Browser</th>
                                    <th class="px-4 py-3 text-left">Timestamp</th>
                                    <th class="px-4 py-3 text-right">Open</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($logs as $log)
                                    <tr class="audit-row">
                                        <td class="px-4 py-4">
                                            <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-700">
                                                <i class="bi bi-activity text-blue-600"></i>
                                                <span>{{ $log->action_label }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="max-w-xl space-y-1">
                                                <div class="text-sm font-semibold text-slate-900">{{ $log->details ?? ($log->entity_name ?? 'Audit record') }}</div>
                                                <div class="text-xs text-slate-500">Model: {{ $log->entity_name ?? 'Record' }} @if(!empty($log->model_id)) · ID {{ $log->model_id }} @endif</div>
                                                @if(!empty($log->changes))
                                                    <div class="flex flex-wrap gap-2 pt-1">
                                                        <span class="audit-chip"><i class="bi bi-list-check"></i>{{ count($log->changes) }} changes</span>
                                                        @foreach(collect($log->changes)->take(2) as $change)
                                                            <span class="audit-chip"><i class="bi bi-dot"></i>{{ $change['label'] ?? $change['field'] }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="audit-avatar">
                                                    @if(!empty($log->user_photo_url))
                                                        <img src="{{ $log->user_photo_url }}" alt="{{ $log->user_name ?? 'System' }}">
                                                    @else
                                                        <span>{{ $log->user_initials }}</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-semibold text-slate-900">{{ $log->user_name ?? 'System' }}</div>
                                                    <div class="truncate text-xs text-slate-500">{{ $log->user_email ?? 'System generated' }}</div>
                                                    <div class="text-xs font-medium text-slate-400">{{ $log->user_role_label }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-semibold text-slate-900">{{ $log->ip_address ?: 'Hidden' }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                                <i class="bi {{ $log->browser_icon }} text-blue-600"></i>
                                                <span>{{ $log->browser_name }}</span>
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $log->browser_platform }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-semibold text-slate-900">{{ $log->timestamp_label ?? '—' }}</div>
                                            <div class="text-xs text-slate-500">{{ $log->timestamp_human ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                                <i class="bi bi-eye"></i>
                                                Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-4">
                    <x-pagination :paginator="$logs" />
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-3xl text-slate-400 shadow-sm">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">No audit logs found</h3>
                    <p class="mt-2 text-sm text-slate-500">Try a different search term or clear the filters to see all recorded actions.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

