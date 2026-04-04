<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->user->name ?? 'Student' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: #fff;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
        }

        .sheet {
            width: 100%;
        }

        .report-box {
            width: 100%;
            border: 1px solid #111827;
            overflow: hidden;
            background: #fff;
        }

        .report-card {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .report-card td,
        .report-card th {
            border: 1px solid #111827;
            padding: 5px 6px;
            vertical-align: middle;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
        }

        .report-card th {
            background: #e9e3f7;
            font-size: 9.5px;
            line-height: 1.15;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .section-bar {
            background: #ece6ff;
            color: #1f2937;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            text-align: center;
            padding: 7px 8px;
        }

        .subtle-bar {
            background: #f8fafc;
            font-weight: 700;
            text-align: center;
        }

        .meta-label {
            font-size: 8.5px;
            font-weight: 700;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: .05em;
            line-height: 1.1;
        }

        .meta-value {
            font-size: 10px;
            font-weight: 700;
            color: #111827;
            margin-top: 2px;
            line-height: 1.15;
        }

        .report-title {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: .02em;
            color: #1f2f8a;
            line-height: 1.05;
        }

        .report-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            line-height: 1.2;
        }

        .report-small {
            font-size: 9px;
            color: #475569;
            line-height: 1.2;
        }

        .logo-box {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .result-pass {
            background: #dcfce7;
            color: #166534;
            font-weight: 700;
        }

        .result-fail {
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 700;
        }

        .result-abs {
            background: #fef3c7;
            color: #b45309;
            font-weight: 700;
        }

        .page-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .subject-cell {
            font-size: 9.5px;
            font-weight: 700;
            line-height: 1.15;
        }

        .compact-cell {
            font-size: 9.5px;
            line-height: 1.15;
        }

        .no-print {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 12px;
        }

        .no-print button {
            border: 0;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-print {
            background: #2563eb;
            color: #fff;
        }

        .btn-close {
            background: #e5e7eb;
            color: #334155;
        }

        .signature-line {
            border-top: 1px solid #111827;
            width: 72%;
            margin: 0 auto 10px;
        }

        .signature-cell {
            height: 72px;
            vertical-align: bottom;
            padding-top: 22px;
            padding-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-card {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @php
        $marks = collect($marksheetData['exam_marks'] ?? []);
        $isCtevt = strtolower($filters['exam_category'] ?? ($marks->first()?->exam?->exam_category ?? 'assessment')) === 'ctevt';
        $columns = $isCtevt ? 9 : 7;
        $grandTotal = (float) ($marksheetData['total_obtained'] ?? 0);
        $totalFull = (float) ($marksheetData['total_full'] ?? 0);
        $percentage = (float) ($marksheetData['percentage'] ?? 0);
        $grade = $marksheetData['grade'] ?? '-';
        $result = strtoupper((string) ($marksheetData['result'] ?? 'FAIL'));
        $resultText = $result === 'PASS' ? 'PASS' : 'FAIL';
        $examTitle = $marks->first()?->exam?->exam_name ?? ucfirst($filters['exam_category'] ?? 'Marksheet');
        $departmentEntity = $college ?? $department ?? null;
        $departmentName = $departmentEntity?->name ?? 'Department';
        $addressParts = array_filter([
            $departmentEntity?->address ?? null,
            $departmentEntity?->city ?? null,
            $departmentEntity?->district ?? null,
        ]);
        $contactParts = array_filter([
            $departmentEntity?->email ? 'Email: ' . $departmentEntity->email : null,
            $departmentEntity?->phone ? 'Phone: ' . $departmentEntity->phone : null,
        ]);
    @endphp

    <div class="sheet mx-auto max-w-6xl p-2 sm:p-4">
        <div class="no-print">
            <button type="button" onclick="window.print()" class="btn-print">Print</button>
            <button type="button" onclick="window.close()" class="btn-close">Close</button>
        </div>

        <div class="report-box">
            <table class="report-card">
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
        </div>

        <div class="report-box mt-6">
            <div class="section-bar">Student Information</div>
            <table class="report-card">
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
        </div>

        <div class="report-box mt-6">
            <div class="section-bar">Academic Performance</div>
            <table class="report-card">
                <thead>
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
                </thead>

                <tbody>
                    @if($marks && $marks->count() > 0)
                        @foreach($marks as $index => $mark)
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
                                $status = $mark->isAbsent() ? 'ABS' : ($mark->isFilled() ? (($mark->result ?? '') === 'PASS' || ($mark->percentage ?? 0) >= 40 ? 'PASS' : 'FAIL') : 'PENDING');
                                $statusClass = match ($status) {
                                    'PASS' => 'result-pass',
                                    'FAIL' => 'result-fail',
                                    'ABS' => 'result-abs',
                                    default => 'bg-slate-200 text-slate-700 font-bold',
                                };
                            @endphp

                            @if($isCtevt)
                                <tr class="page-break">
                                    <td class="text-center font-bold" rowspan="2">{{ $index + 1 }}</td>
                                    <td class="subject-cell">{{ $subjectName }} (Th.)</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $tiFull, 2) }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $teFull, 2) }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $tiPass, 2) }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $tePass, 2) }}</td>
                                    <td class="text-center compact-cell {{ ($tiObt ?? 0) < $tiPass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ !$mark->isFilled() ? 'N/A' : number_format((float) $tiObt, 2) }}</td>
                                    <td class="text-center compact-cell {{ ($teObt ?? 0) < $tePass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ !$mark->isFilled() ? 'N/A' : number_format((float) $teObt, 2) }}</td>
                                    <td class="text-center compact-cell font-bold">{{ number_format((float) $theoryTotal, 2) }}</td>
                                </tr>
                                <tr class="page-break">
                                    <td class="subject-cell">{{ $subjectName }} (Pr.)</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $piFull, 2) }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $peFull, 2) }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $piPass, 2) }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $pePass, 2) }}</td>
                                    <td class="text-center compact-cell {{ ($piObt ?? 0) < $piPass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ !$mark->isFilled() ? 'N/A' : number_format((float) $piObt, 2) }}</td>
                                    <td class="text-center compact-cell {{ ($peObt ?? 0) < $pePass ? 'bg-red-50 text-red-700 font-bold' : '' }}">{{ !$mark->isFilled() ? 'N/A' : number_format((float) $peObt, 2) }}</td>
                                    <td class="text-center compact-cell font-bold">{{ number_format((float) $practicalTotal, 2) }}</td>
                                </tr>
                            @else
                                <tr class="page-break">
                                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                                    <td class="subject-cell">{{ $subjectName }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $mark->full_marks, 2) }}</td>
                                    <td class="text-center compact-cell">{{ number_format((float) $mark->passing_marks, 2) }}</td>
                                    <td class="text-center compact-cell {{ $mark->isAbsent() ? 'bg-amber-50 text-amber-700 font-bold' : '' }}">
                                        {{ $mark->isAbsent() ? 'ABS' : (!$mark->isFilled() ? 'N/A' : number_format((float) $mark->marks_obtained, 2)) }}
                                    </td>
                                    <td class="text-center compact-cell font-bold">{{ $mark->grade ?? '-' }}</td>
                                    <td class="text-center compact-cell">
                                        <span class="inline-flex min-w-[54px] items-center justify-center rounded px-2 py-1 text-[11px] {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $columns }}" class="p-4 text-center text-gray-500">No marks found</td>
                        </tr>
                    @endif
                </tbody>

                <tfoot>
                    <tr class="subtle-bar">
                        <td colspan="{{ $columns }}" class="p-2 text-right font-bold">
                            Grand Total:
                            {{ number_format($grandTotal, 2) }}
                            @if(!$isCtevt)
                                /
                                {{ number_format($totalFull, 2) }}
                                ({{ number_format($percentage, 2) }}%)
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="report-box mt-6">
            <div class="section-bar">Result</div>
            <table class="report-card">
                <tr>
                    <td class="p-3">
                        <div class="meta-label">Overall Percentage</div>
                        <div class="meta-value">{{ number_format($percentage, 2) }}%</div>
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

            <table class="report-card">
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

            <p class="text-xs text-gray-500 mt-4 text-center">
                This is a computer-generated document. No signature is required.<br>
                For verification, please contact the examination department.
            </p>
        </div>
    </div>
</body>
</html>
