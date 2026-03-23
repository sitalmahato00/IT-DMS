<!-- Student Sidebar -->
<aside id="sidebar" class="hidden lg:flex lg:w-56 bg-white dark:bg-gray-800 shadow-lg flex-col fixed lg:static w-64 lg:w-56 left-0 z-30 overflow-y-auto">
    <div class="hidden lg:flex px-4 py-2 min-h-[76px] border-b border-blue-600 bg-blue-600 dark:bg-blue-800 text-white items-center">
        <div class="flex items-center gap-3 w-full min-w-0">
            @if($department && $department->logo_path)
                <img src="{{ $departmentLogoUrl }}" alt="{{ $department->name ?? 'Department Logo' }}" class="h-12 w-12 object-contain bg-white rounded p-1 flex-shrink-0">
            @else
                <div class="h-12 w-12 bg-white text-blue-600 p-1.5 rounded shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-mortarboard text-sm"></i>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="font-semibold text-xs leading-4 text-white line-clamp-2 break-words">
                    {{ $department?->short_name ?? ($department?->name ?? __('IT-DMS')) }}
                </h1>
                <p class="text-[11px] leading-4 text-blue-100">{{ __("Student Portal") }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-2 py-3">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('student.dashboard') ?? '#' }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('student.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600' }} rounded text-sm font-medium transition duration-300">
                    <i class="bi bi-speedometer2 text-xs"></i>
                    <span>{{ __("Dashboard") }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-file-earmark text-xs"></i>
                    <span>{{ __("My Courses") }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-calendar-check text-xs"></i>
                    <span>{{ __("Attendance") }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-graph-up text-xs"></i>
                    <span>{{ __("Marks/Results") }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-journal-bookmark text-xs"></i>
                    <span>{{ __("Study Materials") }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-bell text-xs"></i>
                    <span>{{ __("Notices") }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-file-text text-xs"></i>
                    <span>{{ __("Assignments") }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-person-check text-xs"></i>
                    <span>{{ __("My Teachers") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.profile.edit') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('student.profile.*') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-gear text-xs"></i>
                    <span>{{ __('Settings') }}</span>
                </a>
            </li>
        </ul>

        <!-- Bottom Section - Logout Only -->
        <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-3">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 lg:py-1.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 rounded text-sm transition duration-300">
                    <i class="bi bi-box-arrow-right text-xs"></i>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </nav>
</aside>

<!-- Mobile Sidebar Toggle (visible on mobile) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.querySelector('[data-toggle-sidebar]');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }
    });
</script>
