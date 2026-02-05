<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-6 py-5">
        <div class="flex items-start justify-between gap-6">
            <div class="flex-1 min-w-0">
                <div class="space-y-1">
                    <h2 class="text-2xl font-bold text-gray-900 truncate">{{ trim($__env->yieldContent('title', 'Dashboard')) }}</h2>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ __('Department of Computer Science & Engineering') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="relative hidden md:block">
                    <input type="text" placeholder="{{ __('Search') }}" class="px-3 py-2 pl-9 pr-4 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent w-56 bg-gray-50">
                    <i class="bi bi-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                </div>

                <!-- Language switcher -->
                <div class="hidden md:block">
                    <label for="locale-select" class="sr-only">{{ __('Language') }}</label>
                    <select id="locale-select" class="px-3 py-1.5 border rounded text-sm bg-white cursor-pointer w-36">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" title="{{ __('Toggle Dark Mode') }}">
                    <i class="bi bi-moon-fill text-gray-600 dark:text-yellow-400 text-lg" id="darkModeIcon"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="relative group">
                    <button class="relative p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" title="{{ __('Notifications') }}">
                        <i class="bi bi-bell text-gray-600 dark:text-gray-300 text-lg"></i>
                        @if(isset($unreadNoticeCount) && $unreadNoticeCount > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ min($unreadNoticeCount, 9) }}{{ $unreadNoticeCount > 9 ? '+' : '' }}</span>
                        @else
                        <span class="absolute top-1 right-1 w-2 h-2 bg-gray-300 rounded-full"></span>
                        @endif
                    </button>
                    
                    <!-- Notifications Dropdown Panel -->
                    <div class="hidden group-hover:block absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-0 z-50 transition-all duration-300 max-h-96 overflow-hidden flex flex-col">
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Notifications') }}</h3>
                            @if(isset($unreadNoticeCount) && $unreadNoticeCount > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">{{ $unreadNoticeCount }} {{ __('new') }}</span>
                            @endif
                        </div>
                        
                        <!-- Notifications List -->
                        <div class="overflow-y-auto flex-1">
                            @if(isset($recentNotices) && count($recentNotices) > 0)
                                @forelse($recentNotices as $notice)
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                                    <div class="flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 rounded-full mt-2 {{ $notice['is_important'] ? 'bg-red-500' : 'bg-green-500' }}"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $notice['title'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">{{ $notice['message'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $notice['time'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="px-4 py-6 text-center">
                                    <i class="bi bi-bell-slash text-2xl text-gray-300 dark:text-gray-600"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('No notifications') }}</p>
                                </div>
                                @endforelse
                            @else
                            <div class="px-4 py-6 text-center">
                                <i class="bi bi-bell-slash text-2xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('No notifications') }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Footer -->
                        @if(isset($recentNotices) && count($recentNotices) > 0)
                        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <a href="{{ route('admin.notice-board') }}" class="text-xs font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">{{ __('View All Notices') }} →</a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="relative group">
                    <button class="flex items-center gap-2 p-1.5 hover:bg-gray-100 rounded-lg transition">
                        @if(Auth::user() && Auth::user()->profile_photo_path)
                            <img src="{{ Storage::disk('public')->url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-300 hover:border-red-500 transition">
                        @else
                            <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-bold">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                    </button>
                    <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 transition-all duration-300 delay-200 opacity-0 group-hover:opacity-100 invisible group-hover:visible group-hover:delay-200">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="bi bi-person text-gray-400"></i>
                            {{ __('Profile') }}
                        </a>
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                <i class="bi bi-box-arrow-right text-gray-400"></i>
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
        if (localeSelect) {
            localeSelect.addEventListener('change', function() {
                const locale = this.value;
                // Create a form and submit it
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
    });
</script>

