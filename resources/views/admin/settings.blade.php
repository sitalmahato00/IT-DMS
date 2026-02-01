@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- Settings Grid -->

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Profile & Account -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Admin Profile Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-person-circle text-gray-500"></i>
                        Admin Profile
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex items-start gap-4">
                        @if(Auth::user() && Auth::user()->profile_photo_path)
                            <img src="{{ Storage::disk('public')->url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300 flex-shrink-0">
                        @else
                            <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-orange-600 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</h4>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            <p class="text-xs text-gray-500 mt-1 capitalize">Role: {{ Auth::user()->role }}</p>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition">
                                    <i class="bi bi-pencil mr-1"></i>
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-shield-lock text-gray-500"></i>
                        Account Security
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i class="bi bi-key text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Password</p>
                                <p class="text-xs text-gray-500">Last changed: Never</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs text-red-600 hover:text-red-700 font-medium">Change</a>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-100 p-2 rounded-lg">
                                <i class="bi bi-phone text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Two-Factor Authentication</p>
                                <p class="text-xs text-gray-500">Not enabled</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs text-red-600 hover:text-red-700 font-medium">Enable</a>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i class="bi bi-clock-history text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Active Sessions</p>
                                <p class="text-xs text-gray-500">1 active session</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs text-red-600 hover:text-red-700 font-medium">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-bell text-gray-500"></i>
                        Notification Settings
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <i class="bi bi-envelope text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Email Notifications</p>
                                <p class="text-xs text-gray-500">Receive updates via email</p>
                            </div>
                        </div>
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500" checked>
                    </label>

                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i class="bi bi-people text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">User Activity Alerts</p>
                                <p class="text-xs text-gray-500">New registrations & changes</p>
                            </div>
                        </div>
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500" checked>
                    </label>

                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i class="bi bi-exclamation-triangle text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">System Alerts</p>
                                <p class="text-xs text-gray-500">Important system notifications</p>
                            </div>
                        </div>
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500" checked>
                    </label>
                </div>
            </div>
        </div>

        <!-- Right Column: System Info & Quick Links -->
        <div class="space-y-6">
            <!-- System Information -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-info-circle text-gray-500"></i>
                        System Info
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">Laravel Version</span>
                        <span class="text-xs font-medium text-gray-900">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">PHP Version</span>
                        <span class="text-xs font-medium text-gray-900">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">Database</span>
                        <span class="text-xs font-medium text-gray-900">SQLite</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">Environment</span>
                        <span class="text-xs font-medium text-green-600">Local</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-600">Debug Mode</span>
                        <span class="text-xs font-medium text-green-600">On</span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-lightning text-gray-500"></i>
                        Quick Links
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="#" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition text-sm text-gray-700">
                        <i class="bi bi-gear text-gray-400"></i>
                        System Settings
                    </a>
                    <a href="#" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition text-sm text-gray-700">
                        <i class="bi bi-database text-gray-400"></i>
                        Database Backup
                    </a>
                    <a href="#" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition text-sm text-gray-700">
                        <i class="bi bi-file-earmark-text text-gray-400"></i>
                        Audit Logs
                    </a>
                    <a href="#" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition text-sm text-gray-700">
                        <i class="bi bi-question-circle text-gray-400"></i>
                        Help & Support
                    </a>
                    <a href="#" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition text-sm text-gray-700">
                        <i class="bi bi-file-earmark-code text-gray-400"></i>
                        API Documentation
                    </a>
                </div>
            </div>

            <!-- Logout -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                            <i class="bi bi-box-arrow-right"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

