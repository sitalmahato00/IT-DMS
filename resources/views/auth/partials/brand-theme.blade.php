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
        grid-template-columns: minmax(0, 1.2fr) minmax(340px, 0.65fr);
    }

    /* ─── LEFT HERO PANEL ────────────────────────────── */
    .auth-hero {
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
        height: 100%;
        padding: clamp(2rem, 5vw, 4rem);
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
        gap: 0.9rem;
        margin-bottom: 2.2rem;
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
        font-size: clamp(2.3rem, 4vw, 3.5rem);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.04em;
        color: #fff;
    }


    /* Bold subtitle line */
    .auth-hero-summary {
        margin-top: 1rem;
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.35;
        color: #fff;
    }


    /* Lighter description */
    .auth-hero-text {
        margin-top: 0.4rem;
        max-width: 24rem;
        font-size: 0.9rem;
        line-height: 1.55;
        color: rgba(255, 255, 255, 0.80);
    }


    /* Contact info cards */
    .auth-info-list {
        display: grid;
        gap: 0.85rem;
        margin-top: 2rem;
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
        padding: 1rem 1.5rem;
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
        max-width: 28rem;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    /* ─── FORM CARD ───────────────────────────────────── */
    .auth-card {
        width: 100%;
        position: relative;
        z-index: 1;
        background: #ffffff;
        border-radius: 1.4rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 1rem 1.25rem 0.9rem;
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
            gap: 0.2rem;
            text-align: center;
            margin-bottom: 0.25rem;
            padding-top: 0.75rem;
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
        margin-bottom: 1.2rem;
        text-align: center;
    }

    .auth-form-title {
        margin: 0;
        font-size: 1.7rem;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.025em;
        color: #27272a;
    }

    .auth-form-text {
        margin: 0.5rem 0 0;
        color: #78716b;
        font-size: 0.88rem;
        line-height: 1.5;
    }

    /* ─── FORM FIELDS ──────────────────────────────────── */
    .auth-field + .auth-field {
        margin-top: 0.75rem;
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
        min-height: 2.75rem;
        border: 1px solid #f0e6e6;
        border-radius: 0.75rem;
        background: #fafaf8;
        padding: 0.55rem 3rem 0.55rem 1rem;
        font-size: 0.94rem;
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
        gap: 0.75rem;
        margin-top: 0.6rem;
        margin-bottom: 0.75rem;
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
        padding: 0.72rem 1rem;
        font-size: 1rem;
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
        margin: 0.8rem 0 0;
        text-align: center;
        color: #a39690;
        font-size: 0.74rem;
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
