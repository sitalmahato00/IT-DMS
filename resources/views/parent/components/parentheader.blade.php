<!-- Parent Header -->
<header class="bg-amber-600 dark:bg-amber-800 shadow-sm border-b border-amber-600 text-white">
    <div class="px-6 py-0 h-16">
        <div class="flex items-center justify-between gap-4 h-full">
            <div class="flex-1 min-w-0 flex items-center h-full">
                <div class="space-y-0 flex flex-col justify-center h-full">
                    <h2 class="text-xl font-bold text-white truncate leading-tight">{{ trim($__env->yieldContent('title', 'Dashboard')) }}</h2>
                    <p class="text-xs text-amber-100 line-clamp-1 leading-tight">{{ __('Parent Portal - IT Department') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0 h-full">
                <!-- Export CSV -->
                <a href="{{ route('parent.export') }}" class="px-3 py-2 bg-amber-500 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-600 rounded-lg transition text-sm font-medium flex items-center gap-2" title="{{ __('Export Data') }}">
                    <i class="bi bi-download"></i>
                    <span class="hidden md:inline">{{ __('Export CSV') }}</span>
                </a>

                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="p-2 bg-amber-500 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-600 rounded-lg transition" title="{{ __('Toggle Dark Mode') }}">
                    <i class="bi bi-moon-fill text-white text-lg" id="darkModeIcon"></i>
                </button>

                <!-- Notifications -->
                @php
                    $__notifList = auth()->user() ? auth()->user()->notifications()->orderBy('created_at', 'desc')->take(6)->get() : collect();
                    $__unreadCount = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
                @endphp
                <div class="relative">
                    <button id="notifToggle" class="relative bg-amber-500 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-600 p-2 rounded-lg transition" aria-expanded="false">
                        <i class="bi bi-bell text-white text-lg"></i>
                        @if($__unreadCount > 0)
                            <span id="notifBadge" class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-semibold text-white bg-red-600 rounded-full">{{ $__unreadCount }}</span>
                        @endif
                    </button>

                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <p class="text-sm font-semibold dark:text-white">{{ __('Notifications') }}</p>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse($__notifList as $n)
                                @php
                                    $data = is_array($n->data) ? $n->data : (array) ($n->data ?? []);
                                    $title = $data['title'] ?? ($data['heading'] ?? __('Notification'));
                                    $message = $data['message'] ?? ($data['body'] ?? '');
                                @endphp
                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-start gap-3 cursor-pointer {{ isset($n->read_at) && $n->read_at ? '' : 'bg-amber-50 dark:bg-amber-900/30' }}">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ Str::limit($message, 120) }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $n->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No notifications') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative group">
                    <button class="flex items-center gap-2 bg-amber-500 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-600 px-3 py-2 rounded-lg transition">
                        @php
                            $user = auth()->user();
                            $profilePhotoPath = $user->profile_photo_path;
                        @endphp
                        @if($profilePhotoPath && Storage::disk('public')->exists($profilePhotoPath))
                            <img src="{{ asset('/storage/' . $profilePhotoPath) }}" alt="{{ $user->name }}" class="w-6 h-6 rounded-full object-cover border border-white" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="w-6 h-6 rounded-full bg-amber-600 text-white flex items-center justify-center text-xs font-bold" style="display:none;">
                                {{ substr($user->name ?? 'P', 0, 1) }}
                            </div>
                        @else
                            <i class="bi bi-person-circle text-white text-lg"></i>
                        @endif
                        <span class="text-sm font-medium text-white max-w-[150px] truncate">{{ auth()->user()?->name ?? 'Parent' }}</span>
                    </button>
                    <div class="hidden group-hover:block absolute right-0 mt-0 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-40">
                        <a href="{{ route('parent.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700">
                            <i class="bi bi-gear mr-2"></i>{{ __('Settings') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                <i class="bi bi-box-arrow-right mr-2"></i>{{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Notification toggle
    const notifToggle = document.getElementById('notifToggle');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifToggle) {
        notifToggle.addEventListener('click', () => {
            notifDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!notifToggle.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }


</script>

