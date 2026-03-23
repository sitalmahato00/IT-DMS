<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @page { size: A4; margin: 1cm; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            .print-container { box-shadow: none; padding: 0; margin: 0; max-width: 100%; }
        }
        
        @media screen {
            body { background: #f0f0f0; padding: 20px; padding-left: 260px; }
            .print-container { max-width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; padding: 20mm; box-shadow: 0 4px 15px rgba(0,0,0,0.15); border-radius: 10px; }
            .print-btn { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #DC2626; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
            .print-btn:hover { background: #b91c1c; }
        }
        
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11pt; color: #333; background: #fff; }
        
        /* Sidebar */
        .sidebar { position: fixed; left: 10px; top: 10px; z-index: 1000; width: 220px; }
        .sidebar-box { background: white; border: 2px solid #DC2626; border-radius: 8px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
        
        /* Header */
        .header-section { text-align: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid #DC2626; }
        .school-logo { margin-bottom: 10px; }
        .school-logo img { max-height: 60px; max-width: 60px; border-radius: 50%; }
        .logo-placeholder { font-size: 48px; line-height: 1; }
        .school-name { font-size: 18pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; color: #DC2626; }
        .report-title { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 10px 0; padding: 8px 0; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; }
        
        /* Student Photo */
        .student-photo-section { text-align: center; margin-bottom: 20px; }
        .student-photo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #DC2626; }
        .photo-placeholder { width: 150px; height: 150px; border-radius: 50%; background: #DC2626; color: white; font-size: 60px; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
        .student-name { font-size: 20pt; font-weight: bold; color: #DC2626; margin-top: 10px; }
        .student-id { font-size: 12pt; color: #666; }
        
        /* Info */
        .info-row { display: flex; border-bottom: 1px solid #eee; padding: 10px 0; }
        .info-label { font-weight: bold; width: 150px; color: #333; }
        .info-value { flex: 1; color: #666; }
        .section-title { border-left: 4px solid #DC2626; padding-left: 10px; margin: 20px 0 15px 0; font-size: 14px; font-weight: bold; color: #1f2937; }
        
        /* Status badges */
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: #e5e7eb; color: #374151; }
        
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; color: #6b7280; font-size: 9pt; }
    </style>
</head>
<body>
    <!-- Print Controls - Sidebar -->
    <div class="no-print sidebar">
        <div class="sidebar-box">
            <h4 style="margin: 0 0 15px 0; color: #DC2626; font-size: 14px; font-weight: 700; text-align: center; padding-bottom: 10px; border-bottom: 1px solid #fecaca;">🖨️ Print Options</h4>
            <button onclick="window.print()" style="padding: 12px 20px; background: #DC2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; width: 100%; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);">
                🖨️ Print Report
            </button>
            <a href="{{ route('admin.reports') }}" style="display: block; margin-top: 10px; padding: 10px; text-align: center; background: #f3f4f6; color: #333; border-radius: 6px; text-decoration: none; font-size: 13px;">
                ← Back to Reports
            </a>
        </div>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="header-section">
            <div class="school-logo">
                @if(!empty($college) && !empty($college->logo_path))
                    <img src="{{ asset($college->logo_path) }}" alt="College Logo">
                @else
                    <div class="logo-placeholder">🎓</div>
                @endif
            </div>
            <div class="school-name">{{ !empty($college) ? $college->name : 'IT-DMS COLLEGE' }}</div>
            <div class="report-title">Student Profile</div>
        </div>
        
        <!-- Student Photo and Basic Info -->
        <div class="student-photo-section">
            @if($student->photo_base64)
                <img src="{{ $student->photo_base64 }}" alt="{{ $student->name }}" class="student-photo">
            @elseif($student->student && $student->student->profile_photo_path)
                <img src="{{ asset($student->student->profile_photo_path) }}" alt="{{ $student->name }}" class="student-photo">
            @else
                <div class="photo-placeholder">{{ substr($student->name ?? 'S', 0, 1) }}</div>
            @endif
            <div class="student-name">{{ $student->name }}</div>
            <div class="student-id">Student ID: {{ $student->student->student_id ?? $student->id }}</div>
        </div>
        
        <!-- Personal Information -->
        <div class="section-title">📋 Personal Information</div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $student->email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span class="info-value">{{ $student->phone ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address:</span>
            <span class="info-value">{{ $student->address ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date of Birth:</span>
            <span class="info-value">{{ $student->date_of_birth ?? 'N/A' }}</span>
        </div>
        
        <!-- Academic Information -->
        <div class="section-title">🎓 Academic Information</div>
        <div class="info-row">
            <span class="info-label">Program:</span>
            <span class="info-value">{{ $student->student->program ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Semester:</span>
            <span class="info-value">{{ $student->student->semester ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                @if($student->student && $student->student->is_alumni)
                    <span class="badge badge-info">Alumni</span>
                @elseif($student->status === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-secondary">Inactive</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Department:</span>
            <span class="info-value">{{ $student->department ?? 'IT Department' }}</span>
        </div>
        
        <!-- Parent Information -->
        @if($student->student && $student->student->parent_name)
        <div class="section-title">👨‍👩‍👧 Parent Information</div>
        <div class="info-row">
            <span class="info-label">Father's Name:</span>
            <span class="info-value">{{ $student->student->father_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mother's Name:</span>
            <span class="info-value">{{ $student->student->mother_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Parent Phone:</span>
            <span class="info-value">{{ $student->student->parent_phone ?? 'N/A' }}</span>
        </div>
        @endif
        
        <!-- Performance Summary -->
        <div class="section-title">📊 Performance Summary</div>
        <div class="info-row">
            <span class="info-label">Average Marks:</span>
            <span class="info-value" style="font-weight: bold; color: #DC2626;">{{ round($student->avg_percentage ?? 0, 1) }}%</span>
        </div>
        <div class="info-row">
            <span class="info-label">Attendance:</span>
            <span class="info-value" style="font-weight: bold; color: #DC2626;">{{ $student->attendance_percentage ?? 0 }}%</span>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>IT-DMS College ERP | Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
