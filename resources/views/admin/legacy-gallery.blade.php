@extends('layouts.admin-layout')

@section('title', 'Gallery')

@section('content')
<div class="admin-container">
    <x-admin-page-header title="Photo Gallery" subtitle="Manage campus photos and albums" />
    
    <div class="admin-grid">
        <!-- Stats Cards -->
        <x-admin-stats-cards />
        
        <!-- Filter Card -->
        <x-admin-filter-card 
            title="Filter Gallery"
            filters="[
                {{__('category')}}: ['campus', 'events', 'students', 'faculty'],
                {{__('date')}}: 'range'
            ]"
        />
        
        <!-- Table Card -->
        <x-admin-table-card 
            title="{{__('Gallery Images')}}"
            emptyMessage="{{__('No images found')}}"
            :actions="true"
        >
            <x-admin-table-header 
                title="{{__('Gallery')}}"
                count="{{ $images->total() ?? 0 }}"
                addRoute="{{ route('admin.gallery.create') }}"
                addLabel="{{__('Add Image')}}"
            />
            
            <tbody>
                @forelse($images as $image)
                    <tr>
                        <td class="p-4">
                            <div class="flex items-center">
                                <img src="{{ $image->thumbnail_url }}" alt="{{ $image->title }}" class="w-16 h-16 object-cover rounded-lg mr-4">
                                <div>
                                    <div class="font-medium">{{ $image->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $image->category }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            {{ Str::limit($image->description, 50) }}
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                {{ $image->views_count }} {{__('views')}}
                            </span>
                        </td>
                        <td class="p-4">
                            {{ $image->created_at->format('M d, Y') }}
                        </td>
                        <td class="p-4 text-right">
                            <x-admin-table-actions 
                                viewRoute="{{ route('admin.gallery.show', $image) }}"
                                editRoute="{{ route('admin.gallery.edit', $image) }}"
                                deleteRoute="{{ route('admin.gallery.destroy', $image) }}"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="text-gray-500">
                                <x-heroicon-o-photo class="mx-auto h-12 w-12 mb-4" />
                                <p>{{__('No gallery images. Upload first photo!')}}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-admin-table-card>
        
        <x-admin-pagination :items="$images" />
    </div>
</div>
@endsection
