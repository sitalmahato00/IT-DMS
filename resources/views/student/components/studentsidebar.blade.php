@php
    $isDashboard = request()->routeIs('student.dashboard');
    $isCourses = request()->routeIs('student.courses*');
    $isAttendance = request()->routeIs('student.attendance*');
    $isMarks = request()->routeIs('student.marks*');
    $isProfile = request()->routeIs('student.profile*');

    $activeGroup = match (true) {
        $isCourses => 'academics',
        $isAttendance => 'attendance',
        $isMarks => 'exam',
        $isProfile => 'system',
        default => null,
    };
@endphp

<aside
    id="sidebar"
    x-data="{
        activeGroup: @js($activeGroup),
        toggleGroup(group) {
            this.activeGroup = this.activeGroup === group ? null : group;
        }
    }"
    class="hidden lg:flex lg:w-60 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 flex-col fixed lg:static w-64 left-0 top-0 z-30 overflow-y-auto transition-all duration-300 shadow-xl border-r border-red-500/40"
>
    <div class="hidden lg:flex flex-col items-center justify-center px-4 py-2 min-h-[88px] bg-[#FF0037] text-white border-b border-red-500">
        @if($department && $department->logo_path)
            <img src="{{ $departmentLogoUrl }}" alt="{{ $department->name ?? 'Department Logo' }}" class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg">
        @else
            <img src="{{ asset('images/default-logo.svg') }}" alt="Default Logo" class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg">
        @endif

        <div class="sidebar-brand-text mt-3 text-center">
            <h1 class="font-semibold text-xl sm:text-2xl leading-7 text-white block tracking-tight">
                {{ $department?->short_name ?? ($department?->name ?? __('IT-DMS')) }}
            </h1>
            <p class="text-sm sm:text-[13px] leading-5 text-white/80">{{ __('Student Portal') }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <div class="rounded-2xl border border-red-500/20 bg-white dark:bg-slate-900 shadow-[0_15px_30px_rgba(0,0,0,0.08)] px-2 py-4 space-y-2">
            <p class="text-[9px] uppercase tracking-[0.6em] text-slate-500 dark:text-slate-400 px-1 mb-2 border-b border-red-500/30 pb-2">{{ __('Navigation') }}</p>

            <a href="{{ route('student.dashboard') }}" class="nav-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ $isDashboard ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                <i class="bi bi-speedometer2 text-lg flex-shrink-0"></i>
                <span class="sidebar-label">{{ __('Dashboard') }}</span>
            </a>

            <button type="button" @click="toggleGroup('academics')" :class="activeGroup === 'academics' ? 'text-red-600 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900 shadow-sm' : 'text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-book-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Academics') }}</span>
                </span>
                <i :class="activeGroup === 'academics' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'academics'" x-transition.opacity>
                <a href="{{ route('student.courses') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isCourses ? 'bg-red-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-journal-bookmark text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('My Courses') }}</span>
                </a>
            </div>

            <a href="{{ route('student.timetable') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ activeGroup === 'schedule' ? 'bg-red-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-[#FF0037] hover:bg-red-500/10' }}">
    <i class="bi bi-calendar3 text-lg flex-shrink-0 text-[#FF0037]"></i>
    <span class="sidebar-label">{{ __('Timetable') }}</span>
</a>

            <button type="button" @click="toggleGroup('attendance')" :class="activeGroup === 'attendance' ? 'text-red-600 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900 shadow-sm' : 'text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-calendar-check text-lg text-[#FF0037]"></i>
                    <span>{{ __('Attendance') }}</span>
                </span>
                <i :class="activeGroup === 'attendance' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'attendance'" x-transition.opacity>
                <a href="{{ route('student.attendance') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isAttendance ? 'bg-red-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar-check text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Attendance') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('resources')" :class="activeGroup === 'resources' ? 'text-red-600 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900 shadow-sm' : 'text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-folder-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Resources') }}</span>
                </span>
                <i :class="activeGroup === 'resources' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'resources'" x-transition.opacity>
                <div class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/60">
                    <span class="sidebar-label flex items-center gap-2">
                        <i class="bi bi-journal-richtext text-base flex-shrink-0"></i>
                        <span>{{ __('Study Materials') }}</span>
                    </span>
                    <span class="sidebar-label inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ __('Soon') }}</span>
                </div>
            </div>

            <button type="button" @click="toggleGroup('announcement')" :class="activeGroup === 'announcement' ? 'text-red-600 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900 shadow-sm' : 'text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-megaphone-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Announcements') }}</span>
                </span>
                <i :class="activeGroup === 'announcement' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'announcement'" x-transition.opacity>
                <div class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/60">
                    <span class="sidebar-label flex items-center gap-2">
                        <i class="bi bi-bell text-base flex-shrink-0"></i>
                        <span>{{ __('Notices') }}</span>
                    </span>
                    <span class="sidebar-label inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ __('Soon') }}</span>
                </div>
            </div>

            <button type="button" @click="toggleGroup('exam')" :class="activeGroup === 'exam' ? 'text-red-600 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900 shadow-sm' : 'text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-file-earmark-text text-lg text-[#FF0037]"></i>
                    <span>{{ __('Exam') }}</span>
                </span>
                <i :class="activeGroup === 'exam' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'exam'" x-transition.opacity>
                <a href="{{ route('student.marks') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isMarks ? 'bg-red-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-clipboard-data text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Marks / Results') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('system')" :class="activeGroup === 'system' ? 'text-red-600 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900 shadow-sm' : 'text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-gear-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('System') }}</span>
                </span>
                <i :class="activeGroup === 'system' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'system'" x-transition.opacity>
                <a href="{{ route('student.profile.edit') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isProfile ? 'bg-red-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-person-gear text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Profile Settings') }}</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="p-3 border-t border-slate-200 dark:border-slate-700 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-3 py-2 text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-red-500/10 hover:text-[#FF0037] rounded-lg transition-all duration-150 text-sm">
                <i class="bi bi-box-arrow-right text-base flex-shrink-0"></i>
                <span class="sidebar-label">{{ __('Logout') }}</span>
            </button>
        </form>
    </div>
</aside>

<style>
    #sidebar.sidebar-collapsed {
        width: 5rem !important;
        min-width: 5rem !important;
    }

    #sidebar.sidebar-collapsed .sidebar-label,
    #sidebar.sidebar-collapsed .sidebar-brand-text,
    #sidebar.sidebar-collapsed .sidebar-section-label span {
        display: none !important;
    }

    #sidebar.sidebar-collapsed .nav-link,
    #sidebar.sidebar-collapsed .collapsible-section > div {
        position: relative;
        padding: 0.35rem 0.5rem;
        border-radius: 999px;
        gap: 0 !important;
        width: 100%;
        justify-content: center;
    }

    #sidebar.sidebar-collapsed .nav-link i,
    #sidebar.sidebar-collapsed .collapsible-section > div i {
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

    #sidebar.sidebar-collapsed .collapsible-section {
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

    @media (min-width: 1024px) {
        #sidebar {
            position: static;
            height: 100vh;
            max-height: 100vh;
        }
    }
</style>
