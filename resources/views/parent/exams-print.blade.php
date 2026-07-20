<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Marksheet') }} - {{ $selectedChild['name'] ?? __('Student') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 10mm; }
        * { box-sizing: border-box; }
        body { background: #fff; color: #111827; font-family: Arial, Helvetica, sans-serif; }
        .sheet { width: 100%; }
        .report-card { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .report-card td, .report-card th {
            border: 1px solid #111827;
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
        .result-pending { background: #e2e8f0; color: #475569; font-weight: 700; }
        .no-print { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 12px; }
        .no-print button, .no-print a { border: 0; border-radius: 999px; padding: 10px 16px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-close { background: #e5e7eb; color: #334155; }
        .signature-line { border-top: 1px solid #111827; width: 72%; margin: 0 auto 10px; }
        .signature-cell { height: 72px; vertical-align: bottom; padding-top: 22px; padding-bottom: 10px; }
        .subject-cell { font-size: 9.5px; font-weight: 700; line-height: 1.15; }
        .compact-cell { font-size: 9.5px; line-height: 1.15; }
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
        $entries = collect($examEntries ?? []);
        $grandTotal = (float) (($examTotals['obtained'] ?? 0));
        $totalFull = (float) (($examTotals['full'] ?? 0));
        $percentage = (float) (($examTotals['percentage'] ?? 0));
        $result = strtoupper((string) ($examTotals['result'] ?? 'FAIL'));
        $resultText = $result === 'PASS' ? 'PASS' : ($result === 'PENDING' ? 'PENDING' : 'FAIL');
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

        <div class="report-card">
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
                        <div class="mt-2 text-lg font-bold text-slate-800">{{ __('ACADEMIC TRANSCRIPT') }}</div>
                        <div class="report-small mt-1 break-words">
                            {{ __('Academic Year') }}: {{ $selectedChild['academic_year'] ?? __('All') }} |
                            {{ __('Semester') }}: {{ $selectedChild['semester'] ?? __('All') }} |
                            {{ __('Exam') }}: {{ $examTitle ?? __('Exam') }}
                        </div>
                    </td>
                    <td class="w-24 text-center align-middle p-3">
                        <div class="text-3xl font-black text-slate-300">MARKS</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-card mt-6">
            <div class="section-bar">{{ __('Student Information') }}</div>
            <table class="report-card">
                <tr>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Student Name') }}</div>
                        <div class="meta-value">{{ $selectedChild['name'] ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Student ID') }}</div>
                        <div class="meta-value">{{ $selectedChild['id'] ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Roll Number') }}</div>
                        <div class="meta-value">{{ $selectedChild['roll_no'] ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Semester') }}</div>
                        <div class="meta-value">{{ $selectedChild['semester'] ?? 'N/A' }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Academic Year (BS)') }}</div>
                        <div class="meta-value">{{ $selectedChild['academic_year'] ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Exam Category') }}</div>
                        <div class="meta-value">{{ $examCategoryLabel ?? __('Exam') }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Issue Date') }}</div>
                        <div class="meta-value">{{ now()->format('Y-m-d') }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Result Status') }}</div>
                        <div class="meta-value">{{ $resultText }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-card mt-6">
            <div class="section-bar">{{ __('Academic Performance') }}</div>
            <table class="report-card">
                <thead>
                    <tr>
                        <th class="text-center">{{ __('S.N.') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th class="text-center">{{ __('Full Marks') }}</th>
                        <th class="text-center">{{ __('Pass Mark') }}</th>
                        <th class="text-center">{{ __('Marks Obtained') }}</th>
                        <th class="text-center">{{ __('Percentage') }}</th>
                        <th class="text-center">{{ __('Result') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $index => $entry)
                        @php
                            $status = strtoupper($entry['status'] ?? 'PENDING');
                            $statusClass = match ($status) {
                                'PASS' => 'result-pass',
                                'FAIL' => 'result-fail',
                                'ABSENT' => 'result-abs',
                                default => 'result-pending',
                            };
                        @endphp
                        <tr>
                            <td class="text-center font-bold">{{ $index + 1 }}</td>
                            <td class="subject-cell">
                                <div>{{ $entry['subject_name'] ?? 'N/A' }}</div>
                                <div class="report-small">{{ $entry['subject_code'] ?? '' }}</div>
                            </td>
                            <td class="text-center compact-cell">{{ number_format((float) ($entry['full_marks'] ?? 0), 2) }}</td>
                            <td class="text-center compact-cell">{{ number_format((float) ($entry['passing_marks'] ?? 0), 2) }}</td>
                            <td class="text-center compact-cell">{{ $entry['obtained_marks'] ?? 'N/A' }}</td>
                            <td class="text-center compact-cell">{{ isset($entry['percentage']) && $entry['percentage'] !== null ? number_format((float) $entry['percentage'], 2) . '%' : '—' }}</td>
                            <td class="text-center compact-cell">
                                <span class="inline-flex min-w-[54px] items-center justify-center rounded px-2 py-1 text-[11px] {{ $statusClass }}">
                                    {{ $status === 'ABSENT' ? 'ABS' : $status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">{{ __('No marks found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="subtle-bar">
                        <td colspan="4" class="text-right font-bold">{{ __('Grand Total') }}</td>
                        <td class="font-bold text-center">{{ number_format($grandTotal, 2) }}</td>
                        <td class="font-bold text-center">{{ number_format($percentage, 2) }}%</td>
                        <td class="font-bold text-center">{{ $resultText }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="report-card mt-6">
            <div class="section-bar">{{ __('Result') }}</div>
            <table class="report-card">
                <tr>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Overall Percentage') }}</div>
                        <div class="meta-value">{{ number_format($percentage, 2) }}%</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Total Obtained') }}</div>
                        <div class="meta-value">{{ number_format($grandTotal, 2) }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Result') }}</div>
                        <div class="meta-value">{{ $resultText }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Subjects') }}</div>
                        <div class="meta-value">{{ $entries->count() }}</div>
                    </td>
                </tr>
            </table>

            <table class="report-card">
                <tr>
                    <td class="signature-cell p-4 text-center" style="width:33.333%;">
                        <div class="signature-line"></div>
                        <div class="meta-value">{{ __('Class Teacher') }}</div>
                    </td>
                    <td class="signature-cell p-4 text-center" style="width:33.333%;">
                        <div class="signature-line"></div>
                        <div class="meta-value">{{ __('Controller of Examination') }}</div>
                    </td>
                    <td class="signature-cell p-4 text-center" style="width:33.333%;">
                        <div class="signature-line"></div>
                        <div class="meta-value">{{ __('Parent / Guardian') }}</div>
                    </td>
                </tr>
            </table>

            <div class="p-3">
                <div class="text-center text-[11px] text-slate-500">
                    {{ __('This is a computer-generated transcript. Only published marks for the selected exam are shown.') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

