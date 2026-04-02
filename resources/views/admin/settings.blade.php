@extends('admin.layouts.app')

@section('title', 'Settings')

@section('styles')
<script>
    document.documentElement.classList.add('settings-ui-enhanced');
</script>
<style>
    html.settings-ui-enhanced:not(.dark) .settings-page {
        color: #0f172a;
    }

    html.settings-ui-enhanced:not(.dark) .settings-card {
        border-radius: 28px;
        border-color: rgba(226, 232, 240, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 250, 251, 0.98));
        box-shadow: 0 24px 52px -40px rgba(15, 23, 42, 0.22);
    }

    html.settings-ui-enhanced:not(.dark) .settings-card-header {
        background: linear-gradient(180deg, #f8fafc, #ffffff);
    }

    html.settings-ui-enhanced:not(.dark) .settings-action-btn {
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.3);
    }
</style>
@endsection

@section('content')
<div class="settings-page grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Department shortcut -->
        <div class="settings-card bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="settings-card-header px-4 py-3 border-b border-gray-200">
                <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                    <i class="bi bi-building text-gray-500"></i>
                    Department
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="text-base font-semibold text-gray-900 truncate">{{ $department?->name ?? 'Department' }}</div>
                        <div class="mt-1 text-sm text-gray-600">
                            {{ $department?->address ?? '—' }}
                        </div>
                        <div class="mt-1 text-sm text-gray-600">
                            {{ $department?->phone ?? '' }}{{ (!empty($department?->phone) && !empty($department?->email)) ? ' • ' : '' }}{{ $department?->email ?? '' }}
                        </div>
                    </div>
                    <a href="{{ route('admin.department.edit') }}" class="settings-action-btn inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                        <i class="bi bi-pencil mr-1"></i>
                        Edit details
                    </a>
                </div>
            </div>
        </div>

        <!-- Admin Profile -->
        <div class="settings-card bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="settings-card-header px-4 py-3 border-b border-gray-200">
                <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                    <i class="bi bi-person-circle text-gray-500"></i>
                    Admin Profile
                </h3>
            </div>
            <div class="p-4">
                <div class="flex items-start gap-4">
                    @php
                        $user = Auth::user();
                        $photoPath = $user->profile_photo_path ?? null;
                        $hasFile = !empty($photoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath);
                    @endphp
                    @if($hasFile)
                        <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300 flex-shrink-0">
                    @else
                        <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-orange-600 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                            {{ substr($user->name ?? 'A', 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        <p class="text-xs text-gray-500 mt-1 capitalize">Role: {{ $user->role }}</p>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('profile.edit') }}" class="settings-action-btn inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                                <i class="bi bi-pencil mr-1"></i>
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: System Info -->
    <div class="space-y-6">
        <div class="settings-card bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="settings-card-header px-4 py-3 border-b border-gray-200">
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
                <div class="flex justify-between items-center py-2">
                    <span class="text-xs text-gray-600">Environment</span>
                    <span class="text-xs font-medium text-green-600">{{ app()->environment() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
