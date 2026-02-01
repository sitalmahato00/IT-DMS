@extends('admin.layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="bg-white rounded-lg border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-900">Audit Logs</h3>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search actions, module, user" class="px-2 py-1 border rounded text-sm" />
            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm">Search</button>
        </form>
    </div>

    @if($logs->count() > 0)
        <div class="space-y-2">
            @foreach($logs as $log)
                <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="block p-3 rounded hover:bg-gray-50 border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-600">{{ strtoupper(substr($log->module ?? 'G', 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $log->action }}</p>
                            <p class="text-xs text-gray-600">{{ $log->user_name ?? 'System' }} · {{ $log->module ?? 'General' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($log->timestamp)->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @else
        <p class="text-sm text-gray-500">No audit logs found.</p>
    @endif
</div>
@endsection
