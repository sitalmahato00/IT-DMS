<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->user->name ?? 'Student' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 10mm; }
        * { box-sizing: border-box; }
        body { background: #fff; color: #111827; font-family: Arial, Helvetica, sans-serif; }
        .sheet { width: 100%; }
        .report-card { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .report-card td, .report-card th {
            border: 1px solid #1f2937;
            padding: 5px 6px;
            vertical-align: middle;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
        }
        .report-card th { background: #e9e3f7; font-size: 9.5px; line-height: 1.15; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
        .section-bar { background: #ece6ff; color: #1f2937; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; text-align: center; padding: 7px 8px; }
        .subtle-bar { background: #f8fafc; font-weight: 700; text-align: center; }
        .meta-label { font-size: 8.5px; font-weight: 700; color: #4b5563; text-transform: uppercase; letter-spacing: .05em; line-height: 1.1; }
        .meta-value { font-size: 10px; font-weight: 700; color: #111827; margin-top: 2px; line-height: 1.15; }
        .report-title { font-size: 24px; font-weight: 800; letter-spacing: .02em; color: #1f2f8a; line-height: 1.05; }
        .report-subtitle { font-size: 11px; font-weight: 700; color: #334155; line-height: 1.2; }
        .report-small { font-size: 9px; color: #475569; line-height: 1.2; }
        .logo-box { width: 68px; height: 68px; object-fit: contain; }
        .result-pass { background: #dcfce7; color: #166534; font-weight: 700; }
        .result-fail { background: #fee2e2; color: #b91c1c; font-weight: 700; }
        .result-abs { background: #fef3c7; color: #b45309; font-weight: 700; }
        .no-print { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 12px; }
        .no-print button { border: 0; border-radius: 999px; padding: 10px 16px; font-weight: 700; cursor: pointer; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-close { background: #e5e7eb; color: #334155; }
        .signature-line { border-top: 1px solid #111827; width: 72%; margin: 0 auto 10px; }
        .signature-cell { height: 72px; vertical-align: bottom; padding-top: 22px; padding-bottom: 10px; }
        .page-break { page-break-inside: avoid; break-inside: avoid; }
        .subject-cell { font-size: 9.5px; font-weight: 700; line-height: 1.15; }
        .compact-cell { font-size: 9.5px; line-height: 1.15; }
        .sheet-header { table-layout: fixed; width: 100%; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .report-card { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    @php
        $marks = collect($marksheetData['exam_marks'] ?? []);
        $isCtevt = strtolower($filters['exam_category'] ?? '') === 'ctevt';
        $columns = $isCtevt ? 9 : 7;
        $grandTotal = (float) ($marksheetData['total_obtained'] ?? 0);
        $totalFull = (float) ($marksheetData['total_full'] ?? 0);
        $percentage = (float) ($marksheetData['percentage'] ?? 0);
        $grade = $marksheetData['grade'] ?? '-';
        $result = strtoupper((string) ($marksheetData['result'] ?? 'FAIL'));
        $resultClass = $result === 'PASS' ? 'result-pass' : 'result-fail';
        $resultText = $result === 'PASS' ? 'PASS' : 'FAIL';
        $examTitle = $marks->first()?->exam?->exam_name ?? ucfirst($filters['exam_category'] ?? 'Marksheet');
        $issueDate = now()->format('Y-m-d');
        $departmentName = $department->name ?? 'Department';
        $addressParts = array_filter([
            $department->address ?? null,
            $department->city ?? null,
            $department->district ?? null,
        ]);
        $contactParts = array_filter([
            $department->email ? 'Email: '.$department->email : null,
            $department->phone ? 'Phone: '.$department->phone : null,
        ]);
    @endphp

    <div class="sheet mx-auto max-w-6xl p-2 sm:p-4">
        <div class="no-print">
            <button type="button" onclick="window.print()" class="btn-print">Print</button>
            <button type="button" onclick="window.close()" class="btn-close">Close</button>
        </div>

        <table class="report-card">
            <tr>
                <td colspan="{{ $columns }}" class="p-0">
                    <table class="sheet-header border-collapse">
                        <tr>
                            <td class="w-24 text-center align-middle p-3">
                                <img src="{{ $departmentLogoUrl ?? '/images/default-logo.svg' }}" alt="Logo" class="logo-box mx-auto">
                            </td>
                            <td class="text-center py-3 px-2">
                                <div class="report-title break-words">{{ $departmentName }}</div>
                                @if(count($addressParts) > 0)
                                    <div class="report-subtitle mt-1">{{ implode(', ', $addressParts) }}</div>
                                @endif
                                @if(count($contactParts) > 0)
                                    <div class="report-small mt-1">{{ implode(' | ', $contactParts) }}</div>
                                @endif
                                <div class="mt-2 text-lg font-bold text-slate-800">ACADEMIC TRANSCRIPT</div>
                                <div class="report-small mt-1 break-words">
                                    Academic Year: {{ $filters['academic_year'] ?? 'All' }} |
                                    Semester: {{ $filters['semester'] ?? 'All' }} |
                                    Category: {{ ucfirst($filters['exam_category'] ?? 'Assessment') }} |
                                    Exam: {{ $examTitle }}
                                </div>
                            </td>
                            <td class="w-24 text-center align-middle p-3">
                                <div class="text-3xl font-black text-slate-300">MARKS</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr><td colspan="{{ $columns }}" class="section-bar">Student Information</td></tr>

            <tr>
                <td colspan="{{ $columns }}" class="p-0">
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="p-3" style="width:25%;">
                                <div class="meta-label">Student Name</div>
                                <div class="meta-value">{{ $student->user->name ?? 'N/A' }}</div>
                            </td>
                            <td class="p-3" style="width:25%;">
                                <div class="meta-label">Student ID</div>
                                <div class="meta-value">{{ $student->id }}</div>
                            </td>
                            <td class="p-3" style="width:25%;">
                                <div class="meta-label">Roll Number</div>
                                <div class="meta-value">{{ $student->roll_no ?? 'N/A' }}</div>
                            </td>
                            <td class="p-3" style="width:25%;">
                                <div class="meta-label">Semester</div>
                                <div class="meta-value">{{ $student->semester ?? 'N/A' }}</div>
                            </td>
                        </tr>
                <tr>
                    <td class="p-3">
                        <div class="meta-label">Academic Year (BS)</div>
                        <div class="meta-value">{{ $student->academic_year_bs ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">Issue Date</div>
                        <div class="meta-value">{{ now()->format('Y-m-d') }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">Exam Category</div>
                        <div class="meta-value">{{ ucfirst($filters['exam_category'] ?? 'assessment') }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">Public Entries</div>
                        <div class="meta-value">{{ $marks->count() }}</div>
                    </td>
                </tr>
                    </table>
                </td>
            </tr>

            <tr><td colspan="{{ $columns }}" class="section-bar">Academic Performance</td></tr>

            <tr>
                <th class="text-center">S.N.</th>
                <th>Subject</th>
                @if($isCtevt)
                    <th class="text-center">Full Mark (Int)</th>
                    <th class="text-center">Full Mark (Ext)</th>
                    <th class="text-center">Pass Mark (Int)</th>
                    <th class="text-center">Pass Mark (Ext)</th>
                    <th class="text-center">Marks Obtained (Int)</th>
                    <th class="text-center">Marks Obtained (Ext)</th>
                    <th class="text-center">Total</th>
                @else
                    <th class="text-center">Full Marks</th>
                    <th class="text-center">Pass Mark</th>
                    <th class="text-center">Marks Obtained</th>
                    <th class="text-center">Grade</th>
                    <th class="text-center">Result</th>
                @endif
            </tr>

            @forelse($marks as $index => $mark)
                @php
                    $subjectName = $mark->subject->subject_name ?? 'N/A';

                    $tiFull = $mark->theory_internal_full_marks ?? $mark->exam->theory_internal_max_marks ?? 0;
                    $teFull = $mark->theory_external_full_marks ?? $mark->exam->theory_external_max_marks ?? 0;
                    $tiPass = $mark->theory_internal_pass_marks ?? $mark->exam->theory_internal_pass_marks ?? 0;
                    $tePass = $mark->theory_external_pass_marks ?? $mark->exam->theory_external_pass_marks ?? 0;
                    $piFull = $mark->practical_internal_full_marks ?? $mark->exam->practical_internal_max_marks ?? 0;
                    $peFull = $mark->practical_external_full_marks ?? $mark->exam->practical_external_max_marks ?? 0;
                    $piPass = $mark->practical_internal_pass_marks ?? $mark->exam->practical_internal_pass_marks ?? 0;
                    $pePass = $mark->practical_external_pass_marks ?? $mark->exam->practical_external_pass_marks ?? 0;
                    $tiObt = $mark->theory_internal_marks ?? 0;
                    $teObt = $mark->theory_external_marks ?? 0;
                    $piObt = $mark->practical_internal_marks ?? 0;
                    $peObt = $mark->practical_external_marks ?? 0;
                    $theoryTotal = $tiObt + $teObt;
                    $practicalTotal = $piObt + $peObt;
                    $full = $mark->full_marks > 0 ? $mark->full_marks : ($mark->exam->full_marks ?? 0);
                    $pass = $mark->passing_marks > 0 ? $mark->passing_marks : ($mark->exam->passing_marks ?? ($full * 0.4));
                    $obt = $mark->isAbsent() ? 'ABS' : ($mark->marks_obtained ?? 0);
                    $resultClassRow = $mark->isAbsent() ? 'result-abs' : (($mark->result ?? '') === 'PASS' || (($mark->percentage ?? 0) >= 40) ? 'result-pass' : 'result-fail');
                @endphp

                @if($isCtevt)
                    <tr class="page-break">
                        <td class="text-center font-bold" rowspan="2">{{ $index + 1 }}</td>
                        <td class="subject-cell">{{ $subjectName }} (Th.)</td>
                        <td class="text-center compact-cell">{{ $tiFull }}</td>
                        <td class="text-center compact-cell">{{ $teFull }}</td>
                        <td class="text-center compact-cell">{{ $tiPass }}</td>
                        <td class="text-center compact-cell">{{ $tePass }}</td>
                        <td class="text-center compact-cell {{ $tiObt < $tiPass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ $tiObt }}</td>
                        <td class="text-center compact-cell {{ $teObt < $tePass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ $teObt }}</td>
                        <td class="text-center compact-cell font-bold">{{ $theoryTotal }}</td>
                    </tr>
                    <tr class="page-break">
                        <td class="subject-cell">{{ $subjectName }} (Pr.)</td>
                        <td class="text-center compact-cell">{{ $piFull }}</td>
                        <td class="text-center compact-cell">{{ $peFull }}</td>
                        <td class="text-center compact-cell">{{ $piPass }}</td>
                        <td class="text-center compact-cell">{{ $pePass }}</td>
                        <td class="text-center compact-cell {{ $piObt < $piPass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ $piObt }}</td>
                        <td class="text-center compact-cell {{ $peObt < $pePass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ $peObt }}</td>
                        <td class="text-center compact-cell font-bold">{{ $practicalTotal }}</td>
                    </tr>
                @else
                    <tr class="page-break">
                        <td class="text-center font-bold">{{ $index + 1 }}</td>
                        <td class="subject-cell">{{ $subjectName }}</td>
                        <td class="text-center compact-cell">{{ $full }}</td>
                        <td class="text-center compact-cell">{{ $pass }}</td>
                        <td class="text-center compact-cell {{ $mark->isAbsent() ? 'bg-amber-50 text-amber-700 font-bold' : '' }}">{{ $obt }}</td>
                        <td class="text-center compact-cell font-bold">{{ $mark->grade ?? '-' }}</td>
                        <td class="text-center compact-cell">
                            <span class="inline-flex min-w-[54px] items-center justify-center rounded px-2 py-1 text-[11px] {{ $resultClassRow }}">
                                {{ $mark->isAbsent() ? 'ABS' : (($mark->result ?? '') === 'PASS' || (($mark->percentage ?? 0) >= 40) ? 'PASS' : 'FAIL') }}
                            </span>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ $columns }}" class="p-4 text-center text-gray-500">No marks found</td>
                </tr>
            @endforelse

            <tr class="subtle-bar">
                <td colspan="{{ $columns - 4 }}" class="text-right font-bold">Grand Total</td>
                <td class="font-bold text-center">{{ number_format($grandTotal, 2) }}</td>
                <td class="font-bold text-center">{{ number_format($percentage, 1) }}%</td>
                <td class="font-bold text-center">{{ $grade }}</td>
                <td class="font-bold text-center">{{ $resultText }}</td>
            </tr>

            <tr><td colspan="{{ $columns }}" class="section-bar">Result</td></tr>
            <tr>
                <td colspan="{{ $columns }}" class="p-0">
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="p-3">
                                <div class="meta-label">Overall Percentage</div>
                                <div class="meta-value">{{ number_format($percentage, 1) }}%</div>
                            </td>
                            <td class="p-3">
                                <div class="meta-label">Grade</div>
                                <div class="meta-value">{{ $grade }}</div>
                            </td>
                            <td class="p-3">
                                <div class="meta-label">Result</div>
                                <div class="meta-value">{{ $resultText }}</div>
                            </td>
                            <td class="p-3">
                                <div class="meta-label">Total Obtained</div>
                                <div class="meta-value">{{ number_format($grandTotal, 2) }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="{{ $columns }}" class="p-0">
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="signature-cell p-4 text-center" style="width:33.333%;">
                                <div class="signature-line"></div>
                                <div class="meta-value">Class Teacher</div>
                            </td>
                            <td class="signature-cell p-4 text-center" style="width:33.333%;">
                                <div class="signature-line"></div>
                                <div class="meta-value">Controller of Examination</div>
                            </td>
                            <td class="signature-cell p-4 text-center" style="width:33.333%;">
                                <div class="signature-line"></div>
                                <div class="meta-value">Parent / Guardian</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="{{ $columns }}" class="p-3">
                    <div class="text-center text-[11px] text-slate-500">
                        This is a computer-generated document. No signature is required. For verification, contact the examination department.
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
