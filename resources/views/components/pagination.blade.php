@props(['paginator' => null])

@if($paginator && method_exists($paginator, 'links'))
<div class="px-3 py-2 border-t border-gray-200 bg-gray-50">
    <div class="flex items-center justify-between text-xs text-gray-600">
        <div>
            Showing {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
        </div>
        <div>
            {{ $paginator->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endif

