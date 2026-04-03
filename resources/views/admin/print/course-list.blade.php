<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses List - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4 portrait; margin: 1in; }
        body { font-family: 'Arial', 'Helvetica', sans-serif; font-size: 11pt; line-height: 1.4; color: #000; background: #fff; }
        @media screen {
            body { background: #f0f0f0; padding: 20px; }
            .print-container { max-width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; padding: 20mm; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
            .print-btn { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
            .print-btn:hover { background: #1d4ed8; }
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-btn { display: none !important; }
        }
        .header-section { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #000; }
        .school-logo { margin-bottom: 10px; }
        .school-logo img { max-height: 60px; }
        .logo-placeholder { font-size: 48px; }
        .school-name { font-size: 20pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .report-title { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 10px 0; padding: 8px 0; border-top: 1px solid #000; border-bottom: 1px solid #000; }
        .meta-info { display: flex; justify-content: center; gap: 30px; margin: 15px 0; font-size: 10pt; }
        .meta-item { display: flex; gap: 5px; }
        .meta-label { font-weight: bold; }
        .table-container { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        th, td { border: 1px solid #000; padding: 8px 10px; text-align: left; font-size: 10pt; }
        th { background: #e5e5e5 !important; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9pt; letter-spacing: 0.5px; }
        td { text-align: left; }
        td.numeric { text-align: right; }
        td.center { text-align: center; }
        tr { page-break-inside: avoid; }
        .footer-section { margin-top: 30px; padding-top: 20px; border-top: 1px solid #000; }
        .footer-signatures { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .signature-block { width: 45%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-size: 10pt; }
        .footer-note { text-align: center; font-size: 9pt; color: #666; font-style: italic; }
    </style>
</head>
<body>
    <button class="Print-btn no-print" onclick="window.print()">🖨️ Print Document</button>
    <div class="Print-container">
        <div class="header-section">
            <div class="school-logo">
                @if($college && $college->logo_path)
                    <img src="{{ \App\Support\Media::publicUrl($college->logo_path) }}" alt="School Logo">
                @else
                    <div class="logo-placeholder">🏫</div>
                @endif
            </div>
            <div class="school-name">{{ $college->name ?? 'IT-DMS COLLEGE' }}</div>
            <div class="report-title">COURSES LIST</div>
            <div class="meta-info">
                <div class="meta-item"><span class="meta-label">Academic Year:</span><span>{{ date('Y') }}</span></div>
                <div class="meta-item"><span class="meta-label">Total Courses:</span><span>{{ $courses->count() }}</span></div>
                <div class="meta-item"><span class="meta-label">Print Date:</span><span>{{ now()->format('Y-m-d') }}</span></div>
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">S.N</th>
                        <th style="width: 30%;">Course Name</th>
                        <th style="width: 15%;">Code</th>
                        <th style="width: 15%;">Semester</th>
                        <th style="width: 20%;">Category</th>
                        <th style="width: 15%;">Credits</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td class="center numeric">{{ $loop->iteration }}</td>
                        <td>{{ $course->subject_name ?? $course->name }}</td>
                        <td class="center">{{ $course->subject_code ?? $course->code }}</td>
                        <td class="center">{{ $course->semester ?? '-' }}</td>
                        <td>{{ $course->category ?? 'General' }}</td>
                        <td class="center">{{ $course->credits ?? 3 }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="center">No courses found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="footer-section">
            <div class="footer-signatures">
                <div class="signature-block"><div class="signature-line">Prepared By</div></div>
                <div class="signature-block"><div class="signature-line">Authorized Signature</div></div>
            </div>
            <div class="footer-note">This is a computer-generated courses list.</div>
        </div>
    </div>
</body>
</html>
