<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .student-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 4px solid #b45309;
        }
        .student-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #b45309;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .present {
            color: green;
            font-weight: bold;
        }
        .absent {
            color: red;
            font-weight: bold;
        }
        .stats {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 4px;
        }
        .stats p {
            margin: 5px 0;
            font-size: 14px;
        }
        .stats .percentage {
            font-size: 24px;
            font-weight: bold;
            color: #4caf50;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="student-info">
        <p><strong>Student Name:</strong> {{ $child->user?->name ?? 'N/A' }}</p>
        <p><strong>Roll Number:</strong> {{ $child->roll_no ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $child->user?->email ?? 'N/A' }}</p>
        <p><strong>Semester:</strong> {{ $child->semester ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Subject</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendance as $record)
                <tr>
                    <td>{{ $record->attendance_date?->format('Y-m-d') ?? 'N/A' }}</td>
                    <td>{{ $record->subject?->name ?? 'N/A' }}</td>
                    <td>
                        <span class="{{ $record->is_present ? 'present' : 'absent' }}">
                            {{ $record->is_present ? 'Present' : 'Absent' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No attendance records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="stats">
        <p>Overall Attendance Percentage</p>
        <p class="percentage">{{ round($attendancePercentage, 1) }}%</p>
    </div>

    <footer>
        <p>This is an automated report from Manmohan Memorial Polytechnic. For more information, please contact the administration.</p>
    </footer>
</body>
</html>

