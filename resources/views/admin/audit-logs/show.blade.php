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
        <pre class="mt-2 bg-gray-50 p-3 rounded text-xs text-gray-700">{{ json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-red-600 hover:text-red-700">Back to logs</a>
    </div>
</div>
@endsection
