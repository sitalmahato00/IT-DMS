@extends('admin.print.layout')

@section('title', 'Student Marks Print')

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
    </style>
@endsection

@section('content')
<div class="print-preview">
    <div class="header-section">
        <h1>{{ $selectedSubject->subject_name ?? 'Student Marks' }}</h1>
        <p>{{ \Illuminate\Support\Str::title($category) }} Category · Printed {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="section-title">Active Filters</div>
    @php
        $academicYearLabel = $currentFilters['academic_year'] ?: 'All';
        $semesterLabel = $currentFilters['semester'] ? "{$currentFilters['semester']} Semester" : 'All';
        $subjectLabel = $selectedSubject->subject_name ?? 'Not selected';
        $statusLabel = $currentFilters['status'] ? ucwords(str_replace('_', ' ', $currentFilters['status'])) : 'All';
        $sortMap = [
            'roll_no' => 'Roll Number',
            'name' => 'Student Name',
            'highest' => 'Highest Marks',
            'lowest' => 'Lowest Marks',
        ];
        $sortLabel = $sortMap[$currentFilters['sort_by'] ?? 'roll_no'] ?? 'Roll Number';
    @endphp
    <div class="filter-info">
        <div><strong>Academic Year:</strong> {{ $academicYearLabel }}</div>
        <div><strong>Semester:</strong> {{ $semesterLabel }}</div>
        <div><strong>Subject:</strong> {{ $subjectLabel }}</div>
        <div><strong>Status:</strong> {{ $statusLabel }}</div>
        <div><strong>Sort By:</strong> {{ $sortLabel }}</div>
    </div>

    @if(!$selectedSubject)
        <div class="alert-box">
            Please select a subject from the filters before printing the marks report.
        </div>
    @else
        @php
            $subjectLabel = $selectedSubject->subject_name;
        @endphp
        @if($category === 'assessment')
            <div class="section-title">Assessment Marks - {{ $subjectLabel }}</div>
            <div class="table-wrapper">
                <table class="print-table">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
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
                                $examMark = $student->getExamMarkForSubject($selectedSubject->id, $category);
                                $totalFull = $examMark ? $examMark->calculateFullMarks() : 0;
                                $totalObtained = $examMark ? $examMark->calculateTotalMarks() : 0;
                                $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
                                $resultLabel = $examMark ? $examMark->getResultAttribute() : 'Pending';
                                $badgeClass = $resultLabel === 'PASS' ? 'badge badge-pass' : 'badge badge-fail';
                            @endphp
                            <tr>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->user->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $examMark ? ($examMark->full_marks ?? '-') : '-' }}</td>
                                <td class="text-center">{{ $examMark ? ($examMark->passing_marks ?? '-') : '-' }}</td>
                                <td class="text-center">{{ $examMark ? ($examMark->marks_obtained ?? '-') : '-' }}</td>
                                <td class="text-center">{{ $examMark ? ($percentage . '%') : '-' }}</td>
                                <td class="text-center">
                                    <span class="{{ $badgeClass }}">{{ $examMark ? $resultLabel : 'Pending' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No student marks found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="section-title">CTEVT Marks - {{ $subjectLabel }}</div>
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
                                $examMark = $student->getExamMarkForSubject($selectedSubject->id, $category);
                                $totalObtained = $examMark ? $examMark->calculateTotalMarks() : 0;
                                $resultLabel = $examMark ? $examMark->getResultAttribute() : 'Pending';
                                $badgeClass = $resultLabel === 'PASS' ? 'badge badge-pass' : 'badge badge-fail';
                                $componentValues = [];
                                foreach (['TI', 'TE', 'PI', 'PE'] as $component) {
                                    $componentValues[$component] = (array) $student->getComponentMarks($selectedSubject->id, $component);
                                    $componentValues[$component]['is_pass'] = $componentValues[$component]['is_pass'] ?? null;
                                }
                            @endphp
                            <tr>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->user->name ?? 'N/A' }}</td>
                                @foreach(['TI', 'TE', 'PI', 'PE'] as $component)
                                    @php $componentPass = $componentValues[$component]['is_pass']; @endphp
                                    <td class="text-center">{{ $componentValues[$component]['full'] }}</td>
                                    <td class="text-center">{{ $componentValues[$component]['pass'] }}</td>
                                    <td class="text-center font-semibold {{ $componentPass === false ? 'text-red-600' : ($componentPass === true ? 'text-green-600' : 'text-black') }}">
                                        {{ $componentValues[$component]['obtained'] }}
                                    </td>
                                @endforeach
                                <td class="text-center font-bold">{{ $examMark ? $totalObtained : '-' }}</td>
                                <td class="text-center">
                                    <span class="{{ $badgeClass }}">{{ $examMark ? $resultLabel : 'Pending' }}</span>
                                </td>
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
    @endif
</div>
@endsection
