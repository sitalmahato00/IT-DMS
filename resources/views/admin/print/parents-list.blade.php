<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parents List - Print</title>
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
        .status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9pt; font-weight: bold; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        tr { page-break-inside: avoid; }
        .footer-section { margin-top: 30px; padding-top: 20px; border-top: 1px solid #000; }
        .footer-signatures { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .signature-block { width: 45%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-size: 10pt; }
        .footer-note { text-align: center; font-size: 9pt; color: #666; font-style: italic; }
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
            <div class="school-name">{{ $college->name ?? 'Manmohan Memorial Polytechnic' }}</div>
            <div class="report-title">PARENT LIST</div>
            <div class="meta-info">
                <div class="meta-item"><span class="meta-label">Academic Year:</span><span>{{ date('Y') }}</span></div>
                <div class="meta-item"><span class="meta-label">Total Parents:</span><span>{{ $parents->count() }}</span></div>
                <div class="meta-item"><span class="meta-label">Print Date:</span><span>{{ now()->format('Y-m-d') }}</span></div>
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">S.N</th>
                        <th style="width: 25%;">Parent Name</th>
                        <th style="width: 15%;">Parent ID</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 15%;">Phone</th>
                        <th style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parents as $parent)
                    <tr>
                        <td class="center numeric">{{ $loop->iteration }}</td>
                        <td>{{ $parent->name }}</td>
                        <td class="center">{{ $parent->parent->parent_code ?? 'P' . str_pad($parent->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $parent->email }}</td>
                        <td>{{ $parent->parent->phone ?? '-' }}</td>
                        <td class="center">
                            @php $status = $parent->parent->status ?? 'active'; @endphp
                            @if($status === 'active')
                                <span class="status status-active">Active</span>
                            @else
                                <span class="status status-inactive">{{ ucfirst($status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="center">No parents found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="footer-section">
            <div class="footer-signatures">
                <div class="signature-block"><div class="signature-line">Prepared By</div></div>
                <div class="signature-block"><div class="signature-line">Authorized Signature</div></div>
            </div>
            <div class="footer-note">This is a computer-generated parent list.</div>
        </div>
    </div>
</body>
</html>

