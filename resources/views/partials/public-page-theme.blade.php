<style>
    .brand-page-bg {
        position: relative;
        overflow: hidden;
        background: linear-gradient(180deg, #fff5f5 0%, #fff1f2 24%, #fffaf9 62%, #fff 100%);
    }

    .dark .brand-page-bg {
        background: linear-gradient(180deg, #020617 0%, #111827 35%, #0f172a 100%);
    }

    .brand-page-shell {
        max-width: 88rem;
    }

    .brand-page-orb {
        position: absolute;
        border-radius: 9999px;
        filter: blur(12px);
        opacity: 0.5;
        animation: brandFloat 10s ease-in-out infinite;
        pointer-events: none;
    }

    .brand-page-panel {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.6);
        background: rgba(255, 255, 255, 0.82);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
        backdrop-filter: blur(14px);
    }

    .brand-page-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent 46%);
    }

    .dark .brand-page-panel {
        border-color: rgba(127, 29, 29, 0.28);
        background: rgba(15, 23, 42, 0.8);
        box-shadow: 0 26px 70px rgba(2, 6, 23, 0.5);
    }

    .brand-page-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        border-radius: 9999px;
        padding: 0.55rem 1rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #b91c1c;
        background: #fee2e2;
        box-shadow: inset 0 0 0 1px #fecaca;
    }

    .dark .brand-page-chip {
        color: #fca5a5;
        background: rgba(127, 29, 29, 0.35);
        box-shadow: inset 0 0 0 1px rgba(127, 29, 29, 0.55);
    }

    .brand-page-title {
        letter-spacing: -0.03em;
    }

    .brand-page-input,
    .brand-page-select {
        border-radius: 1rem;
        border: 1px solid #fecaca;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
    }

    .brand-page-input:focus,
    .brand-page-select:focus {
        border-color: #ef4444;
        --tw-ring-color: rgb(248 113 113 / 0.35);
    }

    .dark .brand-page-input,
    .dark .brand-page-select {
        border-color: rgba(127, 29, 29, 0.4);
        background: rgba(15, 23, 42, 0.86);
    }

    .brand-page-cta {
        position: relative;
        overflow: hidden;
    }

    .brand-page-cta::after {
        content: "";
        position: absolute;
        inset: -120% auto auto -30%;
        width: 36%;
        height: 260%;
        transform: rotate(20deg);
        background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.45), transparent);
        opacity: 0;
        transition: opacity 200ms ease, transform 320ms ease;
    }

    .brand-page-cta:hover::after {
        opacity: 1;
        transform: translateX(240%) rotate(20deg);
    }

    .brand-page-card {
        background: linear-gradient(135deg, #fef2f2 0%, #fff 55%, #fee2e2 100%);
        border: 1px solid #fecaca;
        box-shadow: 0 22px 50px rgba(15, 23, 42, 0.08);
        transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
    }

    .brand-page-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 28px 56px rgba(15, 23, 42, 0.12);
    }

    .dark .brand-page-card {
        background: linear-gradient(135deg, rgba(69, 10, 10, 0.45) 0%, rgba(15, 23, 42, 0.92) 55%, rgba(127, 29, 29, 0.22) 100%);
        border-color: rgba(127, 29, 29, 0.35);
    }

    .brand-page-soft {
        background: linear-gradient(135deg, #fff 0%, #fef2f2 45%, #fff 100%);
    }

    .dark .brand-page-soft {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.94) 0%, rgba(69, 10, 10, 0.25) 45%, rgba(15, 23, 42, 0.92) 100%);
    }

    @keyframes brandFloat {
        0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
        50% { transform: translate3d(0, -14px, 0) scale(1.05); }
    }
</style>

