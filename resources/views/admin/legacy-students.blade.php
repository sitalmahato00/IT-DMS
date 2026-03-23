@extends('layouts.admin-layout')

@section('title', 'Students')

@section('content')
<div class="admin-container">
    <x-admin-page-header title="Students Management" subtitle="View and manage all students" />
    
    <div class="admin-grid">
        <!-- Stats Cards -->
        <x-admin-stats-cards />
        
        <!-- Filter Card -->
        <x-admin-filter-card 
            title="Filter Students"
            filters="[
                {{__('status')}}: ['active', 'inactive', 'alumni'],
                {{__('program')}}: ['BIT', 'BCA', 'BBA'],
                {{__('semester')}}: [1,2,3,4,5,6,7,8],
                {{__('batch')}}: [2020,2021,2022,2023]
            ]"
        />
        
        <!-- Table Card -->
        <x-admin-table-card 
            title="{{__('Students List')}}"
            emptyMessage="{{__('No students found')}}"
            :actions="true"
            exportable
            searchable
        >
            <x-admin-table-header 
                title="{{__('Students')}}"
                count="{{ $students->total() ?? 0 }}"
                addRoute="{{ route('admin.students.create') }}"
                addLabel="{{__('Add Student')}}"
            />
            
            <x-admin-table-actions />
            
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="p-4">
                            <div class="flex items-center">
                                <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->roll_no }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $student->program ?? 'BIT' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($student->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="p-4">
                            {{ $student->email }}
                        </td>
                        <td class="p-4">
                            {{ $student->phone }}
                        </td>
                        <td class="p-4 text-right">
                            <x-admin-table-actions 
                                viewRoute="{{ route('admin.students.show', $student) }}"
                                editRoute="{{ route('admin.students.edit', $student) }}"
                                deleteRoute="{{ route('admin.students.destroy', $student) }}"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="text-gray-500">
                                <x-heroicon-o-users class="mx-auto h-12 w-12 mb-4" />
                                <p>{{__('No students found. Create your first student!')}}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-admin-table-card>
        
        <x-admin-pagination :items="$students" />
    </div>
</div>
@endsection
