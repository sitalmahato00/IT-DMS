<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Print Attendance</title>
  <style>
    :root {
      --brand: #e11d48;
      --text: #0f172a;
      --muted: #475569;
      --line: #e2e8f0;
      --bg: #f1f5f9;
    }

    html, body { height: 100%; }
    body { margin: 0; font-family: Arial, sans-serif; color: var(--text); background: var(--bg); }

    .page {
      width: 210mm;
      min-height: 297mm;
      margin: 16px auto;
      background: #fff;
      border: 1px solid rgba(226, 232, 240, 0.9);
      border-radius: 10px;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
      padding: 20mm;
      box-sizing: border-box;
    }

    .print-topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 10px;
    }
    .print-topbar .title { font-weight: 700; color: #111827; }

    .print-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      padding-bottom: 10px;
      border-bottom: 3px solid var(--brand);
    }

    .print-header .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
    }

    .print-header img { height: 72px; width: auto; object-fit: contain; }
    .college { line-height: 1.1; min-width: 0; }
    .college-name { font-weight: 800; font-size: 20px; }
    .college-address { font-size: 12px; color: var(--muted); }

    h1 {
      font-family: Georgia, serif;
      font-size: 24px;
      margin: 14px 0 8px;
      line-height: 1.15;
    }

    .header-details {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 8px;
      border: 1px solid var(--line);
      border-left: 4px solid var(--brand);
      border-radius: 8px;
      background: #fff1f2;
      padding: 10px 12px;
      margin-top: 6px;
      box-sizing: border-box;
    }
    .header-details .label {
      display: block;
      font-size: 10px;
      letter-spacing: 0.02em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 2px;
    }
    .header-details .value {
      display: block;
      font-size: 12px;
      font-weight: 700;
      color: #111827;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 12px; }
    th, td { border: 1px solid #000; padding: 7px 8px; }
    thead th { background: var(--brand); color: #fff; font-weight: 700; }
    tbody tr:nth-child(even) { background: #f8fafc; }

    .badge {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      border: 1px solid transparent;
      text-transform: capitalize;
    }
    .badge.present { background: #dcfce7; color: #166534; border-color: #86efac; }
    .badge.absent { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
    .badge.leave { background: #ede9fe; color: #5b21b6; border-color: #c4b5fd; }

    @media (max-width: 820px) {
      .page { width: 100%; min-height: auto; margin: 0; border-radius: 0; box-shadow: none; padding: 16px; }
      .header-details { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @page { size: A4; margin: 20mm; }
    @media print {
      html, body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
      body { background: #fff; color: #000; }
      .page { width: auto; min-height: auto; margin: 0; border: none; border-radius: 0; box-shadow: none; padding: 0; }
      .print-topbar { display: none; }
      h1 { font-size: 22px; margin: 10px 0 6px; }
    }
  </style>
</head>
<body>
  <div class="page">
    <div class="print-topbar">
      <div>{{ \Carbon\Carbon::now()->format('d/m/Y, H:i') }}</div>
      <div class="title">Print Attendance</div>
      <div></div>
    </div>

    <div class="print-header">
      <div class="brand">
        <img src="{{ $collegeLogo }}" alt="College Logo" />
        <div class="college">
          <div class="college-name">{{ $collegeName ?? 'College Name' }}</div>
          <div class="college-address">{{ $collegeAddress ?? '' }}</div>
        </div>
      </div>
      <div class="print-date" style="text-align:right; font-size:12px; color: var(--muted);">
        Print Date: {{ \Carbon\Carbon::now()->toDateString() }}
      </div>
    </div>

    <h1>Attendance - {{ $subject_code ?? '' }} {{ $subject_name ?? '' }} - {{ $date ?? '' }}</h1>

    <div class="header-details">
      <div class="item">
        <span class="label">Semester</span>
        <span class="value">{{ $subject_semester ?? '-' }}</span>
      </div>
      <div class="item">
        <span class="label">Subject</span>
        <span class="value">{{ $subject_code ?? '' }} - {{ $subject_name ?? '' }}</span>
      </div>
      <div class="item">
        <span class="label">Date (BS)</span>
        <span class="value">{{ $date_bs ?? '-' }}</span>
      </div>
      <div class="item">
        <span class="label">Marked by</span>
        <span class="value">{{ $marked_by ?? '-' }}</span>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width: 28%;">Name</th>
          <th>Email</th>
          <th style="width: 14%;">Roll No</th>
          <th style="width: 10%;">Semester</th>
          <th style="width: 12%;">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($students as $s)
        @php($status = strtolower(trim($s->status ?? 'present')))
        <tr>
          <td>{{ $s->name }}</td>
          <td>{{ $s->email }}</td>
          <td>{{ $s->roll_no ?? '' }}</td>
          <td>{{ $s->semester ?? '' }}</td>
          <td>
            <span class="badge {{ in_array($status, ['present','absent','leave']) ? $status : 'present' }}">
              {{ $s->status ?? 'present' }}
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</body>
</html>
