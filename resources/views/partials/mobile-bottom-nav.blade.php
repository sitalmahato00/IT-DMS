@php
    $role = $role ?? 'public';
    $items = [];

    if ($role === 'admin') {
        $items = [
            ['label' => __('Home'), 'icon' => 'bi-speedometer2', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
            ['label' => __('Users'), 'icon' => 'bi-people', 'href' => route('admin.students'), 'active' => request()->routeIs('admin.students*') || request()->routeIs('admin.teachers*') || request()->routeIs('admin.parents*') || request()->routeIs('admin.alumni-students*')],
            ['label' => __('Attend'), 'icon' => 'bi-calendar-check', 'href' => route('admin.attendance'), 'active' => request()->routeIs('admin.attendance*')],
            ['label' => __('Exams'), 'icon' => 'bi-file-earmark-text', 'href' => route('admin.exam'), 'active' => request()->routeIs('admin.exam*') || request()->routeIs('admin.marks*') || request()->routeIs('admin.marksheet*')],
            ['label' => __('Reports'), 'icon' => 'bi-bar-chart', 'href' => route('admin.reports'), 'active' => request()->routeIs('admin.reports*') || request()->routeIs('admin.audit-logs*') || request()->routeIs('admin.department*') || request()->routeIs('admin.settings*')],
        ];
    } elseif ($role === 'teacher') {
        $items = [
            ['label' => __('Home'), 'icon' => 'bi-speedometer2', 'href' => route('teacher.dashboard'), 'active' => request()->routeIs('teacher.dashboard')],
            ['label' => __('Students'), 'icon' => 'bi-people', 'href' => route('teacher.students'), 'active' => request()->routeIs('teacher.students*') || request()->routeIs('teacher.subjects*')],
            ['label' => __('Attend'), 'icon' => 'bi-calendar-check', 'href' => route('teacher.attendance'), 'active' => request()->routeIs('teacher.attendance*')],
            ['label' => __('Marks'), 'icon' => 'bi-clipboard-data', 'href' => route('teacher.marks'), 'active' => request()->routeIs('teacher.marks*') || request()->routeIs('teacher.marksheet*') || request()->routeIs('teacher.exams*')],
            ['label' => __('Notices'), 'icon' => 'bi-bell', 'href' => route('teacher.notices'), 'active' => request()->routeIs('teacher.notices*') || request()->routeIs('teacher.notifications*')],
        ];
    } elseif ($role === 'student') {
        $items = [
            ['label' => __('Home'), 'icon' => 'bi-speedometer2', 'href' => route('student.dashboard'), 'active' => request()->routeIs('student.dashboard')],
            ['label' => __('Courses'), 'icon' => 'bi-journal-bookmark', 'href' => route('student.courses'), 'active' => request()->routeIs('student.courses*') || request()->routeIs('student.timetable*')],
            ['label' => __('Attend'), 'icon' => 'bi-calendar-check', 'href' => route('student.attendance'), 'active' => request()->routeIs('student.attendance*')],
            ['label' => __('Results'), 'icon' => 'bi-clipboard-data', 'href' => route('student.marks'), 'active' => request()->routeIs('student.marks*') || request()->routeIs('student.marksheet*') || request()->routeIs('student.exams*')],
            ['label' => __('Notices'), 'icon' => 'bi-bell', 'href' => route('student.notices'), 'active' => request()->routeIs('student.notices*') || request()->routeIs('student.study-materials*')],
        ];
    } elseif ($role === 'parent') {
        $items = [
            ['label' => __('Home'), 'icon' => 'bi-speedometer2', 'href' => route('parent.dashboard'), 'active' => request()->routeIs('parent.dashboard')],
            ['label' => __('Children'), 'icon' => 'bi-people', 'href' => route('parent.children'), 'active' => request()->routeIs('parent.children*') || request()->routeIs('parent.courses*')],
            ['label' => __('Attend'), 'icon' => 'bi-calendar-check', 'href' => route('parent.attendance'), 'active' => request()->routeIs('parent.attendance*')],
            ['label' => __('Results'), 'icon' => 'bi-clipboard-data', 'href' => route('parent.results'), 'active' => request()->routeIs('parent.results*') || request()->routeIs('parent.exams*')],
            ['label' => __('Notices'), 'icon' => 'bi-bell', 'href' => route('parent.notices'), 'active' => request()->routeIs('parent.notices*') || request()->routeIs('parent.communication*') || request()->routeIs('parent.events*')],
        ];
    } else {
        $items = [
            ['label' => __('Home'), 'icon' => 'bi-house-door', 'href' => route('home'), 'active' => request()->routeIs('home') || request()->routeIs('department.about')],
            ['label' => __('Faculty'), 'icon' => 'bi-people', 'href' => route('faculty.index'), 'active' => request()->routeIs('faculty.index')],
            ['label' => __('Notices'), 'icon' => 'bi-bell', 'href' => route('public.notices.index'), 'active' => request()->routeIs('public.notices.index') || request()->routeIs('notices.show')],
            ['label' => __('Resources'), 'icon' => 'bi-journal-bookmark', 'href' => route('public.resources.index'), 'active' => request()->routeIs('public.resources.index') || request()->routeIs('gallery.index') || request()->routeIs('subjects.index')],
            ['label' => auth()->check() ? __('Dashboard') : __('Login'), 'icon' => auth()->check() ? 'bi-speedometer2' : 'bi-box-arrow-in-right', 'href' => auth()->check() ? route('dashboard') : route('login'), 'active' => request()->routeIs('login') || request()->routeIs('register')],
        ];
    }
@endphp

<nav class="mobile-bottom-nav lg:hidden" aria-label="{{ __('Mobile navigation') }}">
    <div class="mobile-bottom-nav__inner {{ $role !== 'public' ? 'mobile-bottom-nav__inner--with-menu' : '' }}">
        @foreach($items as $item)
            <a
                href="{{ $item['href'] }}"
                class="mobile-nav-link {{ $item['active'] ? 'is-active' : '' }}"
                data-mobile-nav-link
                aria-current="{{ $item['active'] ? 'page' : 'false' }}"
            >
                <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        @if($role !== 'public')
            <button
                type="button"
                class="mobile-nav-link mobile-nav-link--button"
                data-mobile-drawer-toggle
                aria-label="{{ __('Open menu') }}"
            >
                <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                <span>{{ __('Menu') }}</span>
            </button>
        @endif
    </div>
</nav>

