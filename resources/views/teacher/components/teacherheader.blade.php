<!-- Teacher Header -->
<header class="bg-[#FF0037] dark:bg-[#FF0037] shadow-sm border-b border-red-500 text-white">
    <div class="px-6 py-0 h-16">
        <div class="flex items-center justify-between gap-4 h-full">
            <div class="flex-1 min-w-0 flex items-center h-full gap-3">
                <!-- Mobile sidebar toggle -->
                <button id="sidebarToggle" class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg bg-white text-[#FF0037] border border-white/80 shadow-sm hover:bg-gray-100 transition flex-shrink-0" title="Toggle Sidebar">
                    <i class="bi bi-list text-[#FF0037] text-lg"></i>
                </button>
                <!-- Desktop sidebar collapse toggle -->
                <button id="desktopSidebarToggle" class="hidden lg:flex items-center justify-center w-9 h-9 rounded-lg hover:bg-red-500 transition flex-shrink-0" title="Toggle Sidebar">
                    <i class="bi bi-layout-sidebar text-white/80 text-lg"></i>
                </button>
                <div class="space-y-0 flex flex-col justify-center h-full">
                    <h2 class="text-lg font-bold text-white truncate leading-tight">{{ trim($__env->yieldContent('title', 'Dashboard')) }}</h2>
                    <p class="text-xs text-white/70 line-clamp-1 leading-tight">{{ __('Department of Information Technology') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0 h-full">
                <!-- Language switcher -->
                <div class="hidden md:block">
                    <label for="locale-select" class="sr-only">{{ __('Language') }}</label>
                    <select id="locale-select" class="px-3 py-1.5 min-w-[120px] border border-gray-200 rounded-full text-sm text-gray-900 bg-white cursor-pointer focus:ring-2 focus:ring-[#FF0037] focus:border-transparent shadow-sm transition">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="p-2 border border-gray-200 bg-white text-gray-900 rounded-lg shadow-sm hover:bg-gray-100 transition" title="{{ __('Toggle Dark Mode') }}">
                    <i class="bi bi-moon-fill text-gray-900 dark:text-yellow-400 text-sm" id="darkModeIcon"></i>
                </button>

                <!-- Notifications -->
                @php
                    $__notifList = auth()->user() ? auth()->user()->notifications()->orderBy('created_at', 'desc')->take(6)->get() : collect();
                    $__unreadCount = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
                @endphp
                <div class="relative">
                    <button id="notifToggle" class="relative p-2 rounded-lg border border-gray-200 bg-white text-gray-900 hover:bg-gray-100 transition" aria-expanded="false">
                        <i class="bi bi-bell text-gray-900 text-sm"></i>
                        @if($__unreadCount > 0)
                            <span id="notifBadge" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-xs font-semibold text-white bg-red-600 rounded-full">{{ $__unreadCount }}</span>
                        @endif
                    </button>

                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-red-200 py-0 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-red-100 bg-red-50 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-bell text-red-700"></i> {{ __('Notifications') }}
                            </p>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                            @forelse($__notifList as $n)
                                @php
                                    $data = is_array($n->data) ? $n->data : (array) ($n->data ?? []);
                                    $title = $data['title'] ?? ($data['heading'] ?? __('Notification'));
                                    $message = $data['message'] ?? ($data['body'] ?? '');
                                @endphp
                                <div class="px-4 py-3 hover:bg-red-50 flex items-start gap-3 cursor-pointer transition {{ isset($n->read_at) && $n->read_at ? '' : 'bg-red-50/50' }}">
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
                            <a href="{{ route('teacher.notifications') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">{{ __('View all notifications') }} →</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button id="profileToggle" class="flex items-center gap-2 p-1.5 border border-gray-200 bg-white text-gray-900 rounded-lg transition shadow-sm hover:bg-gray-50">
                        @php
                            $user = Auth::user();
                            $photoPath = $user->profile_photo_url;
                            $hasFile = !empty($photoPath);
                        @endphp
                        @if($hasFile)
                            <img src="{{ $photoPath }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-300">
                        @else
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center border border-gray-300">
                                <span class="text-sm font-medium text-red-700">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                        <span class="hidden md:block text-sm font-medium text-gray-700">{{ $user->name }}</span>
                        <i class="bi bi-chevron-down text-gray-500 text-xs"></i>
                    </button>

                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-1 z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route('teacher.profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#FF0037] transition">
                            <i class="bi bi-person"></i>
                            {{ __('Profile Settings') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition">
                                <i class="bi bi-box-arrow-right"></i>
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
    // Profile dropdown toggle
    document.getElementById('profileToggle').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('profileDropdown').classList.toggle('hidden');
        document.getElementById('notifDropdown').classList.add('hidden');
    });

    // Notification dropdown toggle
    document.getElementById('notifToggle').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('notifDropdown').classList.toggle('hidden');
        document.getElementById('profileDropdown').classList.add('hidden');
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#profileToggle') && !e.target.closest('#profileDropdown')) {
            document.getElementById('profileDropdown').classList.add('hidden');
        }
        if (!e.target.closest('#notifToggle') && !e.target.closest('#notifDropdown')) {
            document.getElementById('notifDropdown').classList.add('hidden');
        }
    });

    const localeSelect = document.getElementById('locale-select');
    if (localeSelect) {
        localeSelect.addEventListener('change', function() {
            const form = document.createElement('form');
            const csrfToken = document.querySelector('meta[name="csrf-token"]');

            form.method = 'POST';
            form.action = '{{ route("language.switch") }}';

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
</script>
