<div class="px-3 py-2 border-t border-gray-200 flex items-center justify-between text-xs text-gray-600">
    <div class="flex items-center gap-2">
        <span>Show</span>
        <select id="perPageSelect" name="per_page" class="px-2 py-0.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
        <span>entries</span>
    </div>
    <div>
        <span>Showing {{ $exams->firstItem() ?? 0 }} to {{ $exams->lastItem() ?? 0 }} of {{ $exams->total() }} entries</span>
    </div>
</div>
    <div class="p-3" id="examsPaginationLinks">
        <x-pagination :paginator="$exams" />
    </div>

