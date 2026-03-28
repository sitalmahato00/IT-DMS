<style>
    .auth-page {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(248, 113, 113, 0.12), transparent 32%),
            radial-gradient(circle at bottom right, rgba(185, 28, 28, 0.12), transparent 26%),
            #fff8f8;
    }

    .auth-shell {
        display: grid;
        min-height: 100vh;
        grid-template-columns: minmax(0, 1.08fr) minmax(380px, 0.92fr);
    }

    .auth-hero {
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: clamp(3rem, 7vw, 6rem);
        background:
            linear-gradient(135deg, rgba(127, 29, 29, 0.12) 0%, rgba(255, 255, 255, 0) 38%),
            linear-gradient(180deg, #b91c1c 0%, #ef4444 100%);
        color: #fff;
    }

    .auth-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 15%, rgba(255, 255, 255, 0.14) 0 2px, transparent 2px) 0 0 / 18px 18px,
            linear-gradient(150deg, transparent 55%, rgba(255, 255, 255, 0.08) 55%, rgba(255, 255, 255, 0.08) 60%, transparent 60%),
            linear-gradient(25deg, transparent 68%, rgba(127, 29, 29, 0.18) 68%, rgba(127, 29, 29, 0.18) 78%, transparent 78%);
        opacity: 0.9;
        pointer-events: none;
    }

    .auth-hero::after {
        content: "";
        position: absolute;
        top: 0;
        right: -1px;
        width: 38%;
        height: 100%;
        background: #fff8f8;
        clip-path: polygon(100% 0, 0 100%, 100% 100%);
    }

    .auth-hero-content {
        position: relative;
        z-index: 1;
        max-width: 34rem;
    }

    .auth-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.95rem;
        margin-bottom: 2rem;
    }

    .auth-brand-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4.2rem;
        height: 4.2rem;
        border-radius: 1.2rem;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.16);
    }

    .auth-brand-logo {
        width: 2.8rem;
        height: 2.8rem;
        object-fit: contain;
    }

    .auth-brand-copy {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .auth-brand-kicker {
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.78);
    }

    .auth-brand-title {
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .auth-hero-title {
        margin: 0;
        font-size: clamp(3rem, 5.5vw, 4.45rem);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.04em;
    }

    .auth-hero-text {
        margin-top: 1.2rem;
        max-width: 27rem;
        font-size: 1.08rem;
        line-height: 1.75;
        color: rgba(255, 255, 255, 0.9);
    }

    .auth-info-list {
        display: grid;
        gap: 0.9rem;
        margin-top: 2rem;
        max-width: 32rem;
    }

    .auth-info-item {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        min-height: 4rem;
        padding: 0.95rem 1.2rem;
        border-radius: 1.15rem;
        background: rgba(255, 255, 255, 0.12);
        box-shadow:
            inset 0 0 0 1px rgba(255, 255, 255, 0.1),
            0 14px 28px rgba(127, 29, 29, 0.14);
        backdrop-filter: blur(10px);
        color: rgba(255, 255, 255, 0.96);
        font-size: 1rem;
        font-weight: 600;
    }

    .auth-info-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.6rem;
        height: 2.6rem;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        flex-shrink: 0;
    }

    .auth-info-icon svg {
        width: 1.2rem;
        height: 1.2rem;
    }

    .auth-side {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1.75rem;
        background:
            radial-gradient(circle at 18% 12%, rgba(248, 113, 113, 0.14), transparent 20%),
            linear-gradient(180deg, #fffdfd 0%, #fff4f4 100%);
    }

    .auth-side::before {
        content: "";
        position: absolute;
        inset: auto auto 8% 12%;
        width: 10rem;
        height: 10rem;
        border-radius: 9999px;
        background: radial-gradient(circle, rgba(254, 202, 202, 0.8) 0%, rgba(254, 202, 202, 0) 68%);
        pointer-events: none;
    }

    .auth-stack {
        width: 100%;
        max-width: 31rem;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }

    .auth-emblem {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 5.5rem;
        height: 5.5rem;
        margin: 0 auto;
        border: 4px solid #dc2626;
        border-radius: 9999px;
        background: #fff;
        color: #b91c1c;
        box-shadow: 0 18px 40px rgba(185, 28, 28, 0.14);
    }

    .auth-emblem-mark {
        font-size: 2.2rem;
        font-weight: 800;
        letter-spacing: -0.08em;
    }

    .auth-card {
        width: 100%;
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(248, 113, 113, 0.2);
        border-radius: 1.75rem;
        box-shadow:
            0 26px 60px rgba(127, 29, 29, 0.14),
            0 10px 24px rgba(15, 23, 42, 0.05);
        padding: 2rem;
        backdrop-filter: blur(14px);
    }

    .auth-alert {
        width: 100%;
        border: 1px solid #fecaca;
        border-radius: 1rem;
        background: #fff1f2;
        padding: 0.9rem 1rem;
        font-size: 0.92rem;
        color: #b91c1c;
    }

    .auth-status {
        width: 100%;
        border: 1px solid #bbf7d0;
        border-radius: 1rem;
        background: #f0fdf4;
        padding: 0.9rem 1rem;
        font-size: 0.92rem;
        color: #15803d;
    }

    .auth-panel-intro {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .auth-panel-logo-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 5.25rem;
        height: 5.25rem;
        border-radius: 9999px;
        background: linear-gradient(180deg, #fff 0%, #fff5f5 100%);
        box-shadow:
            inset 0 0 0 1px rgba(248, 113, 113, 0.22),
            0 16px 28px rgba(127, 29, 29, 0.1);
        flex-shrink: 0;
    }

    .auth-panel-logo {
        width: 3.15rem;
        height: 3.15rem;
        object-fit: contain;
    }

    .auth-panel-copy {
        min-width: 0;
    }

    .auth-panel-title {
        margin: 0;
        font-size: 1.9rem;
        line-height: 1.1;
        letter-spacing: -0.03em;
        color: #18181b;
    }

    .auth-panel-subtitle {
        margin: 0.35rem 0 0;
        color: #6b7280;
        font-size: 0.98rem;
    }

    .auth-panel-meta {
        margin: 0.7rem 0 0;
        color: #4b5563;
        font-size: 0.96rem;
    }

    .auth-divider {
        width: 100%;
        height: 1px;
        margin: 1.45rem 0 1.65rem;
        background: linear-gradient(90deg, rgba(248, 113, 113, 0), rgba(248, 113, 113, 0.3), rgba(248, 113, 113, 0));
    }

    .auth-form-intro {
        margin-bottom: 1.35rem;
    }

    .auth-form-title {
        margin: 0;
        font-size: 2rem;
        line-height: 1.1;
        letter-spacing: -0.03em;
        color: #18181b;
    }

    .auth-form-text {
        margin: 0.65rem 0 0;
        color: #6b7280;
        line-height: 1.7;
    }

    .auth-field + .auth-field {
        margin-top: 1.15rem;
    }

    .auth-label {
        display: block;
        margin-bottom: 0.48rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #3f3f46;
    }

    .auth-input-wrap {
        position: relative;
    }

    .auth-input-icon {
        position: absolute;
        top: 50%;
        left: 0.95rem;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.2rem;
        height: 1.2rem;
        color: #9ca3af;
        pointer-events: none;
    }

    .auth-input-icon svg {
        width: 1.15rem;
        height: 1.15rem;
    }

    .auth-input {
        width: 100%;
        min-height: 3.45rem;
        border: 1px solid #f1c7c7;
        border-radius: 1rem;
        background: #fff;
        padding: 0.8rem 3rem 0.8rem 1rem;
        font-size: 0.95rem;
        color: #0f172a;
        transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
    }

    .auth-input-wrap .auth-input-icon + .auth-input {
        padding-left: 3rem;
    }

    .auth-input:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.14);
        background: #fffdfd;
    }

    .auth-input::placeholder {
        color: #94a3b8;
    }

    .auth-toggle {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
    }

    .auth-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1rem;
        margin-bottom: 1.25rem;
    }

    .auth-remember {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.9rem;
        color: #52525b;
    }

    .auth-remember input {
        width: 1rem;
        height: 1rem;
        accent-color: #dc2626;
    }

    .auth-link {
        color: #b91c1c;
        text-decoration: none;
        font-weight: 600;
        transition: color 160ms ease, opacity 160ms ease;
    }

    .auth-link:hover {
        color: #991b1b;
    }

    .auth-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        border: 0;
        border-radius: 9999px;
        background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
        padding: 0.95rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 12px 24px rgba(220, 38, 38, 0.2);
        transition: transform 160ms ease, box-shadow 160ms ease, filter 160ms ease;
    }

    .auth-submit:hover {
        transform: translateY(-1px);
        filter: brightness(0.98);
        box-shadow: 0 16px 28px rgba(220, 38, 38, 0.24);
    }

    .auth-submit.full {
        margin-top: 0.25rem;
    }

    .auth-secondary-action,
    .auth-back-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 3.35rem;
        margin-top: 0.85rem;
        border-radius: 9999px;
        text-decoration: none;
        font-weight: 600;
        transition: border-color 160ms ease, background-color 160ms ease, color 160ms ease, transform 160ms ease;
    }

    .auth-secondary-action {
        border: 1px solid #f4d4d4;
        background: #f8fafc;
        color: #27272a;
    }

    .auth-secondary-action:hover {
        background: #fdf2f2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .auth-support-note {
        margin: 1rem 0 0;
        text-align: center;
        color: #52525b;
        font-size: 0.96rem;
    }

    .auth-back-link {
        border: 1px solid #fde2e2;
        background: #fff8f8;
        color: #b91c1c;
    }

    .auth-back-link:hover {
        background: #fff1f2;
        border-color: #fecaca;
        color: #991b1b;
        transform: translateY(-1px);
    }

    .auth-footer-note {
        margin: 1.1rem 0 0;
        text-align: center;
        color: #71717a;
        font-size: 0.82rem;
    }

    .auth-helper-links {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        align-items: center;
        font-size: 0.92rem;
        color: #52525b;
    }

    .auth-helper-links a {
        color: #52525b;
        text-decoration: none;
    }

    .auth-helper-links a:hover {
        color: #b91c1c;
    }

    .auth-inline-note {
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #52525b;
        line-height: 1.7;
    }

    @media (max-width: 1024px) {
        .auth-shell {
            grid-template-columns: 1fr;
        }

        .auth-hero {
            min-height: 36vh;
            align-items: flex-end;
            padding: 3rem 1.75rem 5rem;
        }

        .auth-hero::before {
            background:
                radial-gradient(circle at 20% 15%, rgba(255, 255, 255, 0.14) 0 2px, transparent 2px) 0 0 / 16px 16px,
                linear-gradient(160deg, transparent 55%, rgba(255, 255, 255, 0.08) 55%, rgba(255, 255, 255, 0.08) 60%, transparent 60%);
        }

        .auth-hero::after {
            top: auto;
            right: 0;
            bottom: -1px;
            width: 100%;
            height: 34%;
            clip-path: polygon(0 100%, 100% 0, 100% 100%);
        }

        .auth-side {
            margin-top: -3.5rem;
            padding-top: 0;
            position: relative;
            z-index: 1;
        }

        .auth-stack {
            max-width: 40rem;
        }
    }

    @media (max-width: 640px) {
        .auth-card,
        .auth-alert,
        .auth-status {
            padding: 1.15rem;
        }

        .auth-row {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 1rem;
        }

        .auth-brand {
            margin-bottom: 1.4rem;
        }

        .auth-panel-intro {
            flex-direction: column;
            text-align: center;
        }

        .auth-panel-title {
            font-size: 1.55rem;
        }

        .auth-form-title {
            font-size: 1.75rem;
        }

        .auth-info-item {
            font-size: 0.94rem;
            padding: 0.9rem 1rem;
        }
    }

    html.dark .auth-page,
    html.dark .auth-side {
        background:
            radial-gradient(circle at top left, rgba(248, 113, 113, 0.12), transparent 32%),
            radial-gradient(circle at bottom right, rgba(185, 28, 28, 0.12), transparent 26%),
            #fff8f8 !important;
    }

    html.dark .auth-card {
        background: rgba(255, 255, 255, 0.92) !important;
        border-color: rgba(248, 113, 113, 0.2) !important;
        box-shadow:
            0 26px 60px rgba(127, 29, 29, 0.14),
            0 10px 24px rgba(15, 23, 42, 0.05) !important;
    }

    html.dark .auth-panel-title,
    html.dark .auth-form-title {
        color: #18181b !important;
    }

    html.dark .auth-label,
    html.dark .auth-remember,
    html.dark .auth-inline-note,
    html.dark .auth-helper-links,
    html.dark .auth-helper-links a {
        color: #52525b !important;
    }

    html.dark .auth-form-text,
    html.dark .auth-panel-subtitle,
    html.dark .auth-panel-meta,
    html.dark .auth-support-note,
    html.dark .auth-footer-note {
        color: #6b7280 !important;
    }

    html.dark .auth-input {
        background: #fff !important;
        color: #0f172a !important;
        border-color: #f1c7c7 !important;
    }

    html.dark .auth-toggle,
    html.dark .auth-input-icon {
        color: #94a3b8 !important;
    }

    html.dark .auth-toggle {
        color: #94a3b8 !important;
    }

    html.dark .auth-helper-links a:hover,
    html.dark .auth-link:hover {
        color: #991b1b !important;
    }
</style>
