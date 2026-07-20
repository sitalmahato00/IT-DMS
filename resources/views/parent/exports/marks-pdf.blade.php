<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Marks Report</title>
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
        .section-title {
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            border-left: 4px solid #b45309;
            padding-left: 10px;
        }
        .performance-grid {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        .performance-card {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .performance-card h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .performance-card p {
            margin: 5px 0;
            font-size: 13px;
        }
        .performance-card .average {
            font-size: 20px;
            font-weight: bold;
            color: #b45309;
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
        <h1>Examination Results Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="student-info">
        <p><strong>Student Name:</strong> {{ $child->user?->name ?? 'N/A' }}</p>
        <p><strong>Roll Number:</strong> {{ $child->roll_no ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $child->user?->email ?? 'N/A' }}</p>
        <p><strong>Semester:</strong> {{ $child->semester ?? 'N/A' }}</p>
    </div>

    @if(!$subjectPerformance->isEmpty())
        <div class="section-title">Subject-Wise Performance</div>
        <div class="performance-grid">
            @foreach($subjectPerformance as $performance)
                <div class="performance-card">
                    <h4>{{ $performance->subject?->name ?? 'N/A' }}</h4>
                    <p><strong>Average Marks:</strong> <span class="average">{{ round($performance->avg_marks, 1) }}</span></p>
                    <p><strong>Total Exams:</strong> {{ $performance->total_exams }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="section-title">All Examination Marks</div>
    <table>
        <thead>
            <tr>
                <th>Exam</th>
                <th>Subject</th>
                <th>Obtained Marks</th>
                <th>Total Marks</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @forelse($marks as $mark)
                <tr>
                    <td>{{ $mark->exam?->name ?? 'N/A' }}</td>
                    <td>{{ $mark->subject?->name ?? 'N/A' }}</td>
                    <td>{{ $mark->obtained_marks ?? 'N/A' }}</td>
                    <td>{{ $mark->total_marks ?? 'N/A' }}</td>
                    <td>{{ round($mark->obtained_marks / ($mark->total_marks ?: 1) * 100, 1) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No examination records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        <p>This is an automated report from Manmohan Memorial Polytechnic. For more information, please contact the administration.</p>
    </footer>
</body>
</html>

