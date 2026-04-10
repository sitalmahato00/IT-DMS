@extends('parent.layouts.parentlayout')

@section('title', __('My Children'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('My Children') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Manage and monitor your children\'s information') }}</p>
        </div>
    </div>

    @if($children->isEmpty())
        <!-- No Children Message -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 dark:bg-amber-900 rounded-full mb-4">
                <i class="bi bi-mortarboard text-2xl text-amber-600 dark:text-amber-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('No Children Added') }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ __('You haven\'t added any children to your account yet. Contact your administrator to link your children.') }}</p>
        </div>
    @else
        <!-- Children List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($children as $child)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-shadow">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-amber-600 to-amber-700 dark:from-amber-800 dark:to-amber-900 p-4 text-white">
                        <h3 class="text-lg font-semibold">{{ $child->user?->name ?? 'Unknown' }}</h3>
                        <p class="text-amber-100 text-sm">{{ __('Roll No: ') }}{{ $child->roll_no ?? 'N/A' }}</p>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Semester') }}</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $child->semester ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Status') }}</p>
                                <div class="mt-1">
                                    @if($child->status === 'active')
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">{{ __('Active') }}</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Email') }}</p>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $child->user?->email ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Phone') }}</p>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $child->phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex gap-3">
                        <a href="{{ route('parent.children.show', $child->id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-800 dark:hover:bg-amber-700 text-white rounded-lg font-medium transition">
                            <i class="bi bi-eye"></i>
                            {{ __('View Profile') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

