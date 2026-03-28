@include('partials.public-page-theme')

<style>
    .auth-stage {
        position: relative;
    }

    .auth-hero-panel {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background:
            radial-gradient(circle at top right, rgba(252, 165, 165, 0.22), transparent 34%),
            linear-gradient(135deg, rgba(127, 29, 29, 0.96) 0%, rgba(185, 28, 28, 0.94) 42%, rgba(15, 23, 42, 0.98) 100%);
        box-shadow: 0 28px 80px rgba(127, 29, 29, 0.24);
    }

    .auth-hero-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 26px 26px;
        mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.5), transparent 84%);
        opacity: 0.55;
    }

    .dark .auth-hero-panel {
        border-color: rgba(248, 113, 113, 0.18);
        box-shadow: 0 30px 80px rgba(2, 6, 23, 0.48);
    }

    .auth-hero-glow {
        position: absolute;
        border-radius: 9999px;
        filter: blur(18px);
        opacity: 0.55;
        pointer-events: none;
    }

    .auth-spotlight-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.16);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(14px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .auth-spotlight-card::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.14), transparent 48%);
    }

    .auth-form-panel {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.94) 0%, rgba(254, 242, 242, 0.88) 52%, rgba(255, 255, 255, 0.94) 100%);
    }

    .dark .auth-form-panel {
        border-color: rgba(127, 29, 29, 0.34);
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(69, 10, 10, 0.18) 46%, rgba(15, 23, 42, 0.95) 100%);
    }

    .auth-input-icon {
        pointer-events: none;
    }

    .auth-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(239, 68, 68, 0.28), transparent);
    }
</style>
