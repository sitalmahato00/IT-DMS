@php
    $isDashboard = request()->routeIs('teacher.dashboard');
    $isSubjects = request()->routeIs('teacher.subjects*');
    $isStudents = request()->routeIs('teacher.students*');
    $isAttendance = request()->routeIs('teacher.attendance*');
    $isLabAttendance = request()->routeIs('teacher.attendance.lab*');
    $isClassAttendance = $isAttendance && !$isLabAttendance;
    $isMarks = request()->routeIs('teacher.marks*') && !request()->routeIs('teacher.marksheet*');
    $isMarksheet = request()->routeIs('teacher.marksheet*');
    $isTimetable = request()->routeIs('teacher.timetable*');
    $isStudyMaterials = request()->routeIs('teacher.study-materials*');
    $isExams = request()->routeIs('teacher.exams*');
    $isReports = request()->routeIs('teacher.reports*');
    $isNotices = request()->routeIs('teacher.notices*');
    $isNotifications = request()->routeIs('teacher.notifications*');
    $isProfile = request()->routeIs('teacher.profile*');

    $activeGroup = match (true) {
        $isSubjects || $isStudents || $isMarks || $isMarksheet => 'academics',
        $isTimetable => 'schedule',
        $isAttendance => 'attendance',
        $isStudyMaterials => 'resources',
        $isNotices || $isNotifications => 'announcement',
        $isExams => 'exam',
        $isReports || $isProfile => 'system',
        default => null,
    };
@endphp

<!-- Sidebar -->
<aside
    id="sidebar"
    x-data="{ activeGroup: @js($activeGroup), toggleGroup(group){ this.activeGroup = this.activeGroup === group ? null : group } }"
    class="hidden lg:flex lg:w-60 bg-white text-slate-900 flex-col fixed lg:static w-64 left-0 z-30 overflow-y-auto transition-all duration-300 shadow-xl border-r border-red-500/40"
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
            <p class="text-sm sm:text-[13px] leading-5 text-white/80">{{ __('Faculty Portal') }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <div class="rounded-2xl border border-red-500/20 bg-white shadow-[0_15px_30px_rgba(0,0,0,0.08)] px-2 py-4 space-y-2">
            <p class="text-[9px] uppercase tracking-[0.6em] text-slate-500 px-1 mb-2 border-b border-red-500/30 pb-2">Navigation</p>

            <a href="{{ route('teacher.dashboard') }}" class="nav-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ $isDashboard ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                <i class="bi bi-speedometer2 text-lg flex-shrink-0"></i>
                <span class="sidebar-label">{{ __('Dashboard') }}</span>
            </a>

            <button type="button" @click="toggleGroup('academics')" :class="activeGroup === 'academics' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-book-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Academics') }}</span>
                </span>
                <i :class="activeGroup === 'academics' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'academics'" x-collapse>
                <a href="{{ route('teacher.subjects') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isSubjects ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-book text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('My Subjects') }}</span>
                </a>
                <a href="{{ route('teacher.students') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isStudents ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-people text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('My Students') }}</span>
                </a>
                <a href="{{ route('teacher.marks') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isMarks ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-clipboard-data text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Marks/Results') }}</span>
                </a>
                <a href="{{ route('teacher.marksheet.search') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isMarksheet ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-file-earmark-ruled text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Marksheet Search') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('schedule')" :class="activeGroup === 'schedule' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-calendar3 text-lg text-[#FF0037]"></i>
                    <span>{{ __('Schedule') }}</span>
                </span>
                <i :class="activeGroup === 'schedule' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'schedule'" x-collapse>
                <a href="{{ route('teacher.timetable') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isTimetable ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar3 text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Timetable') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('attendance')" :class="activeGroup === 'attendance' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-calendar-check text-lg text-[#FF0037]"></i>
                    <span>{{ __('Attendance') }}</span>
                </span>
                <i :class="activeGroup === 'attendance' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'attendance'" x-collapse>
                <a href="{{ route('teacher.attendance') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isClassAttendance ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar-check text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Class Attendance') }}</span>
                </a>
                <a href="{{ route('teacher.attendance.lab') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isLabAttendance ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-beaker text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Lab Attendance') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('resources')" :class="activeGroup === 'resources' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-folder-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Resources') }}</span>
                </span>
                <i :class="activeGroup === 'resources' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'resources'" x-collapse>
                <a href="{{ route('teacher.study-materials') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isStudyMaterials ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-journal-bookmark text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Study Materials') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('announcement')" :class="activeGroup === 'announcement' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-megaphone-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Announcement') }}</span>
                </span>
                <i :class="activeGroup === 'announcement' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'announcement'" x-collapse>
                <a href="{{ route('teacher.notices') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isNotices ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-megaphone text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Notices') }}</span>
                </a>
                <a href="{{ route('teacher.notifications') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isNotifications ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-bell text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Notifications') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('exam')" :class="activeGroup === 'exam' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-file-earmark-text text-lg text-[#FF0037]"></i>
                    <span>{{ __('Exam') }}</span>
                </span>
                <i :class="activeGroup === 'exam' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'exam'" x-collapse>
                <a href="{{ route('teacher.exams') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isExams ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-file-earmark-text text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Exams') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('system')" :class="activeGroup === 'system' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'" class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-gear-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('System') }}</span>
                </span>
                <i :class="activeGroup === 'system' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300" x-show="activeGroup === 'system'" x-collapse>
                <a href="{{ route('teacher.reports') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isReports ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-bar-chart text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Reports') }}</span>
                </a>
                <a href="{{ route('teacher.profile.edit') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isProfile ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-person-gear text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Profile') }}</span>
                </a>
            </div>
        </div>
    </nav>

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

    @media (max-width: 1023px) {
        #sidebar {
            top: 40px;
            height: calc(100vh - 40px);
            max-height: calc(100vh - 40px);
        }
    }

    @media (min-width: 1024px) {
        #sidebar {
            position: static;
            height: 100vh;
            max-height: 100vh;
            top: auto;
        }
    }
</style>
