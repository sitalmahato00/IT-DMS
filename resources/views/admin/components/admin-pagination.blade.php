{{--
    Admin Pagination Component
    Features:
    - Record counts and page navigation
    - Active page highlighted in red
    
    Usage:
    @include('admin.components.admin-pagination', [
        'paginator' => $students
    ])
    
    Or with custom values:
    @include('admin.components.admin-pagination', [
        'total' => 100,
        'perPage' => 10,
        'currentPage' => 1,
        'from' => 1,
        'to' => 10
    ])
--}}

@php
    // Handle both Laravel paginator and custom values
    if (isset($paginator) && ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator || $paginator instanceof \Illuminate\Contracts\Pagination\Paginator)) {
        $total = $paginator->total();
        $perPage = $paginator->perPage();
        $currentPage = $paginator->currentPage();
        $from = $paginator->firstItem() ?? 0;
        $to = $paginator->lastItem() ?? 0;
        $hasPages = $paginator->hasPages();
        $previousPageUrl = $paginator->previousPageUrl();
        $nextPageUrl = $paginator->nextPageUrl();
        $items = $paginator->items();
    } else {
        $total = $total ?? 0;
        $perPage = $perPage ?? 10;
        $currentPage = $currentPage ?? 1;
        $from = $from ?? 0;
        $to = $to ?? 0;
        $hasPages = false;
        $previousPageUrl = $previousPageUrl ?? null;
        $nextPageUrl = $nextPageUrl ?? null;
    }
    
    $lastPage = ceil($total / $perPage);
@endphp

@if($total > 0)
<div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {{-- Record Count --}}
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $from }}</span> to 
            <span class="font-semibold text-gray-900 dark:text-white">{{ $to }}</span> of 
            <span class="font-semibold text-gray-900 dark:text-white">{{ $total }}</span> entries
        </div>

        {{-- Pagination Links --}}
        @if($hasPages || $lastPage > 1)
        <nav class="flex flex-wrap items-center gap-1 sm:justify-end">
            {{-- Previous Button --}}
            @if($previousPageUrl)
                <a 
                    href="{{ $previousPageUrl }}" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition"
                >
                    <i class="bi bi-chevron-left"></i>
                </a>
            @else
                <span class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-gray-500 text-sm font-medium cursor-not-allowed">
                    <i class="bi bi-chevron-left"></i>
                </span>
            @endif

            {{-- Page Numbers --}}
            @php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
                
                // Adjust if at edges
                if ($currentPage <= 3) {
                    $endPage = min(5, $lastPage);
                }
                if ($currentPage >= $lastPage - 2) {
                    $startPage = max(1, $lastPage - 4);
                }
            @endphp

            @if($startPage > 1)
                <a 
                    href="{{ \Illuminate\Support\Facades\URL::current() }}?page=1" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition"
                >
                    1
                </a>
                @if($startPage > 2)
                    <span class="px-2 py-2 text-gray-400 dark:text-gray-500">...</span>
                @endif
            @endif

            @for($i = $startPage; $i <= $endPage; $i++)
                @if($i == $currentPage)
                    <span class="px-3 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold">
                        {{ $i }}
                    </span>
                @else
                    <a 
                        href="{{ \Illuminate\Support\Facades\URL::current() }}?page={{ $i }}" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition"
                    >
                        {{ $i }}
                    </a>
                @endif
            @endfor

            @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                    <span class="px-2 py-2 text-gray-400 dark:text-gray-500">...</span>
                @endif
                <a 
                    href="{{ \Illuminate\Support\Facades\URL::current() }}?page={{ $lastPage }}" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition"
                >
                    {{ $lastPage }}
                </a>
            @endif

            {{-- Next Button --}}
            @if($nextPageUrl)
                <a 
                    href="{{ $nextPageUrl }}" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition"
                >
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-gray-500 text-sm font-medium cursor-not-allowed">
                    <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </nav>
        @endif
    </div>
</div>
@endif

