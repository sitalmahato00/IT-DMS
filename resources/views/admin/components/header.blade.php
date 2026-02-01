<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-6 py-5">
        <div class="flex items-start justify-between gap-6">
            <div class="flex-1 min-w-0">
                <div class="space-y-1">
                    <h2 class="text-2xl font-bold text-gray-900 truncate">{{ trim($__env->yieldContent('title', 'Dashboard')) }}</h2>
                    <p class="text-sm text-gray-600 line-clamp-2">Department of Computer Science & Engineering</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="relative hidden md:block">
                    <input type="text" placeholder="Search..." class="px-3 py-2 pl-9 pr-4 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent w-56 bg-gray-50">
                    <i class="bi bi-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                </div>
                <label class="relative inline-flex items-center gap-4 cursor-pointer" title="Toggle dark mode">
                    <input id="darkModeCheckbox" type="checkbox" class="sr-only peer" aria-label="Toggle dark mode">

                    <div class="relative">
                        <div
                            class="w-20 h-10 bg-gray-300 rounded-full
                                   peer-checked:bg-green-500
                                   transition-colors duration-300">
                        </div>

                        <div
                            class="absolute left-1 top-1 w-8 h-8 bg-white rounded-full
                                   shadow-md
                                   transition-transform duration-300
                                   peer-checked:translate-x-10">
                        </div>
                    </div>

                    <span
                        id="darkModeLabel"
                        class="text-sm font-semibold text-gray-400
                               peer-checked:text-green-500
                               transition-colors duration-300">
                        ON
                    </span>
                </label>
                <button class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                    <i class="bi bi-bell text-gray-600 text-lg"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
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
                            Profile Settings
                        </a>
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                <i class="bi bi-box-arrow-right text-gray-400"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

