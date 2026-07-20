<style>
    .routine-page {
        color: #0f172a;
    }

    .routine-paper {
        width: 100%;
        margin: 0 auto;
        border: 1px solid #0f172a;
        background: #ffffff;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        page-break-inside: avoid;
    }

    .routine-paper__header {
        display: grid;
        grid-template-columns: 92px 1fr auto;
        gap: 18px;
        align-items: center;
        padding: 22px 24px 16px;
        border-bottom: 1px solid #0f172a;
    }

    .routine-paper__logo {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 92px;
        height: 92px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
    }

    .routine-paper__logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .routine-paper__titles {
        text-align: center;
    }

    .routine-paper__titles h2 {
        margin: 6px 0 0;
        font-size: 1.2rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    .routine-paper__institution,
    .routine-paper__department {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.14em;
    }

    .routine-paper__institution {
        font-size: 1rem;
    }

    .routine-paper__department {
        margin-top: 4px;
        font-size: 0.82rem;
        color: #475569;
    }

    .routine-paper__meta-top {
        text-align: right;
        font-size: 0.8rem;
        line-height: 1.6;
        color: #334155;
    }

    .routine-paper__meta-top span,
    .routine-paper__summary span,
    .routine-paper__footer span {
        font-weight: 700;
    }

    .routine-paper__summary {
        display: grid;
        gap: 0;
        border-bottom: 1px solid #0f172a;
    }

    .routine-paper__summary div {
        padding: 12px 16px;
        border-right: 1px solid #0f172a;
        font-size: 0.86rem;
        line-height: 1.5;
    }

    .routine-paper__summary div:last-child {
        border-right: 0;
    }

    .routine-paper__summary strong {
        display: block;
        margin-top: 4px;
        font-size: 1rem;
        color: #111827;
    }

    .routine-table-wrap {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    .routine-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .routine-table th,
    .routine-table td {
        border: 1px solid #0f172a;
        padding: 8px 10px;
        vertical-align: middle;
        font-size: 0.84rem;
    }

    .routine-table thead th {
        background: #f8fafc;
        text-align: center;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .routine-table__head-day {
        width: 120px;
    }

    .routine-table__head-period {
        width: 124px;
    }

    .routine-table__head-title {
        font-size: 0.9rem;
    }

    .routine-table__day {
        background: #f8fafc;
        text-align: center;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .routine-table__period {
        background: #f8fafc;
        text-align: center;
        font-weight: 700;
        white-space: nowrap;
    }

    .routine-table__subject-cell,
    .routine-table__stack-cell {
        padding: 6px 8px;
    }

    .routine-table__stack-cell {
        text-align: center;
    }

    .routine-slot,
    .routine-stack-item {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 7px 8px;
        background: #ffffff;
    }

    .routine-slot + .routine-slot,
    .routine-stack-item + .routine-stack-item {
        margin-top: 6px;
    }

    .routine-slot__title {
        font-weight: 800;
        color: #111827;
    }

    .routine-slot__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
        font-size: 0.72rem;
        color: #475569;
    }

    .routine-slot__meta span {
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.72);
        padding: 2px 7px;
    }

    .routine-slot__note {
        margin-top: 6px;
        font-size: 0.72rem;
        color: #334155;
    }

    .routine-stack-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .routine-stack-item__name {
        font-weight: 700;
        color: #111827;
    }

    .routine-stack-item__meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
        font-size: 0.7rem;
        color: #64748b;
    }

    .routine-stack-item__meta span {
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #f8fafc;
        padding: 2px 7px;
    }

    .routine-slot--theory {
        background: #eff6ff;
        border-color: #93c5fd;
    }

    .routine-slot--practical {
        background: #ecfdf5;
        border-color: #86efac;
    }

    .routine-slot--tutorial {
        background: #fffbeb;
        border-color: #fcd34d;
    }

    .routine-slot--elective {
        background: #f5f3ff;
        border-color: #c4b5fd;
    }

    .routine-slot--teacher-focus {
        border-color: #fb7185;
        background: linear-gradient(135deg, #fff1f2, #ffe4e6);
        box-shadow: 0 16px 32px -28px rgba(225, 29, 72, 0.45);
    }

    .routine-slot--teacher-focus .routine-slot__title {
        color: #9f1239;
    }

    .routine-stack-item--teacher-focus {
        border-color: #fda4af;
        background: linear-gradient(135deg, #fff1f2, #fff7f8);
        color: #9f1239;
        font-weight: 700;
    }

    .routine-table__blank,
    .routine-table__empty,
    .routine-table__break {
        text-align: center;
        color: #475569;
    }

    .routine-table__break {
        background: #f8fafc;
        font-style: italic;
        font-weight: 700;
    }

    .routine-paper__footer {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 16px;
        border-top: 1px solid #0f172a;
        font-size: 0.8rem;
        color: #334155;
    }

    .routine-paper--compact .routine-paper__header {
        grid-template-columns: 68px 1fr auto;
        gap: 12px;
        padding: 12px 14px 10px;
    }

    .routine-paper--compact .routine-paper__logo {
        width: 68px;
        height: 68px;
        border-radius: 8px;
    }

    .routine-paper--compact .routine-paper__logo img {
        padding: 5px;
    }

    .routine-paper--compact .routine-paper__institution {
        font-size: 0.84rem;
    }

    .routine-paper--compact .routine-paper__department {
        margin-top: 2px;
        font-size: 0.68rem;
    }

    .routine-paper--compact .routine-paper__titles h2 {
        margin-top: 4px;
        font-size: 0.92rem;
        letter-spacing: 0.1em;
    }

    .routine-paper--compact .routine-paper__meta-top {
        font-size: 0.68rem;
        line-height: 1.35;
    }

    .routine-paper--compact .routine-paper__summary div {
        padding: 7px 10px;
        font-size: 0.7rem;
        line-height: 1.3;
    }

    .routine-paper--compact .routine-paper__summary strong {
        margin-top: 2px;
        font-size: 0.86rem;
    }

    .routine-paper--compact .routine-table th,
    .routine-paper--compact .routine-table td {
        padding: 4px 5px;
        font-size: 0.68rem;
        line-height: 1.15;
    }

    .routine-paper--compact .routine-table thead th {
        letter-spacing: 0.05em;
    }

    .routine-paper--compact .routine-table__head-day {
        width: 90px;
    }

    .routine-paper--compact .routine-table__head-period {
        width: 94px;
    }

    .routine-paper--compact .routine-table__head-title {
        font-size: 0.74rem;
    }

    .routine-paper--compact .routine-table__day {
        vertical-align: top;
        padding-top: 8px;
        font-size: 0.7rem;
    }

    .routine-paper--compact .routine-table__period {
        font-size: 0.68rem;
    }

    .routine-paper--compact .routine-table__subject-cell,
    .routine-paper--compact .routine-table__stack-cell {
        padding: 3px 4px;
    }

    .routine-paper--compact .routine-slot,
    .routine-paper--compact .routine-stack-item {
        border-radius: 5px;
        padding: 4px 5px;
    }

    .routine-paper--compact .routine-slot + .routine-slot,
    .routine-paper--compact .routine-stack-item + .routine-stack-item {
        margin-top: 4px;
    }

    .routine-paper--compact .routine-slot__title {
        font-size: 0.68rem;
        line-height: 1.15;
    }

    .routine-paper--compact .routine-slot__meta {
        gap: 4px;
        margin-top: 3px;
        font-size: 0.58rem;
    }

    .routine-paper--compact .routine-slot__meta span {
        padding: 1px 5px;
    }

    .routine-paper--compact .routine-slot__note {
        margin-top: 4px;
        font-size: 0.58rem;
        line-height: 1.15;
    }

    .routine-paper--compact .routine-stack-item {
        gap: 3px;
    }

    .routine-paper--compact .routine-stack-item__name {
        font-size: 0.66rem;
        line-height: 1.15;
    }

    .routine-paper--compact .routine-stack-item__meta {
        gap: 4px;
        font-size: 0.56rem;
    }

    .routine-paper--compact .routine-stack-item__meta span {
        padding: 1px 5px;
    }

    .routine-paper--compact .routine-paper__footer {
        padding: 8px 10px;
        font-size: 0.66rem;
    }

    @media (max-width: 900px) {
        .routine-paper {
            border-radius: 22px;
            overflow: hidden;
        }

        .routine-paper__header {
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 18px 16px 14px;
            text-align: center;
        }

        .routine-paper__logo,
        .routine-paper__meta-top {
            margin: 0 auto;
        }

        .routine-paper__meta-top {
            text-align: center;
        }

        .routine-paper__summary {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .routine-paper__summary div {
            border-bottom: 1px solid #0f172a;
        }

        .routine-paper__summary div:nth-child(2n) {
            border-right: 0;
        }

        .routine-table {
            min-width: 760px;
        }

        .routine-table__head-day,
        .routine-table__day {
            position: sticky;
            left: 0;
            min-width: 92px;
            background: #f8fafc;
            z-index: 2;
        }

        .routine-table__head-period,
        .routine-table__period {
            position: sticky;
            left: 92px;
            min-width: 104px;
            background: #f8fafc;
            z-index: 1;
        }

        .routine-table thead th {
            position: sticky;
            top: 0;
            z-index: 4;
        }

        .routine-table__head-day,
        .routine-table__head-period {
            z-index: 5;
        }

        .routine-table__subject-cell {
            min-width: 320px;
        }

        .routine-table__stack-cell {
            min-width: 140px;
        }

        .routine-slot,
        .routine-stack-item {
            padding: 6px 7px;
        }

        .routine-slot__meta {
            gap: 4px;
        }
    }

    @media (max-width: 640px) {
        .routine-paper__summary div {
            padding: 10px 12px;
            font-size: 0.78rem;
        }

        .routine-paper__summary strong {
            font-size: 0.92rem;
        }

        .routine-table {
            min-width: 680px;
        }

        .routine-table th,
        .routine-table td {
            padding: 7px 8px;
            font-size: 0.78rem;
        }

        .routine-table__head-day,
        .routine-table__day {
            min-width: 84px;
        }

        .routine-table__head-period,
        .routine-table__period {
            left: 84px;
            min-width: 96px;
        }

        .routine-paper__footer {
            flex-direction: column;
        }
    }

    html.dark .routine-page {
        color: #e2e8f0;
    }

    html.dark .routine-page .bg-white {
        background-color: #0f172a !important;
    }

    html.dark .routine-page .bg-slate-50 {
        background-color: #111c2f !important;
    }

    html.dark .routine-page .bg-red-50 {
        background-color: rgba(225, 29, 72, 0.16) !important;
    }

    html.dark .routine-page .bg-rose-50 {
        background-color: rgba(244, 63, 94, 0.14) !important;
    }

    html.dark .routine-page .bg-amber-50,
    html.dark .routine-page .bg-yellow-50 {
        background-color: rgba(120, 53, 15, 0.22) !important;
    }

    html.dark .routine-page .border-slate-200 {
        border-color: #334155 !important;
    }

    html.dark .routine-page .border-slate-300 {
        border-color: #475569 !important;
    }

    html.dark .routine-page .border-red-200,
    html.dark .routine-page .border-rose-200 {
        border-color: rgba(244, 63, 94, 0.34) !important;
    }

    html.dark .routine-page .border-amber-200,
    html.dark .routine-page .border-yellow-200 {
        border-color: rgba(217, 119, 6, 0.42) !important;
    }

    html.dark .routine-page .text-slate-900 {
        color: #f8fafc !important;
    }

    html.dark .routine-page .text-slate-700 {
        color: #dbe4f0 !important;
    }

    html.dark .routine-page .text-slate-600,
    html.dark .routine-page .text-slate-500 {
        color: #94a3b8 !important;
    }

    html.dark .routine-page .text-red-700,
    html.dark .routine-page .text-rose-700 {
        color: #fda4af !important;
    }

    html.dark .routine-page .text-amber-900,
    html.dark .routine-page .text-yellow-900 {
        color: #fde68a !important;
    }

    html.dark .routine-page .text-amber-800,
    html.dark .routine-page .text-yellow-800 {
        color: #fcd34d !important;
    }

    html.dark .routine-page .shadow-sm {
        box-shadow: 0 24px 48px -38px rgba(2, 6, 23, 0.9) !important;
    }

    html.dark .routine-page .hover\:bg-slate-50:hover {
        background-color: #172033 !important;
    }

    html.dark .routine-page .hover\:border-slate-400:hover {
        border-color: #64748b !important;
    }

    html.dark .routine-paper {
        border-color: #334155;
        background:
            radial-gradient(circle at top right, rgba(244, 63, 94, 0.14), transparent 26%),
            linear-gradient(180deg, #081120, #0f172a);
        box-shadow: 0 28px 56px -34px rgba(2, 6, 23, 0.78);
    }

    html.dark .routine-paper__header,
    html.dark .routine-paper__footer {
        border-color: #334155;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(9, 15, 28, 0.98));
    }

    html.dark .routine-paper__logo {
        border-color: #334155;
        background: rgba(15, 23, 42, 0.95);
    }

    html.dark .routine-paper__titles h2,
    html.dark .routine-paper__institution,
    html.dark .routine-paper__summary strong,
    html.dark .routine-stack-item__name,
    html.dark .routine-slot__title {
        color: #f8fafc;
    }

    html.dark .routine-paper__department,
    html.dark .routine-paper__meta-top,
    html.dark .routine-paper__footer,
    html.dark .routine-slot__meta,
    html.dark .routine-slot__note,
    html.dark .routine-stack-item__meta,
    html.dark .routine-table__blank,
    html.dark .routine-table__empty,
    html.dark .routine-table__break {
        color: #94a3b8;
    }

    html.dark .routine-paper__summary {
        border-color: #334155;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(12, 19, 34, 0.98));
    }

    html.dark .routine-paper__summary div {
        border-color: #334155;
        color: #cbd5e1;
    }

    html.dark .routine-table th,
    html.dark .routine-table td {
        border-color: #334155;
        color: #e2e8f0;
    }

    html.dark .routine-table thead th {
        background: linear-gradient(180deg, #ff174a, #d90429);
        color: #ffffff;
    }

    html.dark .routine-table__day,
    html.dark .routine-table__period {
        background: #111c2f;
        color: #f8fafc;
    }

    html.dark .routine-table__break {
        background: #0f1b2d;
    }

    html.dark .routine-table__blank,
    html.dark .routine-table__empty {
        background: rgba(10, 18, 34, 0.72);
    }

    html.dark .routine-slot,
    html.dark .routine-stack-item {
        border-color: #475569;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(9, 15, 28, 0.98));
        box-shadow: 0 18px 32px -28px rgba(2, 6, 23, 0.7);
    }

    html.dark .routine-slot__meta span,
    html.dark .routine-stack-item__meta span {
        border-color: #475569;
        background: rgba(15, 23, 42, 0.9);
        color: #e2e8f0;
    }

    html.dark .routine-slot--theory {
        background: linear-gradient(180deg, rgba(30, 64, 175, 0.24), rgba(15, 23, 42, 0.98));
        border-color: rgba(96, 165, 250, 0.55);
    }

    html.dark .routine-slot--practical {
        background: linear-gradient(180deg, rgba(22, 163, 74, 0.2), rgba(15, 23, 42, 0.98));
        border-color: rgba(74, 222, 128, 0.45);
    }

    html.dark .routine-slot--tutorial {
        background: linear-gradient(180deg, rgba(202, 138, 4, 0.2), rgba(15, 23, 42, 0.98));
        border-color: rgba(250, 204, 21, 0.44);
    }

    html.dark .routine-slot--elective {
        background: linear-gradient(180deg, rgba(124, 58, 237, 0.2), rgba(15, 23, 42, 0.98));
        border-color: rgba(167, 139, 250, 0.45);
    }

    html.dark .routine-slot--teacher-focus {
        border-color: rgba(251, 113, 133, 0.7);
        background: linear-gradient(135deg, rgba(159, 18, 57, 0.42), rgba(15, 23, 42, 0.98));
        box-shadow: 0 22px 42px -32px rgba(244, 63, 94, 0.58);
    }

    html.dark .routine-slot--teacher-focus .routine-slot__title,
    html.dark .routine-stack-item--teacher-focus .routine-stack-item__name {
        color: #fecdd3;
    }

    html.dark .routine-stack-item--teacher-focus {
        border-color: rgba(251, 113, 133, 0.65);
        background: linear-gradient(135deg, rgba(159, 18, 57, 0.28), rgba(15, 23, 42, 0.98));
        color: #fecdd3;
    }

    @media print {
        .routine-paper {
            box-shadow: none;
        }

        .routine-page {
            color: #000000;
        }

        .routine-paper--compact .routine-table-wrap {
            overflow: visible;
        }

        .routine-table thead th,
        .routine-table__head-day,
        .routine-table__head-period,
        .routine-table__day,
        .routine-table__period {
            position: static;
        }

        .routine-paper--compact .routine-paper__header {
            grid-template-columns: 54px 1fr auto;
            gap: 8px;
            padding: 8px 10px 7px;
        }

        .routine-paper--compact .routine-paper__logo {
            width: 54px;
            height: 54px;
        }

        .routine-paper--compact .routine-paper__institution {
            font-size: 0.72rem;
        }

        .routine-paper--compact .routine-paper__department {
            font-size: 0.58rem;
        }

        .routine-paper--compact .routine-paper__titles h2 {
            font-size: 0.78rem;
        }

        .routine-paper--compact .routine-paper__meta-top {
            font-size: 0.56rem;
            line-height: 1.2;
        }

        .routine-paper--compact .routine-paper__summary div {
            padding: 5px 7px;
            font-size: 0.58rem;
        }

        .routine-paper--compact .routine-paper__summary strong {
            font-size: 0.72rem;
        }

        .routine-paper--compact .routine-table th,
        .routine-paper--compact .routine-table td {
            padding: 3px 4px;
            font-size: 0.56rem;
            line-height: 1.08;
        }

        .routine-paper--compact .routine-table__head-day {
            width: 72px;
        }

        .routine-paper--compact .routine-table__head-period {
            width: 76px;
        }

        .routine-paper--compact .routine-table__day {
            font-size: 0.6rem;
            padding-top: 6px;
        }

        .routine-paper--compact .routine-slot,
        .routine-paper--compact .routine-stack-item {
            padding: 3px 4px;
            border-radius: 4px;
        }

        .routine-paper--compact .routine-slot__title {
            font-size: 0.58rem;
        }

        .routine-paper--compact .routine-slot__meta,
        .routine-paper--compact .routine-slot__note {
            font-size: 0.5rem;
        }

        .routine-paper--compact .routine-paper__footer {
            padding: 5px 7px;
            font-size: 0.54rem;
        }
    }
</style>

