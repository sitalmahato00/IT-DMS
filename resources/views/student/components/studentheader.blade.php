@php
    $resolvedTitle = trim($__env->yieldContent('title', $studentHeaderTitle ?? __('Dashboard')));
    $resolvedSubtitle = trim($__env->yieldContent('subtitle', __('Student Portal')));
    $user = auth()->user();
    $photoPath = $user?->profile_photo_url ?: $user?->student?->profile_photo_url;
    $hasProfilePhoto = !empty($photoPath);

    $__notifList = $user ? $user->notifications()->latest()->take(6)->get() : collect();
    $__unreadCount = $user ? $user->unreadNotifications()->count() : 0;
@endphp

<header class="bg-[#FF0037] dark:bg-[#FF0037] shadow-sm border-b border-red-500 text-white">
    <div class="px-6 py-0 h-16">
        <div class="flex items-center justify-between gap-4 h-full">
            <div class="flex min-w-0 flex-1 items-center gap-3">
                <button id="sidebarToggle" class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg bg-white text-[#FF0037] border border-white/80 shadow-sm hover:bg-gray-100 transition flex-shrink-0" title="{{ __('Toggle Sidebar') }}">
                    <i class="bi bi-list text-lg"></i>
                </button>

                <button id="desktopSidebarToggle" class="hidden lg:flex items-center justify-center w-9 h-9 rounded-lg hover:bg-red-500 transition flex-shrink-0" title="{{ __('Toggle Sidebar') }}">
                    <i class="bi bi-layout-sidebar text-white/80 text-lg"></i>
                </button>

                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-white truncate leading-tight">{{ $resolvedTitle }}</h2>
                    <p class="text-xs text-white/70 truncate">{{ $resolvedSubtitle }}</p>
                </div>
            </div>


            <div class="hidden md:flex flex-1 justify-center">
            </div>


            <div class="flex items-center gap-2 flex-shrink-0 h-full">
                <div class="hidden md:block">
                    <label for="locale-select" class="sr-only">{{ __('Language') }}</label>
                    <select id="locale-select" class="px-3 py-1.5 border border-gray-200 rounded-full text-sm text-gray-900 bg-white cursor-pointer focus:ring-2 focus:ring-[#FF0037] focus:border-transparent shadow-sm transition dark:bg-slate-900 dark:text-white dark:border-slate-700">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button id="darkModeToggle" class="p-2 border border-gray-200 bg-white text-gray-900 rounded-lg shadow-sm hover:bg-gray-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-white dark:hover:bg-slate-800 transition" title="{{ __('Toggle Dark Mode') }}">
                    <i class="bi bi-moon-fill text-gray-900 dark:text-yellow-400 text-sm" id="darkModeIcon"></i>
                </button>

                <div class="relative">
                    <button id="notifToggle" class="relative p-2 rounded-lg border border-gray-200 bg-white text-gray-900 hover:bg-gray-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-white dark:hover:bg-slate-800 transition" aria-expanded="false">
                        <i class="bi bi-bell text-sm"></i>
                        @if($__unreadCount > 0)
                            <span id="notifBadge" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-xs font-semibold text-white bg-red-600 rounded-full">{{ $__unreadCount }}</span>
                        @endif
                    </button>

                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-red-200 dark:border-slate-700 py-0 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-red-100 dark:border-slate-700 bg-red-50 dark:bg-slate-900/80 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="bi bi-bell text-red-700 dark:text-red-400"></i>
                                {{ __('Notifications') }}
                            </p>
                            <span class="text-xs text-gray-500 dark:text-slate-400">{{ __('Latest 6') }}</span>
                        </div>

                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-50 dark:divide-slate-700">
                            @forelse($__notifList as $notification)
                                @php
                                    $data = is_array($notification->data) ? $notification->data : (array) ($notification->data ?? []);
                                    $title = $data['title'] ?? ($data['heading'] ?? __('Notification'));
                                    $message = $data['message'] ?? ($data['body'] ?? '');
                                @endphp
                                <div class="px-4 py-3 hover:bg-red-50 dark:hover:bg-slate-700/60 flex items-start gap-3 transition {{ empty($notification->read_at) ? 'bg-red-50/50 dark:bg-red-950/20' : '' }}">
                                    <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="bi bi-bell text-red-600 dark:text-red-300 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ \Illuminate\Support\Str::limit($message, 100) }}</p>
                                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(empty($notification->read_at))
                                        <div class="w-2 h-2 rounded-full bg-red-600 flex-shrink-0 mt-2"></div>
                                    @endif
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <i class="bi bi-bell-slash text-2xl text-gray-300 dark:text-slate-600 block mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">{{ __('No notifications') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="profileToggle" class="flex items-center gap-2 p-1.5 border border-gray-200 bg-white text-gray-900 rounded-lg transition shadow-sm hover:bg-gray-50 dark:border-slate-700 dark:bg-slate-900/80 dark:text-white dark:hover:bg-slate-800">
                        @if($hasProfilePhoto)
                            <img src="{{ $photoPath }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-white/60">
                        @else
                            <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <span class="hidden md:block text-sm font-medium text-gray-900 dark:text-white max-w-[110px] truncate">{{ $user->name ?? __('Student') }}</span>
                        <i class="bi bi-chevron-down text-gray-600 dark:text-white/80 text-xs"></i>
                    </button>

                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-red-200 dark:border-slate-700 py-1 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-red-100 dark:border-slate-700 bg-red-50 dark:bg-slate-900/80">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name ?? __('Student') }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $user->email ?? '' }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-full font-medium">{{ __('Student') }}</span>
                        </div>

                        <a href="{{ route('student.profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 hover:bg-red-50 dark:hover:bg-slate-700 transition">
                            <i class="bi bi-person-gear text-gray-400 dark:text-slate-400"></i>
                            {{ __('Profile Settings') }}
                        </a>

                        <hr class="my-1 border-red-100 dark:border-slate-700">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 text-left transition">
                                <i class="bi bi-box-arrow-right text-red-400"></i>
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const localeSelect = document.getElementById('locale-select');
        const notifToggle = document.getElementById('notifToggle');
        const notifDropdown = document.getElementById('notifDropdown');
        const profileToggle = document.getElementById('profileToggle');
        const profileDropdown = document.getElementById('profileDropdown');
        const headerSearchForm = document.getElementById('studentHeaderSearchForm');
        const headerSearchInput = document.getElementById('studentHeaderSearchInput');

        if (localeSelect) {
            localeSelect.addEventListener('change', function () {
                const form = document.createElement('form');
                const csrfToken = document.querySelector('meta[name="csrf-token"]');

                form.method = 'POST';
                form.action = '{{ route('language.switch') }}';

                if (csrfToken) {
                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = csrfToken.getAttribute('content');
                    form.appendChild(csrfField);
                }

                const localeField = document.createElement('input');
                localeField.type = 'hidden';
                localeField.name = 'locale';
                localeField.value = this.value;
                form.appendChild(localeField);

                document.body.appendChild(form);
                form.submit();
            });
        }

        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                profileDropdown.classList.toggle('hidden');
                notifDropdown?.classList.add('hidden');
            });
        }

        if (notifToggle && notifDropdown) {
            notifToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                notifDropdown.classList.toggle('hidden');
                profileDropdown?.classList.add('hidden');
            });
        }

        document.addEventListener('click', function (event) {
            if (profileToggle && profileDropdown && !profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }

            if (notifToggle && notifDropdown && !notifToggle.contains(event.target) && !notifDropdown.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }
        });

        if (headerSearchForm && headerSearchInput) {
            const filterForm = document.getElementById('filterForm');
            const filterSearchInput = filterForm?.querySelector('input[name="search"], input[name="q"], input[name="query"]');
            const localSearchRoot = document.querySelector('[data-student-search-root]');
            const localSearchItems = Array.from(localSearchRoot?.querySelectorAll('[data-student-search-item]') ?? []);
            const localSearchEmpty = localSearchRoot?.querySelector('[data-student-search-empty]');
            const initialUrl = new URL(window.location.href);
            const initialQuery = (filterSearchInput?.value ?? initialUrl.searchParams.get('search') ?? initialUrl.searchParams.get('q') ?? '').toString();

            headerSearchInput.value = initialQuery;

            const applyLocalSearch = (query) => {
                const normalizedQuery = (query || '').trim().toLowerCase();
                let visibleCount = 0;

                localSearchItems.forEach((item) => {
                    const haystack = ((item.dataset.studentSearchText || item.textContent || '') + '').toLowerCase();
                    const isVisible = !normalizedQuery || haystack.includes(normalizedQuery);
                    item.classList.toggle('hidden', !isVisible);

                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                if (localSearchEmpty) {
                    localSearchEmpty.classList.toggle('hidden', !normalizedQuery || visibleCount > 0);
                }

                const nextUrl = new URL(window.location.href);
                nextUrl.searchParams.delete('page');
                nextUrl.searchParams.delete('q');

                if (normalizedQuery) {
                    nextUrl.searchParams.set('search', query.trim());
                } else {
                    nextUrl.searchParams.delete('search');
                }

                window.history.replaceState({}, '', nextUrl.toString());
            };

            if (localSearchItems.length > 0) {
                applyLocalSearch(initialQuery);

                headerSearchInput.addEventListener('input', function () {
                    applyLocalSearch(this.value);
                });

                headerSearchForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    applyLocalSearch(headerSearchInput.value);
                });

                return;
            }

            headerSearchForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const query = (headerSearchInput.value || '').trim();

                if (filterForm && filterSearchInput) {
                    filterSearchInput.value = query;
                    filterForm.submit();
                    return;
                }

                const nextUrl = new URL(window.location.href);
                nextUrl.searchParams.delete('page');

                if (!query) {
                    nextUrl.searchParams.delete('search');
                    nextUrl.searchParams.delete('q');
                    window.location.href = nextUrl.toString();
                    return;
                }

                nextUrl.searchParams.set(nextUrl.searchParams.has('q') ? 'q' : 'search', query);
                window.location.href = nextUrl.toString();
            });
        }
    });
</script>
