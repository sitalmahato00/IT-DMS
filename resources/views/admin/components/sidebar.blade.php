<!-- Sidebar -->
<aside id="sidebar" class="hidden lg:flex lg:w-56 bg-white shadow-lg flex-col fixed lg:static w-full left-0 z-30 overflow-y-auto">
    <!-- Logo Section - Desktop Only -->
    <div class="hidden lg:block p-3 border-b border-gray-200">
        <div class="flex items-center gap-2">
            <div class="bg-red-600 text-white p-1.5 rounded">
                <i class="bi bi-calendar-check text-sm"></i>
            </div>
            <div>
                <h1 class="font-bold text-sm text-gray-900">{{ __("IT-DMS") }}</h1>
                <p class="text-xs text-gray-500">{{ __("Admin Portal") }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-2 py-3">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm font-medium transition duration-300">
                    <i class="bi bi-speedometer2 text-xs"></i>
                    <span>{{ __("Dashboard") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.students') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.students') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-mortarboard text-xs"></i>
                    <span>{{ __("Students") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.teachers') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.teachers') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-book text-xs"></i>
                    <span>{{ __("Teachers") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.parents') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.parents') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-people text-xs"></i>
                    <span>{{ __("Parents") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.attendance') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.attendance') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-calendar-check text-xs"></i>
                    <span>{{ __("Attendance") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.assessment') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.assessment') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-file-earmark text-xs"></i>
                    <span>{{ __("Exam") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.courses') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.courses') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-book-half text-xs"></i>
                    <span>{{ __("Courses") }}</span>
                </a>
            </li>
           
            

            <li>
                <a href="{{ route('admin.study-material') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.study-material') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-journal-bookmark text-xs"></i>
                    <span>{{ __("Document") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.gallery') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.gallery') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-images text-xs"></i>
                    <span>{{ __("Gallery") }}</span>
                </a>
            </li>
             <li>
                <a href="{{ route('admin.reports') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.reports') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-bar-chart text-xs"></i>
                    <span>{{ __("Reports") }}</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.notifications') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.notifications') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-bell text-xs"></i>
                    <span>{{ __("Notifications") }}</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.notice-board') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.notice-board') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-megaphone text-xs"></i>
                    <span>{{ __("Notice Board") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-list-columns text-xs"></i>
                    <span>{{ __("Audit Logs") }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-3 py-2 lg:py-1.5 {{ request()->routeIs('admin.settings') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-red-50 hover:text-red-600' }} rounded text-sm transition duration-300">
                    <i class="bi bi-gear text-xs"></i>
                    <span>{{ __("Settings") }}</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Profile Section -->
    <a href="{{ route('profile.edit') }}" class="p-2 border-t border-gray-200 hover:bg-gray-50 transition-colors duration-500 block">
        <div class="flex items-center gap-2">
            @if(Auth::user() && Auth::user()->profile_photo_path)
                <img src="{{ Storage::disk('public')->url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-300 flex-shrink-0">
            @else
                <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-orange-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ __('Admin') }}</p>
            </div>
        </div>
    </a>
</aside>

<style>
    /* Mobile dropdown sizing */
    @media (max-width: 1023px) {
        #sidebar {
            top: 48px;
            height: calc(100vh - 48px);
            max-height: calc(100vh - 48px);
        }
    }
    
    /* Desktop fixed sizing */
    @media (min-width: 1024px) {
        #sidebar {
            position: static;
            height: 100vh;
            max-height: 100vh;
            top: auto;
        }
    }
</style>
