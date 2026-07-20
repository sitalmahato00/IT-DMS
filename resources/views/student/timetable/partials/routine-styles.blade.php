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
        grid-template-columns: repeat(4, minmax(0, 1fr));
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
    }

    .routine-table {
        width: 100%;
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

    .routine-paper--compact .routine-paper__footer {
        padding: 8px 10px;
        font-size: 0.66rem;
    }

    @media (max-width: 900px) {
        .routine-paper__header {
            grid-template-columns: 1fr;
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
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .routine-paper__summary {
            grid-template-columns: 1fr;
        }

        .routine-paper__summary div {
            border-right: 0;
            border-bottom: 1px solid #0f172a;
        }

        .routine-paper__summary div:last-child {
            border-bottom: 0;
        }

        .routine-paper__footer {
            flex-direction: column;
        }
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

