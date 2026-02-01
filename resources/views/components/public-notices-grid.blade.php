@foreach($notices as $notice)
    <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 notice-card"
         data-notice-id="{{ $notice->id }}"
         data-audience="{{ $notice->audience }}">
        <!-- Card Header with Importance Indicator -->
        <div class="h-2 {{ $notice->is_important ? 'bg-gradient-to-r from-red-500 to-orange-500' : 'bg-gradient-to-r from-blue-500 to-cyan-500' }}"></div>
        <div class="p-6">
            <!-- Notice Meta -->
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                    <i class="bi bi-calendar3"></i>
                    {{ $notice->formatted_date ?? ($notice->published_at_bs ?? 'N/A') }}
                </span>
                @if($notice->is_important)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-semibold">
                        <i class="bi bi-star-fill"></i>Important
                    </span>
                @endif
            </div>
            <!-- Notice Title -->
            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                {{ $notice->title }}
            </h3>
            <!-- Notice Content Preview -->
            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                {{ Str::limit(strip_tags($notice->message), 120) }}
            </p>
            <!-- Notice Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($notice->creator->name ?? 'A', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $notice->creator->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500">{{ $notice->audience_text ?? 'All' }}</p>
                    </div>
                </div>
                <!-- View Notice Button -->
                <button onclick="openPublicNoticeModal({{ $notice->id }})"
                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                    <i class="bi bi-eye"></i>
                    View
                </button>
            </div>
        </div>
    </div>
@endforeach
@if($notices->isEmpty())
    <div class="col-span-full text-center py-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
            <i class="bi bi-bell-slash text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Notices Found</h3>
        <p class="text-gray-600">There are no notices to display at the moment.</p>
    </div>
@endif

