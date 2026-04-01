<!-- Header -->
<header class="bg-[#FF0037] dark:bg-[#FF0037] shadow-sm border-b border-[#D90033] dark:border-[#D90033] text-white red-header">
    <div class="px-6 py-0 h-16">
        <div class="flex items-center justify-between gap-4 h-full">
            <div class="flex-1 min-w-0">
                <!-- Quick Action Buttons -->
                <div class="hidden lg:flex items-center gap-2">
                    <a href="{{ route('admin.students') }}" class="px-3 py-1.5 text-xs font-medium text-white bg-white/15 border border-white/30 rounded-lg hover:bg-white/25 transition" title="{{ __('View Students') }}">
                        <i class="bi bi-people-fill mr-1"></i> {{ __('Students') }}
                    </a>
                    <a href="{{ route('admin.teachers') }}" class="px-3 py-1.5 text-xs font-medium text-white bg-white/15 border border-white/30 rounded-lg hover:bg-white/25 transition" title="{{ __('View Teachers') }}">
                        <i class="bi bi-person-chalkboard mr-1"></i> {{ __('Teachers') }}
                    </a>
                    <a href="{{ route('admin.courses') }}" class="px-3 py-1.5 text-xs font-medium text-white bg-white/15 border border-white/30 rounded-lg hover:bg-white/25 transition" title="{{ __('View Courses') }}">
                        <i class="bi bi-book-fill mr-1"></i> {{ __('Courses') }}
                    </a>
                    <a href="{{ route('admin.attendance') }}" class="px-3 py-1.5 text-xs font-medium text-white bg-white/15 border border-white/30 rounded-lg hover:bg-white/25 transition" title="{{ __('Attendance') }}">
                        <i class="bi bi-check-circle-fill mr-1"></i> {{ __('Attendance') }}
                    </a>
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
        const desktopSidebarToggle = document.getElementById('desktopSidebarToggle');
        const sidebar = document.getElementById('sidebar');
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
