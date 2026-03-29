<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Teacher Routine') }} - {{ auth()->user()?->name ?? __('Teacher') }}</title>
    @include('shared.timetable.partials.routine-styles')
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
<body>
    @php
        $paperClass = 'routine-paper--compact';
        $sheetTitle = __('Teacher Routine');
        $sheetHeading = $selectedSemester
            ? __('Semester') . ' ' . $selectedSemester
            : __('All Assigned Semesters');
        $institutionName = $college?->name ?? 'IT-DMS';
        $departmentLine = $college?->short_name ?? __('Department');
        $metaItems = [
            ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
            ['label' => __('Academic Year'), 'value' => now()->format('Y')],
        ];
        $summaryItems = [
            ['label' => __('Teacher'), 'value' => auth()->user()?->name ?? __('Teacher')],
            ['label' => __('Subjects'), 'value' => $totalSubjects],
            ['label' => __('Slots'), 'value' => $totalSlots],
            ['label' => __('Semester Filter'), 'value' => $selectedSemester ?: __('All')],
        ];
        $footerLeft = collect($subjects ?? [])->count() . ' ' . __('subjects');
    @endphp

    <div class="routine-print-shell">
        <div class="routine-print-actions">
            <a href="{{ route('teacher.timetable', array_filter(['semester' => $selectedSemester], fn ($value) => filled($value))) }}">
                {{ __('Back to timetable') }}
            </a>
            <button type="button" onclick="window.print()">{{ __('Print') }}</button>
        </div>

        @include('shared.timetable.partials.routine-sheet')
    </div>
</body>
</html>
