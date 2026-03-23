 <!-- Sidebar -->
<aside id="sidebar" x-data="{ activeGroup: 'academic', toggleGroup(group){this.activeGroup = this.activeGroup === group ? null : group} }" class="hidden lg:flex lg:w-60 bg-white text-slate-900 flex-col fixed lg:static w-64 left-0 z-30 overflow-y-auto transition-all duration-300 shadow-xl border-r border-red-500/40">
    <!-- Brand Header - Logo kept as is -->
    <div class="hidden lg:flex flex-col items-center justify-center px-4 py-2 min-h-[88px] bg-[#FF0037] text-white border-b border-red-500">
        @if($college && $college->logo_path)
            <img src="{{ $collegeLogoUrl }}" alt="{{ $college->name ?? 'College Logo' }}" class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg">
        @else
            <img src="{{ asset('images/default-logo.svg') }}" alt="Default Logo" class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg">
        @endif
        <div class="mt-3 text-center">
            <h1 class="font-semibold text-xl sm:text-2xl leading-7 text-white block tracking-tight">
                {{ $college?->short_name ?? ($college?->name ?? __('IT-DMS')) }}
            </h1>
            <p class="text-sm sm:text-[13px] leading-5 text-white/80">{{ __("Faculty Portal") }}</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <div class="rounded-2xl border border-red-500/20 bg-white shadow-[0_15px_30px_rgba(0,0,0,0.08)] px-2 py-4 space-y-2">
            <p class="text-[9px] uppercase tracking-[0.6em] text-slate-500 px-1 mb-2 border-b border-red-500/30 pb-2">Navigation</p>

            <!-- Dashboard -->
            <a href="{{ route('teacher.dashboard') }}" class="nav-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.dashboard') ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                <i class="bi bi-speedometer2 text-lg flex-shrink-0"></i>
                <span class="sidebar-label">{{ __("Dashboard") }}</span>
            </a>

            <!-- ACADEMIC Section -->
            <button type="button" @click="toggleGroup('academic')" :class="activeGroup === 'academic' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="flex items-center gap-1.5">
                    <i class="bi bi-book-fill text-lg text-[#FF0037]"></i>
                    {{ __('ACADEMIC') }}
                </span>
                <i :class="activeGroup === 'academic' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'academic'" x-collapse>
                <a href="{{ route('teacher.subjects') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.subjects*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-book text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('My Subjects') }}</span>
                </a>
                <a href="{{ route('teacher.students') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.students*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-people text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('My Students') }}</span>
                </a>
                <a href="{{ route('teacher.attendance') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.attendance*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar-check text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Attendance') }}</span>
                </a>
                <a href="{{ route('teacher.marks') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.marks*') && !request()->routeIs('teacher.marksheet*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-clipboard-data text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Marks/Results') }}</span>
                </a>
                <a href="{{ route('teacher.marksheet.search') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.marksheet*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-file-earmark-ruled text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Marksheet Search') }}</span>
                </a>
            </div>

            <!-- RESOURCES Section -->
            <button type="button" @click="toggleGroup('resources')" :class="activeGroup === 'resources' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="flex items-center gap-1.5">
                    <i class="bi bi-folder-fill text-lg text-[#FF0037]"></i>
                    {{ __('RESOURCES') }}
                </span>
                <i :class="activeGroup === 'resources' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'resources'" x-collapse>
                <a href="{{ route('teacher.timetable') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.timetable*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar3 text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Timetable') }}</span>
                </a>
                <a href="{{ route('teacher.study-materials') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.study-materials*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-journal-bookmark text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Study Materials') }}</span>
                </a>
                <a href="{{ route('teacher.exams') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.exams*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-file-earmark-text text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Exams') }}</span>
                </a>
                <a href="{{ route('teacher.reports') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.reports*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-bar-chart text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Reports') }}</span>
                </a>
            </div>

            <!-- COMMUNICATION Section -->
            <button type="button" @click="toggleGroup('communication')" :class="activeGroup === 'communication' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="flex items-center gap-1.5">
                    <i class="bi bi-chat-dots-fill text-lg text-[#FF0037]"></i>
                    {{ __('COMMUNICATION') }}
                </span>
                <i :class="activeGroup === 'communication' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'communication'" x-collapse>
                <a href="{{ route('teacher.notices') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.notices*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-megaphone text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Notices') }}</span>
                </a>
                <a href="{{ route('teacher.notifications') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.notifications*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-bell text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Notifications') }}</span>
                </a>
            </div>

            <!-- SYSTEM Section -->
            <button type="button" @click="toggleGroup('system')" :class="activeGroup === 'system' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="flex items-center gap-1.5">
                    <i class="bi bi-gear-fill text-lg text-[#FF0037]"></i>
                    {{ __('SYSTEM') }}
                </span>
                <i :class="activeGroup === 'system' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'system'" x-collapse>
                <a href="{{ route('teacher.profile.edit') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('teacher.profile*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-person-gear text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Profile') }}</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Bottom Section - Logout Only (kept as is) -->
    <div class="p-3 border-t border-slate-200 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-3 py-2 text-slate-600 bg-slate-50 hover:bg-red-500/10 hover:text-[#FF0037] rounded-lg transition-all duration-150 text-sm">
                <i class="bi bi-box-arrow-right text-base flex-shrink-0"></i>
                <span class="sidebar-label">{{ __('Logout') }}</span>
            </button>
        </form>
    </div>
</aside>

<style>
    /* Sidebar collapsed state */
    #sidebar.sidebar-collapsed {
        width: 5rem !important;
        min-width: 5rem !important;
    }
    #sidebar.sidebar-collapsed .sidebar-label,
    #sidebar.sidebar-collapsed .sidebar-brand-text,
    #sidebar.sidebar-collapsed .sidebar-section-label span {
        display: none !important;
    }
    #sidebar.sidebar-collapsed .nav-link {
        position: relative;
        padding: 0.35rem 0;
        border-radius: 999px;
        gap: 0 !important;
        width: 100%;
        justify-content: center;
    }
    #sidebar.sidebar-collapsed .nav-link i {
        display: flex !important;
        margin: 0 auto;
        color: #FF0037 !important;
        font-size: 1.4rem;
        width: 2.25rem;
        height: 2.25rem;
        align-items: center;
        justify-content: center;
        visibility: visible !important;
        opacity: 1 !important;
    }

    #sidebar.sidebar-collapsed .collapsible-section,
    #sidebar.sidebar-collapsed .collapsible-section.section-collapsed {
        max-height: none;
        opacity: 1;
        padding-top: 0;
    }
    #sidebar.sidebar-collapsed .bi {
        visibility: visible !important;
        opacity: 1 !important;
    }
    #sidebar.sidebar-collapsed .nav-link.bg-red-600,
    #sidebar.sidebar-collapsed .nav-link.text-white {
        background-color: transparent !important;
        color: #FF0037 !important;
    }
    
    /* Mobile dropdown sizing */
    @media (max-width: 1023px) {
        #sidebar {
            top: 40px;
            height: calc(100vh - 40px);
            max-height: calc(100vh - 40px);
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

