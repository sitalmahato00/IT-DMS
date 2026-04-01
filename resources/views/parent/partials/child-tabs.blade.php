@props([
    'children' => collect(),
    'selectedChildId' => null,
    'routeName' => null,
    'extraParams' => [],
])

@if($children->isNotEmpty())
    <div class="rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Student Focus') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Switch between children to review specific records and timelines.') }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach($children as $child)
                    <a
                        href="{{ $routeName ? route($routeName, array_merge($extraParams, ['child' => $child['id']])) : '#' }}"
                        class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-medium transition {{ $selectedChildId === $child['id'] ? 'bg-red-600 text-white shadow-sm' : 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-950/20 dark:text-red-200 dark:hover:bg-red-950/30' }}"
                    >
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full {{ $selectedChildId === $child['id'] ? 'bg-white/20 text-white' : 'bg-white text-red-700 dark:bg-slate-800 dark:text-red-200' }}">
                            {{ strtoupper(substr($child['name'], 0, 1)) }}
                        </span>
                        <span>{{ $child['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

