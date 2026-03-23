{{--
    Admin Table Card Component
    Features:
    - Card container with shadow
    - Toolbar with entries selector, search, print, export
    - Enhanced row hover effects
    
    Usage:
    @include('admin.components.admin-table-card', [
        'title' => 'Students List',
        'showToolbar' => true,
        'entriesOptions' => [10, 25, 50],
        'currentEntries' => request('per_page', 10),
        'searchValue' => request('q', ''),
        'printRoute' => route('students.print-list'),
        'exportCsvRoute' => route('admin.students.export'),
        'exportExcelRoute' => route('admin.students.export-excel'),
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
    $exportExcelRoute = $exportExcelRoute ?? null;
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

            {{-- Right: Search, Print, Export --}}
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Search Box --}}
                <div class="relative">
                    <input 
                        type="text" 
                        id="tableSearch"
                        value="{{ $searchValue }}"
                        placeholder="Search..."
                        onkeyup="handleTableSearch(event)"
                        class="w-full sm:w-48 pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>

                {{-- Print Button --}}
                @if($printRoute)
                <button 
                    onclick="window.open('{{ $printRoute }}', '_blank')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 border border-purple-300 dark:border-purple-600 bg-white dark:bg-purple-900/30 hover:bg-purple-50 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded-lg text-sm font-medium transition"
                    title="Print"
                >
                    <i class="bi bi-printer"></i>
                    <span class="hidden sm:inline">Print</span>
                </button>
                @endif

                {{-- Export CSV Button --}}
                @if($exportCsvRoute)
                <button 
                    onclick="exportTable('csv')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 border border-blue-300 dark:border-blue-600 bg-white dark:bg-blue-900/30 hover:bg-blue-50 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium transition"
                    title="Export CSV"
                >
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span class="hidden sm:inline">CSV</span>
                </button>
                @endif

                {{-- Export Excel Button --}}
                @if($exportExcelRoute)
                <button 
                    onclick="exportTable('excel')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 border border-green-300 dark:border-green-600 bg-white dark:bg-green-900/30 hover:bg-green-50 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium transition"
                    title="Export Excel"
                >
                    <i class="bi bi-file-earmark-excel"></i>
                    <span class="hidden sm:inline">Excel</span>
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Content for table --}}
    @if($content)
        {!! $content !!}
    @endif
</div>

@push('scripts')
<script>
    function handleEntriesChange(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function handleTableSearch(event) {
        if (event.key === 'Enter' || event.target.value === '') {
            const url = new URL(window.location.href);
            if (event.target.value) {
                url.searchParams.set('q', event.target.value);
            } else {
                url.searchParams.delete('q');
            }
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }
    }

    function exportTable(type) {
        const url = new URL(window.location.href);
        url.searchParams.set('export', type);
        window.location.href = url.toString();
    }
</script>
@endpush
