<!-- Header -->
<header id="adminTopHeader" class="w-full bg-[#FF0037] dark:bg-[#FF0037] shadow-md border-b border-[#D90033] dark:border-[#D90033] text-white red-header" data-mobile-app-header>
    <div class="px-3 py-3 sm:px-6 sm:py-4">
        <div class="flex items-center justify-between gap-4">
            <!-- Left Section: Title & Department -->
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <!-- Mobile Sidebar Toggle -->
                <button id="mobileSidebarToggle" class="lg:hidden p-2 text-white hover:bg-white/15 rounded-lg transition-colors flex-shrink-0" aria-label="Toggle sidebar menu" title="Toggle menu">
                    <i class="bi bi-list text-xl"></i>
                </button>

                <!-- Page Title & Department -->
                <div class="min-w-0">
                    @php
                        $currentRoute = Route::currentRouteName() ?? '';
                        $pageNames = [
                            'students' => 'Students Dashboard',
                            'teachers' => 'Teachers Dashboard',
                            'courses' => 'Courses Dashboard',
                            'attendance' => 'Attendance Dashboard',
                            'exam' => 'Exams Dashboard',
                            'marks' => 'Marks Dashboard',
                            'dashboard' => 'Admin Dashboard',
                            'default' => 'Admin Dashboard'
                        ];
                        
                        $currentPageName = 'Admin Dashboard';
                        foreach ($pageNames as $key => $name) {
                            if (strpos($currentRoute, $key) !== false) {
                                $currentPageName = $name;
                                break;
                            }
                        }
                    @endphp
                    <h1 class="text-lg sm:text-xl font-bold text-white truncate">{{ $currentPageName }}</h1>
                    <p class="text-xs sm:text-sm text-white/80 mt-0.5 truncate">
                        @if(isset($department))
                            {{ $department->name ?? 'Department of Information Technology' }}
                        @else
                            Department of Information Technology
                        @endif
                    </p>
                </div>
            </div>

            <!-- Right Section: Controls -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <!-- Language Selector -->
                <div class="hidden sm:block">
                    <select id="locale-select" class="px-3 py-1.5 min-w-[120px] border border-white/30 rounded-lg text-xs text-white bg-white/10 cursor-pointer hover:bg-white/15 focus:ring-2 focus:ring-white focus:ring-offset-0 focus:border-transparent shadow-sm transition duration-150 dark:bg-white/10 dark:border-white/30">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }} class="text-gray-900">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="p-2 rounded-lg hover:bg-white/15 transition text-white" title="{{ __('Toggle Dark Mode') }}">
                    <i class="bi bi-moon-fill text-lg" id="darkModeIcon"></i>
                </button>

                <!-- Notifications -->
                @php
                    $__notifList = auth()->user() ? auth()->user()->notifications()->orderBy('created_at', 'desc')->take(6)->get() : collect();
                    $__unreadCount = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
                @endphp
                <div class="relative hidden sm:block">
                    <button id="notifToggle" class="relative p-2 rounded-lg hover:bg-white/15 transition text-white" aria-expanded="false">
                        <i class="bi bi-bell text-lg"></i>
                        @if($__unreadCount > 0)
                            <span id="notifBadge" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-xs font-semibold text-white bg-red-700 rounded-full">{{ $__unreadCount }}</span>
                        @endif
                    </button>

                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-red-200 py-0 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-red-100 bg-red-50 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-bell text-red-700"></i> {{ __('Notifications') }}
                            </p>
                            <button id="markAllReadBtn" class="text-xs text-[#D5002C] hover:text-[#B00029] font-medium" data-mark-all-read-url="{{ route('admin.notifications.markRead') }}">{{ __('Mark all read') }}</button>
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
                    <button id="profileToggle" class="flex items-center gap-1.5 p-1.5 rounded-lg hover:bg-white/15 transition text-white">
                        @php
                            $user = Auth::user();
                        @endphp
                        @if($user?->profile_photo_url)
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-7 h-7 rounded-full object-cover border border-white/30">
                        @else
                            <div class="w-7 h-7 rounded-full bg-white/20 text-white flex items-center justify-center text-xs font-bold">
                                {{ substr($user->name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <i class="bi bi-chevron-down text-white text-sm hidden sm:inline"></i>
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

<script data-mobile-static-script>
    document.addEventListener('DOMContentLoaded', function() {
        const localeSelect = document.getElementById('locale-select');
        const notifDropdown = document.getElementById('notifDropdown');
        const sidebar = document.getElementById('sidebar');
        sidebar?.classList.remove('sidebar-collapsed');
        localStorage.removeItem('sidebar-collapsed');

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

    });
</script>

<script data-mobile-static-script>
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
