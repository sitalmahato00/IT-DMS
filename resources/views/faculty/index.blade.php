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

@push('head')
    @include('partials.public-page-theme')
@endpush

@section('content')
    <div class="brand-page-bg min-h-screen text-gray-900 dark:text-gray-100">
        <div class="brand-page-orb left-[-4rem] top-24 h-36 w-36 bg-red-300/55 dark:bg-red-500/20"></div>
        <div class="brand-page-orb right-[-2rem] top-[24rem] h-44 w-44 bg-red-200/55 [animation-delay:1.3s] dark:bg-red-400/20"></div>
        <header class="border-b border-white/10 bg-gradient-to-r from-red-700 via-red-600 to-red-800 backdrop-blur-xl">
            <div class="brand-page-shell mx-auto flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ $departmentLogoUrl }}" alt="" class="h-10 w-10 rounded-lg bg-white/10 object-contain ring-1 ring-white/20">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-white">{{ $departmentName }}</div>
                        <div class="mt-0.5 text-xs text-white/80">{{ $locale === 'ne' ? 'शिक्षक तथा स्टाफ' : 'Faculty & Staff' }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}#faculty" class="hidden rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/40 sm:inline-flex">
                        {{ $locale === 'ne' ? 'गृह पृष्ठ' : 'Home' }}
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="brand-page-cta rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-white/40">
                            {{ $locale === 'ne' ? 'ड्यासबोर्ड' : 'Dashboard' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="brand-page-cta rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-white/40">
                            {{ $locale === 'ne' ? 'लगइन' : 'Login' }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="brand-page-shell mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="brand-page-chip">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                        {{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}
                    </p>
                    <h1 class="brand-page-title mt-4 text-3xl font-bold text-gray-900 dark:text-gray-100 sm:text-5xl">
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
                            <div class="brand-page-card overflow-hidden rounded-[1.75rem]">
                                <div class="flex h-40 items-center justify-center bg-gradient-to-br from-red-300 via-red-400 to-red-600 dark:from-red-900/50 dark:via-red-800/40 dark:to-red-950/40">
                                    @if (!empty($leaderPhoto))
                                        <img src="{{ $leaderPhoto }}" alt="{{ $leaderName }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-white/20 text-white ring-1 ring-white/25 dark:bg-white/10 dark:text-red-100">
                                            <span class="text-4xl font-bold">{{ $leaderInitial }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="border-t border-red-100 bg-gradient-to-br from-red-100 via-red-50 to-white p-5 dark:border-red-900/30 dark:bg-gradient-to-br dark:from-red-950/35 dark:via-slate-950 dark:to-slate-950">
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
                        <div class="brand-page-card overflow-hidden rounded-[1.75rem]">
                            <div class="flex h-40 items-center justify-center bg-gradient-to-br from-red-200 via-red-300 to-red-500 dark:from-red-900/45 dark:via-red-800/35 dark:to-red-950/35">
                                @if (!empty($photoUrl))
                                    <img src="{{ $photoUrl }}" alt="{{ $name }}" class="h-full w-full object-cover" />
                                @else
                                    <div class="flex h-20 w-24 items-center justify-center rounded-lg bg-white/20 text-white ring-1 ring-white/25 dark:bg-white/10 dark:text-red-100">
                                        <span class="text-4xl font-bold">{{ $initials ?: Str::of($name)->substr(0, 1)->upper() }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="border-t border-red-100 bg-gradient-to-br from-red-100 via-red-50 to-white p-5 dark:border-red-900/30 dark:bg-gradient-to-br dark:from-red-950/35 dark:via-slate-950 dark:to-slate-950">
                                <div class="truncate text-sm font-bold text-gray-900 dark:text-gray-100">{{ $name }}</div>
                                <div class="mt-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $titleText }}</div>
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                    {{ $locale === 'ne' ? 'विशेषज्ञता' : 'Expertise Area' }}:
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $expertiseText ?: ($locale === 'ne' ? 'उपलब्ध छैन' : 'Not specified') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="brand-page-panel rounded-[1.75rem] border-dashed border-red-200 bg-gradient-to-br from-white to-red-50 p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:bg-gradient-to-br dark:from-slate-950 dark:to-red-950/10 dark:text-gray-300 sm:col-span-2 lg:col-span-4">
                            {{ $locale === 'ne' ? 'हाल कुनै शिक्षक उपलब्ध छैन।' : 'No faculty records available yet.' }}
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
@endsection
