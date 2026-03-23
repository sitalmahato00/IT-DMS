{{--
    Teacher Pagination Component
    Features:
    - Styled pagination links
    - Page info display
    - Per page selector
    
    Usage:
    @include('teacher.components.teacher-pagination', [
        'paginator' => $students,
        'perPageOptions' => [10, 25, 50, 100]
    ])
--}}

@php
    $paginator = $paginator ?? null;
    $perPageOptions = $perPageOptions ?? [10, 25, 50, 100];
    $currentPerPage = $currentPerPage ?? request('per_page', 10);
@endphp

@if($paginator && $paginator->hasPages())
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-5 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
    {{-- Left: Page Info & Per Page --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 text-sm">
        <span class="text-gray-600 dark:text-gray-400">
            Showing
            <span class="font-medium text-gray-900 dark:text-gray-200">{{ $paginator->firstItem() ?? 0 }}</span>
            to
            <span class="font-medium text-gray-900 dark:text-gray-200">{{ $paginator->lastItem() ?? 0 }}</span>
            of
            <span class="font-medium text-gray-900 dark:text-gray-200">{{ $paginator->total() }}</span>
            results
        </span>
        
        <div class="flex items-center gap-2">
            <label class="text-gray-600 dark:text-gray-400">Per page:</label>
            <select 
                onchange="changePerPage(this.value)"
                class="px-2 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-200 rounded text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
            >
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Right: Pagination Links --}}
    <nav class="flex items-center gap-1">
        {{-- Previous Page --}}
        @if($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-gray-400 cursor-not-allowed rounded-lg">
                <i class="bi bi-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-gray-600 hover:text-red-600 hover:bg-red-50 dark:text-gray-400 dark:hover:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if($page == $paginator->currentPage())
                <span class="px-3 py-1.5 bg-red-600 text-white rounded-lg font-medium">
                    {{ $page }}
                </span>
            @elseif($page <= 2 || $page > $paginator->lastPage() - 2 || abs($page - $paginator->currentPage()) <= 1)
                <a href="{{ $url }}" class="px-3 py-1.5 text-gray-600 hover:text-red-600 hover:bg-red-50 dark:text-gray-400 dark:hover:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    {{ $page }}
                </a>
            @elseif($page == 3 || $page == $paginator->lastPage() - 2)
                <span class="px-2 py-1.5 text-gray-400">...</span>
            @endif
        @endforeach

        {{-- Next Page --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-gray-600 hover:text-red-600 hover:bg-red-50 dark:text-gray-400 dark:hover:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="px-3 py-1.5 text-gray-400 cursor-not-allowed rounded-lg">
                <i class="bi bi-chevron-right"></i>
            </span>
        @endif
    </nav>
</div>

<script>
    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
</script>
@elseif($paginator)
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-5 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
    <div class="text-sm text-gray-600 dark:text-gray-400">
        Showing
        <span class="font-medium text-gray-900 dark:text-gray-200">{{ $paginator->firstItem() ?? 0 }}</span>
        to
        <span class="font-medium text-gray-900 dark:text-gray-200">{{ $paginator->lastItem() ?? 0 }}</span>
        of
        <span class="font-medium text-gray-900 dark:text-gray-200">{{ $paginator->total() }}</span>
        results
    </div>
</div>
@endif
