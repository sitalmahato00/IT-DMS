@extends('admin.layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-4">
    <!-- Filters & Actions -->
    <form method="GET" class="flex items-center justify-between gap-3 flex-wrap">
        <!-- Filter Inputs Row -->
        <div class="flex-1 min-w-64">
            <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Actions, module, user..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
        </div>

        <!-- Action Buttons Row -->
        <div class="flex gap-2 items-center pt-5">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                <i class="bi bi-funnel"></i>
                <span>Search</span>
            </button>
            <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md text-sm font-medium transition shadow-sm">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Reset</span>
            </a>
        </div>
    </form>

    <div class="bg-white rounded-lg border border-gray-200 p-4">

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
                <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="block p-3 rounded hover:bg-gray-50 border border-gray-100">
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

