@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();

    $departmentName = $department
        ? (($locale === 'ne' && !empty($department->name_nepali)) ? $department->name_nepali : $department->name)
        : ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology Department');

    $departmentLogoUrl = $department?->getLogoUrl() ?? asset('images/default-logo.svg');
@endphp

@extends('layouts.public')

@section('content')
    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <header class="border-b border-white/10 bg-gradient-to-r from-red-700 via-red-600 to-red-700 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ $departmentLogoUrl }}" alt="" class="h-10 w-10 rounded-lg bg-white/10 object-contain ring-1 ring-white/20">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-white">{{ $departmentName }}</div>
                        <div class="mt-0.5 text-xs text-white/80">{{ $locale === 'ne' ? 'शिक्षक तथा स्टाफ' : 'Faculty & Staff' }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}#faculty" class="hidden rounded-lg border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/40 sm:inline-flex">
                        {{ $locale === 'ne' ? 'गृह पृष्ठ' : 'Home' }}
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-white/40">
                            {{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-white/40">
                            {{ $locale === 'ne' ? 'लगइन' : 'Login' }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">
                        {{ $locale === 'ne' ? 'हाम्रा शिक्षक' : 'Meet Our Faculty' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ $locale === 'ne' ? 'विभागका नेतृत्व र शिक्षकहरूको सूची।' : 'Department leadership and active faculty list.' }}
                    </p>
                </div>
            </div>

            @if (($hods ?? collect())->isNotEmpty())
                <section class="mt-10">
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $locale === 'ne' ? 'विभाग प्रमुख (HOD)' : 'HOD' }}
                    </div>

                    <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach (($hods ?? collect()) as $leader)
                            @php
                                $leaderName = $leader->name ?: 'Admin';
                                $leaderInitial = Str::of($leaderName)->trim()->substr(0, 1)->upper();
                                $leaderDept = $leader->department ?? null;
                                $leaderPhoto = $leader->profile_photo_url ?? null;
                                $leaderMeta = $leaderDept ?: ($leader->email ?: null);
                            @endphp
                            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950">
                                <div class="flex h-28 items-center justify-center bg-red-100/60 dark:bg-red-950/25">
                                    @if (!empty($leaderPhoto))
                                        <img src="{{ $leaderPhoto }}" alt="{{ $leaderName }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-red-200 text-red-800 dark:bg-red-900/40 dark:text-red-200">
                                            <span class="text-sm font-bold">{{ $leaderInitial }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <div class="truncate text-sm font-bold text-gray-900 dark:text-gray-100">{{ $leaderName }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $locale === 'ne' ? 'विभाग प्रमुख' : 'HOD / Admin' }}</div>
                                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                        {{ $locale === 'ne' ? 'विवरण' : 'Detail' }}:
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $leaderMeta ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not specified') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="mt-12">
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ $locale === 'ne' ? 'शिक्षक सूची' : 'Faculty List' }}
                </div>

                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse (($teachers ?? collect()) as $t)
                        @php
                            $name = $t->user?->name ?: 'Unknown';
                            $initials = Str::of($name)->trim()->explode(' ')->filter()->take(2)->map(fn ($p) => Str::substr($p, 0, 1))->join('');
                            $photoUrl = $t->user?->profile_photo_url;
                            $titleText = $t->qualification ?: ($locale === 'ne' ? 'शिक्षक' : 'Professor');
                            $expertiseText = $t->bio ?: ($t->department ?: ($t->user?->department ?: null));
                        @endphp
                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950">
                            <div class="flex h-28 items-center justify-center bg-gray-200/80 dark:bg-gray-900">
                                @if (!empty($photoUrl))
                                    <img src="{{ $photoUrl }}" alt="{{ $name }}" class="h-full w-full object-cover" />
                                @else
                                    <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-gray-300 text-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                        <span class="text-sm font-bold">{{ $initials ?: Str::of($name)->substr(0, 1)->upper() }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="truncate text-sm font-bold text-gray-900 dark:text-gray-100">{{ $name }}</div>
                                <div class="mt-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $titleText }}</div>
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                    {{ $locale === 'ne' ? 'विशेषज्ञता' : 'Expertise Area' }}:
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $expertiseText ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not specified') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300 sm:col-span-2 lg:col-span-4">
                            {{ $locale === 'ne' ? 'हाल कुनै शिक्षक उपलब्ध छैन।' : 'No faculty records available yet.' }}
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
@endsection
