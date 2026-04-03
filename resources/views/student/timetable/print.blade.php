<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Class Routine') }} - {{ $student->user->name ?? __('Student') }}</title>
    @include('shared.timetable.partials.routine-styles')
    <script>
        try {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        } catch (error) {}
    </script>
    <style>
        body {
            margin: 0;
            background: #eef2f7;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
        }

        .routine-print-shell {
            max-width: 1320px;
            margin: 0 auto;
            padding: 12px;
        }

        .routine-print-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .routine-print-actions button,
        .routine-print-actions a {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .routine-print-actions button:hover,
        .routine-print-actions a:hover {
            background: #f8fafc;
        }

        .routine-preview-body .routine-paper {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
        }

        html.dark body {
            background: #020817;
            color: #e2e8f0;
        }

        html.dark .routine-print-actions button,
        html.dark .routine-print-actions a {
            border-color: #334155;
            background: #0f172a;
            color: #e2e8f0;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .routine-print-shell {
                max-width: none;
                padding: 0;
            }

            .routine-print-actions {
                display: none;
            }

            @page {
                size: A4 landscape;
                margin: 6mm;
            }
        }
    </style>
</head>
<body class="routine-preview-body">
    <div class="routine-print-shell">
        <div class="routine-print-actions">
            <a href="{{ route('student.timetable', array_filter(['semester' => $selectedSemester, 'section' => $selectedSection], fn ($value) => filled($value))) }}">
                {{ __('Back to routine') }}
            </a>
            <button type="button" onclick="window.print()">{{ __('Print') }}</button>
        </div>

        @php
            $paperClass = 'routine-paper--compact';
            $sheetTitle = __('Class Routine');
            $sheetHeading = __('Semester') . ' ' . ($displaySemester ?: ($selectedSemester ?: __('N/A')))
                . (filled($displaySection ?: $selectedSection) ? ' / ' . __('Section') . ' ' . ($displaySection ?: $selectedSection) : '');
            $institutionName = $college?->name ?? 'IT-DMS';
            $departmentLine = $student->department ?: ($college?->short_name ?? __('Department'));
            $metaItems = [
                ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
                ['label' => __('Academic Year'), 'value' => $student->academic_year ?: now()->format('Y')],
            ];
            $summaryItems = [
                ['label' => __('Student'), 'value' => $student->user->name ?? auth()->user()?->name ?? __('Student')],
                ['label' => __('Roll No'), 'value' => $student->roll_no ?? __('N/A')],
                ['label' => __('Semester'), 'value' => $displaySemester ?: ($selectedSemester ?: __('N/A'))],
                ['label' => __('Section'), 'value' => $displaySection ?: ($selectedSection ?: __('All'))],
            ];
            $footerLeft = collect($subjects ?? [])->count() . ' ' . __('subjects');
            $showSlotSection = blank($displaySection ?: $selectedSection);
        @endphp
        @include('shared.timetable.partials.routine-sheet')
    </div>
</body>
</html>
