@extends('layouts.admin-layout')

@section('title', 'Attendance')

@section('content')
<div class="admin-container">
    <x-admin-page-header title="Attendance Management" subtitle="Track student attendance by class/date" />
    
    <div class="admin-grid">
        <!-- Stats Cards -->
        <x-admin-stats-cards />
        
        <!-- Filter Card -->
        <x-admin-filter-card 
            title="Filter Attendance"
            filters="[
                {{__('date')}}: 'range',
                {{__('class')}}: 'dropdown',
                {{__('subject')}}: 'dropdown',
                {{__('status')}}: ['present', 'absent', 'leave']
            ]"
        />
        
        <!-- Table Card -->
        <x-admin-table-card 
            title="{{__('Attendance Records')}}"
            emptyMessage="{{__('No attendance records found')}}"
            :actions="true"
            exportable
        >
            <x-admin-table-header 
                title="{{__('Attendance')}}"
                count="{{ $attendances->total() ?? 0 }}"
            />
            
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="bi bi-person text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium">{{ $attendance->student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $attendance->student->roll_no }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            {{ $attendance->subject->subject_name ?? 'N/A' }}
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $attendance->status == 'present' ? 'bg-green-100 text-green-800' : ($attendance->status == 'absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td class="p-4">
                            {{ $attendance->attendance_date }}
                        </td>
                        <td class="p-4">
                            {{ $attendance->remarks ?? '-' }}
                        </td>
                        <td class="p-4 text-right">
                            <x-admin-table-actions 
                                viewRoute="{{ route('admin.attendance.show', $attendance) }}"
                                editRoute="{{ route('admin.attendance.edit', $attendance) }}"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="text-gray-500">
                                <x-heroicon-o-calendar class="mx-auto h-12 w-12 mb-4" />
                                <p>{{__('No attendance records. Mark first attendance!')}}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-admin-table-card>
        
        <x-admin-pagination :items="$attendances" />
    </div>
</div>
@endsection
