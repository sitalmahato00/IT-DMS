<!-- Quick Actions -->
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">{{ __('Quick Actions') }}</h3>
    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Add Student -->
        <a href="{{ route('admin.students.create') }}" class="bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-person-plus text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('Add Student') }}</span>
        </a>

        <!-- Add Teacher -->
        <a href="{{ route('admin.teachers.create') }}" class="bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-person-badge text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('Add Teacher') }}</span>
        </a>

        <!-- Take Attendance -->
        <a href="{{ route('admin.attendance') }}" class="bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-calendar-check text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('Take Attendance') }}</span>
        </a>

        <!-- Create Notice -->
        <a href="{{ route('admin.notice-board.create') }}" class="bg-amber-500 dark:bg-amber-600 hover:bg-amber-600 dark:hover:bg-amber-500 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-megaphone text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('Create Notice') }}</span>
        </a>

        <!-- View Reports -->
        <a href="{{ route('admin.reports') }}" class="bg-purple-600 dark:bg-purple-700 hover:bg-purple-700 dark:hover:bg-purple-600 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-bar-chart-line text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('View Reports') }}</span>
        </a>

        <!-- Manage Semesters -->
        <a href="{{ route('admin.semesters') }}" class="bg-teal-600 dark:bg-teal-700 hover:bg-teal-700 dark:hover:bg-teal-600 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-calendar3 text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('Manage Semesters') }}</span>
        </a>

        <!-- Manage Electives -->
        <a href="{{ route('admin.electives') }}" class="bg-pink-600 dark:bg-pink-700 hover:bg-pink-700 dark:hover:bg-pink-600 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-shuffle text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('Manage Electives') }}</span>
        </a>

        <!-- Timetable -->
        <a href="{{ route('admin.timetable') }}" class="bg-cyan-600 dark:bg-cyan-700 hover:bg-cyan-700 dark:hover:bg-cyan-600 text-white rounded-lg p-4 flex flex-col items-center justify-center text-center transition transform hover:scale-105 shadow-sm">
            <i class="bi bi-grid-3x3-gap text-xl mb-1"></i>
            <span class="font-medium text-xs">{{ __('Timetable') }}</span>
        </a>
    </div>
</div>

