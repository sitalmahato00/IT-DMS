@extends('layouts.public')

@push('head')
    <style>
        :root {
            --mmp-blue: #b31221;
            --mmp-blue-dark: #7d0c18;
            --mmp-blue-soft: #c51f2d;
            --mmp-red: #bf1f2f;
            --mmp-ink: #0f172a;
            --mmp-muted: #5f6b7a;
            --mmp-line: #dbe1e8;
            --mmp-surface: #ffffff;
            --mmp-bg: #f2f4f7;
            --mmp-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        }

        body,
        html {
            margin: 0 !important;
            padding: 0 !important;
            background: var(--mmp-red);
        }

        #mobileAppShellRoot,
        #mobileAppShellRoot > .min-h-screen,
        #mobileAppShellRoot > .min-h-screen > main {
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
        }

        .mmp-page {
            background: var(--mmp-bg);
            min-height: 100vh;
            color: var(--mmp-ink);
            padding-top: 0;
        }

        .mmp-container {
            width: min(1120px, calc(100vw - 2rem));
            margin: 0 auto;
        }

        .mmp-topbar {
            background: var(--mmp-red);
            color: #fff;
            font-size: 0.6rem;
            position: relative;
            z-index: 7;
        }

        .mmp-topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0;
        }

        .mmp-topbar-inner a {
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            padding: 0.35rem 0.7rem;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.18);
        }

        .mmp-header {
            background: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid var(--mmp-line);
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9;
        }

        .mmp-header-inner {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            padding: 0.9rem 0;
        }

        .mmp-logo {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid var(--mmp-red);
            padding: 0.3rem;
            object-fit: contain;
        }

        .mmp-brand h1 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--mmp-red);
            font-weight: 800;
        }

        .mmp-brand p {
            margin: 0.2rem 0 0;
            color: var(--mmp-muted);
            font-size: 0.85rem;
        }

        .mmp-search {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mmp-search input {
            height: 38px;
            border: 1px solid var(--mmp-line);
            border-radius: 999px;
            padding: 0 1rem;
            min-width: 220px;
        }

        .mmp-search button {
            height: 38px;
            border-radius: 999px;
            border: 0;
            background: var(--mmp-red);
            color: #fff;
            padding: 0 1rem;
            font-weight: 700;
        }

        .mmp-nav {
            background: var(--mmp-red);
        }

        .mmp-nav-inner {
            display: flex;
            align-items: center;
            gap: 1.6rem;
            padding: 0.7rem 0;
        }

        .mmp-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 1.6rem;
            flex-wrap: wrap;
        }

        .mmp-nav li {
            position: relative;
        }

        .mmp-nav a {
            color: #fff;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: center;
            padding: 0.25rem 0;
            text-decoration: none;
            font-weight: 700;
            position: relative;
        }

        .mmp-nav a.has-caret::after {
            content: "";
            display: inline-block;
            margin-left: 0.3rem;
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #fff;
            transform: translateY(-1px);
        }

        .mmp-nav a.is-active::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -0.7rem;
            height: 3px;
            background: #fff;
            border-radius: 999px;
        }

        .mmp-nav .dropdown {
            position: absolute;
            top: calc(100% + 0.6rem);
            left: 0;
            min-width: 220px;
            background: #444;
            border-radius: 4px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
            padding: 0.25rem 0;
            display: block;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s ease;
            pointer-events: none;
            z-index: 10;
        }

        .mmp-nav .dropdown li {
            width: 100%;
        }

        .mmp-nav .dropdown a {
            display: block;
            padding: 0.7rem 1rem;
            text-transform: none;
            letter-spacing: normal;
            font-size: 0.85rem;
            font-weight: 600;
            color: #f8f8f8;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .mmp-nav .dropdown li:last-child a {
            border-bottom: 0;
        }

        .mmp-nav li:hover > .dropdown,
        .mmp-nav li:focus-within > .dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .mmp-hero-mini {
            background: linear-gradient(120deg, rgba(191, 31, 47, 0.88) 0%, rgba(191, 31, 47, 0.65) 100%);
            color: #fff;
            padding: 2.5rem 0;
        }

        .mmp-card {
            background: #fff;
            border: 1px solid var(--mmp-line);
            box-shadow: var(--mmp-shadow);
            border-radius: 6px;
            padding: 1.5rem;
        }

        .mmp-main {
            margin-top: 40px;
            position: relative;
            z-index: 2;
        }

        .mmp-section-title {
            margin: 0 0 1rem;
            font-size: 1.6rem;
            color: var(--mmp-ink);
        }

        .mmp-summary-card {
            display: grid;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .mmp-summary-card .item {
            background: #f8fafc;
            border: 1px solid var(--mmp-line);
            border-radius: 8px;
            padding: 1rem;
        }

        .mmp-summary-card .item strong {
            display: block;
            font-size: 0.85rem;
            color: var(--mmp-muted);
            margin-bottom: 0.35rem;
        }

        .mmp-summary-card .item span {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--mmp-ink);
        }

        .mmp-result-form {
            display: grid;
            gap: 1rem;
        }

        .mmp-result-form label {
            font-size: 0.9rem;
            color: var(--mmp-muted);
            margin-bottom: 0.35rem;
            display: block;
        }

        .mmp-result-form input,
        .mmp-result-form select {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            border: 1px solid var(--mmp-line);
            font-size: 0.95rem;
            color: var(--mmp-ink);
        }

        .mmp-result-form .form-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .mmp-result-form .form-row-single {
            display: block;
        }

        .mmp-result-form button {
            width: fit-content;
            padding: 0.95rem 1.5rem;
            background: var(--mmp-blue);
            color: #fff;
            border: 0;
            border-radius: 999px;
            font-weight: 700;
            cursor: pointer;
        }

        .mmp-alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            border: 1px solid transparent;
            margin-top: 1rem;
        }

        .mmp-alert-error {
            background: #fee2e2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .mmp-alert-success {
            background: #ecfdf5;
            border-color: #bbf7d0;
            color: #166534;
        }

        .mmp-result-card {
            margin-top: 1.5rem;
        }

        .mmp-result-card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .mmp-result-card th,
        .mmp-result-card td {
            padding: 0.9rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--mmp-line);
        }

        .mmp-result-card th {
            color: var(--mmp-ink);
            font-weight: 700;
        }

        .mmp-result-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .mmp-result-badge.pass {
            background: #dcfce7;
            color: #166534;
        }

        .mmp-result-badge.fail {
            background: #fee2e2;
            color: #b91c1c;
        }

        .mmp-result-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .mmp-result-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.25rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid var(--mmp-line);
            color: var(--mmp-ink);
            text-decoration: none;
            font-weight: 700;
        }

        /* Transcript Styles */
        .mmp-transcript-card {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            border: 2px solid var(--mmp-line);
            position: relative;
        }

        .mmp-transcript-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--mmp-red), var(--mmp-blue));
        }

        .mmp-transcript-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            border-bottom: 2px solid var(--mmp-line);
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }

        .mmp-transcript-logo {
            flex-shrink: 0;
        }

        .mmp-transcript-title {
            flex: 1;
        }

        .mmp-transcript-title h2 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--mmp-red);
            font-weight: 800;
        }

        .mmp-transcript-title p {
            margin: 0.25rem 0;
            color: var(--mmp-ink);
            font-weight: 600;
        }

        .mmp-transcript-student-info {
            padding: 1.5rem;
            border-bottom: 1px solid var(--mmp-line);
        }

        .mmp-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .mmp-info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .mmp-info-item strong {
            color: var(--mmp-ink);
            font-weight: 600;
        }

        .mmp-transcript-exam-details {
            padding: 1.5rem;
            border-bottom: 1px solid var(--mmp-line);
            background: #f8fafc;
        }

        .mmp-transcript-exam-details h3 {
            margin: 0 0 1rem 0;
            color: var(--mmp-red);
            font-size: 1.2rem;
            font-weight: 700;
        }

        .mmp-exam-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .mmp-exam-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #fff;
            border-radius: 6px;
            border: 1px solid var(--mmp-line);
        }

        .mmp-exam-item strong {
            color: var(--mmp-ink);
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .mmp-transcript-marks {
            padding: 1.5rem;
        }

        .mmp-transcript-marks h3 {
            margin: 0 0 1rem 0;
            color: var(--mmp-red);
            font-size: 1.2rem;
            font-weight: 700;
        }

        .mmp-transcript-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .mmp-transcript-table th,
        .mmp-transcript-table td {
            padding: 0.75rem 0.5rem;
            text-align: left;
            border: 1px solid var(--mmp-line);
        }

        .mmp-transcript-table th {
            background: var(--mmp-red);
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .mmp-transcript-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .mmp-transcript-table tbody tr:hover {
            background: #e2e8f0;
        }

        .mmp-total-row {
            background: var(--mmp-red) !important;
            color: #fff !important;
        }

        .mmp-total-row th,
        .mmp-total-row td {
            font-weight: 700 !important;
            font-size: 0.95rem !important;
        }

        .mmp-transcript-summary {
            padding: 1.5rem;
            border-top: 2px solid var(--mmp-line);
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }

        .mmp-result-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            max-width: 600px;
        }

        .mmp-summary-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #fff;
            border-radius: 8px;
            border: 2px solid var(--mmp-line);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .mmp-summary-item strong {
            color: var(--mmp-ink);
            font-weight: 700;
            margin-right: 0.5rem;
        }

        .mmp-transcript-footer {
            padding: 2rem 1.5rem 1.5rem;
            border-top: 1px solid var(--mmp-line);
            background: #f8fafc;
        }

        .mmp-signature-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .mmp-signature {
            text-align: center;
        }

        .mmp-signature-line {
            width: 200px;
            height: 2px;
            background: var(--mmp-ink);
            margin: 0 auto 0.5rem;
            border-radius: 1px;
        }

        .mmp-signature p {
            margin: 0.25rem 0;
            font-size: 0.85rem;
            color: var(--mmp-ink);
            font-weight: 600;
        }

        .mmp-transcript-note {
            border-top: 1px solid var(--mmp-line);
            padding-top: 1rem;
            font-size: 0.8rem;
            color: var(--mmp-muted);
            line-height: 1.5;
        }

        .mmp-transcript-note p {
            margin: 0.5rem 0;
        }

        @media (max-width: 768px) {
            .mmp-transcript-header {
                flex-direction: column;
                text-align: center;
            }

            .mmp-info-grid {
                grid-template-columns: 1fr;
            }

            .mmp-exam-summary,
            .mmp-result-summary {
                grid-template-columns: 1fr;
            }

            .mmp-signature-section {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .mmp-transcript-table {
                font-size: 0.8rem;
            }

            .mmp-transcript-table th,
            .mmp-transcript-table td {
                padding: 0.5rem 0.25rem;
            }
        }

        @media print {
            .mmp-transcript-card {
                box-shadow: none;
                border: 1px solid #000;
            }

            .mmp-result-actions {
                display: none;
            }

            .mmp-transcript-card::before {
                background: #000;
            }
        }

        @media (max-width: 1100px) {
            .mmp-nav-inner,
            .mmp-nav ul {
                gap: 0.9rem;
                justify-content: center;
            }

            .mmp-nav a.is-active::after {
                bottom: -0.4rem;
            }
        }

        @media (max-width: 780px) {
            .mmp-result-form .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.mmp-nav li');

    navItems.forEach(item => {
        let hideTimeout;

        item.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            const dropdown = this.querySelector('.dropdown');
            if (dropdown) {
                dropdown.style.opacity = '1';
                dropdown.style.visibility = 'visible';
                dropdown.style.transform = 'translateY(0)';
                dropdown.style.pointerEvents = 'auto';
            }
        });

        item.addEventListener('mouseleave', function() {
            const dropdown = this.querySelector('.dropdown');
            if (dropdown) {
                hideTimeout = setTimeout(() => {
                    dropdown.style.opacity = '0';
                    dropdown.style.visibility = 'hidden';
                    dropdown.style.transform = 'translateY(6px)';
                    dropdown.style.pointerEvents = 'none';
                }, 300);
            }
        });
    });

    const examResultMeta = @json($examResultMeta);
    const form = document.getElementById('examResultForm');
    const assessmentContainer = document.getElementById('assessment-number-field');
    const assessmentSelect = document.getElementById('assessment_number');
    const categorySelect = document.getElementById('exam_category');
    const yearSelect = document.getElementById('academic_year');
    const semesterSelect = document.getElementById('semester');
    const feedbackEl = document.getElementById('examResultFeedback');
    const outputEl = document.getElementById('examResultOutput');

    const getAssessmentNumbersFor = () => {
        const key = `${yearSelect.value || 'all'}|${semesterSelect.value || 'all'}`;
        const assessmentMap = examResultMeta.assessmentMap || {};
        return assessmentMap[key] || assessmentMap['all|all'] || [];
    };

    const updateAssessmentOptions = () => {
        const options = getAssessmentNumbersFor();
        assessmentSelect.innerHTML = '<option value="">' + '{{ __('Auto select') }}' + '</option>' +
            options.map(number => `<option value="${number}">${number}</option>`).join('');
    };

    const updateAssessmentField = () => {
        const isAssessment = categorySelect.value === 'assessment';
        assessmentContainer.style.display = isAssessment ? 'block' : 'none';
        assessmentSelect.required = isAssessment;
        if (!isAssessment) {
            assessmentSelect.value = '';
        }
    };

    updateAssessmentOptions();
    updateAssessmentField();

    yearSelect.addEventListener('change', updateAssessmentOptions);
    semesterSelect.addEventListener('change', updateAssessmentOptions);
    categorySelect.addEventListener('change', updateAssessmentField);

    const showFeedback = (message, type = 'error') => {
        feedbackEl.innerHTML = `<div class="mmp-alert ${type === 'success' ? 'mmp-alert-success' : 'mmp-alert-error'}">${message}</div>`;
    };

    const renderResult = (result) => {
        const badgeClass = result.result.status === 'pass' ? 'pass' : 'fail';
        const rows = result.marks.map(mark => `
            <tr>
                <td>${mark.subject}</td>
                <td>${mark.full_marks}</td>
                <td>${mark.obtained}</td>
                <td>${mark.grade}</td>
                <td>${mark.remarks}</td>
            </tr>
        `).join('');

        outputEl.innerHTML = `
            <div class="mmp-alert mmp-alert-success">${result.message}</div>
            <div class="mmp-transcript-card mmp-card">
                <!-- Transcript Header -->
                <div class="mmp-transcript-header">
                    <div class="mmp-transcript-logo">
                        <img src="{{ asset('images/default-logo.svg') }}" alt="MMP Logo" style="width: 60px; height: 60px;">
                    </div>
                    <div class="mmp-transcript-title">
                        <h2>Manmohan Memorial Polytechnic</h2>
                        <p>Official Academic Transcript</p>
                        <p style="font-size: 0.9rem; color: var(--mmp-muted);">Morang, Nepal</p>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="mmp-transcript-student-info">
                    <div class="mmp-info-grid">
                        <div class="mmp-info-item">
                            <strong>Student Name:</strong> ${result.student.name}
                        </div>
                        <div class="mmp-info-item">
                            <strong>Student ID:</strong> ${result.student.id}
                        </div>
                        <div class="mmp-info-item">
                            <strong>Roll Number:</strong> ${result.student.roll_no || 'N/A'}
                        </div>
                        <div class="mmp-info-item">
                            <strong>Department:</strong> ${result.student.department || 'N/A'}
                        </div>
                        <div class="mmp-info-item">
                            <strong>Academic Year:</strong> ${result.filters.academic_year || 'N/A'}
                        </div>
                        <div class="mmp-info-item">
                            <strong>Semester:</strong> ${result.filters.semester || 'N/A'}
                        </div>
                        <div class="mmp-info-item">
                            <strong>Exam Type:</strong> ${result.exam.category || 'N/A'}
                        </div>
                        <div class="mmp-info-item">
                            <strong>Assessment Number:</strong> ${result.filters.assessment_number || 'N/A'}
                        </div>
                    </div>
                </div>

                <!-- Exam Details -->
                <div class="mmp-transcript-exam-details">
                    <h3>Examination Details</h3>
                    <div class="mmp-exam-summary">
                        <div class="mmp-exam-item">
                            <strong>Exam Name:</strong> ${result.exam.name}
                        </div>
                        <div class="mmp-exam-item">
                            <strong>Result Status:</strong>
                            <span class="mmp-result-badge ${badgeClass}" style="margin-left: 0.5rem;">${result.result.label}</span>
                        </div>
                        <div class="mmp-exam-item">
                            <strong>Overall Grade:</strong> ${result.result.grade}
                        </div>
                        <div class="mmp-exam-item">
                            <strong>Percentage:</strong> ${result.result.percentage}%
                        </div>
                    </div>
                </div>

                <!-- Subject-wise Marks -->
                <div class="mmp-transcript-marks">
                    <h3>Subject-wise Performance</h3>
                    <table class="mmp-transcript-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Subject</th>
                                <th style="width: 15%;">Full Marks</th>
                                <th style="width: 15%;">Obtained</th>
                                <th style="width: 15%;">Grade</th>
                                <th style="width: 15%;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                        <tfoot>
                            <tr class="mmp-total-row">
                                <th>Total</th>
                                <th>${result.result.total_full}</th>
                                <th>${result.result.total_obtained}</th>
                                <th colspan="2">${result.result.grade} (${result.result.percentage}%)</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Result Summary -->
                <div class="mmp-transcript-summary">
                    <div class="mmp-result-summary">
                        <div class="mmp-summary-item">
                            <strong>Final Result:</strong>
                            <span class="mmp-result-badge ${badgeClass}" style="margin-left: 0.5rem;">${result.result.label}</span>
                        </div>
                        <div class="mmp-summary-item">
                            <strong>Grade Point Average:</strong> ${result.result.grade}
                        </div>
                        <div class="mmp-summary-item">
                            <strong>Percentage:</strong> ${result.result.percentage}%
                        </div>
                    </div>
                </div>

                <!-- Authorization -->
                <div class="mmp-transcript-footer">
                    <div class="mmp-signature-section">
                        <div class="mmp-signature">
                            <div class="mmp-signature-line"></div>
                            <p>Controller of Examinations</p>
                            <p>Manmohan Memorial Polytechnic</p>
                        </div>
                        <div class="mmp-signature">
                            <div class="mmp-signature-line"></div>
                            <p>Principal</p>
                            <p>Manmohan Memorial Polytechnic</p>
                        </div>
                    </div>
                    <div class="mmp-transcript-note">
                        <p><strong>Note:</strong> This is an official transcript issued by Manmohan Memorial Polytechnic. This document is valid only when bearing the official seal and signatures.</p>
                        <p><strong>Issue Date:</strong> {{ now()->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="mmp-result-actions">
                    <a href="${result.printUrl}" target="_blank">${'{{ __('Print Transcript') }}'}</a>
                    <a href="{{ route('home') }}">${'{{ __('Back to Home') }}'}</a>
                </div>
            </div>
        `;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        feedbackEl.innerHTML = '';
        outputEl.innerHTML = '';

        const formData = new URLSearchParams(new FormData(form));
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(form.dataset.searchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                const errorText = result.error || result.message || (result.errors ? Object.values(result.errors).flat().join(' ') : null);
                showFeedback(errorText || '{{ __('Unable to locate a published result with the provided details.') }}');
                return;
            }

            // Redirect directly to marksheet print view
            window.location.href = result.printUrl;
        } catch (error) {
            showFeedback('{{ __('A secure result lookup could not be completed. Please try again.') }}');
            console.error(error);
        }
    });
});
</script>
@endpush

@section('content')
<div class="mmp-page">
    <div class="mmp-topbar">
        <div class="mmp-container mmp-topbar-inner">
            <span>{{ now()->format('d M Y, l') }}</span>
            <a href="{{ route('login') }}">PORTAL LOGIN</a>
        </div>
    </div>

    <header class="mmp-header">
        <div class="mmp-container mmp-header-inner">
            <img class="mmp-logo" src="{{ asset('images/default-logo.svg') }}" alt="Logo">
            <div class="mmp-brand">
                <h1>Manmohan Memorial Polytechnic</h1>
                <p>Manmohan Memorial Polytechnic public portal.</p>
            </div>
            <form class="mmp-search" action="{{ route('public.notices.index') }}" method="get">
                <input type="search" placeholder="Search">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <nav class="mmp-nav">
        <div class="mmp-container mmp-nav-inner">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.about') }}">About Us</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.about.what-is-mmp') }}">What is MMP</a></li>
                        <li><a href="{{ route('public.pages.about.objectives') }}">Objectives</a></li>
                        <li><a href="{{ route('public.pages.about.presidents-principals') }}">Presidents and Principals</a></li>
                        <li><a href="{{ route('public.pages.about.contact') }}">Contact Us</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.courses') }}">Courses</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.courses.it') }}">Diploma in Information Technology</a></li>
                        <li><a href="{{ route('public.pages.courses.architecture') }}">Diploma in Architecture Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.electrical') }}">Diploma in Electrical Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.electronics') }}">Diploma in Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.mechanical') }}">Diploma in Mechanical Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.civil') }}">Diploma in Civil Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.eee') }}">Diploma in Electrical & Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.courses.short-term') }}">Short Term Trainings</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.features') }}">Features</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.features.classrooms-labs') }}">Classrooms and Labs</a></li>
                        <li><a href="{{ route('public.pages.features.workshops') }}">Workshops</a></li>
                        <li><a href="{{ route('public.pages.features.scholarships') }}">Scholarship Schemes</a></li>
                        <li><a href="{{ route('public.pages.features.transportation') }}">Transportation</a></li>
                        <li><a href="{{ route('public.pages.features.internships') }}">Internships & Placements</a></li>
                        <li><a href="{{ route('public.pages.features.library-hostel') }}">Library and Hostel</a></li>
                        <li><a href="{{ route('public.pages.features.game-courts') }}">Game Courts</a></li>
                        <li><a href="{{ route('public.pages.features.first-aid') }}">First Aid Clinic</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.peoples') }}">Peoples</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.peoples.administrative-staffs') }}">Administrative Staffs</a></li>
                        <li><a href="{{ route('public.pages.peoples.architecture') }}">Department of Architecture Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.civil') }}">Department of Civil Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.electrical') }}">Department of Electrical Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.eee') }}">Department of Electrical & Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.electronics') }}">Department of Electronics Engineering</a></li>
                        <li><a href="{{ route('public.pages.peoples.it') }}">Department of Information Technology</a></li>
                        <li><a href="{{ route('public.pages.peoples.mechanical') }}">Department of Mechanical Engineering</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('public.pages.news') }}">News & Events</a></li>
                <li><a href="{{ route('public.pages.gallery') }}">Gallery</a></li>
                <li><a href="{{ route('public.exam-result') }}">Check Result</a></li>
                <li>
                    <a class="has-caret" href="{{ route('public.pages.resources') }}">Resources</a>
                    <ul class="dropdown">
                        <li><a href="{{ route('public.pages.resources.formats') }}">Formats</a></li>
                        <li><a href="{{ route('public.pages.resources.question-bank') }}">Question Bank</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <section class="mmp-hero-mini">
        <div class="mmp-container">
            <h1>{{ __('Check Published Results') }}</h1>
            <p style="max-width: 720px; margin-top: 0.75rem; color: rgba(255,255,255,0.9);">
                {{ __('Search by student ID or roll number and date of birth to see exam results that have been published in the system.') }}
            </p>
        </div>
    </section>

    <div class="mmp-container mmp-main">
        <div class="mmp-card">
            <div class="mmp-summary-card">
                <div class="item">
                    <strong>{{ __('Published results available') }}</strong>
                    <span>{{ number_format($publishedCount) }}</span>
                </div>
                <div class="item">
                    <strong>{{ __('Latest year') }}</strong>
                    <span>{{ $examResultMeta['years'][0] ?? __('N/A') }}</span>
                </div>
                <div class="item">
                    <strong>{{ __('Latest semester') }}</strong>
                    <span>{{ $examResultMeta['semesters'][0] ?? __('N/A') }}</span>
                </div>
            </div>

            <h2 class="mmp-section-title">{{ __('Check Result') }}</h2>
            <form id="examResultForm" class="mmp-result-form" data-search-url="{{ route('public.exam-result.search') }}">
                @csrf
                <div class="form-row">
                    <div>
                        <label for="academic_year">{{ __('Academic Year') }}</label>
                        <select id="academic_year" name="academic_year">
                            @foreach($examResultMeta['years'] as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="semester">{{ __('Semester') }}</label>
                        <select id="semester" name="semester">
                            @foreach($examResultMeta['semesters'] as $semester)
                                <option value="{{ $semester }}">{{ $semester }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="exam_category">{{ __('Result Type') }}</label>
                        <select id="exam_category" name="exam_category">
                            <option value="assessment">{{ __('Assessment') }}</option>
                            <option value="ctevt">{{ __('CTEVT') }}</option>
                        </select>
                    </div>
                    <div id="assessment-number-field">
                        <label for="assessment_number">{{ __('Assessment Number') }}</label>
                        <select id="assessment_number" name="assessment_number" required>
                            <option value="">{{ __('Auto select') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="student_id">{{ __('Student ID') }}</label>
                        <input id="student_id" type="text" name="student_id" placeholder="STU-0000" autocomplete="off">
                    </div>
                    <div>
                        <label for="dob_bs">{{ __('Date of Birth (BS)') }}</label>
                        <input id="dob_bs" type="text" name="dob_bs" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                </div>
                <div class="form-row-single">
                    <button type="submit">{{ __('Show Published Result') }}</button>
                </div>
            </form>

            <div id="examResultFeedback"></div>
            <div id="examResultOutput"></div>
        </div>
    </div>
</div>
@endsection
