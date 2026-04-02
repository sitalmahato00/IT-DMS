@extends('admin.layouts.app')

@section('title', __('Notifications'))

@section('styles')
<script>
    document.documentElement.classList.add('notifications-ui-enhanced');
</script>
<style>
    html.notifications-ui-enhanced:not(.dark) .notifications-page {
        color: #0f172a;
    }

    html.notifications-ui-enhanced:not(.dark) .notifications-card {
        border-radius: 30px;
        border-color: rgba(215, 227, 243, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(248, 251, 255, 0.98));
        box-shadow: 0 28px 58px -42px rgba(29, 78, 216, 0.28);
    }

    html.notifications-ui-enhanced:not(.dark) .notification-item {
        border-radius: 22px;
        border-color: rgba(219, 234, 254, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 250, 255, 0.96));
        box-shadow: 0 18px 34px -30px rgba(59, 130, 246, 0.35);
    }

    html.notifications-ui-enhanced:not(.dark) .notification-item:hover {
        background: linear-gradient(90deg, rgba(239, 246, 255, 0.84), rgba(255, 255, 255, 0.97));
    }

    html.notifications-ui-enhanced:not(.dark) .notification-status-chip {
        border-radius: 999px;
        padding: 0.4rem 0.8rem;
        font-weight: 700;
        box-shadow: 0 12px 24px -20px rgba(15, 23, 42, 0.24);
    }

    html.notifications-ui-enhanced:not(.dark) .notifications-empty-state {
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(255, 255, 255, 0.98));
    }

    html.notifications-ui-enhanced:not(.dark) #confirmModal > div {
        border-radius: 30px;
        border: 1px solid rgba(215, 227, 243, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(247, 251, 255, 0.98));
        box-shadow: 0 34px 70px -38px rgba(15, 23, 42, 0.42);
    }

    html.notifications-ui-enhanced:not(.dark) #confirmHeader {
        background: linear-gradient(135deg, rgba(239, 246, 255, 0.95), rgba(219, 234, 254, 0.92));
    }

    html.notifications-ui-enhanced:not(.dark) #confirmCancel,
    html.notifications-ui-enhanced:not(.dark) #confirmOk {
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.36);
    }
</style>
@endsection

@section('content')
<div class="notifications-page space-y-6">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Toast Notification - Uses global toast system from layout -->

    <!-- Professional Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300 animate-fade-in">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all duration-300 animate-scale-up">
            <!-- Header with icon background -->
            <div id="confirmHeader" class="relative h-20 bg-gradient-to-r from-blue-50 to-blue-100 flex items-center justify-center">
                <div id="confirmIconContainer" class="absolute h-24 w-24 rounded-full flex items-center justify-center" style="transform: translateY(50%);">
                    <i id="confirmIcon" class="text-4xl"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="pt-16 px-6 pb-6 text-center">
                <h3 id="confirmTitle" class="text-xl font-bold text-gray-900 mb-2">Confirm Action</h3>
                <p id="confirmMessage" class="text-gray-600 text-sm leading-relaxed mb-8">Are you sure you want to proceed?</p>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-3">
                    <button id="confirmCancel" class="flex-1 px-4 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-150 active:scale-95">
                        <i class="bi bi-x-circle mr-1"></i>Cancel
                    </button>
                    <button id="confirmOk" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-150 active:scale-95 shadow-lg hover:shadow-xl">
                        <i id="confirmOkIcon" class="bi bi-check-circle mr-1"></i><span id="confirmOkText">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-card class="notifications-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Notifications') }}</h3>
        </div>

        @if($notifications->count())
            <div class="space-y-2">
                @foreach($notifications as $notification)
                    @php
                        // Notifications often store data payload in `data` attribute
                        $data = is_array($notification->data) ? $notification->data : (array) ($notification->data ?? []);
                        $title = $data['title'] ?? ($data['heading'] ?? __('Notification'));
                        $message = $data['message'] ?? ($data['body'] ?? '');
                        $time = $notification->created_at ? $notification->created_at->diffForHumans() : '';
                    @endphp

                    <div class="notification-item p-3 bg-white border border-gray-100 rounded-md flex items-start gap-3 transition">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $title }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ Str::limit($message, 200) }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $time }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            @if(!$notification->read_at)
                                <span class="notification-status-chip inline-block px-2 py-1 text-xs bg-blue-50 text-blue-600 rounded">{{ __('New') }}</span>
                            @else
                                <span class="notification-status-chip inline-block px-2 py-1 text-xs bg-gray-50 text-gray-600 rounded">{{ __('Read') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <x-pagination :paginator="$notifications" />
            </div>
        @else
            <div class="notifications-empty-state text-center py-8 text-gray-500">{{ __('No notifications found') }}</div>
        @endif
    </x-card>
</div>
@endsection
