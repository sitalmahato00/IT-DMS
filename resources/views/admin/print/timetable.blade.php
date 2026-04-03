<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Admin Timetable') }} - {{ __('Semester') }} {{ $semester }}</title>
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
<body>
    @php
        $paperClass = 'routine-paper--compact';
        $sheetTitle = __('Official Timetable');
        $sheetHeading = __('Semester') . ' ' . $semester . (filled($section) ? ' / ' . __('Section') . ' ' . $section : '');
        $institutionName = $college?->name ?? 'IT-DMS';
        $departmentLine = $college?->short_name ?? __('Department');
        $metaItems = [
            ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
            ['label' => __('Academic Year'), 'value' => now()->format('Y')],
        ];
        $summaryItems = [
            ['label' => __('Role'), 'value' => __('Administrator')],
            ['label' => __('Semester'), 'value' => $semester],
            ['label' => __('Section'), 'value' => $section ?: __('All')],
            ['label' => __('Slots'), 'value' => collect($slots ?? [])->count()],
        ];
        $footerLeft = collect($slots ?? [])->count() . ' ' . __('slots');
        $showSlotSection = blank($section);
    @endphp

    <div class="routine-print-shell">
        <div class="routine-print-actions">
            <a href="{{ route('admin.timetable', array_filter(['semester' => $semester, 'section' => $section], fn ($value) => filled($value))) }}">
                {{ __('Back to timetable') }}
            </a>
            <button type="button" onclick="window.print()">{{ __('Print') }}</button>
        </div>

        @include('shared.timetable.partials.routine-sheet')
    </div>
</body>
</html>
