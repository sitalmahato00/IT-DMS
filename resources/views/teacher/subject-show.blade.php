@extends('teacher.layouts.teacherlayout')

@section('title', $subject->subject_name)

@section('content')
<div class="space-y-6">
    <!-- Back Button and Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.subjects') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                <i class="bi bi-arrow-left text-gray-600 dark:text-gray-300"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->subject_name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">{{ $subject->subject_code }} • Semester {{ $assignment->semester ?? $subject->semester }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                {{ ucfirst($assignment->role ?? 'Primary') }}
            </span>
        </div>
    </div>

    <!-- Main Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <!-- Semester -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-medium">Semester</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $assignment->semester ?? $subject->semester }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/40 p-2.5 rounded-lg">
                    <i class="bi bi-calendar3 text-lg text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Enrolled Students -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-medium">Enrolled Students</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $students->count() }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/40 p-2.5 rounded-lg">
                    <i class="bi bi-people text-lg text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Category -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-medium">Category</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1 truncate">{{ $subject->category ?? 'General' }}</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/40 p-2.5 rounded-lg">
                    <i class="bi bi-tag text-lg text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>

        <!-- Credits -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-medium">Credits</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $subject->credits ?? 4 }}</p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-900/40 p-2.5 rounded-lg">
                    <i class="bi bi-star text-lg text-amber-600 dark:text-amber-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('teacher.attendance') }}?subject={{ $subject->id }}" class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 hover:border-red-300 dark:hover:border-red-700 transition group">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 dark:bg-red-900/40 p-2 rounded-lg group-hover:bg-red-600 transition">
                    <i class="bi bi-calendar-check text-red-600 dark:text-red-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Mark Attendance</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Take today's attendance</p>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.marks') }}?subject={{ $subject->id }}" class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 transition group">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 dark:bg-blue-900/40 p-2 rounded-lg group-hover:bg-blue-600 transition">
                    <i class="bi bi-clipboard-data text-blue-600 dark:text-blue-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Enter Marks</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Add student marks</p>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.study-materials') }}?subject={{ $subject->id }}" class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-700 transition group">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 dark:bg-green-900/40 p-2 rounded-lg group-hover:bg-green-600 transition">
                    <i class="bi bi-journal-bookmark text-green-600 dark:text-green-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Upload Materials</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Add study materials</p>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.students') }}?subject={{ $subject->id }}" class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-700 transition group">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 dark:bg-purple-900/40 p-2 rounded-lg group-hover:bg-purple-600 transition">
                    <i class="bi bi-people text-purple-600 dark:text-purple-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">View Students</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Enrolled student list</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Attendance</h2>
        </div>
        <div class="p-5">
            @if($recentAttendance->isNotEmpty())
                <div class="space-y-3">
                    @foreach($recentAttendance as $date => $records)
                        @php
                            $present = $records->where('status', 'present')->count();
                            $absent = $records->where('status', 'absent')->count();
                            $total = $records->count();
                            $percentage = $total > 0 ? round(($present / $total) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                                    <i class="bi bi-calendar-event text-red-600 dark:text-red-400"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $total }} students</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $percentage }}%</p>
                                <p class="text-xs text-green-600 dark:text-green-400">{{ $present }} present</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-calendar-x text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                    <p class="text-gray-500 dark:text-gray-400">No attendance records yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Enrolled Students -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Enrolled Students</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $students->count() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Roll No</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reg. No</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ strtoupper(substr($student->name, 0, 2)) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $student->roll_no ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $student->registration_number ?? 'N/A' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400">
                                    Active
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                No students enrolled in this subject
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
