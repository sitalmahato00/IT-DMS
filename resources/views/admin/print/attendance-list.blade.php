<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance List - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4 landscape; margin: 0.5in; }
        body { font-family: 'Arial', 'Helvetica', sans-serif; font-size: 10pt; line-height: 1.4; color: #000; background: #fff; }
        @media screen {
            body { background: #f0f0f0; padding: 20px; }
            .print-container { max-width: 297mm; min-height: 210mm; margin: 0 auto; background: #fff; padding: 15mm; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
            .print-btn { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
            .print-btn:hover { background: #1d4ed8; }
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-btn { display: none !important; }
        }
        .header-section { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #000; }
        .school-logo { margin-bottom: 8px; }
        .school-logo img { max-height: 50px; }
        .logo-placeholder { font-size: 36px; }
        .school-name { font-size: 18pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .report-title { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 8px 0; padding: 6px 0; border-top: 1px solid #000; border-bottom: 1px solid #000; }
        .meta-info { display: flex; justify-content: center; gap: 20px; margin: 10px 0; font-size: 9pt; flex-wrap: wrap; }
        .meta-item { display: flex; gap: 5px; }
        .meta-label { font-weight: bold; }
        .table-container { margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; font-size: 9pt; }
        th { background: #e5e5e5 !important; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 8pt; letter-spacing: 0.5px; }
        td { text-align: left; }
        td.numeric { text-align: right; }
        td.center { text-align: center; }
        tr { page-break-inside: avoid; }
        .status-present { color: #166534; font-weight: 600; }
        .status-absent { color: #dc2626; font-weight: 600; }
        .status-leave { color: #ca8a04; font-weight: 600; }
        .footer-section { margin-top: 20px; padding-top: 15px; border-top: 1px solid #000; }
        .footer-signatures { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .signature-block { width: 40%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 35px; padding-top: 5px; font-size: 9pt; }
        .footer-note { text-align: center; font-size: 8pt; color: #666; font-style: italic; }
        .summary-grid { display: flex; gap: 10px; margin-top: 10px; }
        .summary-item { flex: 1; padding: 10px; background: #f5f5f5; border-radius: 4px; text-align: center; }
        .summary-label { font-size: 8pt; text-transform: uppercase; color: #666; }
        .summary-value { font-size: 14pt; font-weight: bold; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print Document</button>
    <div class="print-container">
        <div class="header-section">
            <div class="school-logo">
                @if($college && $college->logo_path)
                    <img src="{{ \App\Support\Media::publicUrl($college->logo_path) }}" alt="School Logo">
                @else
                    <div class="logo-placeholder">🏫</div>
                @endif
            </div>
            <div class="school-name">{{ $college->name ?? 'IT-DMS COLLEGE' }}</div>
            <div class="report-title">ATTENDANCE BY SUBJECT</div>
            <div class="meta-info">
                <div class="meta-item"><span class="meta-label">Date:</span><span>{{ $dateLabel ?? 'All Dates' }}</span></div>
                <div class="meta-item"><span class="meta-label">Semester:</span><span>{{ $semesterLabel ?? 'All' }}</span></div>
                <div class="meta-item"><span class="meta-label">Subject:</span><span>{{ $subjectLabel ?? 'All' }}</span></div>
                <div class="meta-item"><span class="meta-label">Total:</span><span>{{ $attendance->count() }}</span></div>
                <div class="meta-item"><span class="meta-label">Print Date:</span><span>{{ now()->format('Y-m-d') }}</span></div>
            </div>
        </div>

        @php
            $totalPresent = $attendance->sum('present');
            $totalAbsent = $attendance->sum('absent');
            $totalLeave = $attendance->sum('leave');
            $grandTotal = $attendance->sum('total');
        @endphp

        <!-- Summary Cards -->
        @if($attendance->count() > 0)
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Records</div>
                <div class="summary-value">{{ $grandTotal }}</div>
            </div>
            <div class="summary-item" style="background: #dcfce7;">
                <div class="summary-label">Present</div>
                <div class="summary-value" style="color: #166534;">{{ $totalPresent }}</div>
            </div>
            <div class="summary-item" style="background: #fee2e2;">
                <div class="summary-label">Absent</div>
                <div class="summary-value" style="color: #dc2626;">{{ $totalAbsent }}</div>
            </div>
            <div class="summary-item" style="background: #fef9c3;">
                <div class="summary-label">Leave</div>
                <div class="summary-value" style="color: #ca8a04;">{{ $totalLeave }}</div>
            </div>
        </div>
        @endif
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">S.N</th>
                        <th style="width: 25%;">Subject</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 10%;">Date (BS)</th>
                        <th style="width: 10%;">Total</th>
                        <th style="width: 10%;">Present</th>
                        <th style="width: 10%;">Absent</th>
                        <th style="width: 10%;">Leave</th>
                        <th style="width: 5%;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendance as $record)
                    <tr>
                        <td class="center numeric">{{ $loop->iteration }}</td>
                        <td>
                            @if($record['subject_name'] && $record['subject_name'] !== 'General')
                                {{ $record['subject_code'] ?? '' }} - {{ $record['subject_name'] }}
                            @else
                                General
                            @endif
                        </td>
                        <td class="center">{{ $record['date'] ?? '-' }}</td>
                        <td class="center">{{ $record['date_bs'] ?? '-' }}</td>
                        <td class="center numeric">{{ $record['total'] ?? 0 }}</td>
                        <td class="center"><span class="status-present">{{ $record['present'] ?? 0 }}</span></td>
                        <td class="center"><span class="status-absent">{{ $record['absent'] ?? 0 }}</span></td>
                        <td class="center"><span class="status-leave">{{ $record['leave'] ?? 0 }}</span></td>
                        <td class="center numeric">
                            @php
                                $percentage = ($record['total'] ?? 0) > 0 
                                    ? round(($record['present'] ?? 0) / $record['total'] * 100, 1) 
                                    : 0;
                            @endphp
                            {{ $percentage }}%
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="center">No attendance records found</td></tr>
                    @endforelse
                </tbody>
                @if($attendance->count() > 0)
                <tfoot>
                    <tr style="background: #e5e5e5;">
                        <td colspan="4" class="center"><strong>GRAND TOTAL</strong></td>
                        <td class="center numeric"><strong>{{ $grandTotal }}</strong></td>
                        <td class="center"><strong class="status-present">{{ $totalPresent }}</strong></td>
                        <td class="center"><strong class="status-absent">{{ $totalAbsent }}</strong></td>
                        <td class="center"><strong class="status-leave">{{ $totalLeave }}</strong></td>
                        <td class="center numeric">
                            <strong>
                                @php
                                    $overallPercentage = $grandTotal > 0 ? round($totalPresent / $grandTotal * 100, 1) : 0;
                                @endphp
                                {{ $overallPercentage }}%
                            </strong>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        <div class="footer-section">
            <div class="footer-signatures">
                <div class="signature-block"><div class="signature-line">Prepared By</div></div>
                <div class="signature-block"><div class="signature-line">Authorized Signature</div></div>
            </div>
            <div class="footer-note">This is a computer-generated attendance report.</div>
        </div>
    </div>
</body>
</html>
