@props(['title' => '', 'subtitle' => '', 'icon' => null, 'size' => 'md'])

@php
    $outerWidth = $size === 'sm' ? 'max-w-lg' : 'max-w-4xl';
    $headerPadding = $size === 'sm' ? 'p-4' : 'p-5';
    $contentPadding = $size === 'sm' ? 'p-4' : 'p-6';
@endphp

<div class="{{ $outerWidth }} mx-auto">
    <div class="relative rounded-lg overflow-hidden shadow-lg">
        {{-- Gradient header with avatar and title --}}
        <div class="{{ $headerPadding }} bg-gradient-to-r from-red-600 via-orange-500 to-yellow-500 text-white">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="-mt-8 w-16 h-16 rounded-full ring-4 ring-white overflow-hidden flex items-center justify-center bg-white/20">
                        @if(!empty($icon) && is_string($icon))
                            {!! $icon !!}
                        @else
                            {{ $icon ?? '' }}
                        @endif
                    </div>

                    <div class="ml-1">
                        <div class="text-xl font-semibold leading-tight">{{ $title }}</div>
                        @if($subtitle)
                            <div class="text-sm opacity-90">{{ $subtitle }}</div>
                        @endif
                    </div>
                </div>

                <div class="ml-auto">
                    {{-- header slot for actions (e.g., close/edit) --}}
                    {{ $header ?? '' }}
                </div>
            </div>
        </div>

        {{-- Elevated white content panel (overlaps header) --}}
        <div class="-mt-6 px-4">
            <div class="bg-white rounded-lg shadow-sm {{ $contentPadding }}">
                {{ $slot }}
            </div>
        </div>

        {{-- Optional footer area --}}
        @if(isset($footer) && trim($footer) !== '')
            <div class="px-4 pb-4 mt-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
