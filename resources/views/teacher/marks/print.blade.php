@extends('admin.print.layout')

@section('title', 'Teacher Marks Print')

@section('styles')
    <style>
        .print-preview {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 20mm;
            border-radius: 4px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-section h1 {
            font-size: 20pt;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .header-section p {
            font-size: 10pt;
            color: #555;
        }
        .section-title {
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            border-left: 4px solid #ef4444;
            padding-left: 8px;
            margin-bottom: 10px;
            color: #1f2937;
        }
        .filter-info {
            background: #f3f4f6;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 10pt;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 8px;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 9pt;
        }
        .print-table th, .print-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        .print-table th {
            background: #ef4444;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 999px;
            font-size: 8pt;
            font-weight: 700;
        }
        .badge-pass {
            background: #d1fae5;
            color: #047857;
        }
        .badge-fail {
            background: #fee2e2;
            color: #b91c1c;
        }
        .alert-box {
            border: 1px dashed #6b7280;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 10pt;
            color: #374151;
            margin-bottom: 18px;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
<div class="print-preview">
    <div class="header-section">
        <h1>{{ $subjectLabel ?? 'Student Marks' }}</h1>
        <p>{{ \Illuminate\Support\Str::title($selectedCategory ?? 'assessment') }} Category · Printed {{ now()->format('F d, Y') }}</p>
    </div>

    @php
        $academicYearLabel = $currentFilters['academic_year'] ?: 'All';
        $semesterLabel = $currentFilters['semester'] ? ($currentFilters['semester'] . ' Semester') : 'All';
        $subjectDisplay = $selectedSubject['name'] ?? 'Not selected';
        $assessmentDisplay = !empty($currentFilters['assessment_id']) ? ('Selected assessment #' . $currentFilters['assessment_id']) : 'All';
        $searchLabel = $currentFilters['search'] ?: 'None';
    @endphp

    <div class="section-title">Active Filters</div>
    <div class="filter-info">
        <div><strong>Academic Year:</strong> {{ $academicYearLabel }}</div>
        <div><strong>Semester:</strong> {{ $semesterLabel }}</div>
        <div><strong>Subject:</strong> {{ $subjectDisplay }}</div>
        <div><strong>Assessment:</strong> {{ $assessmentDisplay }}</div>
        <div><strong>Search:</strong> {{ $searchLabel }}</div>
    </div>

    @if(!$selectedSubject)
        <div class="alert-box">
            Please select a subject from the filters before printing the marks report.
        </div>
    @elseif(($selectedCategory ?? 'assessment') === 'assessment')
        @php
            $selectedAssessmentId = $currentFilters['assessment_id'] ?? '';
            if (!empty($selectedAssessmentId)) {
                $selectedAssessmentId = (int) $selectedAssessmentId;
            }
        @endphp
        <div class="section-title">Assessment Marks - {{ $subjectDisplay }}</div>
        <div class="table-wrapper">
            <table class="print-table">
                <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th class="text-center">Attendance %</th>
                            <th class="text-center">Full</th>
                            <th class="text-center">Pass</th>
                            <th class="text-center">Obtained</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $selectedAss = null;
                            if (!empty($selectedAssessmentId) && isset($student['assessments']) && is_array($student['assessments'])) {
                                $selectedAss = collect($student['assessments'])->firstWhere('exam_id', $selectedAssessmentId);
                            }

                            $totalObtained = $student['total_marks'] ?? 0;
                            $totalFull = $student['full_marks'] ?? 0;
                            $totalPass = $student['pass_marks'] ?? 0;

                            $displayFull = $selectedAss['full'] ?? $totalFull;
                            $displayPass = $selectedAss['pass'] ?? $totalPass;
                            $displayObtained = $selectedAss['obtained'] ?? $totalObtained;
                            $displayPercentage = $selectedAss['percentage'] ?? ($displayFull > 0 ? round(($displayObtained / $displayFull) * 100, 1) : 0);
                            $isPassed = isset($selectedAss['is_passed']) ? $selectedAss['is_passed'] : ($student['is_passed'] ?? false);
                            $resultLabel = $isPassed ? 'PASS' : 'FAIL';
                            $badgeClass = $resultLabel === 'PASS' ? 'badge badge-pass' : 'badge badge-fail';
                        @endphp
                        <tr>
                            <td>{{ $student['roll_no'] }}</td>
                            <td>{{ $student['name'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ $student['attendance_percentage'] ?? 0 }}%</td>
                            <td class="text-center">{{ $displayFull > 0 ? $displayFull : '-' }}</td>
                            <td class="text-center">{{ $displayPass > 0 ? $displayPass : '-' }}</td>
                            <td class="text-center">{{ $displayFull > 0 ? $displayObtained : '-' }}</td>
                            <td class="text-center">{{ $displayFull > 0 ? ($displayPercentage . '%') : '-' }}</td>
                            <td class="text-center">
                                @if($displayFull > 0)
                                    <span class="{{ $badgeClass }}">{{ $resultLabel }}</span>
                                @else
                                    Pending
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No student marks found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="section-title">CTEVT Marks - {{ $subjectDisplay }}</div>
        <div class="table-wrapper">
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th class="text-center" colspan="3">Theory Internal</th>
                        <th class="text-center" colspan="3">Theory External</th>
                        <th class="text-center" colspan="3">Practical Internal</th>
                        <th class="text-center" colspan="3">Practical External</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Result</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        @foreach(['TI', 'TE', 'PI', 'PE'] as $component)
                            <th class="text-center">Full</th>
                            <th class="text-center">Pass</th>
                            <th class="text-center">Obt</th>
                        @endforeach
                        <th class="text-center">Total</th>
                        <th class="text-center">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $resultLabel = ($student['is_passed'] ?? false) ? 'PASS' : 'FAIL';
                            $badgeClass = $resultLabel === 'PASS' ? 'badge badge-pass' : 'badge badge-fail';
                        @endphp
                        <tr>
                            <td>{{ $student['roll_no'] }}</td>
                            <td>{{ $student['name'] ?? 'N/A' }}</td>
                            @foreach(['ti', 'te', 'pi', 'pe'] as $component)
                                <td class="text-center">{{ $student[$component . '_full'] ?? 0 }}</td>
                                <td class="text-center">{{ $student[$component . '_pass'] ?? 0 }}</td>
                                <td class="text-center">{{ $student[$component . '_obtained'] ?? 0 }}</td>
                            @endforeach
                            <td class="text-center font-bold">{{ $student['total_marks'] ?? 0 }}</td>
                            <td class="text-center"><span class="{{ $badgeClass }}">{{ $resultLabel }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center">No records found for the selected subject.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
