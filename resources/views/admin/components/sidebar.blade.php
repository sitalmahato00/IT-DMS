<!-- Sidebar -->
<aside id="sidebar" x-data="{
    activeGroup: null,
    toggleGroup(group) {
        this.activeGroup = this.activeGroup === group ? null : group;
    },
    isActive(group) {
        return this.activeGroup === group;
    },
    getActiveGroup() {
        const routeName = window.currentRoute || '';
        const groups = {
            'academic-management': ['admin.students', 'admin.alumni', 'admin.teachers', 'admin.parents'],
            'academic-structure': ['admin.semesters', 'admin.courses', 'admin.electives'],
            'academic-activities': ['admin.attendance', 'admin.exam', 'admin.marks', 'admin.marksheet'],
            'scheduling': ['admin.timetable'],
            'resources': ['admin.study-material', 'admin.notice-board', 'admin.notifications', 'admin.gallery'],
            'system': ['admin.reports', 'admin.audit-logs', 'admin.department', 'admin.settings'],
        };

        for (const [group, patterns] of Object.entries(groups)) {
            if (patterns.some(pattern => routeName.startsWith(pattern))) {
                return group;
            }
        }

        return null;
    },
    init() {
        this.activeGroup = this.getActiveGroup();
        window.currentRoute = '{{ request()->route()->getName() ?? "" }}';
    }
}" class="hidden lg:flex lg:w-60 bg-white text-slate-900 flex-col fixed lg:static w-64 left-0 z-30 overflow-y-auto transition-all duration-300 shadow-xl border-r border-[#D90033]/40">
    <!-- Brand Header -->
    <div class="hidden lg:flex flex-col items-center justify-center px-4 py-2 min-h-[88px] bg-[#FF0037] text-white border-b border-[#D90033]">
        @if($department && $department->logo_path)
            <img src="{{ $departmentLogoUrl }}" alt="{{ $department->name ?? 'Department Logo' }}" class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg bg-white p-1">
        @else
            <img src="{{ asset('images/default-logo.svg') }}" alt="Default Logo" class="h-16 w-16 sm:h-20 sm:w-20 object-contain rounded-full shadow-lg bg-white p-1">
        @endif
        <div class="mt-3 text-center">
            <h1 class="font-semibold text-xl sm:text-2xl leading-7 text-white block tracking-tight">
                {{ $department?->short_name ?? ($department?->name ?? __('IT-DMS')) }}
            </h1>
            <p class="text-sm sm:text-[13px] leading-5 text-white/80">IT Admin Portal</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <div class="rounded-2xl border border-[#D90033]/20 bg-white shadow-[0_15px_30px_rgba(0,0,0,0.08)] px-2 py-4 space-y-2">
            <p class="text-[9px] uppercase tracking-[0.6em] text-slate-500 px-1 mb-2 border-b border-[#D90033]/30 pb-2">Navigation</p>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                <i class="bi bi-speedometer2 text-lg flex-shrink-0"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>

            <!-- Group: Academic Management -->
            <div class="section-group">
                <div class="section-toggle flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-widest cursor-pointer hover:bg-red-50/50 rounded-lg transition-all duration-200" @click="toggleGroup('academic-management')" :class="{ 'section-open': isActive('academic-management') }">
                    <i class="bi bi-people-fill text-lg text-[#FF0037]"></i>
                    <span>Academic Management</span>
                    <i class="bi bi-chevron-down section-chevron ml-auto text-sm transition-transform duration-200" :class="{ 'rotate-180': isActive('academic-management') }"></i>
                </div>

                <div class="space-y-0.5 collapsible-section mt-1 px-1" x-show="isActive('academic-management')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" style="display:none;">
                    <a href="{{ route('admin.students') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.students*') && !request()->routeIs('admin.alumni*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-mortarboard text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Students</span>
                    </a>
                    <a href="{{ route('admin.alumni-students') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.alumni-students*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-mortarboard-fill text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Alumni</span>
                    </a>
                    <a href="{{ route('admin.teachers') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.teachers*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-person-badge text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Teachers</span>
                    </a>
                    <a href="{{ route('admin.parents') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.parents*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-people text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Parents</span>
                    </a>
                </div>
            </div>

            <!-- Group: Academic Structure -->
            <div class="section-group">
                <div class="section-toggle flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-widest cursor-pointer hover:bg-red-50/50 rounded-lg transition-all duration-200" @click="toggleGroup('academic-structure')" :class="{ 'section-open': isActive('academic-structure') }">
                    <i class="bi bi-calendar3 text-lg text-[#FF0037]"></i>
                    <span>Academic Structure</span>
                    <i class="bi bi-chevron-down section-chevron ml-auto text-sm transition-transform duration-200" :class="{ 'rotate-180': isActive('academic-structure') }"></i>
                </div>

                <div class="space-y-0.5 collapsible-section mt-1 px-1" x-show="isActive('academic-structure')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" style="display:none;">
                    <a href="{{ Route::has('admin.semesters') ? route('admin.semesters') : '#' }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.semesters*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-calendar3 text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Semesters</span>
                    </a>
                    <a href="{{ route('admin.courses') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.courses*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-book-half text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Subjects</span>
                    </a>
                    <a href="{{ Route::has('admin.electives') ? route('admin.electives') : '#' }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.electives*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-shuffle text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Electives</span>
                    </a>
                </div>
            </div>

            <!-- Group: Academic Activities -->
            <div class="section-group">
                <div class="section-toggle flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-widest cursor-pointer hover:bg-red-50/50 rounded-lg transition-all duration-200" @click="toggleGroup('academic-activities')" :class="{ 'section-open': isActive('academic-activities') }">
                    <i class="bi bi-calendar-check text-lg text-[#FF0037]"></i>
                    <span>Academic Activities</span>
                    <i class="bi bi-chevron-down section-chevron ml-auto text-sm transition-transform duration-200" :class="{ 'rotate-180': isActive('academic-activities') }"></i>
                </div>

                <div class="space-y-0.5 collapsible-section mt-1 px-1" x-show="isActive('academic-activities')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" style="display:none;">
                    <a href="{{ route('admin.attendance') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.attendance*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-calendar-check text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Attendance</span>
                    </a>
                    <a href="{{ route('admin.exam') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.exam*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-file-earmark-text text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Exams</span>
                    </a>
                    <a href="{{ route('admin.marks') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.marks*') && !request()->routeIs('admin.marksheet*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-clipboard-data text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Marks</span>
                    </a>
                    <a href="{{ route('admin.marksheet.search') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.marksheet*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-file-earmark-ruled text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Marksheet Search</span>
                    </a>
                </div>
            </div>

            <!-- Group: Scheduling -->
            <div class="section-group">
                <div class="section-toggle flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-widest cursor-pointer hover:bg-red-50/50 rounded-lg transition-all duration-200" @click="toggleGroup('scheduling')" :class="{ 'section-open': isActive('scheduling') }">
                    <i class="bi bi-grid-3x3-gap text-lg text-[#FF0037]"></i>
                    <span>Scheduling</span>
                    <i class="bi bi-chevron-down section-chevron ml-auto text-sm transition-transform duration-200" :class="{ 'rotate-180': isActive('scheduling') }"></i>
                </div>

                <div class="space-y-0.5 collapsible-section mt-1 px-1" x-show="isActive('scheduling')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" style="display:none;">
                    <a href="{{ Route::has('admin.timetable') ? route('admin.timetable') : '#' }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.timetable*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-grid-3x3-gap text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Timetable</span>
                    </a>
                </div>
            </div>

            <!-- Group: Resources -->
            <div class="section-group">
                <div class="section-toggle flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-widest cursor-pointer hover:bg-red-50/50 rounded-lg transition-all duration-200" @click="toggleGroup('resources')" :class="{ 'section-open': isActive('resources') }">
                    <i class="bi bi-journal-bookmark text-lg text-[#FF0037]"></i>
                    <span>Resources</span>
                    <i class="bi bi-chevron-down section-chevron ml-auto text-sm transition-transform duration-200" :class="{ 'rotate-180': isActive('resources') }"></i>
                </div>

                <div class="space-y-0.5 collapsible-section mt-1 px-1" x-show="isActive('resources')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" style="display:none;">
                    <a href="{{ route('admin.study-material') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.study-material*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-journal-bookmark text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Documents</span>
                    </a>
                    <a href="{{ route('admin.notice-board') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.notice-board*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-megaphone text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Notice Board</span>
                    </a>
                    <a href="{{ route('admin.notifications') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.notifications*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-bell text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Notifications</span>
                    </a>
                    <a href="{{ route('admin.gallery') }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.gallery*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-images text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Gallery</span>
                    </a>
                </div>
            </div>

            <!-- Group: System -->
            <div class="section-group">
                <div class="section-toggle flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-widest cursor-pointer hover:bg-red-50/50 rounded-lg transition-all duration-200" @click="toggleGroup('system')" :class="{ 'section-open': isActive('system') }">
                    <i class="bi bi-gear-fill text-lg text-[#FF0037]"></i>
                    <span>System</span>
                    <i class="bi bi-chevron-down section-chevron ml-auto text-sm transition-transform duration-200" :class="{ 'rotate-180': isActive('system') }"></i>
                </div>

                <div class="space-y-0.5 collapsible-section mt-1 px-1" x-show="isActive('system')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" style="display:none;">
                    <a href="{{ Route::has('admin.reports') ? route('admin.reports') : '#' }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.reports*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-bar-chart text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Reports</span>
                    </a>
                    <a href="{{ Route::has('admin.audit-logs') ? route('admin.audit-logs') : '#' }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.audit-logs*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-journal-text text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Audit Logs</span>
                    </a>
                    <a href="{{ Route::has('admin.department.edit') ? route('admin.department.edit') : '#' }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.department*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-building text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Department</span>
                    </a>
                    <a href="{{ Route::has('admin.settings') ? route('admin.settings') : '#' }}" class="nav-link flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.settings*') ? 'bg-red-600 text-white' : 'text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10' }}">
                        <i class="bi bi-sliders text-base flex-shrink-0"></i>
                        <span class="sidebar-label">Settings</span>
                    </a>
                </div>
            </div>

        </div>

    </nav>

    <!-- Sticky logout tray at bottom -->
    <div class="border-t border-[#D90033]/20 bg-white sticky bottom-0 z-10">
        <form method="POST" action="{{ route('logout') }}" class="p-3">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 text-slate-600 hover:text-[#FF0037] hover:bg-[#FF0037]/10">
                <i class="bi bi-box-arrow-right text-base flex-shrink-0"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

