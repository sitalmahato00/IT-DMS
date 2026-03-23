{{--
    Teacher Table Card Component
    Features:
    - Card container with shadow
    - Toolbar with entries selector, search, print, export
    - Enhanced row hover effects
    
    Usage:
    @include('teacher.components.teacher-table-card', [
        'title' => 'Students List',
        'showToolbar' => true,
        'entriesOptions' => [10, 25, 50],
        'currentEntries' => request('per_page', 10),
        'searchValue' => request('q', ''),
        'printRoute' => route('teacher.students.print'),
        'exportCsvRoute' => route('teacher.students.export'),
    ])
--}}

@php
    $title = $title ?? 'List';
    $showToolbar = $showToolbar ?? true;
    $entriesOptions = $entriesOptions ?? [10, 25, 50];
    $currentEntries = $currentEntries ?? 10;
    $searchValue = $searchValue ?? '';
    $printRoute = $printRoute ?? null;
    $exportCsvRoute = $exportCsvRoute ?? null;
    $content = $content ?? null;
@endphp

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
    {{-- Toolbar --}}
    @if($showToolbar)
    <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Left: Entries Selector --}}
            <div class="flex items-center gap-3">
                <label class="text-sm text-gray-600 dark:text-gray-400">
                    Show
                </label>
                <select 
                    id="entriesSelector" 
                    onchange="handleEntriesChange(this.value)"
                    class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >
                    @foreach($entriesOptions as $option)
                        <option value="{{ $option }}" {{ $currentEntries == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                <label class="text-sm text-gray-600 dark:text-gray-400">
                    entries
                </label>
            </div>

            {{-- Right: Search & Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Search Box --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        id="tableSearch"
                        value="{{ $searchValue }}"
                        placeholder="Search..."
                        class="pl-10 pr-4 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent w-48"
                    >
                </div>

                {{-- Print Button --}}
                @if($printRoute)
                    <a href="{{ $printRoute }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 border border-purple-300 dark:border-purple-600 bg-white dark:bg-purple-900/30 hover:bg-purple-50 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded-lg text-sm font-medium transition-colors" title="Print">
                        <i class="bi bi-printer"></i>
                        <span class="hidden sm:inline">Print</span>
                    </a>
                @endif

                {{-- Export Button --}}
                @if($exportCsvRoute)
                    <a href="{{ $exportCsvRoute }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-blue-300 dark:border-blue-600 bg-white dark:bg-blue-900/30 hover:bg-blue-50 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium transition-colors" title="Export CSV">
                        <i class="bi bi-download"></i>
                        <span class="hidden sm:inline">Export</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Table Content --}}
    <div class="overflow-x-auto">
        @if($content)
            {{ $content }}
        @else
            {{ $slot ?? '' }}
        @endif
    </div>

    {{-- Table Footer with Pagination --}}
    @if(isset($pagination) && $pagination)
    <div class="px-5 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span class="font-medium text-gray-900 dark:text-gray-200">{{ $pagination['start'] ?? 0 }}</span> 
                to <span class="font-medium text-gray-900 dark:text-gray-200">{{ $pagination['end'] ?? 0 }}</span> 
                of <span class="font-medium text-gray-900 dark:text-gray-200">{{ $pagination['total'] ?? 0 }}</span> results
            </div>
            <div>
                {{ $pagination['links'] ?? '' }}
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function handleEntriesChange(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    // Search functionality
    document.getElementById('tableSearch')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const url = new URL(window.location.href);
            url.searchParams.set('q', this.value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }
    });
</script>
