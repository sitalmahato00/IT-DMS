{{-- Statistics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Materials</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" data-stat="total">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                <i class="bi bi-file-earmark-text text-blue-600 dark:text-blue-400"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Lecture Notes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" data-stat="notes">{{ $stats['notes'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center">
                <i class="bi bi-journal-text text-green-600 dark:text-green-400"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Assignments</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" data-stat="assignments">{{ $stats['assignments'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-full flex items-center justify-center">
                <i class="bi bi-pencil-square text-purple-600 dark:text-purple-400"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Assessments/Papers</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" data-stat="papers">{{ $stats['papers'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/40 rounded-full flex items-center justify-center">
                <i class="bi bi-file-earmark-ruled text-orange-600 dark:text-orange-400"></i>
            </div>
        </div>
    </div>
</div>

