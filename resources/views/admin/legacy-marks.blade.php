@extends('layouts.admin-layout')

@section('title', 'Marks')

@section('content')
<div class="admin-container">
    <x-admin-page-header title="Marks Management" subtitle="Manage exam marks and generate reports" />
    
    <div class="admin-grid">
        <!-- Stats Cards -->
        <x-admin-stats-cards />
        
        <!-- Filter Card -->
        <x-admin-filter-card 
            title="Filter Marks"
            filters="[
                {{__('exam')}}: 'dropdown',
                {{__('subject')}}: 'dropdown',
                {{__('semester')}}: 'dropdown',
                {{__('academic_year')}}: 'dropdown'
            ]"
        />
        
        <!-- Table Card -->
        <x-admin-table-card 
            title="{{__('Marks Records')}}"
            emptyMessage="{{__('No marks records found')}}"
            :actions="true"
            exportable
        >
            <x-admin-table-header 
                title="{{__('Marks')}}"
                count="{{ $marks->total() ?? 0 }}"
            />
            
            <tbody>
                @forelse($marks as $mark)
                    <tr>
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="bi bi-award text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium">{{ $mark->student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $mark->student->roll_no }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            {{ $mark->exam->exam_name ?? 'N/A' }}
                        </td>
                        <td class="p-4">
                            {{ $mark->subject->subject_name ?? 'N/A' }}
                        </td>
                        <td class="p-4">
                            <span class="font-mono font-semibold text-lg text-blue-600">{{ $mark->marks_obtained }}</span>
                            <span class="text-xs text-gray-500"> / {{ $mark->full_marks }}</span>
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $mark->result == 'pass' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($mark->result ?? 'pending') }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <x-admin-table-actions 
                                viewRoute="{{ route('admin.marks.show', $mark) }}"
                                editRoute="{{ route('admin.marks.edit', $mark) }}"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="text-gray-500">
                                <x-heroicon-o-chart-bar class="mx-auto h-12 w-12 mb-4" />
                                <p>{{__('No marks records. Enter first marks!')}}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-admin-table-card>
        
        <x-admin-pagination :items="$marks" />
    </div>
</div>
@endsection
