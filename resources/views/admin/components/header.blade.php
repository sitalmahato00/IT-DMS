<!-- Header -->
<header id="adminTopHeader" class="w-full bg-[#FF0037] dark:bg-[#FF0037] shadow-sm border-b border-[#D90033] dark:border-[#D90033] text-white red-header">
    <div class="px-3 py-0 h-16 sm:px-6">
        <div class="flex items-center justify-between gap-4 h-full">
            <!-- Mobile Sidebar Toggle Button -->
            <button id="mobileSidebarToggle" class="lg:hidden p-2 text-white hover:bg-white/15 rounded-lg transition-colors" aria-label="Toggle sidebar menu" title="Toggle menu">
                <i class="bi bi-list text-xl"></i>
            </button>

            <div class="flex-1 min-w-0">
                <!-- Current Page Info for Desktop -->
                <div class="hidden md:flex items-center gap-3">
                    @php
                        // Determine current page and icon
                        $currentRoute = Route::currentRouteName() ?? '';
                        $pageInfo = [
                            'Students' => ['icon' => 'bi-people-fill', 'color' => 'text-blue-600'],
                            'Teachers' => ['icon' => 'bi-person-chalkboard', 'color' => 'text-purple-600'],
                            'Courses' => ['icon' => 'bi-book-fill', 'color' => 'text-green-600'],
                            'Attendance' => ['icon' => 'bi-check-circle-fill', 'color' => 'text-amber-600'],
                            'Dashboard' => ['icon' => 'bi-speedometer2', 'color' => 'text-red-600'],
                            'Exams' => ['icon' => 'bi-pencil-fill', 'color' => 'text-orange-600'],
                            'Marks' => ['icon' => 'bi-file-earmark-text', 'color' => 'text-indigo-600'],
                        ];
                        
                        // Get current page name from route if available
                        $currentPageName = '';
                        $pageIcon = 'bi-folder';
                        $pageColor = 'text-gray-600';
                        
                        if (strpos($currentRoute, 'students') !== false) {
                            $currentPageName = 'Students';
                        } elseif (strpos($currentRoute, 'teachers') !== false) {
                            $currentPageName = 'Teachers';
                        } elseif (strpos($currentRoute, 'courses') !== false) {
                            $currentPageName = 'Courses';
                        } elseif (strpos($currentRoute, 'attendance') !== false) {
                            $currentPageName = 'Attendance';
                        } elseif (strpos($currentRoute, 'exam') !== false) {
                            $currentPageName = 'Exams';
                        } elseif (strpos($currentRoute, 'marks') !== false) {
                            $currentPageName = 'Marks';
                        } elseif (strpos($currentRoute, 'dashboard') !== false) {
                            $currentPageName = 'Dashboard';
                        } else {
                            $currentPageName = 'Admin Panel';
                        }
                        
                        if (isset($pageInfo[$currentPageName])) {
                            $pageIcon = $pageInfo[$currentPageName]['icon'];
                            $pageColor = $pageInfo[$currentPageName]['color'];
                        }
                    @endphp
                    
                    @if($currentPageName !== '')
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-white/20 border border-white/30 rounded-lg">
                        <i class="bi {{ $pageIcon }} {{ $pageColor }} text-base"></i>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white/80 uppercase tracking-wide">Current Page</p>
                            <p class="text-sm font-medium text-white truncate">{{ $currentPageName }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Mobile Current Page Display -->
                <div class="md:hidden flex items-center gap-2 px-2 py-1 text-white">
                    @php
                        $routeName = Route::currentRouteName() ?? '';
                        $shortName = $currentPageName ?? 'Admin';
                    @endphp
                    <i class="bi {{ $pageIcon }} text-base"></i>
                    <span class="text-sm font-semibold truncate">{{ substr($shortName, 0, 10) }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0 h-full">
                <div class="hidden md:block">
                    <label for="locale-select" class="sr-only">{{ __('Language') }}</label>
                    <select id="locale-select" class="px-3 py-1.5 border border-gray-200 rounded-full text-sm text-gray-900 bg-white cursor-pointer focus:ring-2 focus:ring-[#FF0037] focus:border-transparent shadow-sm transition duration-150 dark:bg-slate-900 dark:text-white dark:border-slate-700">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="p-2 border border-gray-200 bg-white text-gray-900 rounded-lg shadow-sm hover:bg-gray-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-white dark:hover:bg-slate-800 transition" title="{{ __('Toggle Dark Mode') }}">
                        <i class="bi bi-moon-fill text-gray-900 dark:text-yellow-400 text-sm" id="darkModeIcon"></i>
                    </button>

                <!-- Notifications -->
                @php
                    $__notifList = auth()->user() ? auth()->user()->notifications()->orderBy('created_at', 'desc')->take(6)->get() : collect();
                    $__unreadCount = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
                @endphp
                    <div class="relative">
                        <button id="notifToggle" class="relative p-2 rounded-lg border border-gray-200 bg-white text-gray-900 hover:bg-gray-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-white dark:hover:bg-slate-800 transition" aria-expanded="false">
                            <i class="bi bi-bell text-gray-900 dark:text-white text-sm"></i>
                        @if($__unreadCount > 0)
                            <span id="notifBadge" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-xs font-semibold text-white bg-red-600 rounded-full">{{ $__unreadCount }}</span>
                        @endif
                     </button>

                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-red-200 py-0 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-red-100 bg-red-50 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-bell text-red-700"></i> {{ __('Notifications') }}
                            </p>
                            <div class="flex items-center gap-2">
                                <button id="markAllReadBtn" class="text-xs text-[#D5002C] hover:text-[#B00029] font-medium">{{ __('Mark all read') }}</button>
                            </div>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                            @forelse($__notifList as $n)
                                @php
                                    $data = is_array($n->data) ? $n->data : (array) ($n->data ?? []);
                                    $title = $data['title'] ?? ($data['heading'] ?? __('Notification'));
                                    $message = $data['message'] ?? ($data['body'] ?? '');
                                    $url = $data['url'] ?? null;
                                @endphp
                                <div class="px-4 py-3 hover:bg-red-50 flex items-start gap-3 cursor-pointer transition {{ isset($n->read_at) && $n->read_at ? '' : 'bg-red-50/50' }}" onclick="handleNotificationClick('{{ $n->id }}', {{ $url ? "'".$url."'" : 'null' }})">
                                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="bi bi-bell text-red-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ Str::limit($message, 100) }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(empty($n->read_at))
                                        <div class="w-2 h-2 rounded-full bg-red-600 flex-shrink-0 mt-2"></div>
                                    @endif
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <i class="bi bi-bell-slash text-2xl text-gray-300 block mb-2"></i>
                                    <p class="text-sm text-gray-500">{{ __('No notifications') }}</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="px-4 py-3 border-t border-red-100 bg-red-50 text-center">
                            <a href="{{ route('admin.notifications') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">{{ __('View all notifications') }} →</a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileToggle" class="flex items-center gap-2 p-1.5 border border-gray-200 bg-white text-gray-900 rounded-lg transition shadow-sm hover:bg-gray-50 dark:border-slate-700 dark:bg-slate-900/80 dark:text-white dark:hover:bg-slate-800">
                        @php
                            $user = Auth::user();
                            $photoPath = $user->profile_photo_path;
                            $hasFile = !empty($photoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath);
                        @endphp
                        @if($hasFile)
                            <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $user->name }}" class="w-7 h-7 rounded-full object-cover border border-white/60">
                        @else
                            <div class="w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-bold">
                                {{ substr($user->name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <span class="hidden md:block text-sm font-medium text-gray-900 dark:text-white max-w-[100px] truncate">{{ $user->name }}</span>
                        <i class="bi bi-chevron-down text-gray-600 dark:text-white/80 text-xs"></i>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-red-200 py-1 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-red-100 bg-red-50">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full font-medium">Administrator</span>
                        </div>
                        <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-900 hover:bg-red-50 transition">
                            <i class="bi bi-gear text-gray-400"></i>
                            {{ __('Settings') }}
                        </a>
                        <hr class="my-1 border-red-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 text-left transition">
                                <i class="bi bi-box-arrow-right text-red-400"></i>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const localeSelect = document.getElementById('locale-select');
        const notifDropdown = document.getElementById('notifDropdown');
        
        // Mobile Sidebar Toggle
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const desktopSidebarToggle = document.getElementById('desktopSidebarToggle');
        const mobileBreakpoint = 1024;
        let hideSidebarTimer = null;

        function isMobileViewport() {
            return window.innerWidth < mobileBreakpoint;
        }

        function clearSidebarHideTimer() {
            if (hideSidebarTimer) {
                window.clearTimeout(hideSidebarTimer);
                hideSidebarTimer = null;
            }
        }

        function openMobileSidebar() {
            if (!sidebar) {
                return;
            }

            clearSidebarHideTimer();
            sidebar.classList.remove('hidden');
            requestAnimationFrame(() => {
                sidebar.classList.remove('-translate-x-full');
            });
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.remove('hidden');
                sidebarBackdrop.style.display = 'block';
            }
            document.body.classList.add('overflow-hidden');
        }

        function closeMobileSidebar() {
            if (!sidebar) {
                return;
            }

            clearSidebarHideTimer();
            sidebar.classList.add('-translate-x-full');
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.add('hidden');
                sidebarBackdrop.style.display = '';
            }
            document.body.classList.remove('overflow-hidden');

            if (isMobileViewport()) {
                hideSidebarTimer = window.setTimeout(() => {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('hidden');
                    }
                }, 300);
            } else {
                sidebar.classList.remove('hidden');
            }
        }

        function syncSidebarForViewport() {
            if (!sidebar) {
                return;
            }

            clearSidebarHideTimer();

            if (isMobileViewport()) {
                sidebar.classList.add('hidden');
                sidebar.classList.add('-translate-x-full');
                if (sidebarBackdrop) {
                    sidebarBackdrop.classList.add('hidden');
                    sidebarBackdrop.style.display = '';
                }
                document.body.classList.remove('overflow-hidden');
                return;
            }

            sidebar.classList.remove('hidden');
            sidebar.classList.remove('-translate-x-full');
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.add('hidden');
                sidebarBackdrop.style.display = '';
            }
            document.body.classList.remove('overflow-hidden');
        }

        syncSidebarForViewport();

        if (mobileSidebarToggle && sidebar) {
            mobileSidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();

                if (sidebar.classList.contains('hidden') || sidebar.classList.contains('-translate-x-full')) {
                    openMobileSidebar();
                    return;
                }

                closeMobileSidebar();
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                closeMobileSidebar();
            });
        }

        const mainPanel = document.getElementById('adminMainPanel');
        const pageContent = document.getElementById('adminPageContent');
        [mainPanel, pageContent].forEach((target) => {
            target?.addEventListener('click', function() {
                if (!isMobileViewport()) {
                    return;
                }

                if (!sidebar.classList.contains('hidden') && !sidebar.classList.contains('-translate-x-full')) {
                    closeMobileSidebar();
                }
            });
        });

        document.addEventListener('pointerdown', function(e) {
            if (!isMobileViewport() || !sidebar || sidebar.classList.contains('hidden') || sidebar.classList.contains('-translate-x-full')) {
                return;
            }

            if (sidebar.contains(e.target) || mobileSidebarToggle?.contains(e.target)) {
                return;
            }

            closeMobileSidebar();
        });

        window.addEventListener('resize', function() {
            syncSidebarForViewport();
        });

        window.adminCloseMobileSidebar = closeMobileSidebar;

        // Close sidebar when clicking on a sidebar navigation link or logout button
        const sidebarLinks = sidebar?.querySelectorAll('a, form button[type="submit"]');
        if (sidebarLinks) {
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (isMobileViewport()) {
                        closeMobileSidebar();
                    }
                });
            });
        }

        if (localeSelect) {
            localeSelect.addEventListener('change', function() {
                const locale = this.value;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("language.switch") }}';
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_token';
                    input.value = csrfToken.getAttribute('content');
                    form.appendChild(input);
                }
                const localeInput = document.createElement('input');
                localeInput.type = 'hidden';
                localeInput.name = 'locale';
                localeInput.value = locale;
                form.appendChild(localeInput);
                document.body.appendChild(form);
                form.submit();
            });
        }

        // Profile dropdown toggle
        const profileToggle = document.getElementById('profileToggle');
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
                notifDropdown?.classList.add('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }

        // Desktop sidebar collapse
        if (desktopSidebarToggle && sidebar) {
            desktopSidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-collapsed');
                // Toggle icon
                const icon = desktopSidebarToggle.querySelector('i');
                if (sidebar.classList.contains('sidebar-collapsed')) {
                    icon.classList.remove('bi-layout-sidebar');
                    icon.classList.add('bi-layout-sidebar-reverse');
                    localStorage.setItem('sidebar-collapsed', '1');
                } else {
                    icon.classList.remove('bi-layout-sidebar-reverse');
                    icon.classList.add('bi-layout-sidebar');
                    localStorage.removeItem('sidebar-collapsed');
                }
            });
            // Restore state
            if (localStorage.getItem('sidebar-collapsed') === '1') {
                sidebar.classList.add('sidebar-collapsed');
                const icon = desktopSidebarToggle.querySelector('i');
                icon.classList.remove('bi-layout-sidebar');
                icon.classList.add('bi-layout-sidebar-reverse');
            }
        }
    });
</script>

<script>
    // Notifications dropdown, polling & actions
    let notificationPollingInitialized = false;
    
    document.addEventListener('DOMContentLoaded', function() {
        if (notificationPollingInitialized) return;
        notificationPollingInitialized = true;
        
        const toggle = document.getElementById('notifToggle');
        const dropdown = document.getElementById('notifDropdown');
        const markAllBtn = document.getElementById('markAllReadBtn');

        if (toggle && dropdown) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
                document.getElementById('profileDropdown')?.classList.add('hidden');
            });
            document.addEventListener('click', function() {
                dropdown.classList.add('hidden');
            });
        }

        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('{{ route('admin.notifications.markRead') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: 'all' })
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        const badge = document.getElementById('notifBadge');
                        if (badge) badge.remove();
                        const dots = dropdown.querySelectorAll('.bg-blue-600.rounded-full');
                        dots.forEach(d => d.remove());
                        // Remove blue backgrounds
                        dropdown.querySelectorAll('.bg-blue-50\\/50').forEach(el => {
                            el.classList.remove('bg-blue-50/50');
                        });
                    }
                });
            });
        }

        pollUnreadCount();
        let pollInterval = setInterval(pollUnreadCount, 30000);
        window.addEventListener('focus', pollUnreadCount);
        window.addEventListener('beforeunload', function() {
            clearInterval(pollInterval);
        });
    });

    async function pollUnreadCount() {
        try {
            const res = await fetch('{{ route('admin.notifications.unreadCount') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const toggle = document.getElementById('notifToggle');
            let badge = document.getElementById('notifBadge');
            if (data.unread && data.unread > 0) {
                if (badge) {
                    badge.textContent = data.unread;
                } else if (toggle) {
                    const span = document.createElement('span');
                    span.id = 'notifBadge';
                    span.className = 'absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-xs font-semibold text-white bg-blue-600 rounded-full';
                    span.textContent = data.unread;
                    toggle.appendChild(span);
                }
            } else {
                if (badge) badge.remove();
            }
        } catch (e) {
            // silent
        }
    }

    function handleNotificationClick(id, url) {
        fetch('{{ route('admin.notifications.markRead') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: id })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                pollUnreadCount();
                if (url) {
                    window.location.href = url;
                } else {
                    window.location.href = '{{ route('admin.notifications') }}';
                }
            }
        });
    }
</script>
