@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Top Stats Cards - Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Students -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Students</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ number_format($totalStudents ?? 0) }}</p>
                    <div class="mt-3 text-xs text-blue-700">
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-arrow-up text-green-600"></i>
                            <span class="text-green-600 font-semibold">+12%</span> from last month
                        </span>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-lg flex-shrink-0">
                    <i class="bi bi-people text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Teachers -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <p class="text-xs text-orange-600 font-semibold uppercase tracking-wide">Teachers</p>
                    <p class="text-3xl font-bold text-orange-900 mt-2">{{ number_format($teachers ?? 0) }}</p>
                    <div class="mt-3 text-xs text-orange-700">
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-arrow-up text-green-600"></i>
                            <span class="text-green-600 font-semibold">+3.4%</span> active teaching
                        </span>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-lg flex-shrink-0">
                    <i class="bi bi-briefcase text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <!-- Parents -->
        <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl border border-pink-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <p class="text-xs text-pink-600 font-semibold uppercase tracking-wide">Parents</p>
                    <p class="text-3xl font-bold text-pink-900 mt-2">{{ number_format($parents ?? 0) }}</p>
                    <div class="mt-3 text-xs text-pink-700">
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-arrow-down text-red-600"></i>
                            <span class="text-red-600 font-semibold">-2.8%</span> from last month
                        </span>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-lg flex-shrink-0">
                    <i class="bi bi-person-vcard text-2xl text-pink-600"></i>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <p class="text-xs text-green-600 font-semibold uppercase tracking-wide">Attendance Rate</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ isset($avgAttendance) ? $avgAttendance.'%' : '—' }}</p>
                    <div class="mt-3 text-xs text-green-700">
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-arrow-up text-green-600"></i>
                            <span class="text-green-600 font-semibold">+6%</span> this semester
                        </span>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-lg flex-shrink-0">
                    <i class="bi bi-percent text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Section - Charts Row (2 columns) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Attendance Overview Chart (Left) -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Attendance Trends</h3>
                <i class="bi bi-graph-up text-gray-400"></i>
            </div>
            <div class="h-48">
                <canvas id="attChart" class="w-full h-full" data-chart='@json(["labels" => $attendancePercentage['labels'] ?? [], "data" => $attendancePercentage['data'] ?? [], "details" => $attendancePercentage['details'] ?? []])'></canvas>
            </div>
        </div>

        <!-- Grade Distribution Pie Chart (Right) -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Grade Distribution</h3>
                <i class="bi bi-pie-chart text-gray-400"></i>
            </div>
            <div class="h-48 flex items-center justify-center">
                <x-grade-pie-chart :gradeDistribution="['A' => 28, 'B' => 35, 'C' => 22, 'D' => 10, 'F' => 5]" />
            </div>
        </div>
    </div>

    <!-- Bottom Section - Three Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Notices -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Recent Notices</h3>
                <i class="bi bi-bell text-gray-400"></i>
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @if(!empty($recentNotices))
                    @foreach($recentNotices as $notice)
                        @php
                            $title = is_array($notice) ? ($notice['title'] ?? $notice['heading'] ?? 'Notice') : ($notice->title ?? $notice->heading ?? 'Notice');
                            $message = is_array($notice) ? ($notice['message'] ?? $notice['content'] ?? $notice['body'] ?? '') : ($notice->message ?? $notice->content ?? $notice->body ?? '');
                            $createdAt = is_array($notice) ? ($notice['created_at'] ?? null) : ($notice->created_at ?? null);
                            $timeDisplay = $createdAt ? (is_string($createdAt) ? $createdAt : $createdAt->diffForHumans()) : 'Recently';
                        @endphp
                        <div class="flex items-start gap-2 pb-3 border-b border-gray-100 last:border-0">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mt-1.5 flex-shrink-0"></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-900 truncate">{{ $title }}</p>
                                <p class="text-xs text-gray-600 line-clamp-2">{{ Str::limit($message, 60) }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $timeDisplay }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-xs text-gray-500 py-3">No recent notices</p>
                @endif
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Recent Activities</h3>
                <i class="bi bi-activity text-gray-400"></i>
            </div>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @if(!empty($recentActivities))
                    @foreach($recentActivities as $act)
                        @php
                            $action = is_array($act) ? ($act['action'] ?? $act['activity'] ?? 'Action') : ($act->action ?? $act->activity ?? 'Action');
                            $userName = is_array($act) ? ($act['user_name'] ?? 'Unknown') : ($act->user_name ?? 'Unknown');
                            $timestamp = is_array($act) ? ($act['timestamp'] ?? null) : ($act->timestamp ?? null);
                            $timeDisplay = $timestamp ? (is_string($timestamp) ? $timestamp : $timestamp->diffForHumans()) : 'Recently';
                        @endphp
                        @php $logId = is_array($act) ? ($act['id'] ?? null) : ($act->id ?? null); @endphp
                        @if($logId)
                        <a href="{{ route('admin.audit-logs.show', $logId) }}" class="block hover:bg-gray-50 rounded-md">
                        <div class="flex items-start gap-2 pb-2 border-b border-gray-100 last:border-0 p-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-1.5 flex-shrink-0"></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-900">{{ $action }}</p>
                                <p class="text-xs text-gray-600 truncate">{{ $userName }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $timeDisplay }}</p>
                            </div>
                        </div>
                        </a>
                        @else
                        <div class="flex items-start gap-2 pb-2 border-b border-gray-100 last:border-0">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-1.5 flex-shrink-0"></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-900">{{ $action }}</p>
                                <p class="text-xs text-gray-600 truncate">{{ $userName }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $timeDisplay }}</p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-6">
                        <i class="bi bi-inbox text-2xl text-gray-300 mb-2"></i>
                        <p class="text-xs text-gray-500">No recent activities</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- New Students -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">New Students</h3>
                <i class="bi bi-person-plus text-gray-400"></i>
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @if(!empty($newStudents))
                    @foreach($newStudents as $student)
                        @php
                            $name = is_array($student) ? ($student['name'] ?? 'Student') : ($student->name ?? 'Student');
                            $email = is_array($student) ? ($student['email'] ?? 'N/A') : ($student->email ?? 'N/A');
                            $createdAt = is_array($student) ? ($student['created_at'] ?? null) : ($student->created_at ?? null);
                            $timeDisplay = $createdAt ? (is_string($createdAt) ? $createdAt : $createdAt->diffForHumans()) : 'Recently';
                        @endphp
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-semibold text-blue-600 flex-shrink-0">
                                {{ substr($name, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-900 truncate">{{ $name }}</p>
                                <p class="text-xs text-gray-600 truncate">{{ $email }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $timeDisplay }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-6">
                        <i class="bi bi-inbox text-2xl text-gray-300 mb-2"></i>
                        <p class="text-xs text-gray-500">No new students this period</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bottom Section - Class Attendance Details -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Today's Classes - Attendance Marked</h3>
            <i class="bi bi-calendar-check text-gray-400"></i>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Course / Subject</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Teacher Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Semester</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-900">Students Present</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-900">Total Students</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-900">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($recentAttendance))
                        @foreach($recentAttendance as $att)
                            @php
                                $date = is_array($att) ? ($att['date'] ?? date('Y-m-d')) : ($att->date ?? date('Y-m-d'));
                                $courseName = is_array($att) ? ($att['course_name'] ?? $att['subject_name'] ?? 'N/A') : ($att->course_name ?? $att->subject_name ?? 'N/A');
                                $teacherName = is_array($att) ? ($att['teacher_name'] ?? 'Not Assigned') : ($att->teacher_name ?? 'Not Assigned');
                                $semester = is_array($att) ? ($att['semester'] ?? 'N/A') : ($att->semester ?? 'N/A');
                                $presentCount = is_array($att) ? ($att['present_count'] ?? 0) : ($att->present_count ?? 0);
                                $totalCount = is_array($att) ? ($att['total_students'] ?? 0) : ($att->total_students ?? 0);
                                $attendanceRate = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 0;
                                
                                // Color based on attendance percentage
                                if ($attendanceRate >= 80) {
                                    $statusClass = 'bg-green-100 text-green-700';
                                    $statusLabel = 'Good';
                                } elseif ($attendanceRate >= 60) {
                                    $statusClass = 'bg-yellow-100 text-yellow-700';
                                    $statusLabel = 'Average';
                                } else {
                                    $statusClass = 'bg-red-100 text-red-700';
                                    $statusLabel = 'Low';
                                }
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ is_string($date) ? $date : $date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    <span class="inline-flex items-center gap-2">
                                        <i class="bi bi-book text-blue-500"></i>
                                        {{ $courseName }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-xs font-semibold text-orange-600">
                                            {{ substr($teacherName, 0, 1) }}
                                        </div>
                                        <span>{{ $teacherName }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                        {{ $semester }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-gray-900">{{ $presentCount }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $totalCount }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                        <span class="text-xs text-gray-600">{{ $attendanceRate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="border-b border-gray-100">
                            <td colspan="7" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-inbox text-3xl text-gray-300 mb-2"></i>
                                    <p class="text-sm text-gray-500">No classes with attendance marked today</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@vite('resources/js/admin-dashboard.js')
@endsection

