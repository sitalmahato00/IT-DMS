@extends('admin.layouts.app')

@section('title', 'Audit Log Detail')

@section('content')
<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-bold mb-2">{{ $log->action }}</h3>
    <p class="text-sm text-gray-600">Module: {{ $log->module ?? 'General' }}</p>
    <p class="text-sm text-gray-600">Performed by: {{ $log->user_name ?? 'System' }} ({{ $log->user_email ?? '' }})</p>
    <p class="text-xs text-gray-500 mt-2">Timestamp: {{ \Carbon\Carbon::parse($log->timestamp)->toDateTimeString() }}</p>

    <div class="mt-4">
        <h4 class="text-sm font-semibold">Details</h4>
        <div class="mt-2 bg-gray-50 p-3 rounded text-xs text-gray-700">
            <p><strong>Model:</strong> {{ $log->model_type ? class_basename($log->model_type) : ($log->module ?? 'General') }} @if($log->model_id) (ID: {{ $log->model_id }}) @endif</p>
            <p><strong>Action:</strong> {{ $log->formatted_action ?? ucfirst($log->action ?? 'action') }}</p>
            <p><strong>User:</strong> {{ $log->user_name ?? 'System' }} @if(!empty($log->user_email)) ({{ $log->user_email }}) @endif</p>
            <p class="mt-2"><strong>IP:</strong> {{ $log->ip_address ?? 'N/A' }} · <strong>Agent:</strong> {{ $log->user_agent ?? 'N/A' }}</p>

            @php
                $old = $log->old_values ?? [];
                $new = $log->new_values ?? [];
            @endphp

            <div class="mt-3">
                <h5 class="text-xs font-semibold">Before</h5>
                @if(!empty($old) && is_array($old))
                    <ul class="mt-1 list-disc list-inside text-xs text-gray-700">
                        @foreach($old as $k => $v)
                            <li><strong>{{ $k }}:</strong> @if(is_array($v)) {{ json_encode($v) }} @else {{ $v }} @endif</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-gray-500 mt-1">No previous values.</p>
                @endif
            </div>

            <div class="mt-3">
                <h5 class="text-xs font-semibold">After</h5>
                @if(!empty($new) && is_array($new))
                    <ul class="mt-1 list-disc list-inside text-xs text-gray-700">
                        @foreach($new as $k => $v)
                            <li><strong>{{ $k }}:</strong> @if(is_array($v)) {{ json_encode($v) }} @else {{ $v }} @endif</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-gray-500 mt-1">No new values recorded.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium transition">
            <i class="bi bi-arrow-left mr-2"></i>Back to logs
        </a>
    </div>
</div>
@endsection
