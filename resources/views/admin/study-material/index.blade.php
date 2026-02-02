@extends('admin.layouts.app')


@section('title', 'Document Management')

@section('content')
<div class="space-y-4">
    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded text-xs">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded text-xs">
        {{ session('error') }}
    </div>
    @endif

    {{-- Statistics Cards --}}
    @include('admin.study-material.partials.statistics-cards')

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900"> Documents Management</h1>
        <button onclick="openAddMaterialModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium">
            <i class="bi bi-plus-lg"></i>
            <span>Add Material</span>
        </button>
    </div>

    {{-- Filters --}}
    @include('admin.study-material.partials.filters')

    {{-- Materials Table --}}
    @include('admin.study-material.partials.materials-table')
</div>

{{-- Add Modal --}}
@include('admin.study-material.partials.add-modal')

{{-- Edit Modal --}}
@include('admin.study-material.partials.edit-modal')

{{-- Delete Modal --}}
@include('admin.study-material.partials.delete-modal')
@endsection
