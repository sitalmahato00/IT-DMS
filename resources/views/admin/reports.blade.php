@extends('admin.layouts.app')

@section('title', 'Report Generation')

@section('content')
<div class="space-y-4">
    <!-- Report Statistics Cards - First Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Attendance Report Card -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Attendance Report</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $attendanceStats['avg'] }}%</p>
                    <p class="text-gray-600 text-xs mt-1">Average attendance rate</p>
                </div>
                <div class="bg-blue-100 p-2.5 rounded-lg">
                    <i class="bi bi-calendar-check text-lg text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="text-green-600 font-medium">+{{ $attendanceStats['change'] }}%</span>
                <span class="text-gray-600">from last month</span>
            </div>
        </div>

        <!-- Marks Report Card -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Marks Report</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $marksStats['avg'] }}%</p>
                    <p class="text-gray-600 text-xs mt-1">Average grade percentage</p>
                </div>
                <div class="bg-green-100 p-2.5 rounded-lg">
                    <i class="bi bi-graph-up text-lg text-green-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="text-green-600 font-medium">+{{ $marksStats['change'] }}%</span>
                <span class="text-gray-600">from last month</span>
            </div>
        </div>

        <!-- Student Progress Card -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Student Progress</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $progressStats['completion'] }}%</p>
                    <p class="text-gray-600 text-xs mt-1">Course completion rate</p>
                </div>
                <div class="bg-red-100 p-2.5 rounded-lg">
                    <i class="bi bi-bar-chart text-lg text-red-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="text-green-600 font-medium">+{{ $progressStats['change'] }}%</span>
                <span class="text-gray-600">from last month</span>
            </div>
        </div>
    </div>

    <!-- Filters - Second Row -->

    <form id="reportFilterForm" action="{{ route('admin.reports') }}" method="GET" class="bg-white p-3 rounded shadow-sm border border-gray-200">
        @csrf
        <h3 class="text-sm font-semibold text-gray-900 mb-2">Report Filters</h3>
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                <select name="semester" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="">All Semesters</option>
                    @foreach($semesters as $s)
                        <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Course *</label>
                <select name="subject" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="">All Courses</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ $subject == $sub->id ? 'selected' : '' }}>{{ $sub->subject_name }} ({{ $sub->subject_code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Report Type</label>
                <select name="report_type" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="all" {{ $reportType == 'all' ? 'selected' : '' }}>All Reports</option>
                    <option value="attendance" {{ $reportType == 'attendance' ? 'selected' : '' }}>Attendance Report</option>
                    <option value="marks" {{ $reportType == 'marks' ? 'selected' : '' }}>Marks Report</option>
                    <option value="progress" {{ $reportType == 'progress' ? 'selected' : '' }}>Student Progress</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium flex items-center justify-center gap-1.5 transition">
                    <i class="bi bi-funnel text-xs"></i>
                    <span>Filter</span>
                </button>
                <button type="button" id="resetFilterBtn" class="flex-1 px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded text-xs font-medium flex items-center justify-center gap-1.5 transition">
                    <i class="bi bi-arrow-clockwise text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Monthly Attendance Trends Chart -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Monthly Attendance Trends</h3>
            <div class="h-48 bg-gradient-to-b from-blue-50 to-transparent rounded flex items-end justify-around p-3 gap-1">
                @if(isset($monthlyAttendance['months']) && count($monthlyAttendance['months']) > 0)
                    @foreach($monthlyAttendance['months'] as $index => $month)
                        <div class="flex flex-col items-center justify-end gap-1">
                            <div class="w-6 bg-blue-400 rounded" style="height: {{ $monthlyAttendance['data'][$index] ?? 100 }}px;"></div>
                            <span class="text-xs text-gray-600">{{ $month }}</span>
                        </div>
                    @endforeach
                @else
                    @php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                    @foreach($months as $m)
                        <div class="flex flex-col items-center justify-end gap-1">
                            <div class="w-6 bg-blue-400 rounded" style="height: {{ rand(100, 145) }}px;"></div>
                            <span class="text-xs text-gray-600">{{ $m }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Grade Distribution Pie Chart -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Grade Distribution</h3>
            <div class="flex items-center justify-center h-48">
                <div class="relative w-40 h-40">
                    <svg viewBox="0 0 100 100" class="w-full h-full">
                        @php
                            $aGrade = $gradeDistribution['A'] ?? 28;
                            $bGrade = $gradeDistribution['B'] ?? 35;
                            $cGrade = $gradeDistribution['C'] ?? 22;
                            $dGrade = $gradeDistribution['D'] ?? 10;
                            $fGrade = $gradeDistribution['F'] ?? 5;
                            
                            // Calculate stroke-dasharray values (circumference = 251.2 for r=40)
                            $circumference = 251.2;
                            $aPercent = ($aGrade / 100) * $circumference;
                            $bPercent = ($bGrade / 100) * $circumference;
                            $cPercent = ($cGrade / 100) * $circumference;
                            $dPercent = ($dGrade / 100) * $circumference;
                            $fPercent = ($fGrade / 100) * $circumference;
                        @endphp
                        <!-- A Grade (Green) -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="8" stroke-dasharray="{{ $aPercent }} {{ $circumference }}" stroke-dashoffset="0" transform="rotate(-90 50 50)"/>
                        <!-- B Grade (Blue) -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="8" stroke-dasharray="{{ $bPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent }}" transform="rotate(-90 50 50)"/>
                        <!-- C Grade (Orange) -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="8" stroke-dasharray="{{ $cPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent + $bPercent }}" transform="rotate(-90 50 50)"/>
                        <!-- D Grade (Red) -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="8" stroke-dasharray="{{ $dPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent + $bPercent + $cPercent }}" transform="rotate(-90 50 50)"/>
                        <!-- F Grade (Gray) -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#9ca3af" stroke-width="8" stroke-dasharray="{{ $fPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent + $bPercent + $cPercent + $dPercent }}" transform="rotate(-90 50 50)"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-bold text-gray-900">Grade</p>
                            <p class="text-xs text-gray-600">Distribution</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                    <span class="text-gray-600">A Grade <span class="font-medium">{{ $aGrade }}%</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                    <span class="text-gray-600">B Grade <span class="font-medium">{{ $bGrade }}%</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-orange-600 rounded-full"></div>
                    <span class="text-gray-600">C Grade <span class="font-medium">{{ $cGrade }}%</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-600 rounded-full"></div>
                    <span class="text-gray-600">D Grade <span class="font-medium">{{ $dGrade }}%</span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Students & Subject Performance Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Top Performing Students -->
        <div class="bg-white rounded shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Top Performing Students</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-3 py-2 text-left font-semibold text-gray-900">Student</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-900">Grade</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-900">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topStudents as $student)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $student->profile_photo_path ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=random' }}" class="w-6 h-6 rounded-full">
                                    <span class="text-gray-900 font-medium">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">{{ round($student->avg_percentage, 1) }}%</span>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700">{{ $student->attendance_percentage ?? 'N/A' }}%</td>
                        </tr>
                        @empty
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=random" class="w-6 h-6 rounded-full">
                                    <span class="text-gray-900 font-medium">Sarah Johnson</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">95.2%</span>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700">98%</td>
                        </tr>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=Michael+Chen&background=random" class="w-6 h-6 rounded-full">
                                    <span class="text-gray-900 font-medium">Michael Chen</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">94.6%</span>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700">96%</td>
                        </tr>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=Emily+Davis&background=random" class="w-6 h-6 rounded-full">
                                    <span class="text-gray-900 font-medium">Emily Davis</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">93.3%</span>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700">94%</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Subject Performance -->
        <div class="bg-white rounded shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Subject Performance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-3 py-2 text-left font-semibold text-gray-900">Subject</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-900">Average</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjectPerformance as $subject)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div>
                                    <p class="text-gray-900 font-medium">{{ $subject->subject_name }}</p>
                                    <p class="text-gray-600">{{ $subject->subject_code }}</p>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-900 font-medium">{{ round($subject->avg_percentage, 1) }}%</td>
                            <td class="px-3 py-2 text-center">
                                @php $avg = $subject->avg_percentage; @endphp
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                    @if($avg >= 85) bg-green-100 text-green-700
                                    @elseif($avg >= 70) bg-blue-100 text-blue-700
                                    @elseif($avg >= 60) bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    @if($avg >= 85) Excellent
                                    @elseif($avg >= 70) Good
                                    @elseif($avg >= 60) Average
                                    @else Needs Improvement @endif
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div>
                                    <p class="text-gray-900 font-medium">Data Structures</p>
                                    <p class="text-gray-600">CS-301</p>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-900 font-medium">85.2%</td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Excellent</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div>
                                    <p class="text-gray-900 font-medium">Algorithms</p>
                                    <p class="text-gray-600">CS-302</p>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-900 font-medium">78.0%</td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">Good</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div>
                                    <p class="text-gray-900 font-medium">Database Systems</p>
                                    <p class="text-gray-600">CS-303</p>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center text-gray-900 font-medium">72.4%</td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">Average</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Report filter form
        const filterForm = document.getElementById('reportFilterForm');
        const resetBtn = document.getElementById('resetFilterBtn');

        // Reset button - clears all filters and reloads with default data
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                // Clear all select inputs
                const selects = filterForm.querySelectorAll('select');
                selects.forEach(select => {
                    select.value = '';
                });
                
                // Submit the form to reload with default (unfiltered) data
                filterForm.submit();
            });
        }

        // Export PDF button
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                alert('PDF Export functionality - Integrate with PDF library like dompdf or tcpdf');
            });
        }

        // Export Excel button
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                alert('Excel Export functionality - Integrate with Maatwebsite Excel package');
            });
        }
    });
</script>

