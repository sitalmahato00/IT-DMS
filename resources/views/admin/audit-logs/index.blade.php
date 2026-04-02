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

    html.audit-logs-ui-enhanced:not(.dark) .audit-filter-panel,
    html.audit-logs-ui-enhanced:not(.dark) .audit-list-panel,
    html.audit-logs-ui-enhanced:not(.dark) .audit-detail-panel {
        border-radius: 28px;
        border-color: rgba(226, 232, 240, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 250, 251, 0.98));
        box-shadow: 0 24px 52px -40px rgba(15, 23, 42, 0.22);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-log-item {
        border-radius: 22px;
        border-color: rgba(226, 232, 240, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.97));
        box-shadow: 0 18px 34px -30px rgba(15, 23, 42, 0.18);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-log-item:hover {
        background: linear-gradient(90deg, rgba(248, 250, 252, 0.96), rgba(255, 255, 255, 0.98));
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-toolbar-btn {
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.3);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-logs-page input {
        border-radius: 16px;
        border-color: #d6dde8;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    html.audit-logs-ui-enhanced:not(.dark) .audit-logs-page input:focus {
        border-color: #64748b;
        box-shadow: 0 0 0 4px rgba(100, 116, 139, 0.12);
    }
</style>
@endsection

@section('content')
<div class="audit-logs-page space-y-6">
    <!-- Filters & Actions -->
    <form method="GET" class="audit-filter-panel flex items-center justify-between gap-3 flex-wrap border border-gray-200 px-4 py-4">
        <!-- Filter Inputs Row -->
        <div class="flex-1 min-w-64">
            <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Actions, module, user..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
        </div>

        <!-- Action Buttons Row -->
        <div class="flex gap-2 items-center pt-5">
            <button type="submit" class="audit-toolbar-btn inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                <i class="bi bi-funnel"></i>
                <span>Search</span>
            </button>
            <a href="{{ route('admin.audit-logs.index') }}" class="audit-toolbar-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md text-sm font-medium transition shadow-sm">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Reset</span>
            </a>
        </div>
    </form>

    <div class="audit-list-panel bg-white rounded-lg border border-gray-200 p-4">

    @if($logs->count() > 0)
        <div class="space-y-2">
            @foreach($logs as $log)
                @php
                    $old = $log->old_values ?? [];
                    $new = $log->new_values ?? [];
                    $nameKeys = ['name','title','subject_name','course_name','exam_name','file_name','gallery_title'];
                    $displayName = null;
                    foreach($nameKeys as $k) {
                        if(isset($new[$k]) && $new[$k]) { $displayName = $new[$k]; break; }
                        if(isset($old[$k]) && $old[$k]) { $displayName = $old[$k]; break; }
                    }
                    $entity = $log->model_type ? class_basename($log->model_type) : ($log->module ?? 'Record');
                @endphp
                <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="audit-log-item block p-3 rounded hover:bg-gray-50 border border-gray-100 transition">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-600">{{ strtoupper(substr($entity, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $log->formatted_action ?? ucfirst($log->action ?? 'action') }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($log->timestamp)->format('Y-m-d H:i') }}
                                    <span class="text-gray-400">({{ \Carbon\Carbon::parse($log->timestamp)->diffForHumans() }})</span>
                                </p>
                            </div>
                            @php $detailsStr = $log->details ?? null; @endphp
                            @if(!empty($detailsStr))
                                <p class="text-xs text-gray-600">{{ Str::limit($detailsStr, 80) }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $log->user_name ?? 'System' }} · {{ $entity }}</p>
                            @else
                                <p class="text-xs text-gray-600">@if($displayName) {{ $displayName }} · @endif {{ $log->user_name ?? 'System' }} · {{ $entity }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            <x-pagination :paginator="$logs" />
        </div>
    @else
        <p class="text-sm text-gray-500">No audit logs found.</p>
    @endif
</div>
@endsection
