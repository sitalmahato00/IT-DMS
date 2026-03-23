<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers List - Print</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* A4 Page Setup */
        @page {
            size: A4 portrait;
            margin: 1in;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        /* Print Button (Screen Only) */
        @media screen {
            body {
                background: #f0f0f0;
                padding: 20px;
            }
            
            .print-container {
                max-width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
                padding: 20mm;
                box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            }
            
            .print-btn {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 24px;
                background: #2563eb;
                color: #fff;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                z-index: 9999;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }
            
            .print-btn:hover {
                background: #1d4ed8;
            }
        }

        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-btn {
                display: none !important;
            }
        }

        /* Header Section */
        .header-section {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .school-logo {
            margin-bottom: 10px;
        }

        .school-logo img {
            max-height: 60px;
        }

        .logo-placeholder {
            font-size: 48px;
        }

        .school-name {
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
            padding: 8px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* Meta Information */
        .meta-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 15px 0;
            font-size: 10pt;
        }

        .meta-item {
            display: flex;
            gap: 5px;
        }

        .meta-label {
            font-weight: bold;
        }

        /* Table */
        .table-container {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
            font-size: 10pt;
        }

        th {
            background: #e5e5e5 !important;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
            letter-spacing: 0.5px;
        }

        td {
            text-align: left;
        }

        td.numeric {
            text-align: right;
        }

        td.center {
            text-align: center;
        }

        /* Status Badges */
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        /* Prevent page break inside rows */
        tr {
            page-break-inside: avoid;
        }

        /* Footer Section */
        .footer-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #000;
        }

        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .signature-block {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10pt;
        }

        .footer-note {
            text-align: center;
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Print Button (Screen Only) -->
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Print Document
    </button>

    <div class="print-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="school-logo">
                @if($college && $college->logo_path)
                    <img src="{{ asset('storage/'.$college->logo_path) }}" alt="School Logo">
                @else
                    <div class="logo-placeholder">🏫</div>
                @endif
            </div>
            
            <div class="school-name">
                {{ $college->name ?? 'IT-DMS COLLEGE' }}
            </div>
            
            <div class="report-title">
                TEACHER LIST
            </div>
            
            <!-- Meta Information -->
            <div class="meta-info">
                <div class="meta-item">
                    <span class="meta-label">Academic Year:</span>
                    <span>{{ date('Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Total Teachers:</span>
                    <span>{{ $teachers->count() }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Print Date:</span>
                    <span>{{ now()->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <!-- Teacher Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">S.N</th>
                        <th style="width: 25%;">Teacher Name</th>
                        <th style="width: 15%;">Teacher ID</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 15%;">Phone</th>
                        <th style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                    <tr>
                        <td class="center numeric">{{ $loop->iteration }}</td>
                        <td>{{ $teacher->name }}</td>
                        <td class="center">{{ $teacher->teacher->teacher_code ?? '-' }}</td>
                        <td>{{ $teacher->email }}</td>
                        <td>{{ $teacher->teacher->phone ?? '-' }}</td>
                        <td class="center">
                            @php $status = $teacher->teacher->status ?? 'active'; @endphp
                            @if($status === 'active')
                                <span class="status status-active">Active</span>
                            @else
                                <span class="status status-inactive">{{ ucfirst($status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="center">No teachers found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <div class="footer-signatures">
                <div class="signature-block">
                    <div class="signature-line">Prepared By</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">Authorized Signature</div>
                </div>
            </div>
            
            <div class="footer-note">
                This is a computer-generated teacher list.
            </div>
        </div>
    </div>
</body>
</html>

