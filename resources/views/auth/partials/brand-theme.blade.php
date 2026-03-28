<style>
    .auth-page {
        min-height: 100vh;
        background: #f3f5f7;
    }

    .auth-shell {
        display: grid;
        min-height: 100vh;
        grid-template-columns: minmax(0, 1.12fr) minmax(360px, 0.88fr);
    }

    .auth-hero {
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: clamp(3rem, 7vw, 6rem);
        background: linear-gradient(180deg, #1177ad 0%, #0f6fa3 100%);
        color: #fff;
    }

    .auth-hero::after {
        content: "";
        position: absolute;
        top: 0;
        right: -1px;
        width: 38%;
        height: 100%;
        background: #f3f5f7;
        clip-path: polygon(100% 0, 0 100%, 100% 100%);
    }

    .auth-hero-content {
        position: relative;
        z-index: 1;
        max-width: 30rem;
    }

    .auth-hero-title {
        margin: 0;
        font-size: clamp(2.5rem, 5vw, 4.25rem);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.04em;
    }

    .auth-hero-text {
        margin-top: 1rem;
        max-width: 24rem;
        font-size: 1rem;
        line-height: 1.75;
        color: rgba(255, 255, 255, 0.86);
    }

    .auth-side {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.5rem;
        background: #f3f5f7;
    }

    .auth-stack {
        width: 100%;
        max-width: 24rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.25rem;
    }

    .auth-emblem {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 5.5rem;
        height: 5.5rem;
        border: 4px solid #1177ad;
        border-radius: 9999px;
        background: #fff;
        color: #1177ad;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .auth-emblem-mark {
        font-size: 2.2rem;
        font-weight: 800;
        letter-spacing: -0.08em;
    }

    .auth-card {
        width: 100%;
        background: #fff;
        border: 1px solid #dbe2ea;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        padding: 1.35rem;
    }

    .auth-alert {
        width: 100%;
        border: 1px solid #fecaca;
        background: #fef2f2;
        padding: 0.85rem 1rem;
        font-size: 0.9rem;
        color: #b91c1c;
    }

    .auth-status {
        width: 100%;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        padding: 0.85rem 1rem;
        font-size: 0.9rem;
        color: #15803d;
    }

    .auth-field + .auth-field {
        margin-top: 1rem;
    }

    .auth-label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
    }

    .auth-input-wrap {
        position: relative;
    }

    .auth-input {
        width: 100%;
        min-height: 2.7rem;
        border: 1px solid #cfd8e3;
        background: #fff;
        padding: 0.65rem 0.85rem;
        font-size: 0.95rem;
        color: #0f172a;
        transition: border-color 180ms ease, box-shadow 180ms ease;
    }

    .auth-input:focus {
        outline: none;
        border-color: #1177ad;
        box-shadow: 0 0 0 3px rgba(17, 119, 173, 0.15);
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
        color: #64748b;
        cursor: pointer;
    }

    .auth-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .auth-remember {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.9rem;
        color: #475569;
    }

    .auth-link {
        color: #1177ad;
        text-decoration: none;
        transition: color 160ms ease;
    }

    .auth-link:hover {
        color: #0f5f8c;
    }

    .auth-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        background: #1177ad;
        color: #fff;
        padding: 0.7rem 1rem;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 160ms ease, transform 160ms ease;
    }

    .auth-submit:hover {
        background: #0f6a9a;
        transform: translateY(-1px);
    }

    .auth-submit.full {
        width: 100%;
        margin-top: 1rem;
    }

    .auth-helper-links {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        font-size: 0.92rem;
        color: #475569;
    }

    .auth-helper-links a {
        color: #475569;
        text-decoration: none;
    }

    .auth-helper-links a:hover {
        color: #1177ad;
    }

    .auth-inline-note {
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #475569;
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
    }

    @media (max-width: 640px) {
        .auth-card,
        .auth-alert,
        .auth-status {
            padding: 1rem;
        }

        .auth-row {
            flex-direction: column;
            align-items: stretch;
        }

        .auth-submit {
            width: 100%;
        }
    }

    html.dark .auth-page,
    html.dark .auth-side {
        background: #f3f5f7 !important;
    }

    html.dark .auth-card {
        background: #fff !important;
        border-color: #dbe2ea !important;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08) !important;
    }

    html.dark .auth-label,
    html.dark .auth-remember,
    html.dark .auth-inline-note,
    html.dark .auth-helper-links,
    html.dark .auth-helper-links a {
        color: #475569 !important;
    }

    html.dark .auth-input {
        background: #fff !important;
        color: #0f172a !important;
        border-color: #cfd8e3 !important;
    }

    html.dark .auth-toggle {
        color: #64748b !important;
    }

    html.dark .auth-helper-links a:hover,
    html.dark .auth-link:hover {
        color: #1177ad !important;
    }
</style>
