<style>
    /* ─── Lock the entire page to viewport — no page scroll on auth pages ── */
    /* brand-theme is ONLY included on auth pages, so these are safe */
    html, body {
        height: 100dvh !important;
        overflow: hidden !important;
        background-image: url('/images/loginbg.jpeg') !important;
        background-size: cover !important;
        background-position: center !important;
        background-attachment: fixed !important;
    }



    .min-h-screen {
        height: 100dvh !important;
        min-height: unset !important;
        overflow: hidden !important;
        background: transparent !important;
    }

    .min-h-screen > main {
        height: 100%;
        overflow: hidden;
    }

    /* ─── Page shell — fits exactly in viewport, no page scroll ─── */
    .auth-page {
        height: 100dvh;
        overflow: hidden;
        background: transparent;
    }

    .auth-shell {
        display: grid;
        height: 100%;
        overflow: hidden;
        grid-template-columns: minmax(0, 1fr) minmax(300px, 0.7fr);
    }

    /* ─── LEFT HERO PANEL ────────────────────────────── */
    .auth-hero {
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
        height: 100%;
        padding: clamp(1.5rem, 3vw, 2.5rem);
        background: transparent;
        color: #fff;
    }


    /* No hero overlays - pure image */

    .auth-hero-content {
        position: relative;
        z-index: 1;
        max-width: 32rem;
    }

    /* Brand row (logo box + IT name) */
    .auth-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        margin-bottom: 1.5rem;
    }

    .auth-brand-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(12px);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
    }

    .auth-brand-logo {
        width: 2.6rem;
        height: 2.6rem;
        object-fit: contain;
    }

    .auth-brand-copy {
        display: flex;
        flex-direction: column;
        gap: 0.18rem;
    }

    .auth-brand-kicker {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.75);
    }

    .auth-brand-title {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.2;
        color: #fff;
    }

    /* Big heading */
    .auth-hero-title {
        margin: 0;
        font-size: clamp(1.8rem, 3vw, 2.8rem);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.04em;
        color: #fff;
    }


    /* Bold subtitle line */
    .auth-hero-summary {
        margin-top: 0.6rem;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.35;
        color: #fff;
    }


    /* Lighter description */
    .auth-hero-text {
        margin-top: 0.3rem;
        max-width: 24rem;
        font-size: 0.85rem;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.80);
    }


    /* Contact info cards */
    .auth-info-list {
        display: grid;
        gap: 0.6rem;
        margin-top: 1.3rem;
        max-width: 30rem;
    }

    .auth-info-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-height: 3.8rem;
        padding: 0.88rem 1.15rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.13);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        color: rgba(255, 255, 255, 0.95);
        font-size: 0.97rem;
        font-weight: 600;
        transition: background 200ms ease;
    }

    .auth-info-item:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    .auth-info-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.4rem;
        height: 2.4rem;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        flex-shrink: 0;
    }

    .auth-info-icon svg {
        width: 1.15rem;
        height: 1.15rem;
    }

    /* ─── RIGHT FORM PANEL ───────────────────────────── */
    .auth-side {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 0.8rem 1rem;
        background: transparent;
        overflow-y: auto;  /* scrolls internally — page stays fixed */
    }

    /* Soft bottom-left pink orb */
    .auth-side::before {
        content: "";
        position: absolute;
        bottom: 6%;
        left: 8%;
        width: 16rem;
        height: 16rem;
        border-radius: 9999px;
        background: radial-gradient(circle, rgba(254, 226, 226, 0.6) 0%, transparent 68%);
        pointer-events: none;
    }

    .auth-stack {
        width: 100%;
        max-width: 26rem;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
        position: relative;
        z-index: 1;
    }

    /* ─── FORM CARD ───────────────────────────────────── */
    .auth-card {
        width: 100%;
        position: relative;
        z-index: 1;
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 0.8rem 1rem 0.7rem;
        margin-top: -0.25rem;
    }


    /* Alert / status banners */
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

    /* ─── CARD HEADER (logo + name + address) ─────────── */
        .auth-panel-intro {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.1rem;
            text-align: center;
            margin-bottom: 0.15rem;
            padding-top: 0.5rem;
        }

        .auth-panel-logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3.8rem;
            height: 3.8rem;
            border-radius: 9999px;
            background: linear-gradient(160deg, #fff 0%, #fff5f5 100%);
            box-shadow:
                inset 0 0 0 1px rgba(220, 38, 38, 0.2),
                0 8px 24px rgba(127, 29, 29, 0.12);
            flex-shrink: 0;
            margin-bottom: 0.25rem;
        }

    .auth-panel-logo {
        width: 2.6rem;
        height: 2.6rem;
        object-fit: contain;
    }

    .auth-panel-copy {
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.3rem;
    }

        .auth-panel-title {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.025em;
            color: #27272a;
        }

    .auth-panel-subtitle {
        margin: 0;
        color: #78716b;
        font-size: 0.85rem;
        font-weight: 500;
        line-height: 1.4;
    }

    .auth-panel-meta {
        margin: 0;
        color: #8b7969;
        font-size: 0.81rem;
        line-height: 1.4;
    }

    /* Gradient divider */
    .auth-divider {
        width: 100%;
        height: 1px;
        margin: 0.8rem 0 1rem;
        background: transparent;
    }

    /* ─── FORM INTRO ───────────────────────────────────── */
    .auth-form-intro {
        margin-bottom: 0.8rem;
        text-align: center;
    }

    .auth-form-title {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.025em;
        color: #27272a;
    }

    .auth-form-text {
        margin: 0.3rem 0 0;
        color: #78716b;
        font-size: 0.82rem;
        line-height: 1.5;
    }

    /* ─── FORM FIELDS ──────────────────────────────────── */
    .auth-field + .auth-field {
        margin-top: 0.6rem;
    }

    .auth-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.87rem;
        font-weight: 600;
        color: #27272a;
    }

    .auth-input-wrap {
        position: relative;
    }

    .auth-input-icon {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.15rem;
        height: 1.15rem;
        color: #9ca3af;
        pointer-events: none;
    }

    .auth-input-icon svg {
        width: 1.1rem;
        height: 1.1rem;
    }

    .auth-input {
        width: 100%;
        min-height: 2.4rem;
        border: 1px solid #f0e6e6;
        border-radius: 0.6rem;
        background: #fafaf8;
        padding: 0.45rem 2.7rem 0.45rem 0.9rem;
        font-size: 0.88rem;
        color: #27272a;
        transition: border-color 180ms ease, box-shadow 180ms ease, background 180ms ease;
    }

    .auth-input-wrap .auth-input-icon + .auth-input {
        padding-left: 2.85rem;
    }

    .auth-input:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        background: #fff;
    }

    .auth-input::placeholder {
        color: #94a3b8;
    }

    .auth-toggle {
        position: absolute;
        right: 0.8rem;
        top: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        padding: 0.25rem;
        transition: color 150ms ease;
    }

    .auth-toggle:hover {
        color: #6b7280;
    }

    /* ─── REMEMBER / FORGOT ROW ───────────────────────── */
    .auth-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-top: 0.5rem;
        margin-bottom: 0.6rem;
    }

    .auth-remember {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.88rem;
        color: #6b7280;
        cursor: pointer;
    }

    .auth-remember input {
        width: 1rem;
        height: 1rem;
        accent-color: #dc2626;
        cursor: pointer;
    }

    .auth-link {
        color: #b91c1c;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 600;
        transition: color 150ms ease;
    }

    .auth-link:hover {
        color: #991b1b;
    }

    /* ─── BUTTONS ──────────────────────────────────────── */
    .auth-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        border: 0;
        border-radius: 9999px;
        background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.28);
        transition: transform 155ms ease, box-shadow 155ms ease, filter 155ms ease;
    }

    .auth-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 28px rgba(220, 38, 38, 0.34);
        filter: brightness(1.04);
    }

    .auth-submit.full {
        margin-top: 0.2rem;
    }

    .auth-secondary-action,
    .auth-back-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 2.6rem;
        margin-top: 0.55rem;
        border-radius: 9999px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        transition: border-color 155ms ease, background-color 155ms ease, color 155ms ease, transform 155ms ease;
    }

    .auth-secondary-action {
        border: 1px solid #e8e0df;
        background: #f5f3f2;
        color: #4b4641;
        font-weight: 600;
    }

    .auth-secondary-action:hover {
        background: #ede5e3;
        border-color: #d9d0cc;
        color: #27272a;
    }

    .auth-support-note {
        margin: 0.7rem 0 0;
        text-align: center;
        color: #78716b;
        font-size: 0.85rem;
    }

    .auth-back-link {
        border: 1px solid #fde2e2;
        background: #fff8f8;
        color: #b91c1c;
        font-size: 0.9rem;
    }

    .auth-back-link:hover {
        background: #fff1f2;
        border-color: #fecaca;
        color: #991b1b;
        transform: translateY(-1px);
    }

    .auth-footer-note {
        margin: 0.6rem 0 0;
        text-align: center;
        color: #a39690;
        font-size: 0.7rem;
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

    /* ─── RESPONSIVE ───────────────────────────────────── */
    @media (max-width: 1024px) {
        .auth-shell {
            grid-template-columns: 1fr;
        }

        /* Hide the left hero panel on tablets and below */
        .auth-hero {
            display: none;
        }

        /* Full screen form panel with small red accent strip at top */
        .auth-side {
            min-height: 100vh;
            align-items: flex-start;
            padding: 1.25rem 1rem 1.25rem;
        background: transparent;

        }

        .auth-side::before {
            display: none;
        }

        .auth-stack {
            max-width: 100%;
            width: 100%;
            padding-top: 0;
        }

        /* Lift card to overlap the red strip slightly */
        .auth-card {
            border-radius: 1.25rem;
            margin-top: 0;
        }

        .auth-page-login .auth-side {
            padding: 1.5rem 1rem;
        }

        .auth-page-login .auth-stack {
            max-width: 34rem;
        }

        .auth-page-login .auth-card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 1.5rem;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
            padding: 1.5rem 1.3rem;
        }
    }

    @media (max-width: 640px) {
        .auth-card,
        .auth-alert,
        .auth-status {
            padding: 1.25rem;
        }

        .auth-row {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 0.9rem;
        }

        .auth-brand {
            margin-bottom: 1.4rem;
        }

        .auth-panel-intro {
            flex-direction: column;
            text-align: center;
        }

        .auth-panel-title {
            font-size: 1.5rem;
        }

        .auth-form-title {
            font-size: 1.65rem;
        }

        .auth-info-item {
            font-size: 0.92rem;
            padding: 0.85rem 1rem;
        }

        .auth-page-login .auth-form-title {
            font-size: 2rem;
        }

        .auth-page-login .auth-input,
        .auth-page-login .auth-secondary-action,
        .auth-page-login .auth-back-link,
        .auth-page-login .auth-submit {
            min-height: 3.15rem;
        }
    }

    /* ─── FORCE LIGHT THEME ON AUTH PAGES ─────────────── */
    html.dark .auth-page,
    html.dark .auth-side {
        background-image: url('/images/loginbg.jpeg') !important;
        background-size: cover !important;
    }
    html.dark * {
        color-scheme: light !important;
    }

    html.dark .auth-side {
        background-image: url('/images/loginbg.jpeg') !important;
        background-size: cover !important;
    }

    html.dark .auth-card {
        background: #1f1b24 !important;
        border-color: rgba(148, 163, 184, 0.3) !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4) !important;
        color: #ffffff !important;
    }

    html.dark .auth-panel-title,
    html.dark .auth-form-title,
    html.dark .auth-label {
        color: #f8fafc !important;
    }

    html.dark .auth-form-text,
    html.dark .auth-panel-subtitle,
    html.dark .auth-panel-meta {
        color: #cbd5e1 !important;
    }

    html.dark .auth-input {
        background: #374151 !important;
        border-color: #4b5563 !important;
        color: #f9fafb !important;
    }

    html.dark .auth-input::placeholder {
        color: #9ca3af !important;
    }

    html.dark .auth-remember,
    html.dark .auth-footer-note {
        color: #d1d5db !important;
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
        border-color: #e5c4c4 !important;
    }

    html.dark .auth-toggle,
    html.dark .auth-input-icon {
        color: #94a3b8 !important;
    }

    html.dark .auth-secondary-action {
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        color: #27272a !important;
    }

    html.dark .auth-helper-links a:hover,
    html.dark .auth-link:hover {
        color: #991b1b !important;
    }

    /* ─── LOGIN PAGE MATCHING OVERRIDES ────────────────── */
    .auth-page-login {
        font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .locale-ne .auth-page-login {
        font-family: var(--font-nepali), 'Figtree', sans-serif;
    }

    .auth-page-login .auth-shell {
        grid-template-columns: minmax(0, 1.1fr) minmax(470px, 0.84fr);
    }

    .auth-page-login .auth-hero {
        padding: 3.25rem 4rem;
    }

    .auth-page-login .auth-hero-content {
        max-width: 36rem;
    }

    .auth-page-login .auth-brand {
        gap: 1rem;
        margin-bottom: 2.85rem;
    }

    .auth-page-login .auth-brand-mark {
        width: 5.25rem;
        height: 5.25rem;
        border-radius: 1.35rem;
        background: rgba(255, 255, 255, 0.1);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.14);
    }

    .auth-page-login .auth-brand-logo {
        width: 3.35rem;
        height: 3.35rem;
    }

    .auth-page-login .auth-brand-kicker {
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0;
        color: rgba(255, 255, 255, 0.88);
    }

    .auth-page-login .auth-brand-title {
        max-width: 22rem;
        font-size: 0.98rem;
        font-weight: 600;
        line-height: 1.3;
    }

    .auth-page-login .auth-hero-title {
        max-width: 17rem;
        font-size: clamp(4rem, 5vw, 5rem);
        font-weight: 700;
        line-height: 0.94;
        letter-spacing: -0.06em;
    }

    .auth-page-login .auth-hero-summary {
        margin-top: 1.2rem;
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.4;
    }

    .auth-page-login .auth-hero-text {
        margin-top: 0.95rem;
        max-width: 31rem;
        font-size: 0.96rem;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.9);
    }

    .auth-page-login .auth-info-list {
        max-width: 34rem;
        gap: 1rem;
        margin-top: 3rem;
    }

    .auth-page-login .auth-info-item {
        min-height: 4.1rem;
        padding: 1rem 1.35rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.11);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
        font-size: 0.94rem;
        font-weight: 600;
    }

    .auth-page-login .auth-info-icon {
        width: 2.65rem;
        height: 2.65rem;
        background: rgba(255, 255, 255, 0.16);
    }

    .auth-page-login .auth-side {
        padding: 2.5rem 4rem 2rem;
    }

    .auth-page-login .auth-side::before {
        display: none;
    }

    .auth-page-login .auth-stack {
        max-width: 30.5rem;
        gap: 1rem;
    }

    .auth-page-login .auth-card {
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        padding: 0;
        margin-top: 0;
    }

    .auth-page-login .auth-panel-intro {
        gap: 0.45rem;
        margin-bottom: 0;
        padding-top: 0;
    }

    .auth-page-login .auth-panel-logo-wrap {
        width: 5.3rem;
        height: 5.3rem;
        margin-bottom: 0.5rem;
        background: rgba(255, 255, 255, 0.58);
        box-shadow: inset 0 0 0 1px rgba(248, 113, 113, 0.22);
    }

    .auth-page-login .auth-panel-logo {
        width: 3.9rem;
        height: 3.9rem;
    }

    .auth-page-login .auth-panel-copy {
        gap: 0.35rem;
        max-width: 25rem;
    }

    .auth-page-login .auth-panel-title {
        font-size: clamp(1.95rem, 2.6vw, 2.35rem);
        font-weight: 600;
        line-height: 1.08;
        letter-spacing: -0.04em;
        color: #1f2937;
    }

    .auth-page-login .auth-panel-subtitle,
    .auth-page-login .auth-panel-meta {
        font-size: 1rem;
        color: #6b7280;
    }

    .auth-page-login .auth-divider {
        margin: 1.65rem 0 1.75rem;
        background: rgba(15, 23, 42, 0.08);
    }

    .auth-page-login .auth-form-intro {
        margin-bottom: 1.55rem;
    }

    .auth-page-login .auth-form-title {
        font-size: clamp(2.25rem, 3vw, 2.8rem);
        font-weight: 600;
        line-height: 1.08;
        letter-spacing: -0.04em;
        color: #31343b;
    }

    .auth-page-login .auth-form-text {
        margin-top: 0.85rem;
        font-size: 0.95rem;
        line-height: 1.65;
        color: #6b7280;
    }

    .auth-page-login .auth-field + .auth-field {
        margin-top: 1rem;
    }

    .auth-page-login .auth-label {
        margin-bottom: 0.6rem;
        font-size: 0.96rem;
        font-weight: 700;
        color: #111827;
    }

    .auth-page-login .auth-input-icon {
        left: 1.05rem;
        width: 1.2rem;
        height: 1.2rem;
        color: #9ca3af;
    }

    .auth-page-login .auth-input {
        min-height: 3.55rem;
        border: 1px solid #dbe2ea;
        border-radius: 1rem;
        background: rgba(245, 247, 252, 0.92);
        padding: 0.8rem 3.2rem 0.8rem 1rem;
        font-size: 0.95rem;
        color: #374151;
        box-shadow: none;
    }

    .auth-page-login .auth-input-wrap .auth-input-icon + .auth-input {
        padding-left: 3.15rem;
    }

    .auth-page-login .auth-input:focus {
        border-color: #cfd8e3;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08);
        background: #ffffff;
    }

    .auth-page-login .auth-input::placeholder {
        color: #94a3b8;
    }

    .auth-page-login .auth-toggle {
        right: 1rem;
        color: #a8afbf;
    }

    .auth-page-login .auth-row {
        margin-top: 0.95rem;
        margin-bottom: 1.25rem;
    }

    .auth-page-login .auth-remember {
        gap: 0.6rem;
        font-size: 0.95rem;
        color: #6b7280;
    }

    .auth-page-login .auth-link {
        font-size: 0.95rem;
        font-weight: 600;
        color: #c0262d;
    }

    .auth-page-login .auth-submit {
        min-height: 3.3rem;
        padding: 0.85rem 1rem;
        border-radius: 1rem;
        font-size: 1rem;
        box-shadow: 0 10px 28px rgba(239, 68, 68, 0.24);
    }

    .auth-page-login .auth-submit.full {
        margin-top: 0;
    }

    .auth-page-login .auth-secondary-action,
    .auth-page-login .auth-back-link {
        min-height: 3.15rem;
        margin-top: 0.85rem;
        border-radius: 1rem;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .auth-page-login .auth-secondary-action {
        background: rgba(255, 255, 255, 0.45);
        border-color: #d9dee8;
        color: #3f3f46;
    }

    .auth-page-login .auth-support-note {
        margin-top: 1.15rem;
        font-size: 0.95rem;
        color: #6b7280;
    }

    .auth-page-login .auth-support-highlight,
    .auth-page-login .auth-support-note .auth-link {
        color: #27272a;
        font-weight: 700;
    }

    .auth-page-login .auth-back-link {
        border-color: #f2d6d9;
        background: rgba(255, 250, 250, 0.55);
        color: #b42318;
    }

    .auth-page-login .auth-footer-note {
        margin-top: 1.45rem;
        font-size: 0.83rem;
        color: #8f93a3;
    }

    /* ─── THEME TOGGLE BUTTON ─────────────────────────── */
    .auth-theme-toggle {
        display: none !important;
    }



    .auth-theme-toggle:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    .auth-theme-toggle svg {
        width: 1.4rem;
        height: 1.4rem;
    }

    .auth-theme-toggle .sun-icon {
        display: none;
    }

    html.dark .auth-theme-toggle .moon-icon {
        display: none;
    }

    html.dark .auth-theme-toggle .sun-icon {
        display: block;
    }
</style>

<!-- Theme Toggle Button -->
<button id="authThemeToggle" class="auth-theme-toggle" title="Toggle Dark Mode" aria-label="Toggle Dark Mode">
    <svg class="moon-icon" fill="currentColor" viewBox="0 0 20 20">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
    </svg>
    <svg class="sun-icon" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v2a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l1.414 1.414a1 1 0 001.414-1.414l-1.414-1.414a1 1 0 00-1.414 1.414zm2.828-2.828l1.414-1.414a1 1 0 00-1.414-1.414l-1.414 1.414a1 1 0 001.414 1.414zm4.242-4.242l1.414 1.414a1 1 0 01-1.414 1.414l-1.414-1.414a1 1 0 011.414-1.414zM3.464 3.464a1 1 0 00-1.414 1.414l1.414 1.414a1 1 0 001.414-1.414L3.464 3.464zm2.828 2.828l-1.414-1.414a1 1 0 00-1.414 1.414l1.414 1.414a1 1 0 001.414-1.414zm0 5.656l-1.414 1.414a1 1 0 01-1.414-1.414l1.414-1.414a1 1 0 011.414 1.414zM10 15a1 1 0 011 1v2a1 1 0 11-2 0v-2a1 1 0 011-1z" clip-rule="evenodd"></path>
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('authThemeToggle');
        
        // Initialize theme from localStorage
        const isDarkMode = localStorage.getItem('theme') === 'dark';
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Toggle theme
        themeToggle.addEventListener('click', function() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    });
</script>
