@php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $homeUrl = route('home');
    $onLanding = request()->routeIs('home') || request()->is('/');

    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology Department');

    $departmentShort = $department?->short_name ?: ($locale === 'ne' ? 'आईटी' : 'IT');
    $departmentLogo = $departmentLogoUrl ?? ($department?->getLogoUrl() ?? asset('images/default-logo.svg'));

    $brandTitle = trim((string) Str::of($departmentName)->replace([' Department', ' विभाग'], ''));
    if (blank($brandTitle)) {
        $brandTitle = $departmentShort;
    }
    if (blank($brandTitle)) {
        $brandTitle = $locale === 'ne' ? 'सूचना प्रविधि' : 'Information Technology';
    }

    $addressText = $department
        ? (($locale === 'ne' && !empty($department->address_nepali))
            ? $department->address_nepali
            : ($department->address ?: $department->location))
        : null;

    if (blank($addressText)) {
        $addressText = $locale === 'ne' ? 'काठमाडौं, नेपाल' : 'Kathmandu';
    }

    $sectionLink = function (string $section) use ($homeUrl, $onLanding): string {
        return $onLanding ? "#{$section}" : "{$homeUrl}#{$section}";
    };

    $aboutUrl = $onLanding
        ? $sectionLink('about')
        : ($department?->id
            ? route('department.about', ['id' => $department->id])
            : $sectionLink('about'));

    $navItems = [
        ['href' => $aboutUrl, 'label' => $locale === 'ne' ? 'बारेमा' : 'About'],
        ['href' => $sectionLink('programs'), 'label' => $locale === 'ne' ? 'कार्यक्रम' : 'Programs'],
        ['href' => $onLanding ? $sectionLink('curriculum') : route('subjects.index'), 'label' => $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum'],
        ['href' => $onLanding ? $sectionLink('faculty') : route('faculty.index'), 'label' => $locale === 'ne' ? 'शिक्षक' : 'Faculty'],
        ['href' => $onLanding ? $sectionLink('notices') : route('public.notices.index'), 'label' => $locale === 'ne' ? 'सूचना' : 'News & Events'],
        ['href' => $onLanding ? $sectionLink('resources') : route('public.resources.index'), 'label' => $locale === 'ne' ? 'स्रोत' : 'Resources'],
        ['href' => $sectionLink('contact'), 'label' => $locale === 'ne' ? 'सम्पर्क' : 'Contact'],
    ];

    $quickContactUrl = $sectionLink('contact');
    if (!empty($department?->phone)) {
        $quickContactUrl = 'tel:' . preg_replace('/\s+/', '', $department->phone);
    } elseif (!empty($department?->email)) {
        $quickContactUrl = 'mailto:' . $department->email;
    }

    $topBarContactText = !empty($department?->phone)
        ? $department->phone
        : (!empty($department?->email)
            ? $department->email
            : ($locale === 'ne' ? 'सम्पर्क विवरण छिट्टै' : 'Contact details coming soon'));

    $headerStats = is_array($stats ?? null) ? $stats : null;

    if (!$headerStats) {
        $headerStats = [
            'students' => 0,
            'teachers' => 0,
            'subjects' => 0,
        ];

        try {
            if (Schema::hasTable('students')) {
                $headerStats['students'] = \App\Models\Student::count();
            }

            if (Schema::hasTable('subjects')) {
                $headerStats['subjects'] = \App\Models\Subject::query()
                    ->where('status', 'active')
                    ->count();
            }

            if (Schema::hasTable('teachers')) {
                $departmentKey = $department?->short_name ?: $department?->name;

                $teachersQuery = \App\Models\Teacher::query()
                    ->where('status', 'active');

                if (!empty($departmentKey)) {
                    $teachersQuery->where(function ($query) use ($departmentKey) {
                        $query->where('department', $departmentKey)
                            ->orWhereHas('user', fn ($userQuery) => $userQuery->where('department', $departmentKey));
                    });
                }

                $headerStats['teachers'] = $teachersQuery->count();

                if ($headerStats['teachers'] === 0) {
                    $headerStats['teachers'] = \App\Models\Teacher::query()
                        ->where('status', 'active')
                        ->count();
                }
            }
        } catch (\Throwable $e) {
            // Keep the header resilient even when the database is not fully ready.
        }
    }
@endphp

<header x-data="{ mobileMenuOpen: false }" class="sticky top-0 isolate z-[9999] border-b border-red-100/80 bg-white shadow-[0_16px_38px_rgba(15,23,42,0.08)] dark:border-slate-800 dark:bg-slate-950">
    <div class="bg-red-600 text-white dark:bg-red-700">
        <div class="flex w-full items-center justify-between gap-4 px-4 py-2 text-[11px] font-semibold sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 shrink-0">
                    <path fill-rule="evenodd" d="M11.54 22.351a.75.75 0 0 0 .92 0c4.884-3.73 7.29-7.15 7.29-10.601a7.75 7.75 0 1 0-15.5 0c0 3.45 2.406 6.87 7.29 10.6ZM12 12.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                </svg>
                <span class="truncate">{{ $addressText }}</span>
            </div>

            <div class="hidden items-center gap-3 whitespace-nowrap md:flex">
                <span class="uppercase tracking-[0.16em] text-white/80">{{ $locale === 'ne' ? 'सम्पर्क' : 'Contact' }}</span>
                <a href="{{ $quickContactUrl }}" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-white transition hover:bg-white/15">
                    @if (!empty($department?->phone))
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path d="M6.648 2.25A2.25 2.25 0 0 0 4.5 4.5c0 9.113 7.387 16.5 16.5 16.5a2.25 2.25 0 0 0 2.25-2.148l.248-3.705a2.25 2.25 0 0 0-1.54-2.278l-3.405-1.136a2.25 2.25 0 0 0-2.56.94l-.724 1.086a18.11 18.11 0 0 1-5.028-5.028l1.086-.724a2.25 2.25 0 0 0 .94-2.56L11.13 2.54A2.25 2.25 0 0 0 8.852 1l-2.204.148Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path d="M1.5 6.75A2.25 2.25 0 0 1 3.75 4.5h16.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 17.25V6.75Zm2.03-.75a.75.75 0 0 0-.48 1.33l8.47 6.97a.75.75 0 0 0 .96 0l8.47-6.97A.75.75 0 0 0 20.47 6H3.53Z" />
                        </svg>
                    @endif
                    <span>{{ $topBarContactText }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="flex w-full items-center gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ $homeUrl }}" class="flex min-w-0 items-center gap-3">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white shadow-[0_14px_22px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700">
                <img src="{{ $departmentLogo }}" alt="{{ $departmentName }} logo" class="h-full w-full object-contain p-1.5" />
            </span>

            <span class="min-w-0 leading-tight">
                <span class="block text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">
                    {{ $locale === 'ne' ? 'आधिकारिक' : 'Official' }}
                </span>
                <span class="block truncate text-base font-bold text-slate-900 dark:text-white sm:text-lg">
                    {{ $brandTitle }}
                </span>
                @if (filled($departmentName) && $departmentName !== $brandTitle)
                    <span class="block truncate text-xs font-medium text-slate-500 dark:text-slate-400">
                        {{ $departmentName }}
                    </span>
                @endif
            </span>
        </a>

        <nav class="hidden flex-1 items-center justify-center gap-1 lg:flex">
            @foreach ($navItems as $item)
                <a href="{{ $item['href'] }}" class="rounded-full px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-red-50 hover:text-red-600 dark:text-slate-200 dark:hover:bg-slate-900 dark:hover:text-white">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-2">
            <a href="{{ $quickContactUrl }}" class="hidden items-center gap-2 rounded-full border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 lg:inline-flex dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-900 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M6.648 2.25A2.25 2.25 0 0 0 4.5 4.5c0 9.113 7.387 16.5 16.5 16.5a2.25 2.25 0 0 0 2.25-2.148l.248-3.705a2.25 2.25 0 0 0-1.54-2.278l-3.405-1.136a2.25 2.25 0 0 0-2.56.94l-.724 1.086a18.11 18.11 0 0 1-5.028-5.028l1.086-.724a2.25 2.25 0 0 0 .94-2.56L11.13 2.54A2.25 2.25 0 0 0 8.852 1l-2.204.148Z" clip-rule="evenodd" />
                </svg>
                <span>{{ $locale === 'ne' ? 'छिटो सम्पर्क' : 'Quick Contact' }}</span>
            </a>

            <form method="POST" action="{{ route('language.switch') }}" class="hidden sm:block">
                @csrf
                <label class="sr-only" for="headerLocale">{{ $locale === 'ne' ? 'भाषा' : 'Language' }}</label>
                <select id="headerLocale" name="locale" onchange="this.form.submit()" class="rounded-full border border-transparent bg-transparent px-2 py-2 text-sm font-semibold text-slate-700 focus:border-red-200 focus:outline-none focus:ring-2 focus:ring-red-100 dark:text-slate-200 dark:focus:border-slate-600 dark:focus:ring-slate-800">
                    @foreach (config('locales.supported') as $code => $label)
                        <option value="{{ $code }}" @selected($code === $locale)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>

            <button
                id="darkModeToggle"
                type="button"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white p-2 text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-900 dark:hover:text-white dark:focus:ring-slate-800"
                aria-label="{{ $locale === 'ne' ? 'डार्क मोड टगल' : 'Toggle dark mode' }}"
                aria-pressed="false"
            >
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                    <path d="M21 14.1A8.5 8.5 0 0 1 9.9 3 7 7 0 1 0 21 14.1Z" />
                </svg>
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="hidden h-5 w-5">
                    <path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12ZM12 2.75a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V3.5a.75.75 0 0 1 .75-.75Zm0 16a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V19.5a.75.75 0 0 1 .75-.75ZM2.75 12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H3.5a.75.75 0 0 1-.75-.75Zm16 0a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM5.28 5.28a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 1 1-1.06 1.06L5.28 6.34a.75.75 0 0 1 0-1.06Zm11.26 11.26a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 0 1-1.06 1.06l-1.06-1.06a.75.75 0 0 1 0-1.06ZM18.72 5.28a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 1 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0ZM7.46 16.54a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 0 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0Z" />
                </svg>
            </button>

            @auth
                <a href="{{ route('dashboard') }}" class="hidden rounded-full bg-red-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-red-700 sm:inline-flex">
                    {{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-full bg-red-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-red-700 sm:inline-flex">
                    {{ $locale === 'ne' ? 'लगइन' : 'Login' }}
                </a>
            @endauth

            <button
                type="button"
                @click="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen.toString()"
                aria-controls="public-mobile-menu"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 p-2 text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-900 dark:hover:text-white lg:hidden"
            >
                <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
    </div>

    <div
        id="public-mobile-menu"
        x-show="mobileMenuOpen"
        x-transition
        @click.outside="mobileMenuOpen = false"
        class="border-t border-red-100 bg-white px-4 py-4 shadow-2xl dark:border-slate-800 dark:bg-slate-950 sm:px-6 lg:hidden"
    >
        <div class="w-full space-y-3">
            <div class="grid gap-2">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" @click="mobileMenuOpen = false" class="rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-red-50 hover:text-red-600 dark:text-slate-200 dark:hover:bg-slate-900 dark:hover:text-white">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="space-y-3 border-t border-slate-100 pt-3 dark:border-slate-800">
                <a href="{{ $quickContactUrl }}" @click="mobileMenuOpen = false" class="flex items-center justify-center gap-2 rounded-full border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path fill-rule="evenodd" d="M6.648 2.25A2.25 2.25 0 0 0 4.5 4.5c0 9.113 7.387 16.5 16.5 16.5a2.25 2.25 0 0 0 2.25-2.148l.248-3.705a2.25 2.25 0 0 0-1.54-2.278l-3.405-1.136a2.25 2.25 0 0 0-2.56.94l-.724 1.086a18.11 18.11 0 0 1-5.028-5.028l1.086-.724a2.25 2.25 0 0 0 .94-2.56L11.13 2.54A2.25 2.25 0 0 0 8.852 1l-2.204.148Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $locale === 'ne' ? 'छिटो सम्पर्क' : 'Quick Contact' }}</span>
                </a>

                <form method="POST" action="{{ route('language.switch') }}" class="sm:hidden">
                    @csrf
                    <label class="sr-only" for="headerLocaleMobile">{{ $locale === 'ne' ? 'भाषा' : 'Language' }}</label>
                    <select id="headerLocaleMobile" name="locale" onchange="this.form.submit()" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 focus:border-red-200 focus:outline-none focus:ring-2 focus:ring-red-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:focus:border-slate-600 dark:focus:ring-slate-800">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" @selected($code === $locale)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>

                @auth
                    <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center justify-center rounded-full bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700">
                        {{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}
                    </a>
                @else
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="flex items-center justify-center rounded-full bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700">
                        {{ $locale === 'ne' ? 'लगइन' : 'Login' }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
