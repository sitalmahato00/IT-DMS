<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Parent Summary') }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 0; background: #f8fafc; }
        .page { max-width: 1100px; margin: 0 auto; padding: 32px; }
        .toolbar { display: flex; justify-content: flex-end; margin-bottom: 16px; }
        .toolbar button { background: #dc2626; color: #fff; border: 0; border-radius: 8px; padding: 10px 16px; cursor: pointer; font-weight: 600; }
        .hero { background: linear-gradient(135deg, #dc2626, #991b1b); color: #fff; border-radius: 18px; padding: 24px; }
        .hero h1 { margin: 0 0 8px; font-size: 28px; }
        .hero p { margin: 0; color: #fecaca; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 20px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 18px; }
        .card h2 { margin: 0 0 8px; font-size: 18px; }
        .muted { color: #6b7280; font-size: 13px; }
        .value { font-size: 28px; font-weight: 700; margin-top: 10px; }
        .section { margin-top: 24px; }
        .section h3 { margin: 0 0 12px; font-size: 20px; }
        .child-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .subject-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 16px; overflow: hidden; }
        .subject-table th, .subject-table td { border-bottom: 1px solid #e5e7eb; padding: 12px 14px; text-align: left; font-size: 13px; vertical-align: top; }
        .subject-table th { background: #f8fafc; text-transform: uppercase; letter-spacing: 0.06em; font-size: 11px; color: #6b7280; }
        .pill { display: inline-block; border-radius: 999px; padding: 4px 10px; font-size: 11px; font-weight: 700; }
        .pill-pass { background: #dcfce7; color: #166534; }
        .pill-fail { background: #fee2e2; color: #991b1b; }
        .pill-pending { background: #fef3c7; color: #92400e; }
        .notice { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 16px; margin-bottom: 12px; }
        .notice strong { display: block; margin-bottom: 6px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { padding: 0; max-width: none; }
            .card, .notice, .subject-table { box-shadow: none; }
        }
        @media (max-width: 900px) {
            .stats, .child-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <button type="button" onclick="window.print()">{{ __('Print / Save as PDF') }}</button>
        </div>

        <div class="hero">
            <h1>{{ __('Parent Summary Report') }}</h1>
            <p>{{ __('A printable overview of linked children, academic status, attendance, notices, and support contacts.') }}</p>
        </div>

        <div class="stats">
            <div class="card">
                <div class="muted">{{ __('Parent') }}</div>
                <div class="value" style="font-size:22px;">{{ $parentUser->name ?? __('Parent') }}</div>
            </div>
            <div class="card">
                <div class="muted">{{ __('Children') }}</div>
                <div class="value">{{ $childrenCount }}</div>
            </div>
            <div class="card">
                <div class="muted">{{ __('Attendance') }}</div>
                <div class="value">{{ $overallAttendance }}%</div>
            </div>
            <div class="card">
                <div class="muted">{{ __('Average Score') }}</div>
                <div class="value">{{ $overallScore !== null ? $overallScore . '%' : '—' }}</div>
            </div>
        </div>

        <div class="section">
            <h3>{{ __('Children Overview') }}</h3>
            <div class="child-grid">
                @foreach($children as $child)
                    <div class="card">
                        <h2>{{ $child['name'] }}</h2>
                        <p class="muted">{{ __('Roll No: :roll | Semester :semester', ['roll' => $child['roll_no'] ?: '—', 'semester' => $child['semester'] ?: '—']) }}</p>
                        <div class="stats" style="grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 16px;">
                            <div>
                                <div class="muted">{{ __('Attendance') }}</div>
                                <div class="value" style="font-size:22px;">{{ $child['attendance_percentage'] }}%</div>
                            </div>
                            <div>
                                <div class="muted">{{ __('Score') }}</div>
                                <div class="value" style="font-size:22px;">{{ $child['overall_percentage'] !== null ? $child['overall_percentage'] . '%' : '—' }}</div>
                            </div>
                        </div>
                        <p class="muted" style="margin-top: 14px;">{{ __('Subjects: :count | Passed: :passed | Pending: :pending', ['count' => $child['subject_count'], 'passed' => $child['passed_subjects'], 'pending' => $child['pending_subjects']]) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="section">
            <h3>{{ __('Subject Performance') }}</h3>
            <table class="subject-table">
                <thead>
                    <tr>
                        <th>{{ __('Student') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Teacher') }}</th>
                        <th>{{ __('Attendance') }}</th>
                        <th>{{ __('Result') }}</th>
                        <th>{{ __('Next Exam') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($children as $child)
                        @foreach($child['subjects'] as $subject)
                            <tr>
                                <td>{{ $child['name'] }}</td>
                                <td>
                                    <strong>{{ $subject['name'] }}</strong>
                                    <div class="muted">{{ $subject['code'] }}</div>
                                </td>
                                <td>{{ $subject['teacher_name'] }}</td>
                                <td>{{ $subject['attendance_percentage'] }}%</td>
                                <td>
                                    @php
                                        $statusClass = $subject['status'] === 'pass' ? 'pill-pass' : ($subject['status'] === 'fail' ? 'pill-fail' : 'pill-pending');
                                    @endphp
                                    <span class="pill {{ $statusClass }}">{{ $subject['status_label'] }}</span>
                                    <div class="muted" style="margin-top: 6px;">{{ $subject['percentage'] !== null ? $subject['percentage'] . '%' : __('Pending') }}</div>
                                </td>
                                <td>{{ $subject['next_exam']['date_label'] ?? __('Not scheduled') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>{{ __('Recent Notices') }}</h3>
            @forelse($recentNotices->take(5) as $notice)
                <div class="notice">
                    <strong>{{ $notice->localized_title }}</strong>
                    <div class="muted">{{ $notice->formatted_date }} • {{ $notice->localized_priority_label }}</div>
                    <p style="margin: 10px 0 0;">{{ \Illuminate\Support\Str::limit(strip_tags($notice->localized_message), 200) }}</p>
                </div>
            @empty
                <div class="card">
                    <p class="muted">{{ __('No notices are available right now.') }}</p>
                </div>
            @endforelse
        </div>

        <div class="section">
            <h3>{{ __('Support Contacts') }}</h3>
            <div class="child-grid">
                <div class="card">
                    <h2>{{ __('Department') }}</h2>
                    <p class="muted">{{ $department?->name ?? __('IT Department') }}</p>
                    <p>{{ $department?->email ?: __('Email not configured') }}</p>
                    <p>{{ $department?->phone ?: __('Phone not configured') }}</p>
                    <p>{{ $department?->address ?: __('Address not configured') }}</p>
                </div>
                <div class="card">
                    <h2>{{ __('Portal Support Notes') }}</h2>
                    <p>{{ __('Use the parent dashboard weekly, monitor attendance below 75%, review notices promptly, and export records whenever offline discussion is needed.') }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

