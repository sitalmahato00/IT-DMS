<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report - IT Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page { size: A4 portrait; margin: 1in; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            .chart-container { page-break-inside: avoid; }
            body { background: #fff; }
            .print-container { box-shadow: none; padding: 0; }
            .print-section { page-break-inside: avoid; }
            .checkbox-wrapper { display: none; }
        }
        
        @media screen {
            body { background: #f0f0f0; padding: 20px; padding-left: 260px; }
            .print-container { max-width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; padding: 20mm; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
            .print-btn { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #DC2626; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
            .print-btn:hover { background: #b91c1c; }
        }
        
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11pt; color: #333; background: #fff; }
        
        .header-section { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #DC2626; }
        .school-logo { margin-bottom: 10px; }
        .school-logo img { max-height: 60px; max-width: 60px; border-radius: 50%; }
        .logo-placeholder { font-size: 48px; line-height: 1; }
        .school-name { font-size: 18pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; color: #DC2626; }
        .report-title { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 10px 0; padding: 8px 0; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; }
        
        .meta-info { display: flex; justify-content: center; gap: 30px; margin: 15px 0; font-size: 10pt; }
        .meta-item { display: flex; gap: 5px; }
        .meta-label { font-weight: bold; }
        
        .kpi-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center; background: #fefefe; }
        .kpi-card .number { font-size: 20px; font-weight: bold; color: #DC2626; }
        .kpi-card .label { font-size: 10px; color: #6b7280; text-transform: uppercase; }
        
        .section-title { border-left: 4px solid #DC2626; padding-left: 10px; margin: 20px 0 15px 0; font-size: 14px; font-weight: bold; color: #1f2937; }
        
        .filter-info { background: #f3f4f6; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 10pt; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { border: 1px solid #000; padding: 6px 8px; text-align: left; font-size: 10pt; }
        table th { background: #DC2626; color: white; }
        table tr:hover { background: #fef2f2; }
        
        .status-badge { padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: bold; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .status-alumni { background: #dbeafe; color: #1e40af; }
        
        .chart-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fff; }
        .chart-title { font-size: 11px; font-weight: bold; color: #DC2626; margin-bottom: 10px; text-align: center; }
        
        .pie-container { display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; }
        .pie-data { flex: 1; min-width: 200px; }
        
        .toggle-container { background: #f3f4f6; padding: 10px; border-radius: 6px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .toggle-btn { padding: 8px 16px; border: 1px solid #d1d5db; background: white; border-radius: 4px; cursor: pointer; font-size: 11px; transition: all 0.2s; }
        .toggle-btn.active { background: #DC2626; color: white; border-color: #DC2626; }
        .toggle-btn:hover:not(.active) { background: #f3f4f6; }
        
        .checkbox-wrapper { background: #fff; border: 1px solid #e5e7eb; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; }
        .checkbox-item { display: inline-flex; align-items: center; gap: 5px; margin-right: 15px; margin-bottom: 5px; }
        
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; color: #6b7280; font-size: 9pt; }
        
        .print-section { margin-bottom: 25px; }
    </style>
</head>
<body>
    <!-- Print Controls - Sidebar -->
    <div class="no-print" style="position: fixed; left: 10px; top: 10px; z-index: 1000; width: 220px;">
        <div style="background: white; border: 2px solid #DC2626; border-radius: 8px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
            <h4 style="margin: 0 0 15px 0; color: #DC2626; font-size: 14px; font-weight: 700; text-align: center; padding-bottom: 10px; border-bottom: 1px solid #fecaca;">🖨️ Print Options</h4>
            
            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px;">
                <button class="toggle-btn active" onclick="switchView('charts')" style="padding: 10px 16px; font-weight: 600; width: 100%;">
                    📊 View Charts
                </button>
                <button onclick="printWithOptions()" style="padding: 10px 16px; background: #DC2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3); transition: all 0.2s; width: 100%;">
                    🖨️ Print Report
                </button>
            </div>
            
            <div style="border-top: 1px solid #fecaca; padding-top: 15px;">
                <p style="margin: 0 0 10px 0; color: #991b1b; font-size: 12px; font-weight: 700; text-align: center;">🎯 Select sections:</p>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fef2f2; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid #fecaca;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                        <input type="checkbox" id="print-kpi" checked style="accent-color: #DC2626; width: 16px; height: 16px;">
                        <span style="font-weight: 600; color: #991b1b; font-size: 11px;">📊 KPI Cards</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fef2f2; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid #fecaca;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                        <input type="checkbox" id="print-semester" checked style="accent-color: #DC2626; width: 16px; height: 16px;">
                        <span style="font-weight: 600; color: #991b1b; font-size: 11px;">📚 Semester Data</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fef2f2; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid #fecaca;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                        <input type="checkbox" id="print-attendance" checked style="accent-color: #DC2626; width: 16px; height: 16px;">
                        <span style="font-weight: 600; color: #991b1b; font-size: 11px;">📈 Attendance</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fef2f2; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid #fecaca;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                        <input type="checkbox" id="print-elective" checked style="accent-color: #DC2626; width: 16px; height: 16px;">
                        <span style="font-weight: 600; color: #991b1b; font-size: 11px;">📖 Electives</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fef2f2; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid #fecaca;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                        <input type="checkbox" id="print-marks" checked style="accent-color: #DC2626; width: 16px; height: 16px;">
                        <span style="font-weight: 600; color: #991b1b; font-size: 11px;">📝 Marks</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fef2f2; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid #fecaca;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                        <input type="checkbox" id="print-grade" checked style="accent-color: #DC2626; width: 16px; height: 16px;">
                        <span style="font-weight: 600; color: #991b1b; font-size: 11px;">🏆 Grades</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fef2f2; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid #fecaca;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                        <input type="checkbox" id="print-top" checked style="accent-color: #DC2626; width: 16px; height: 16px;">
                        <span style="font-weight: 600; color: #991b1b; font-size: 11px;">🎓 Top Performers</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="header-section">
            <div class="school-logo">
                @if(!empty($college) && !empty($college->logo_path))
                    <img src="{{ asset($college->logo_path) }}" alt="College Logo" style="max-height: 60px; max-width: 60px;">
                @else
                    <div class="logo-placeholder" style="font-size: 48px;">🎓</div>
                @endif
            </div>
            <div class="school-name">{{ !empty($college) ? $college->name : 'IT-DMS COLLEGE' }}</div>
            <div class="report-title">Admin Report - IT Department</div>
            <div class="meta-info">
                <div class="meta-item">
                    <span class="meta-label">Academic Year:</span>
                    <span>{{ $year }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Program:</span>
                    <span>{{ $program ?: 'All Programs' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Generated:</span>
                    <span>{{ now()->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Information -->
        <div class="filter-info">
            <strong>Applied Filters:</strong>
            @php
                $filters = [];
                if($semester) $filters[] = "Semester: $semester";
                if($program) $filters[] = "Program: $program";
                if($studentStatus) $filters[] = "Status: $studentStatus";
                if($month) $filters[] = "Month: $month";
                if($search) $filters[] = "Search: $search";
            @endphp
            {{ implode(' | ', $filters) ?: 'No filters applied (Showing all data)' }}
        </div>

        <!-- KPI Cards -->
        <div class="print-section" id="kpi-section">
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="kpi-card">
                        <div class="number">{{ $kpiStats['totalStudents'] }}</div>
                        <div class="label">Total Students</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="kpi-card">
                        <div class="number">{{ $kpiStats['activeStudents'] }}</div>
                        <div class="label">Active Students</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="kpi-card">
                        <div class="number">{{ $kpiStats['alumni'] }}</div>
                        <div class="label">Alumni</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="kpi-card">
                        <div class="number">{{ $kpiStats['attendanceRate'] }}%</div>
                        <div class="label">Attendance %</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="kpi-card">
                        <div class="number">{{ $kpiStats['avgMarks'] }}</div>
                        <div class="label">Avg. Marks</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="kpi-card">
                        <div class="number">{{ $kpiStats['electivesChosen'] }}</div>
                        <div class="label">Electives Chosen</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Mode: Charts -->
        <div id="charts-view">
            <!-- Students per Semester -->
            <div class="print-section" id="semester-section">
                <div class="section-title">Students per Semester</div>
                <div class="chart-box">
                    <div class="chart-title">Students Distribution by Semester</div>
                    @if(isset($studentsPerSemester['semesters']))
                    <table style="width: 50%; margin: 0 auto;">
                        <thead><tr><th>Semester</th><th>Student Count</th><th>Percentage</th></tr></thead>
                        <tbody>
                            @php $totalStudents = array_sum($studentsPerSemester['counts']); @endphp
                            @foreach($studentsPerSemester['semesters'] ?? [] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $studentsPerSemester['counts'][$index] }}</td>
                                <td>{{ $totalStudents > 0 ? round(($studentsPerSemester['counts'][$index] / $totalStudents) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted text-center">No data available</p>
                    @endif
                </div>
            </div>

            <!-- Period Selector for Attendance -->
            <div class="no-print" style="margin-bottom: 15px; padding: 10px; background: #fef2f2; border-radius: 6px;">
                <label style="font-weight: 600; color: #991b1b; font-size: 12px; margin-right: 10px;">📅 Select Period:</label>
                <select id="attendancePeriod" onchange="filterAttendanceByPeriod()" style="padding: 5px 10px; border: 1px solid #fecaca; border-radius: 4px; font-size: 12px;">
                    <option value="all">All Months</option>
                    <option value="3">Last 3 Months</option>
                    <option value="6">Last 6 Months</option>
                    <option value="12">Last 12 Months</option>
                </select>
            </div>
            
            <div class="row">
                <!-- Attendance Trends -->
                <div class="col-md-6 print-section" id="attendance-section">
                    <div class="section-title">Attendance Trends</div>
                    <div class="chart-box">
                        <div class="chart-title">Monthly Attendance (%)</div>
                        @if(isset($monthlyAttendance['months']))
                        <table style="width: 60%; margin: 0 auto;">
                            <thead><tr><th>Month</th><th>Attendance %</th></tr></thead>
                            <tbody>
                                @foreach($monthlyAttendance['months'] as $index => $label)
                                @php 
                                    $present = $monthlyAttendance['present'][$index] ?? 0;
                                    $absent = $monthlyAttendance['absent'][$index] ?? 0;
                                    $total = $present + $absent;
                                    $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                                @endphp
                                <tr><td>{{ $label }}</td><td style="font-weight: bold; color: #DC2626;">{{ $percentage }}%</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted text-center">No data available</p>
                        @endif
                    </div>
                </div>

                <!-- Subject/Elective Distribution -->
                <div class="col-md-6 print-section" id="elective-section">
                    <div class="section-title">Subject/Elective Distribution</div>
                    <div class="chart-box">
                        <div class="chart-title">Elective Subject Enrollment Details</div>
                        @if(isset($electiveDistribution['labels']) && count($electiveDistribution['labels']) > 0)
                        <div class="pie-container">
                            <div class="pie-data">
                                <table style="width: 100%;">
                                    <thead><tr><th>Subject</th><th>Code</th><th>Credits</th><th>Enrolled</th><th>Capacity</th><th>%</th></tr></thead>
                                    <tbody>
                                        @php $totalElective = array_sum($electiveDistribution['data']); @endphp
                                        @foreach($electiveDistribution['labels'] as $index => $label)
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td>{{ $electiveDistribution['codes'][$index] ?? 'N/A' }}</td>
                                            <td>{{ $electiveDistribution['credits'][$index] ?? '3' }}</td>
                                            <td>{{ $electiveDistribution['data'][$index] }}</td>
                                            <td>{{ $electiveDistribution['capacity'][$index] ?? 'N/A' }}</td>
                                            <td style="font-weight: bold; color: #DC2626;">{{ $totalElective > 0 ? round(($electiveDistribution['data'][$index] / $totalElective) * 100, 1) : 0 }}%</td>
                                        </tr>
                                        @endforeach
                                        <tr style="background: #fef2f2; font-weight: bold;">
                                            <td colspan="3">Total</td>
                                            <td>{{ $totalElective }}</td>
                                            <td>-</td>
                                            <td>100%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                        <p class="text-muted text-center">No elective data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Marks per Subject -->
            <div class="print-section" id="marks-section">
                <div class="section-title">Marks per Subject</div>
                <div class="chart-box">
                    <div class="chart-title">Average Marks by Subject</div>
                    @if(isset($marksPerSubject['subjects']))
                    <table style="width: 100%;">
                        <thead><tr><th>Subject</th><th>Average Marks</th><th>Performance</th></tr></thead>
                        <tbody>
                            @foreach($marksPerSubject['subjects'] as $index => $label)
                            @php $marks = round($marksPerSubject['marks'][$index], 1); @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $marks }}%</td>
                                <td>
                                    @if($marks >= 90) Excellent
                                    @elseif($marks >= 80) Very Good
                                    @elseif($marks >= 70) Good
                                    @elseif($marks >= 60) Average
                                    @else Needs Improvement
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @elseif(isset($marksPerSubject['labels']))
                    <table style="width: 100%;">
                        <thead><tr><th>Subject</th><th>Avg. Marks</th></tr></thead>
                        <tbody>
                            @foreach($marksPerSubject['labels'] as $index => $label)
                            <tr><td>{{ $label }}</td><td>{{ round($marksPerSubject['data'][$index], 1) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted text-center">No data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- View Mode: Tables -->
        <div id="tables-view" style="display: none;">
            <!-- Student Details replaced with Top Performers -->
            <div class="print-section" id="top-table-section">
                <div class="section-title">Top Performing Students</div>
                <div class="chart-box" style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Student Name</th>
                                <th>Average Marks</th>
                                <th>Attendance %</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudents as $index => $student)
                            <tr>
                                <td><strong>#{{ $index + 1 }}</strong></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ round($student->avg_percentage, 1) }}%</td>
                                <td>{{ $student->attendance_percentage ?? 0 }}%</td>
                                <td>
                                    @php $marks = $student->avg_percentage; @endphp
                                    @if($marks >= 90) A
                                    @elseif($marks >= 80) B
                                    @elseif($marks >= 70) C
                                    @elseif($marks >= 60) D
                                    @else F
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">No top performers data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Grade Distribution (Always at bottom) -->
        <div class="print-section" id="grade-section">
            <div class="section-title">Grade Distribution</div>
            <div class="chart-box">
                <table>
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Range</th>
                            <th>Percentage</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $grades = [
                                'A' => ['90-100%', 'Excellent'],
                                'B' => ['80-89%', 'Very Good'],
                                'C' => ['70-79%', 'Good'],
                                'D' => ['60-69%', 'Average'],
                                'F' => ['<60%', 'Needs Improvement']
                            ];
                        @endphp
                        @foreach($grades as $grade => $info)
                        <tr>
                            <td><strong>{{ $grade }}</strong></td>
                            <td>{{ $info[0] }}</td>
                            <td>{{ $gradeDistribution[$grade] ?? 0 }}%</td>
                            <td>{{ $info[1] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Performers (Bottom Table for Print) -->
        <div class="print-section" id="top-bottom-section">
            <div class="section-title">Top Performing Students</div>
            <div class="chart-box">
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student Name</th>
                            <th>Average Marks</th>
                            <th>Attendance %</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topStudents as $index => $student)
                        <tr>
                            <td><strong>#{{ $index + 1 }}</strong></td>
                            <td>{{ $student->name }}</td>
                            <td>{{ round($student->avg_percentage, 1) }}%</td>
                            <td>{{ $student->attendance_percentage ?? 0 }}%</td>
                            <td>
                                @php $marks = $student->avg_percentage; @endphp
                                @if($marks >= 90) A
                                @elseif($marks >= 80) B
                                @elseif($marks >= 70) C
                                @elseif($marks >= 60) D
                                @else F
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No top performers data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>IT Department - College ERP | Page generated automatically</p>
        </div>
    </div>

    <script>
        // Dynamic section visibility toggles
        document.addEventListener('DOMContentLoaded', function() {
            // KPI Section toggle
            document.getElementById('print-kpi').addEventListener('change', function() {
                var section = document.getElementById('kpi-section');
                if(section) section.style.display = this.checked ? '' : 'none';
            });
            
            // Semester Section toggle
            document.getElementById('print-semester').addEventListener('change', function() {
                var section = document.getElementById('semester-section');
                if(section) section.style.display = this.checked ? '' : 'none';
            });
            
            // Attendance Section toggle
            document.getElementById('print-attendance').addEventListener('change', function() {
                var section = document.getElementById('attendance-section');
                if(section) section.style.display = this.checked ? '' : 'none';
            });
            
            // Elective Section toggle
            document.getElementById('print-elective').addEventListener('change', function() {
                var section = document.getElementById('elective-section');
                if(section) section.style.display = this.checked ? '' : 'none';
            });
            
            // Marks Section toggle
            document.getElementById('print-marks').addEventListener('change', function() {
                var section = document.getElementById('marks-section');
                if(section) section.style.display = this.checked ? '' : 'none';
            });
            
            // Grade Section toggle
            document.getElementById('print-grade').addEventListener('change', function() {
                var section = document.getElementById('grade-section');
                if(section) section.style.display = this.checked ? '' : 'none';
            });
            
            // Top Performers Section toggle
            document.getElementById('print-top').addEventListener('change', function() {
                var section1 = document.getElementById('top-table-section');
                var section2 = document.getElementById('top-bottom-section');
                if(section1) section1.style.display = this.checked ? '' : 'none';
                if(section2) section2.style.display = this.checked ? '' : 'none';
            });
        });
        
        function filterAttendanceByPeriod() {
            var period = document.getElementById('attendancePeriod').value;
            var rows = document.querySelectorAll('#attendance-section tbody tr');
            var totalMonths = rows.length;
            var showCount = totalMonths;
            
            if (period !== 'all') {
                showCount = parseInt(period);
            }
            
            rows.forEach(function(row, index) {
                if (index < showCount) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        function switchView(view) {
            document.getElementById('charts-view').style.display = view === 'charts' ? 'block' : 'none';
            
            // Update button states
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }
        
        // Print with section filtering
        function printWithOptions() {
            // Hide sections based on checkboxes
            if(!document.getElementById('print-kpi').checked) {
                document.getElementById('kpi-section').style.display = 'none';
            }
            if(!document.getElementById('print-semester').checked) {
                document.getElementById('semester-section').style.display = 'none';
            }
            if(!document.getElementById('print-attendance').checked) {
                document.getElementById('attendance-section').style.display = 'none';
            }
            if(!document.getElementById('print-elective').checked) {
                document.getElementById('elective-section').style.display = 'none';
            }
            if(!document.getElementById('print-marks').checked) {
                document.getElementById('marks-section').style.display = 'none';
            }
            if(!document.getElementById('print-grade').checked) {
                document.getElementById('grade-section').style.display = 'none';
            }
            if(!document.getElementById('print-top').checked) {
                document.getElementById('top-bottom-section').style.display = 'none';
            }
            
            window.print();
            
            // Restore display after printing
            setTimeout(function() {
                document.getElementById('kpi-section').style.display = '';
                document.getElementById('semester-section').style.display = '';
                document.getElementById('attendance-section').style.display = '';
                document.getElementById('elective-section').style.display = '';
                document.getElementById('marks-section').style.display = '';
                document.getElementById('grade-section').style.display = '';
                document.getElementById('top-bottom-section').style.display = '';
            }, 100);
        }
    </script>
</body>
</html>
