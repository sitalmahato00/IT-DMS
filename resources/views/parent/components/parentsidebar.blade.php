@php
    $isDashboard = request()->routeIs('parent.dashboard');
    $isChildren = request()->routeIs('parent.children');
    $isAttendance = request()->routeIs('parent.attendance');
    $isResults = request()->routeIs('parent.results');
    $isCourses = request()->routeIs('parent.courses');
    $isNotices = request()->routeIs('parent.notices');
    $isCommunication = request()->routeIs('parent.communication');
    $isEvents = request()->routeIs('parent.events');
    $isSettings = request()->routeIs('parent.profile.*');
@endphp

<aside
    id="sidebar"
    class="hidden lg:flex lg:w-60 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 flex-col fixed lg:static w-64 left-0 top-0 z-30 overflow-y-auto transition-all duration-300 shadow-xl border-r border-red-500/40"
>
    <div class="hidden lg:flex flex-col items-center justify-center px-4 py-3 min-h-[88px] bg-red-600 dark:bg-red-800 text-white border-b border-red-500">
        @if($department && $department->logo_url)
            <img src="{{ $departmentLogoUrl }}" alt="{{ $department->name ?? 'Department Logo' }}" class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg bg-white/95 p-2">
        @else
            <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-full bg-white/95 text-red-700 flex items-center justify-center shadow-lg">
                <i class="bi bi-people-fill text-3xl"></i>
            </div>
        @endif

        <div class="sidebar-brand-text mt-3 text-center">
            <h1 class="font-semibold text-xl sm:text-2xl leading-7 text-white block tracking-tight">
                {{ $department?->short_name ?? ($department?->name ?? __('IT-DMS')) }}
            </h1>
            <p class="text-sm sm:text-[13px] leading-5 text-red-100">{{ __('Parent Portal') }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <div class="rounded-2xl border border-red-500/20 bg-white dark:bg-slate-900 shadow-[0_15px_30px_rgba(0,0,0,0.08)] px-2 py-4 space-y-1.5">
            <p class="text-[9px] uppercase tracking-[0.6em] text-slate-500 dark:text-slate-400 px-1 mb-2 border-b border-red-500/30 pb-2">{{ __('Navigation') }}</p>

            <a href="{{ route('parent.dashboard') }}" class="nav-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ $isDashboard ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-speedometer2 text-lg flex-shrink-0"></i>
                <span class="sidebar-label">{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('parent.children') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isChildren ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-people text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('My Children') }}</span>
            </a>

            <a href="{{ route('parent.attendance') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isAttendance ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-calendar-check text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('Attendance') }}</span>
            </a>

            <a href="{{ route('parent.results') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isResults ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-clipboard-data text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('Marks / Results') }}</span>
            </a>

            <a href="{{ route('parent.courses') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isCourses ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-journal-bookmark text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('Courses') }}</span>
            </a>

            <a href="{{ route('parent.notices') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isNotices ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-megaphone text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('Notices') }}</span>
            </a>

            <a href="{{ route('parent.communication') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isCommunication ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-chat-dots text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('Communication') }}</span>
            </a>

            <a href="{{ route('parent.events') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isEvents ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-calendar3 text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('Events & Schedule') }}</span>
            </a>

            <a href="{{ route('parent.profile.edit') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isSettings ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-red-700 hover:bg-red-500/10' }}">
                <i class="bi bi-gear text-lg flex-shrink-0 text-red-600 dark:text-red-300"></i>
                <span class="sidebar-label">{{ __('Settings') }}</span>
            </a>
        </div>
    </nav>

    <div class="p-3 border-t border-slate-200 dark:border-slate-700 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-3 py-2 text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-red-500/10 hover:text-red-700 rounded-lg transition-all duration-150 text-sm">
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
    #sidebar.sidebar-collapsed .sidebar-brand-text {
        display: none !important;
    }

    #sidebar.sidebar-collapsed .nav-link {
        position: relative;
        padding: 0.5rem;
        border-radius: 999px;
        gap: 0 !important;
        width: 100%;
        justify-content: center;
    }

    #sidebar.sidebar-collapsed .nav-link i {
        margin: 0 auto;
        font-size: 1.3rem;
        width: 2.25rem;
        height: 2.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #sidebar.sidebar-collapsed .nav-link.bg-red-600,
    #sidebar.sidebar-collapsed .nav-link.text-white {
        background-color: transparent !important;
        color: #dc2626 !important;
    }

    @media (min-width: 1024px) {
        #sidebar {
            position: static;
            height: 100vh;
            max-height: 100vh;
        }
    }
</style>
