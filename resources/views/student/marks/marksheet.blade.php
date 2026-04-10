@extends('student.layouts.studentlayout')

@section('title', __('Marksheet'))

@section('styles')
<style>
    * {
        box-sizing: border-box;
    }

    @page {
        size: A4;
        margin: 10mm;
    }

    .report-box {
        width: 100%;
        border: 1px solid #111827;
        overflow: hidden;
        background: #fff;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .report-table td,
    .report-table th {
        border: 1px solid #111827;
        padding: 5px 6px;
        vertical-align: middle;
        word-break: break-word;
        overflow-wrap: anywhere;
        white-space: normal;
    }

    .report-table th {
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

    .result-pending {
        background: #e2e8f0;
        color: #475569;
        font-weight: 700;
    }

    .no-print {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-bottom: 12px;
    }

    .no-print button,
    .no-print a {
        border: 0;
        border-radius: 999px;
        padding: 10px 16px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
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

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        #sidebar,
        #sidebarBackdrop,
        header {
            display: none !important;
        }

        .report-box {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
        }
    }
</style>
@endsection

@section('content')
@php
    $locale = app()->getLocale();
    $departmentEntity = $college ?? $department ?? null;
    $departmentName = $departmentEntity?->name
        ?? ($locale === 'ne' ? 'सूचना प्रविधि विभाग' : 'Information Technology');
    $addressParts = array_filter([
        $departmentEntity?->address ?? null,
        $departmentEntity?->city ?? null,
        $departmentEntity?->district ?? null,
    ]);
    $contactParts = array_filter([
        $departmentEntity?->email ? 'Email: ' . $departmentEntity->email : null,
        $departmentEntity?->phone ? 'Phone: ' . $departmentEntity->phone : null,
    ]);
    $generatedAtDisplay = isset($generatedAt) ? $generatedAt->format('Y-m-d H:i') : now()->format('Y-m-d H:i');
    $assessmentRows = collect($assessmentTranscriptRows ?? []);
    $ctevtRows = collect($ctevtTranscriptRows ?? []);
    $assessmentTotals = $assessmentTranscriptTotals ?? ['obtained' => 0, 'full' => 0, 'count' => 0];
    $ctevtTotals = $ctevtTranscriptTotals ?? ['obtained' => 0, 'full' => 0, 'count' => 0];
    $overallTotals = $overallTranscriptTotals ?? ['obtained' => 0, 'full' => 0, 'percentage' => 0];
    $assessmentPercentage = ($assessmentTotals['full'] ?? 0) > 0
        ? round((($assessmentTotals['obtained'] ?? 0) / $assessmentTotals['full']) * 100, 2)
        : 0;
    $ctevtPercentage = ($ctevtTotals['full'] ?? 0) > 0
        ? round((($ctevtTotals['obtained'] ?? 0) / $ctevtTotals['full']) * 100, 2)
        : 0;
    $statusClasses = [
        'PASS' => 'result-pass',
        'FAIL' => 'result-fail',
        'ABS' => 'result-abs',
        'PENDING' => 'result-pending',
    ];
@endphp

<div class="student-smooth-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <div class="no-print">
        <a href="{{ route('student.marks') }}" class="btn-close inline-flex items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>{{ __('Back to Results') }}</span>
        </a>
        <button type="button" onclick="window.print()" class="btn-print inline-flex items-center gap-2">
            <i class="bi bi-printer"></i>
            <span>{{ __('Print') }}</span>
        </button>
    </div>

    <div class="space-y-6">
        <div class="report-box">
            <table class="report-table">
                <tr>
                    <td class="w-24 text-center align-middle p-3">
                        <img src="{{ $departmentLogoUrl ?? asset('images/default-logo.svg') }}" alt="Logo" class="logo-box mx-auto">
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
                            {{ __('Public marks only') }} |
                            {{ __('Exam') }}: {{ $selectedExamName ?? __('All Published Exams') }} |
                            {{ __('Academic Year') }}: {{ $student->academic_year_bs ?? __('All') }} |
                            {{ __('Semester') }}: {{ $student->semester ?? __('All') }} |
                            {{ __('Generated') }}: {{ $generatedAtDisplay }}
                        </div>
                    </td>
                    <td class="w-24 text-center align-middle p-3">
                        <div class="text-3xl font-black text-slate-300">MARKS</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-box">
            <div class="section-bar">{{ __('Student Information') }}</div>
            <table class="report-table">
                <tr>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Student Name') }}</div>
                        <div class="meta-value">{{ $student->user->name ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Student ID') }}</div>
                        <div class="meta-value">{{ $student->id }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Roll Number') }}</div>
                        <div class="meta-value">{{ $student->roll_no ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Semester') }}</div>
                        <div class="meta-value">{{ $student->semester ?? 'N/A' }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Academic Year (BS)') }}</div>
                        <div class="meta-value">{{ $student->academic_year_bs ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Date of Birth') }}</div>
                        <div class="meta-value">{{ $student->date_of_birth ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Date of Birth (BS)') }}</div>
                        <div class="meta-value">{{ $student->date_of_birth_bs ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3">
                        <div class="meta-label">{{ __('Public Entries') }}</div>
                        <div class="meta-value">{{ $publicEntryCount ?? ($assessmentRows->count() + $ctevtRows->count()) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-box">
            <div class="section-bar">{{ __('Academic Performance') }}</div>

            <div class="border-t border-black">
                <div class="bg-[#f8fafc] px-3 py-2 text-center text-[12px] font-bold uppercase tracking-[0.08em] text-slate-800">
                    {{ __('Assessment Public Marks') }}
                </div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:6%;">{{ __('S.N.') }}</th>
                            <th style="width:20%;">{{ __('Subject') }}</th>
                            <th style="width:17%;">{{ __('Exam') }}</th>
                            <th class="text-center" style="width:10%;">{{ __('Full Marks') }}</th>
                            <th class="text-center" style="width:10%;">{{ __('Pass Mark') }}</th>
                            <th class="text-center" style="width:12%;">{{ __('Marks Obtained') }}</th>
                            <th class="text-center" style="width:8%;">{{ __('Grade') }}</th>
                            <th class="text-center" style="width:9%;">{{ __('Result') }}</th>
                            <th class="text-center" style="width:8%;">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assessmentRows as $row)
                            @php
                                $status = strtoupper($row['result'] ?? 'PENDING');
                                $statusClass = $statusClasses[$status] ?? $statusClasses['PENDING'];
                            @endphp
                            <tr class="page-break">
                                <td class="text-center font-bold">{{ $row['sn'] }}</td>
                                <td class="subject-cell">
                                    <div>{{ $row['subject_name'] }}</div>
                                    <div class="report-small">{{ $row['subject_code'] }}</div>
                                </td>
                                <td class="compact-cell">
                                    <div>{{ $row['exam_name'] }}</div>
                                    @if(!empty($row['assessment_number']))
                                        <div class="report-small">{{ __('Assessment') }} {{ $row['assessment_number'] }}</div>
                                    @endif
                                </td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['full_marks'], 2) }}</td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['passing_marks'], 2) }}</td>
                                <td class="text-center compact-cell {{ $status === 'ABS' ? 'bg-amber-50 text-amber-700 font-bold' : '' }}">
                                    {{ is_numeric($row['marks_obtained']) ? number_format((float) $row['marks_obtained'], 2) : ($row['marks_obtained'] ?? 'N/A') }}
                                </td>
                                <td class="text-center compact-cell font-bold">{{ $row['grade'] }}</td>
                                <td class="text-center compact-cell">
                                    <span class="inline-flex min-w-[54px] items-center justify-center rounded px-2 py-1 text-[11px] {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="text-center compact-cell">{{ $row['exam_date'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-4 text-center text-gray-500">{{ __('No published assessment marks found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="subtle-bar">
                            <td colspan="9" class="text-right font-bold">
                                {{ __('Assessment Total') }}:
                                {{ number_format((float) ($assessmentTotals['obtained'] ?? 0), 2) }}
                                /
                                {{ number_format((float) ($assessmentTotals['full'] ?? 0), 2) }}
                                ({{ number_format($assessmentPercentage, 2) }}%)
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="border-t border-black">
                <div class="bg-[#f8fafc] px-3 py-2 text-center text-[12px] font-bold uppercase tracking-[0.08em] text-slate-800">
                    {{ __('CTEVT Public Marks') }}
                </div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:5%;">{{ __('S.N.') }}</th>
                            <th style="width:18%;">{{ __('Subject') }}</th>
                            <th class="text-center" style="width:9%;">{{ __('Full Mark (Int)') }}</th>
                            <th class="text-center" style="width:9%;">{{ __('Full Mark (Ext)') }}</th>
                            <th class="text-center" style="width:9%;">{{ __('Pass Mark (Int)') }}</th>
                            <th class="text-center" style="width:9%;">{{ __('Pass Mark (Ext)') }}</th>
                            <th class="text-center" style="width:13%;">{{ __('Marks Obtained (Int)') }}</th>
                            <th class="text-center" style="width:13%;">{{ __('Marks Obtained (Ext)') }}</th>
                            <th class="text-center" style="width:15%;">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ctevtRows as $row)
                            @php
                                $status = strtoupper($row['result'] ?? 'PENDING');
                                $statusClass = $statusClasses[$status] ?? $statusClasses['PENDING'];
                            @endphp
                            <tr class="page-break">
                                <td class="text-center font-bold" rowspan="2">{{ $row['sn'] }}</td>
                                <td class="subject-cell">
                                    {{ $row['subject_name'] }} (Th.)
                                    <div class="report-small">{{ $row['subject_code'] }}</div>
                                </td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['ti_full'], 2) }}</td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['te_full'], 2) }}</td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['ti_pass'], 2) }}</td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['te_pass'], 2) }}</td>
                                <td class="text-center compact-cell {{ ($row['ti_obtained'] ?? 0) < ($row['ti_pass'] ?? 0) ? 'bg-red-50 text-red-700 font-bold' : '' }}">
                                    {{ is_null($row['ti_obtained']) ? 'N/A' : number_format((float) $row['ti_obtained'], 2) }}
                                </td>
                                <td class="text-center compact-cell {{ ($row['te_obtained'] ?? 0) < ($row['te_pass'] ?? 0) ? 'bg-red-50 text-red-700 font-bold' : '' }}">
                                    {{ is_null($row['te_obtained']) ? 'N/A' : number_format((float) $row['te_obtained'], 2) }}
                                </td>
                                <td class="text-center compact-cell font-bold">{{ number_format((float) $row['theory_total'], 2) }}</td>
                            </tr>
                            <tr class="page-break">
                                <td class="subject-cell">
                                    {{ $row['subject_name'] }} (Pr.)
                                </td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['pi_full'], 2) }}</td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['pe_full'], 2) }}</td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['pi_pass'], 2) }}</td>
                                <td class="text-center compact-cell">{{ number_format((float) $row['pe_pass'], 2) }}</td>
                                <td class="text-center compact-cell {{ ($row['pi_obtained'] ?? 0) < ($row['pi_pass'] ?? 0) ? 'bg-red-50 text-red-700 font-bold' : '' }}">
                                    {{ is_null($row['pi_obtained']) ? 'N/A' : number_format((float) $row['pi_obtained'], 2) }}
                                </td>
                                <td class="text-center compact-cell {{ ($row['pe_obtained'] ?? 0) < ($row['pe_pass'] ?? 0) ? 'bg-red-50 text-red-700 font-bold' : '' }}">
                                    {{ is_null($row['pe_obtained']) ? 'N/A' : number_format((float) $row['pe_obtained'], 2) }}
                                </td>
                                <td class="text-center compact-cell font-bold">{{ number_format((float) $row['practical_total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-4 text-center text-gray-500">{{ __('No published CTEVT marks found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="subtle-bar">
                            <td colspan="9" class="text-right font-bold">
                                {{ __('CTEVT Total') }}:
                                {{ number_format((float) ($ctevtTotals['obtained'] ?? 0), 2) }}
                                /
                                {{ number_format((float) ($ctevtTotals['full'] ?? 0), 2) }}
                                ({{ number_format($ctevtPercentage, 2) }}%)
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="report-box">
            <div class="section-bar">{{ __('Result') }}</div>
            <table class="report-table">
                <tr>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Overall Percentage') }}</div>
                        <div class="meta-value">{{ number_format((float) ($overallTotals['percentage'] ?? 0), 2) }}%</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Grade') }}</div>
                        <div class="meta-value">{{ $marksheetGrade ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Result') }}</div>
                        <div class="meta-value">{{ $result ?? 'PENDING' }}</div>
                    </td>
                    <td class="p-3" style="width:25%;">
                        <div class="meta-label">{{ __('Total Obtained') }}</div>
                        <div class="meta-value">{{ number_format((float) ($overallTotals['obtained'] ?? 0), 2) }}</div>
                    </td>
                </tr>
            </table>

            <table class="report-table">
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
                    {{ __('This is a computer-generated transcript. Only published Assessment and CTEVT marks are shown.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

