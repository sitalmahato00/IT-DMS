@php
    $isDashboard = request()->routeIs('admin.dashboard');

    $isStudents = request()->routeIs('admin.students*') && !request()->routeIs('admin.alumni-students*');
    $isTeachers = request()->routeIs('admin.teachers*');
    $isParents = request()->routeIs('admin.parents*');
    $isAlumni = request()->routeIs('admin.alumni-students*');

    $isSubjects = request()->routeIs('admin.courses*');
    $isSemesters = request()->routeIs('admin.semesters*');
    $isElectives = request()->routeIs('admin.electives*');

    $isTimetable = request()->routeIs('admin.timetable*');
    $isAttendance = request()->routeIs('admin.attendance*');
    $isAttendanceLab = request()->routeIs('admin.attendance.lab');
    $isAttendanceClass = $isAttendance && !$isAttendanceLab;

    $isDocuments = request()->routeIs('admin.study-material*');
    $isNotifications = request()->routeIs('admin.notifications*');
    $isGallery = request()->routeIs('admin.gallery*');

    $isNoticeBoard = request()->routeIs('admin.notice-board*');

    $isExams = request()->routeIs('admin.exam*');
    $isLedger = request()->routeIs('admin.marks*');
    $isMarksheet = request()->routeIs('admin.marksheet*');

    $isReports = request()->routeIs('admin.reports*');
    $isAuditLogs = request()->routeIs('admin.audit-logs*');
    $isDepartment = request()->routeIs('admin.department*');
    $isSettings = request()->routeIs('admin.settings*');

    $activeGroup = match (true) {
        $isStudents || $isTeachers || $isParents || $isAlumni => 'users',
        $isSemesters || $isSubjects || $isElectives => 'academics',
        $isTimetable => 'schedule',
        $isAttendance => 'attendance',
        $isDocuments || $isNotifications || $isGallery => 'resources',
        $isNoticeBoard => 'announcement',
        $isExams || $isLedger || $isMarksheet => 'exam',
        $isReports || $isAuditLogs || $isDepartment || $isSettings => 'system',
        default => null,
    };
@endphp

<aside id="sidebar" x-data="{
        activeGroup: @js($activeGroup),
        toggleGroup(group) {
            this.activeGroup = this.activeGroup === group ? null : group;
        }
    }"
    data-mobile-sidebar
    class="hidden lg:flex lg:w-60 bg-white text-slate-900 flex-col fixed lg:static w-64 max-w-[85vw] left-0 z-[50] overflow-y-auto overflow-x-hidden shadow-xl border-r border-red-500/40 top-0 h-screen lg:h-auto"
    style="top: 0; height: 100vh; width: 256px;">
    <div
        class="hidden lg:flex flex-col items-center justify-center px-4 py-2 min-h-[88px] bg-[#FF0037] text-white border-b border-red-500">
        @if($department && $department->logo_url)
            <img src="{{ $departmentLogoUrl }}" alt="{{ $department->name ?? 'College Logo' }}"
                class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg">
        @else
            <img src="/images/default-logo.svg" alt="Default Logo"
                class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg">
        @endif
        <div class="sidebar-brand-text mt-3 text-center">
            <h1 class="font-semibold text-xl sm:text-2xl leading-7 text-white block tracking-tight">
                {{ $department?->short_name ?? ($department?->name ?? __('Manmohan Memorial Polytechnic')) }}
            </h1>
            <p class="text-sm sm:text-[13px] leading-5 text-white/80">{{ __('IT Admin Portal') }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <div
            class="rounded-2xl border border-red-500/20 bg-white shadow-[0_15px_30px_rgba(0,0,0,0.08)] px-2 py-4 space-y-2">
            <p class="text-[9px] uppercase tracking-[0.6em] text-slate-500 px-1 mb-2 border-b border-red-500/30 pb-2">
                Navigation</p>

            <a href="{{ route('admin.dashboard') }}"
                class="nav-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ $isDashboard ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                <i class="bi bi-speedometer2 text-lg flex-shrink-0"></i>
                <span class="sidebar-label">{{ __('Dashboard') }}</span>
            </a>

            <button type="button" @click="toggleGroup('users')"
                :class="activeGroup === 'users' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-people-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Users') }}</span>
                </span>
                <i :class="activeGroup === 'users' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'users'" x-collapse>
                <a href="{{ route('admin.students') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isStudents ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-mortarboard text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Students') }}</span>
                </a>
                <a href="{{ route('admin.teachers') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isTeachers ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-person-badge text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Teachers') }}</span>
                </a>
                <a href="{{ route('admin.parents') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isParents ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-people text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Parents') }}</span>
                </a>
                <a href="{{ route('admin.alumni-students') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isAlumni ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-mortarboard-fill text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Alumni') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('academics')"
                :class="activeGroup === 'academics' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-book-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Academics') }}</span>
                </span>
                <i :class="activeGroup === 'academics' ? 'bi-chevron-down' : 'bi-chevron-right'"
                    class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'academics'" x-collapse>
                <a href="{{ route('admin.semesters') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isSemesters ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar3 text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Semesters') }}</span>
                </a>
                <a href="{{ route('admin.courses') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isSubjects ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-book-half text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Subjects') }}</span>
                </a>
                <a href="{{ route('admin.electives') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isElectives ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-shuffle text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Electives') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('schedule')"
                :class="activeGroup === 'schedule' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-calendar3 text-lg text-[#FF0037]"></i>
                    <span>{{ __('Schedule') }}</span>
                </span>
                <i :class="activeGroup === 'schedule' ? 'bi-chevron-down' : 'bi-chevron-right'"
                    class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'schedule'" x-collapse>
                <a href="{{ route('admin.timetable') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isTimetable ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-grid-3x3-gap text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Timetable') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('attendance')"
                :class="activeGroup === 'attendance' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-calendar-check text-lg text-[#FF0037]"></i>
                    <span>{{ __('Attendance') }}</span>
                </span>
                <i :class="activeGroup === 'attendance' ? 'bi-chevron-down' : 'bi-chevron-right'"
                    class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'attendance'" x-collapse>
                <a href="{{ route('admin.attendance') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isAttendanceClass ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar-check text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Class Attendance') }}</span>
                </a>
                <a href="{{ route('admin.attendance.lab') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isAttendanceLab ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-calendar-check text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Lab Attendance') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('resources')"
                :class="activeGroup === 'resources' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-folder-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Resources') }}</span>
                </span>
                <i :class="activeGroup === 'resources' ? 'bi-chevron-down' : 'bi-chevron-right'"
                    class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'resources'" x-collapse>
                <a href="{{ route('admin.study-material') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isDocuments ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-journal-bookmark text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Documents') }}</span>
                </a>
                <a href="{{ route('admin.notifications') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isNotifications ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-bell text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Notifications') }}</span>
                </a>
                <a href="{{ route('admin.gallery') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isGallery ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-images text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Gallery') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('announcement')"
                :class="activeGroup === 'announcement' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-megaphone-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('Announcement') }}</span>
                </span>
                <i :class="activeGroup === 'announcement' ? 'bi-chevron-down' : 'bi-chevron-right'"
                    class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'announcement'" x-collapse>
                <a href="{{ route('admin.notice-board') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isNoticeBoard ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-megaphone text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Notice Board') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('exam')"
                :class="activeGroup === 'exam' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-file-earmark-text text-lg text-[#FF0037]"></i>
                    <span>{{ __('Exam') }}</span>
                </span>
                <i :class="activeGroup === 'exam' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'exam'" x-collapse>
                <a href="{{ route('admin.exam') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isExams ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-file-earmark-text text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Exams') }}</span>
                </a>
                <a href="{{ route('admin.marks') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isLedger ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-clipboard-data text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Ledger') }}</span>
                </a>
                <a href="{{ route('admin.marksheet.search') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isMarksheet ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-file-earmark-ruled text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Marksheet') }}</span>
                </a>
            </div>

            <button type="button" @click="toggleGroup('system')"
                :class="activeGroup === 'system' ? 'text-red-600 bg-red-50 border border-red-100 shadow-sm' : 'text-slate-600 bg-white'"
                class="w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                <span class="sidebar-section-label flex items-center gap-1.5">
                    <i class="bi bi-gear-fill text-lg text-[#FF0037]"></i>
                    <span>{{ __('System') }}</span>
                </span>
                <i :class="activeGroup === 'system' ? 'bi-chevron-down' : 'bi-chevron-right'" class="bi text-base"></i>
            </button>
            <div class="collapsible-section space-y-0.5 overflow-hidden transition-all duration-300"
                x-show="activeGroup === 'system'" x-collapse>
                <a href="{{ route('admin.reports') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isReports ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-bar-chart text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Reports') }}</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isAuditLogs ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-journal-text text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Audit Logs') }}</span>
                </a>
                <a href="{{ route('admin.department.edit') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isDepartment ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-building text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Department') }}</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ $isSettings ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-red-500/10' }}">
                    <i class="bi bi-sliders text-base flex-shrink-0"></i>
                    <span class="sidebar-label">{{ __('Settings') }}</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="p-3 border-t border-slate-200 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2.5 px-3 py-2 text-slate-600 bg-slate-50 hover:bg-red-500/10 hover:text-[#FF0037] rounded-lg transition-all duration-150 text-sm">
                <i class="bi bi-box-arrow-right text-base flex-shrink-0"></i>
                <span class="sidebar-label">{{ __('Logout') }}</span>
            </button>
        </form>
    </div>
</aside>

<style>
    @media (max-width: 1023px) {
        #sidebar {
            width: min(16rem, 85vw) !important;
            max-width: 85vw !important;
            z-index: 60 !important;
        }
    }

    @media (min-width: 1024px) {
        #sidebar {
            position: static;
            height: 100vh;
            max-height: 100vh;
            top: auto;
            transform: none !important;
        }
    }
</style>

