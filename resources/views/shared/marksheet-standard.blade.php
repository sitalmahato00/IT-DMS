<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->user->name ?? 'Student' }}</title>
    <style>
        @page { size: A4; margin: 8mm; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            background: #f5f5f5;
            color: #111111;
            font-family: Arial, Helvetica, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .sheet {
            width: 100%;
            max-width: 940px;
            margin: 0 auto;
            padding: 12px;
        }
        .paper {
            background: #ffffff;
            border: 1.5px solid #222222;
            padding: 14px 14px 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }
        .no-print {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 12px;
        }
        .no-print button,
        .no-print a {
            border: 1px solid #666666;
            border-radius: 4px;
            background: #ffffff;
            color: #111111;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .brand-grid {
            display: grid;
            grid-template-columns: 88px 1fr;
            gap: 14px;
            align-items: start;
            border-bottom: 1px solid #222222;
            padding-bottom: 10px;
        }
        .brand-cell {
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .brand-center {
            display: block;
            text-align: center;
        }
        .brand-right {
            display: none;
        }
        .logo-box {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }
        .brand-title {
            font-size: 26px;
            line-height: 1.2;
            font-weight: 700;
            color: #111111;
        }
        .brand-subtitle {
            margin-top: 4px;
            font-size: 22px;
            line-height: 1.2;
            font-weight: 700;
            color: #111111;
            text-transform: uppercase;
        }
        .college-name {
            margin-top: 4px;
            text-align: center;
            font-size: 14px;
            line-height: 1.4;
            color: #111111;
        }
        .college-address {
            margin-top: 6px;
            text-align: center;
            font-size: 12px;
            line-height: 1.5;
            color: #222222;
        }
        .section-heading {
            margin-top: 14px;
            border: 1px solid #222222;
            background: #ececec;
            color: #111111;
            text-align: center;
            padding: 7px 10px;
            font-size: 13px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .marksheet-title,
        .section-rule,
        .result-row,
        .wide-gap {
            display: none;
        }
        .info-grid {
            display: block;
        }
        .info-panel {
            border: 1px solid #222222;
            border-top: 0;
            padding: 0;
        }
        .info-table,
        .marks-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .info-table td,
        .marks-table th,
        .marks-table td,
        .summary-table td {
            border: 1px solid #222222;
            padding: 6px 6px;
            vertical-align: middle;
            white-space: normal;
            word-break: normal;
            overflow-wrap: break-word;
        }
        .info-table td {
            vertical-align: top;
            padding: 10px 8px;
        }
        .info-label,
        .summary-label {
            font-size: 10px;
            line-height: 1.2;
            font-weight: 700;
            color: #333333;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .info-value {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.35;
            font-weight: 700;
            color: #111111;
        }
        .marks-table {
            margin-top: 0;
            border: 1px solid #222222;
            border-top: 0;
        }
        .marks-table thead {
            display: table-header-group;
        }
        .marks-table th {
            background: #f0f0f0;
            color: #111111;
            text-transform: uppercase;
            font-size: 10px;
            line-height: 1.25;
            font-weight: 700;
            text-align: center;
        }
        .marks-table td {
            font-size: 11px;
            line-height: 1.32;
            color: #111111;
        }
        .marks-table tfoot td {
            background: #f0f0f0;
            font-weight: 700;
            font-size: 11px;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .subject {
            font-weight: 700;
        }
        .subject-meta {
            display: block;
            margin-top: 2px;
            font-size: 10px;
            color: #444444;
            font-weight: 400;
        }
        .fail-cell {
            font-weight: 700;
        }
        .summary-table {
            margin-top: 0;
            border: 1px solid #222222;
            border-top: 0;
        }
        .summary-table td {
            padding: 10px 8px;
            text-align: center;
        }
        .summary-value {
            margin-top: 6px;
            font-size: 14px;
            line-height: 1.25;
            font-weight: 700;
            color: #111111;
        }
        .signature-grid {
            margin-top: 26px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }
        .signature-box {
            text-align: center;
            min-height: 64px;
        }
        .signature-line {
            width: 80%;
            margin: 28px auto 8px;
            border-top: 1px solid #222222;
        }
        .signature-label {
            font-size: 12px;
            line-height: 1.3;
            font-weight: 700;
            color: #111111;
        }
        .footer-note {
            margin-top: 16px;
            border: 1px solid #222222;
            padding: 8px 10px;
            text-align: center;
            font-size: 11px;
            line-height: 1.4;
            color: #222222;
            background: #f7f7f7;
        }
        @media screen and (max-width: 700px) {
            .brand-grid {
                grid-template-columns: 1fr;
            }
            .brand-cell {
                justify-content: center;
            }
            .signature-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
        }
        @media print {
            body { background: #ffffff; }
            .no-print { display: none !important; }
            .sheet { padding: 0; max-width: none; }
            .paper { box-shadow: none; }
            .marks-table tr,
            .info-table tr,
            .summary-table tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @php
        $marks = collect($marksheetData['exam_marks'] ?? []);
        $category = strtolower((string) ($filters['exam_category'] ?? ($marks->first()?->exam?->exam_category ?? 'assessment')));
        $isCtevt = $category === 'ctevt';
        $departmentEntity = $college ?? $department ?? \App\Models\Department::first();
        $departmentName = $departmentEntity?->name ?? config('app.name', 'IT DMS');
        $departmentAddress = trim(collect([
            $departmentEntity?->address ?? null,
            $departmentEntity?->city ?? null,
            $departmentEntity?->district ?? null,
        ])->filter()->implode(', '));
        $departmentLogo = $departmentLogoUrl ?? ($departmentEntity && method_exists($departmentEntity, 'getLogoUrl')
            ? $departmentEntity->getLogoUrl()
            : asset('images/default-logo.svg'));
        $studentName = $student?->user?->name ?? 'N/A';
        $studentId = $student?->id ?? 'N/A';
        $rollNo = $student?->roll_no ?? 'N/A';
        $dob = optional($student?->date_of_birth)->format('Y-m-d') ?: ($student?->date_of_birth_bs ?? 'N/A');
        $academicYear = $filters['academic_year'] ?? ($student?->academic_year_bs ?? $student?->academic_year ?? 'All');
        $semester = $filters['semester'] ?? ($student?->semester ?? 'All');
        $assessmentNumber = $filters['assessment_number'] ?? '';
        $programName = $student?->program ?: ($student?->department ?: ($departmentEntity?->short_name ?: ($departmentEntity?->name ?? 'N/A')));
        $examTitle = $selectedExamName ?? ($marks->first()?->exam?->exam_name ?? ucfirst($category ?: 'Marksheet'));
        $examCategoryLabel = $selectedExamCategory ?? strtoupper($category ?: 'ASSESSMENT');
        $issueDate = now()->format('Y-m-d');

        $grandTotal = (float) ($marksheetData['total_obtained'] ?? 0);
        $totalFull = (float) ($marksheetData['total_full'] ?? 0);
        $percentage = (float) ($marksheetData['percentage'] ?? 0);
        $grade = $marksheetData['grade'] ?? '-';
        $result = strtoupper((string) ($marksheetData['result'] ?? 'FAIL'));
        $hasAbsent = $marks->contains(fn ($mark) => $mark->isAbsent());
        $resultText = $result === 'PASS' ? 'PASS' : ($hasAbsent ? 'ABS' : ($result === 'PENDING' ? 'PENDING' : 'FAIL'));
        $resultClass = $resultText === 'PASS'
            ? 'result-pass'
            : ($resultText === 'ABS' ? 'result-abs' : ($resultText === 'PENDING' ? 'result-pending' : 'result-fail'));
    @endphp

    <div class="sheet">
        <div class="no-print">
            <a href="{{ request()->fullUrl() }}" target="_blank" rel="noopener noreferrer" class="btn-newtab">New Tab</a>
            <button type="button" onclick="window.print()" class="btn-print">Print</button>
            <button type="button" onclick="window.close()" class="btn-close">Close</button>
        </div>

        <div class="paper">
            <div class="brand-grid">
                <div class="brand-cell brand-left">
                    <img src="{{ $departmentLogo }}" alt="Logo" class="logo-box">
                </div>
                <div class="brand-cell brand-center">
                    <div class="brand-title">{{ $departmentName }}</div>
                    <div class="brand-subtitle">ACADEMIC TRANSCRIPT</div>
                    <div class="college-name">{{ $departmentAddress ?: 'Department Address' }}</div>
                    <div class="college-address">
                        Academic Year: {{ $academicYear }}
                        |
                        Semester: {{ $semester }}
                        |
                        Program: {{ $programName }}
                        |
                        Category: {{ $examCategoryLabel }}
                        |
                        Exam: {{ $examTitle }}
                    </div>
                </div>
            </div>

            <div class="section-heading">Student Information</div>

            <div class="info-grid">
                <div class="info-panel">
                    <table class="info-table">
                        <tr>
                            <td style="width:25%;">
                                <div class="info-label">Student Name</div>
                                <div class="info-value">{{ $studentName }}</div>
                            </td>
                            <td style="width:25%;">
                                <div class="info-label">Student ID</div>
                                <div class="info-value">{{ $studentId }}</div>
                            </td>
                            <td style="width:25%;">
                                <div class="info-label">Roll Number</div>
                                <div class="info-value">{{ $rollNo }}</div>
                            </td>
                            <td style="width:25%;">
                                <div class="info-label">Semester</div>
                                <div class="info-value">{{ $semester }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-label">Academic Year (BS)</div>
                                <div class="info-value">{{ $academicYear }}</div>
                            </td>
                            <td>
                                <div class="info-label">Issue Date</div>
                                <div class="info-value">{{ $issueDate }}</div>
                            </td>
                            <td>
                                <div class="info-label">Exam Category</div>
                                <div class="info-value">{{ $examCategoryLabel }}</div>
                            </td>
                            <td>
                                <div class="info-label">Public Entries</div>
                                <div class="info-value">{{ $marks->count() }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section-heading">Academic Performance</div>

            @if($isCtevt)
                <table class="marks-table">
                    <thead>
                        <tr>
                            <th style="width:5%;">S.N.</th>
                            <th style="width:31%;">Subject</th>
                            <th style="width:8%;">Full Mark (Int)</th>
                            <th style="width:8%;">Full Mark (Ext)</th>
                            <th style="width:8%;">Pass Mark (Int)</th>
                            <th style="width:8%;">Pass Mark (Ext)</th>
                            <th style="width:10%;">Marks Obtained (Int)</th>
                            <th style="width:10%;">Marks Obtained (Ext)</th>
                            <th style="width:12%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marks as $index => $mark)
                            @php
                                $subjectCode = $mark->subject->subject_code ?? '—';
                                $subjectName = $mark->subject->subject_name ?? 'N/A';
                                $tiFull = $mark->theory_internal_full_marks ?? $mark->exam->theory_internal_max_marks;
                                $teFull = $mark->theory_external_full_marks ?? $mark->exam->theory_external_max_marks;
                                $piFull = $mark->practical_internal_full_marks ?? $mark->exam->practical_internal_max_marks;
                                $peFull = $mark->practical_external_full_marks ?? $mark->exam->practical_external_max_marks;
                                $tiPass = $mark->theory_internal_pass_marks ?? $mark->exam->theory_internal_pass_marks;
                                $tePass = $mark->theory_external_pass_marks ?? $mark->exam->theory_external_pass_marks;
                                $piPass = $mark->practical_internal_pass_marks ?? $mark->exam->practical_internal_pass_marks;
                                $pePass = $mark->practical_external_pass_marks ?? $mark->exam->practical_external_pass_marks;
                                $absent = $mark->isAbsent();
                                $tiRaw = $mark->theory_internal_marks;
                                $teRaw = $mark->theory_external_marks;
                                $piRaw = $mark->practical_internal_marks;
                                $peRaw = $mark->practical_external_marks;
                                $tiDisplay = $absent ? 'ABS' : (is_null($tiRaw) ? '—' : number_format((float) $tiRaw, 2));
                                $teDisplay = $absent ? 'ABS' : (is_null($teRaw) ? '—' : number_format((float) $teRaw, 2));
                                $piDisplay = $absent ? 'ABS' : (is_null($piRaw) ? '—' : number_format((float) $piRaw, 2));
                                $peDisplay = $absent ? 'ABS' : (is_null($peRaw) ? '—' : number_format((float) $peRaw, 2));
                                $theoryTotalDisplay = $absent
                                    ? 'ABS'
                                    : ((is_null($tiRaw) && is_null($teRaw)) ? '—' : number_format((float) ($tiRaw ?? 0) + (float) ($teRaw ?? 0), 2));
                                $practicalTotalDisplay = $absent
                                    ? 'ABS'
                                    : ((is_null($piRaw) && is_null($peRaw)) ? '—' : number_format((float) ($piRaw ?? 0) + (float) ($peRaw ?? 0), 2));
                            @endphp
                            <tr>
                                <td class="center" rowspan="2">{{ $index + 1 }}</td>
                                <td class="subject">
                                    {{ $subjectName }} (Th.)
                                    <span class="subject-meta">{{ $subjectCode }}</span>
                                </td>
                                <td class="center">{{ is_null($tiFull) ? '—' : number_format((float) $tiFull, 2) }}</td>
                                <td class="center">{{ is_null($teFull) ? '—' : number_format((float) $teFull, 2) }}</td>
                                <td class="center">{{ is_null($tiPass) ? '—' : number_format((float) $tiPass, 2) }}</td>
                                <td class="center">{{ is_null($tePass) ? '—' : number_format((float) $tePass, 2) }}</td>
                                <td class="center {{ !$absent && !is_null($tiRaw) && !is_null($tiPass) && (float) $tiRaw < (float) $tiPass ? 'fail-cell' : '' }}">{{ $tiDisplay }}</td>
                                <td class="center {{ !$absent && !is_null($teRaw) && !is_null($tePass) && (float) $teRaw < (float) $tePass ? 'fail-cell' : '' }}">{{ $teDisplay }}</td>
                                <td class="center"><strong>{{ $theoryTotalDisplay }}</strong></td>
                            </tr>
                            <tr>
                                <td class="subject">
                                    {{ $subjectName }} (Pr.)
                                    <span class="subject-meta">{{ $subjectCode }}</span>
                                </td>
                                <td class="center">{{ is_null($piFull) ? '—' : number_format((float) $piFull, 2) }}</td>
                                <td class="center">{{ is_null($peFull) ? '—' : number_format((float) $peFull, 2) }}</td>
                                <td class="center">{{ is_null($piPass) ? '—' : number_format((float) $piPass, 2) }}</td>
                                <td class="center">{{ is_null($pePass) ? '—' : number_format((float) $pePass, 2) }}</td>
                                <td class="center {{ !$absent && !is_null($piRaw) && !is_null($piPass) && (float) $piRaw < (float) $piPass ? 'fail-cell' : '' }}">{{ $piDisplay }}</td>
                                <td class="center {{ !$absent && !is_null($peRaw) && !is_null($pePass) && (float) $peRaw < (float) $pePass ? 'fail-cell' : '' }}">{{ $peDisplay }}</td>
                                <td class="center"><strong>{{ $practicalTotalDisplay }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="center" style="padding: 18px 10px;">
                                    No marks found for the selected filters
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="right">Grand Total</td>
                            <td colspan="2" class="center">Percentage: {{ number_format($percentage, 2) }}%</td>
                            <td class="center">Grade: {{ $grade }}</td>
                            <td class="center">{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <table class="marks-table">
                    <thead>
                        <tr>
                            <th style="width:6%;">S.N.</th>
                            <th style="width:40%;">Subject</th>
                            <th style="width:12%;">Full Marks</th>
                            <th style="width:12%;">Pass Mark</th>
                            <th style="width:12%;">Marks Obtained</th>
                            <th style="width:10%;">Percentage</th>
                            <th style="width:8%;">Grade</th>
                            <th style="width:10%;">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marks as $index => $mark)
                            @php
                                $subjectCode = $mark->subject->subject_code ?? '—';
                                $subjectName = $mark->subject->subject_name ?? 'N/A';
                                $fullMarks = $mark->full_marks ?? $mark->exam->full_marks;
                                $passMark = $mark->passing_marks ?? $mark->exam->passing_marks;
                                $absent = $mark->isAbsent();
                                $obtainedValue = $mark->marks_obtained;
                                $obtained = $absent ? 'ABS' : (is_null($obtainedValue) ? '—' : number_format((float) $obtainedValue, 2));
                                $rowPercentage = is_null($obtainedValue) || is_null($fullMarks) || (float) $fullMarks <= 0
                                    ? null
                                    : round(((float) $obtainedValue / (float) $fullMarks) * 100, 2);
                                $rowGrade = $absent ? 'ABS' : ($mark->calculateGrade() ?? '—');
                                $rowResult = $absent
                                    ? 'ABS'
                                    : (($mark->result ?? '') === 'PASS' || (($mark->percentage ?? $rowPercentage ?? 0) >= 40) ? 'PASS' : 'FAIL');
                            @endphp
                            <tr>
                                <td class="center">{{ $index + 1 }}</td>
                                <td class="subject">
                                    {{ $subjectName }}
                                    <span class="subject-meta">{{ $subjectCode }}</span>
                                </td>
                                <td class="center">{{ is_null($fullMarks) ? '—' : number_format((float) $fullMarks, 2) }}</td>
                                <td class="center">{{ is_null($passMark) ? '—' : number_format((float) $passMark, 2) }}</td>
                                <td class="center {{ !$absent && !is_null($obtainedValue) && !is_null($passMark) && (float) $obtainedValue < (float) $passMark ? 'fail-cell' : '' }}">{{ $obtained }}</td>
                                <td class="center">{{ is_null($rowPercentage) ? '—' : number_format($rowPercentage, 2) . '%' }}</td>
                                <td class="center">{{ $rowGrade }}</td>
                                <td class="center">{{ $rowResult }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="center" style="padding: 18px 10px;">
                                    No marks found for the selected filters
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="right">Grand Total</td>
                            <td class="center">{{ number_format($grandTotal, 2) }}</td>
                            <td class="center">{{ number_format($percentage, 2) }}%</td>
                            <td class="center">{{ $grade }}</td>
                            <td class="center">{{ $resultText }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            <div class="section-heading">Result</div>

            <table class="summary-table">
                <tr>
                    <td>
                        <div class="summary-label">Overall Percentage</div>
                        <div class="summary-value">{{ number_format($percentage, 2) }}%</div>
                    </td>
                    <td>
                        <div class="summary-label">Grade</div>
                        <div class="summary-value">{{ $grade }}</div>
                    </td>
                    <td>
                        <div class="summary-label">Result</div>
                        <div class="summary-value">{{ $resultText }}</div>
                    </td>
                    <td>
                        <div class="summary-label">Total Obtained</div>
                        <div class="summary-value">{{ number_format($grandTotal, 2) }}</div>
                    </td>
                </tr>
            </table>

            <div class="result-row">
                <div class="result-col">
                    <div class="summary-label">Remarks</div>
                    <div class="summary-value" style="font-size: 16px;">{{ $resultText === 'PASS' ? 'Qualified' : 'Needs Improvement' }}</div>
                </div>
                <div class="result-col">
                    <div class="summary-label">Selected Exam</div>
                    <div class="summary-value" style="font-size: 16px;">{{ $examTitle }}</div>
                </div>
            </div>

            <div class="wide-gap"></div>

            <div class="signature-grid">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Class Teacher</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Controller of Examination</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Parent / Guardian</div>
                </div>
            </div>

            <div class="footer-note">
                This is a computer-generated document. No signature is required. For verification, contact the examination department.
            </div>
        </div>
    </div>
</body>
</html>
