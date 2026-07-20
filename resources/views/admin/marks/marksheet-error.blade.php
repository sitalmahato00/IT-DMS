@extends('admin.layouts.app')

@section('title', $title ?? 'Student Not Found')

@section('content')
@include('admin.components.admin-page-header', [
    'title' => $title ?? 'Student Not Found',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Marksheet Search', 'url' => route('admin.marksheet.search')],
        ['label' => $title ?? 'Student Not Found']
    ]
])

<div class="mx-auto max-w-3xl">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-rose-500">Marksheet Lookup</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $title ?? 'Student Not Found' }}</h2>
                <p class="mt-3 max-w-xl text-sm leading-6 text-slate-600">
                    {{ $message ?? 'The requested student record could not be located.' }}
                </p>
            </div>
            <div class="rounded-2xl bg-rose-50 px-4 py-3 text-rose-600">
                <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ $searchUrl ?? route('admin.marksheet.search') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-rose-600 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-700 transition">
                <i class="bi bi-search"></i>
                Back to Marksheet Search
            </a>
            <button type="button" onclick="window.close()" class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                <i class="bi bi-x-lg"></i>
                Close Tab
            </button>
        </div>
    </div>
</div>
@endsection

