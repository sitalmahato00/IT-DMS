<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .print-header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #1e3a8a;
            margin-bottom: 20px;
        }
        
        .print-header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        
        .print-header p {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
        }
        
        .print-info {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 10px;
            color: #666;
            margin-bottom: 15px;
            padding: 0 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        
        th {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .print-footer {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            th {
                background-color: #1e3a8a !important;
                color: white !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h1>{{ $college?->name ?? 'Manmohan Memorial Polytechnic' }}</h1>
        <p>{{ $college?->address ?? 'Department of Computer Science' }}</p>
        <p>Student List</p>
    </div>
    
    <div class="print-info">
        <div>
            <p>Date: {{ now()->format('d-m-Y H:i') }}</p>
            <p>Total Students: {{ count($students) }}</p>
        </div>
        <div>
            @if(!empty($filters['semester']))
                <p>Semester: {{ $filters['semester'] }}</p>
            @endif
            @if(!empty($filters['subject']))
                <p>Subject: {{ $filters['subject'] }}</p>
            @endif
            @if(!empty($filters['search']))
                <p>Search: {{ $filters['search'] }}</p>
            @endif
        </div>
    </div>
    
    @if(count($students) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%">S.N.</th>
                <th style="width: 20%">Name</th>
                <th style="width: 18%">Email</th>
                <th style="width: 10%">Roll No</th>
                <th style="width: 12%">Reg. No</th>
                <th style="width: 12%">Phone</th>
                <th style="width: 8%">Gender</th>
                <th style="width: 5%">Sem</th>
                <th style="width: 10%">Academic Year</th>
                <th style="width: 10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->name ?? 'N/A' }}</td>
                <td>{{ $student->email ?? 'N/A' }}</td>
                <td>{{ $student->roll_no ?? 'N/A' }}</td>
                <td>{{ $student->registration_number ?? 'N/A' }}</td>
                <td>{{ $student->phone ?? 'N/A' }}</td>
                <td>{{ !empty($student->gender) ? ucfirst($student->gender) : 'N/A' }}</td>
                <td>{{ $student->semester ?? 'N/A' }}</td>
                <td>{{ $student->academic_year ?? 'N/A' }}</td>
                <td>
                    @if(($student->status ?? '') === 'active')
                        <span class="status-badge status-active">Active</span>
                    @elseif(($student->status ?? '') === 'inactive')
                        <span class="status-badge status-inactive">Inactive</span>
                    @else
                        <span class="status-badge status-pending">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No students found.</p>
    </div>
    @endif
    
    <div class="print-footer">
        <p>Generated by Manmohan Memorial Polytechnic | {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>

