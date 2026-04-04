const MOBILE_BREAKPOINT = 1024;
const MOBILE_CARD_BREAKPOINT = 640;
const AUTHENTICATED_SHELLS = new Set(['admin', 'teacher', 'student', 'parent']);
const DYNAMIC_HEAD_SELECTOR = 'style:not([data-mobile-static-style])';

let deferredInstallPrompt = null;
let serviceWorkerRegistration = null;
let shellAbortController = null;
let shellCleanupCallbacks = [];
let navigationSequence = 0;
let shellEnhancementActive = false;

const prefetchedPages = new Map();

let pageRuntimeState = createPageRuntimeState();

function createPageRuntimeState() {
    return {
        listeners: [],
        timeouts: new Set(),
        intervals: new Set(),
        animationFrames: new Set(),
    };
}

function cleanupPageRuntimeState() {
    pageRuntimeState.listeners.forEach(({ target, type, listener, options }) => {
        try {
            target.removeEventListener(type, listener, options);
        } catch (error) {
            // Ignore listener cleanup errors from detached nodes.
        }
    });

    pageRuntimeState.timeouts.forEach((id) => window.clearTimeout(id));
    pageRuntimeState.intervals.forEach((id) => window.clearInterval(id));
    pageRuntimeState.animationFrames.forEach((id) => window.cancelAnimationFrame(id));

    pageRuntimeState = createPageRuntimeState();
}

function resetShellBindings() {
    shellAbortController?.abort();
    shellCleanupCallbacks.forEach((cleanup) => {
        try {
            cleanup();
        } catch (error) {
            console.warn('Mobile shell cleanup failed:', error);
        }
    });

    shellCleanupCallbacks = [];
}

function registerShellCleanup(cleanup) {
    shellCleanupCallbacks.push(cleanup);
}

function getShellRoot(doc = document) {
    return doc.querySelector('[data-mobile-shell-root]');
}

function getMainContent(doc = document) {
    return doc.querySelector('[data-mobile-main]');
}

function getScrollContainer(doc = document) {
    return getMainContent(doc) || doc.scrollingElement || doc.documentElement || doc.body;
}

function getScrollTop(container = getScrollContainer()) {
    if (!container) {
        return 0;
    }

    if (container === document.body || container === document.documentElement || container === document.scrollingElement) {
        return window.scrollY || container.scrollTop || 0;
    }

    return container.scrollTop || 0;
}

function scrollContainerToTop(container = getScrollContainer()) {
    if (container && typeof container.scrollTo === 'function') {
        container.scrollTo({ top: 0, left: 0, behavior: 'auto' });
        return;
    }

    window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
}

function getCurrentShell() {
    return document.body.dataset.mobileShell || '';
}

function isAuthenticatedShell(shell = getCurrentShell()) {
    return AUTHENTICATED_SHELLS.has(shell);
}

function isStandaloneDisplayMode() {
    return window.matchMedia?.('(display-mode: standalone)').matches === true || window.navigator.standalone === true;
}

function isMobileViewport() {
    return window.innerWidth < MOBILE_BREAKPOINT;
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function invokeListener(listener, event) {
    if (typeof listener === 'function') {
        listener.call(document, event);
        return;
    }

    if (listener && typeof listener.handleEvent === 'function') {
        listener.handleEvent(event);
    }
}

function markCurrentHeadStylesAsManaged() {
    document.head.querySelectorAll(DYNAMIC_HEAD_SELECTOR).forEach((node) => {
        if (node.hasAttribute('data-mobile-managed-head')) {
            return;
        }

        node.setAttribute('data-mobile-managed-head', '');
    });
}

function syncManagedHead(nextDocument) {
    document.head.querySelectorAll('[data-mobile-managed-head]').forEach((node) => node.remove());

    Array.from(nextDocument.head.querySelectorAll(DYNAMIC_HEAD_SELECTOR)).forEach((node) => {
        const clone = node.cloneNode(true);
        clone.setAttribute('data-mobile-managed-head', '');
        document.head.appendChild(clone);
    });
}

function syncBodyState(nextDocument) {
    const nextBody = nextDocument.body;

    document.body.className = nextBody.className;

    Array.from(document.body.attributes)
        .filter((attribute) => attribute.name.startsWith('data-mobile-'))
        .forEach((attribute) => document.body.removeAttribute(attribute.name));

    Array.from(nextBody.attributes)
        .filter((attribute) => attribute.name.startsWith('data-mobile-'))
        .forEach((attribute) => document.body.setAttribute(attribute.name, attribute.value));
}

function isSameOriginUrl(url) {
    try {
        return new URL(url, window.location.href).origin === window.location.origin;
    } catch (error) {
        return false;
    }
}

function shouldHandleLink(anchor) {
    if (!anchor) {
        return false;
    }

    const href = anchor.getAttribute('href');

    if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
        return false;
    }

    if (anchor.target && anchor.target !== '_self') {
        return false;
    }

    if (anchor.hasAttribute('download') || anchor.closest('[data-mobile-no-soft-nav]')) {
        return false;
    }

    return isSameOriginUrl(anchor.href);
}

function destroyChartsWithin(root) {
    if (!root || !window.Chart?.getChart) {
        return;
    }

    root.querySelectorAll('canvas').forEach((canvas) => {
        try {
            window.Chart.getChart(canvas)?.destroy();
        } catch (error) {
            // Ignore chart teardown errors from third-party pages.
        }
    });
}

function runWithTrackedPageRuntime(callback) {
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    const originalSetTimeout = window.setTimeout.bind(window);
    const originalSetInterval = window.setInterval.bind(window);
    const originalRequestAnimationFrame = window.requestAnimationFrame.bind(window);

    EventTarget.prototype.addEventListener = function addTrackedListener(type, listener, options) {
        if (this === document && type === 'DOMContentLoaded' && document.readyState !== 'loading') {
            invokeListener(listener, new Event('DOMContentLoaded'));
            return;
        }

        if (this === window && type === 'load' && document.readyState === 'complete') {
            invokeListener(listener, new Event('load'));
            return;
        }

        pageRuntimeState.listeners.push({ target: this, type, listener, options });
        return originalAddEventListener.call(this, type, listener, options);
    };

    window.setTimeout = function trackedSetTimeout(handler, timeout, ...args) {
        const id = originalSetTimeout(handler, timeout, ...args);
        pageRuntimeState.timeouts.add(id);
        return id;
    };

    window.setInterval = function trackedSetInterval(handler, timeout, ...args) {
        const id = originalSetInterval(handler, timeout, ...args);
        pageRuntimeState.intervals.add(id);
        return id;
    };

    window.requestAnimationFrame = function trackedRequestAnimationFrame(handler) {
        const id = originalRequestAnimationFrame(handler);
        pageRuntimeState.animationFrames.add(id);
        return id;
    };

    try {
        callback();
    } finally {
        EventTarget.prototype.addEventListener = originalAddEventListener;
        window.setTimeout = originalSetTimeout;
        window.setInterval = originalSetInterval;
        window.requestAnimationFrame = originalRequestAnimationFrame;
    }
}

function ensureNavigationProgress() {
    let progress = document.querySelector('.mobile-nav-progress');

    if (!progress) {
        progress = document.createElement('div');
        progress.className = 'mobile-nav-progress';
        document.body.appendChild(progress);
    }

    return progress;
}

function setNavigationProgress(active, completed = false) {
    const progress = ensureNavigationProgress();
    progress.classList.toggle('is-visible', active);
    progress.classList.toggle('is-complete', completed);
}

function ensureInstallSheet() {
    let sheet = document.querySelector('.mobile-install-sheet');

    if (sheet) {
        return sheet;
    }

    sheet = document.createElement('section');
    sheet.className = 'mobile-install-sheet';
    sheet.innerHTML = `
        <div class="mobile-install-sheet__title">Install IT-DMS</div>
        <p class="mobile-install-sheet__text">
            Add the system to your home screen for a faster, app-like experience and offline access to recent pages.
        </p>
        <div class="mobile-install-sheet__actions">
            <button type="button" class="mobile-install-sheet__button">Install</button>
            <button type="button" class="mobile-install-sheet__dismiss">Not now</button>
        </div>
    `;

    document.body.appendChild(sheet);
    return sheet;
}

function showInstallPrompt() {
    if (!deferredInstallPrompt || !isMobileViewport()) {
        return;
    }

    const sheet = ensureInstallSheet();
    const installButton = sheet.querySelector('.mobile-install-sheet__button');
    const dismissButton = sheet.querySelector('.mobile-install-sheet__dismiss');

    installButton.onclick = async () => {
        sheet.classList.remove('is-visible');

        try {
            deferredInstallPrompt.prompt();
            await deferredInstallPrompt.userChoice;
        } catch (error) {
            console.warn('Install prompt failed:', error);
        } finally {
            deferredInstallPrompt = null;
        }
    };

    dismissButton.onclick = () => {
        sheet.classList.remove('is-visible');
        sessionStorage.setItem('it-dms-install-dismissed', '1');
    };

    if (sessionStorage.getItem('it-dms-install-dismissed') !== '1') {
        sheet.classList.add('is-visible');
    }
}

function ensureUpdateToast() {
    let toast = document.querySelector('.mobile-update-toast');

    if (toast) {
        return toast;
    }

    toast = document.createElement('section');
    toast.className = 'mobile-update-toast';
    toast.innerHTML = `
        <div class="mobile-update-toast__title">Update available</div>
        <div class="mobile-update-toast__text">A fresher version of IT-DMS is ready to use.</div>
        <div class="mobile-update-toast__actions">
            <button type="button" data-mobile-update-apply>Refresh</button>
            <button type="button" data-mobile-update-dismiss>Later</button>
        </div>
    `;

    document.body.appendChild(toast);
    return toast;
}

function showUpdateToast(onRefresh) {
    const toast = ensureUpdateToast();
    const applyButton = toast.querySelector('[data-mobile-update-apply]');
    const dismissButton = toast.querySelector('[data-mobile-update-dismiss]');

    applyButton.onclick = () => {
        toast.classList.remove('is-visible');
        onRefresh?.();
    };

    dismissButton.onclick = () => {
        toast.classList.remove('is-visible');
    };

    toast.classList.add('is-visible');
}

async function notifyWithServiceWorker(title, body, url = window.location.href, tag = 'it-dms-mobile-alert') {
    if (!('Notification' in window) || Notification.permission !== 'granted') {
        return;
    }

    try {
        const registration = serviceWorkerRegistration || await navigator.serviceWorker.ready;
        registration.active?.postMessage({
            type: 'SHOW_NOTIFICATION',
            title,
            body,
            url,
            tag,
        });
    } catch (error) {
        console.warn('Notification dispatch failed:', error);
    }
}

async function maybeRequestNotificationPermission() {
    if (!('Notification' in window) || Notification.permission !== 'default') {
        return Notification.permission;
    }

    const permission = await Notification.requestPermission();

    if (permission === 'granted') {
        notifyWithServiceWorker(
            'Notifications enabled',
            'You will now see mobile alerts for new notices and dashboard updates while using IT-DMS.',
            window.location.href,
            'it-dms-notification-enabled'
        );
    }

    return permission;
}

function bindNotificationBridge() {
    const shell = getCurrentShell();
    const storageKey = `it-dms-unread:${shell}`;
    const header = document.querySelector('[data-mobile-app-header]') || document.body;

    const readUnreadCount = () => {
        const badge = document.getElementById('notifBadge');
        const count = parseInt((badge?.textContent || '0').trim(), 10);
        return Number.isFinite(count) ? count : 0;
    };

    const updateCount = () => {
        const current = readUnreadCount();
        const storedValue = sessionStorage.getItem(storageKey);

        if (storedValue === null) {
            sessionStorage.setItem(storageKey, String(current));
            return;
        }

        const previous = parseInt(storedValue, 10) || 0;

        if (current > previous && document.visibilityState === 'visible') {
            notifyWithServiceWorker(
                'New update in IT-DMS',
                `You have ${current} unread notification${current === 1 ? '' : 's'} in the ${shell} app.`,
                window.location.href,
                `it-dms-unread-${shell}`
            );
        }

        sessionStorage.setItem(storageKey, String(current));
    };

    updateCount();

    const observer = new MutationObserver(updateCount);
    observer.observe(header, { childList: true, subtree: true, characterData: true });
    registerShellCleanup(() => observer.disconnect());
}

async function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    try {
        serviceWorkerRegistration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

        if (serviceWorkerRegistration.waiting) {
            showUpdateToast(() => {
                serviceWorkerRegistration.waiting?.postMessage({ type: 'SKIP_WAITING' });
            });
        }

        serviceWorkerRegistration.addEventListener('updatefound', () => {
            const worker = serviceWorkerRegistration.installing;

            if (!worker) {
                return;
            }

            worker.addEventListener('statechange', () => {
                if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                    showUpdateToast(() => {
                        worker.postMessage({ type: 'SKIP_WAITING' });
                    });
                }
            });
        });

        navigator.serviceWorker.addEventListener('controllerchange', () => {
            window.location.reload();
        }, { once: true });
    } catch (error) {
        console.warn('Service worker registration failed:', error);
    }
}

function submitLocaleChange(locale) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/locale';

    const csrfToken = getCsrfToken();
    if (csrfToken) {
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = csrfToken;
        form.appendChild(csrfField);
    }

    const localeField = document.createElement('input');
    localeField.type = 'hidden';
    localeField.name = 'locale';
    localeField.value = locale;
    form.appendChild(localeField);

    document.body.appendChild(form);
    form.submit();
}

function bindHeaderControls(signal) {
    const localeSelect = document.getElementById('locale-select');
    const notifToggle = document.getElementById('notifToggle');
    const notifDropdown = document.getElementById('notifDropdown');
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    const closeDropdowns = () => {
        notifDropdown?.classList.add('hidden');
        profileDropdown?.classList.add('hidden');
    };

    localeSelect?.addEventListener('change', function handleLocaleChange() {
        submitLocaleChange(this.value);
    }, { signal });

    profileToggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        profileDropdown?.classList.toggle('hidden');
        notifDropdown?.classList.add('hidden');
    }, { signal });

    notifToggle?.addEventListener('click', async (event) => {
        event.stopPropagation();
        notifDropdown?.classList.toggle('hidden');
        profileDropdown?.classList.add('hidden');
        await maybeRequestNotificationPermission();
    }, { signal });

    document.addEventListener('click', (event) => {
        if (profileToggle && profileDropdown && !profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
            profileDropdown.classList.add('hidden');
        }

        if (notifToggle && notifDropdown && !notifToggle.contains(event.target) && !notifDropdown.contains(event.target)) {
            notifDropdown.classList.add('hidden');
        }
    }, { signal });

    markAllReadBtn?.addEventListener('click', async (event) => {
        event.preventDefault();

        const url = markAllReadBtn.dataset.markAllReadUrl;
        if (!url) {
            return;
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ id: 'all' }),
            });

            if (!response.ok) {
                return;
            }

            document.getElementById('notifBadge')?.remove();
            notifDropdown?.querySelectorAll('.bg-red-50\\/50, .bg-red-50\\/70, .bg-blue-50\\/50')
                .forEach((node) => node.classList.remove('bg-red-50/50', 'bg-red-50/70', 'bg-blue-50/50'));
        } catch (error) {
            console.warn('Failed to mark notifications as read:', error);
        } finally {
            closeDropdowns();
        }
    }, { signal });
}

function bindSidebarControls(signal) {
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const mobileToggles = [
        document.getElementById('mobileSidebarToggle'),
        document.getElementById('sidebarToggle'),
        document.querySelector('[data-mobile-drawer-toggle]'),
    ].filter(Boolean);

    if (!sidebar) {
        return;
    }

    let hideTimer = null;
    const drawerCloseDuration = 320;

    const clearHideTimer = () => {
        if (hideTimer) {
            window.clearTimeout(hideTimer);
            hideTimer = null;
        }
    };

    const openSidebar = () => {
        clearHideTimer();
        sidebar.classList.remove('hidden', '-translate-x-full');
        sidebar.classList.add('flex');
        sidebarBackdrop?.classList.remove('hidden');
        sidebarBackdrop?.classList.remove('is-visible');
        document.body.classList.add('overflow-hidden');
        sidebar.dataset.mobileDrawer = 'open';

        window.requestAnimationFrame(() => {
            sidebar.classList.add('is-drawer-visible');
            sidebarBackdrop?.classList.add('is-visible');
        });
    };

    const closeSidebar = (immediate = false) => {
        clearHideTimer();
        sidebar.classList.remove('is-drawer-visible');
        sidebar.classList.add('-translate-x-full');
        sidebarBackdrop?.classList.remove('is-visible');
        document.body.classList.remove('overflow-hidden');
        sidebar.dataset.mobileDrawer = 'closed';

        if (!isMobileViewport()) {
            sidebar.classList.remove('hidden');
            sidebarBackdrop?.classList.add('hidden');
            return;
        }

        if (immediate) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
            sidebarBackdrop?.classList.add('hidden');
            return;
        }

        hideTimer = window.setTimeout(() => {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
            sidebarBackdrop?.classList.add('hidden');
        }, drawerCloseDuration);
    };

    const syncSidebarForViewport = () => {
        clearHideTimer();

        if (isMobileViewport()) {
            closeSidebar(true);
            return;
        }

        sidebar.classList.remove('hidden', '-translate-x-full', 'is-drawer-visible');
        sidebar.classList.remove('flex');
        sidebar.classList.remove('sidebar-collapsed');
        sidebarBackdrop?.classList.add('hidden');
        sidebarBackdrop?.classList.remove('is-visible');
        document.body.classList.remove('overflow-hidden');
        sidebar.dataset.mobileDrawer = 'desktop';
    };

    syncSidebarForViewport();

    mobileToggles.forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (sidebar.dataset.mobileDrawer === 'open') {
                closeSidebar();
            } else {
                openSidebar();
            }
        }, { signal });
    });

    sidebarBackdrop?.addEventListener('click', closeSidebar, { signal });

    document.addEventListener('pointerdown', (event) => {
        if (!isMobileViewport() || sidebar.dataset.mobileDrawer !== 'open') {
            return;
        }

        const clickedToggle = mobileToggles.some((toggle) => toggle.contains(event.target));
        if (clickedToggle || sidebar.contains(event.target)) {
            return;
        }

        closeSidebar();
    }, { signal });

    sidebar.querySelectorAll('a, button[type="submit"]').forEach((node) => {
        node.addEventListener('click', () => {
            if (isMobileViewport()) {
                closeSidebar();
            }
        }, { signal });
    });

    window.addEventListener('resize', syncSidebarForViewport, { signal });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    }, { signal });
}

function hydrateResponsiveTables(root = document) {
    root.querySelectorAll('table').forEach((table) => {
        if (table.closest('.mobile-table-shell')) {
            updateTableCardMode(table.closest('.mobile-table-shell'));
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'mobile-table-shell';
        table.parentNode?.insertBefore(wrapper, table);
        wrapper.appendChild(table);
        updateTableCardMode(wrapper);
    });
}

function updateTableCardMode(wrapper) {
    const table = wrapper?.querySelector('table');
    if (!table) {
        return;
    }

    const headers = Array.from(table.querySelectorAll('thead th')).map((cell) => (cell.textContent || '').trim());
    const firstRow = table.querySelector('tbody tr');
    const cellCount = headers.length || firstRow?.children.length || 0;
    const enableCardMode = window.innerWidth < MOBILE_CARD_BREAKPOINT && cellCount >= 4;

    wrapper.classList.toggle('is-card-mode', enableCardMode);

    table.querySelectorAll('tbody tr').forEach((row) => {
        Array.from(row.children).forEach((cell, index) => {
            if (cell instanceof HTMLElement) {
                cell.dataset.mobileLabel = headers[index] || `Column ${index + 1}`;
            }
        });
    });
}

function lazyLoadImages(root = document) {
    root.querySelectorAll('img').forEach((image) => {
        if (!image.hasAttribute('loading')) {
            image.setAttribute('loading', 'lazy');
        }

        if (!image.hasAttribute('decoding')) {
            image.setAttribute('decoding', 'async');
        }
    });
}

function ensurePullIndicator() {
    let indicator = document.querySelector('.mobile-pull-indicator');

    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'mobile-pull-indicator';
        indicator.innerHTML = `
            <span class="mobile-pull-indicator__dot" aria-hidden="true"></span>
            <span class="mobile-pull-indicator__label">Pull to refresh</span>
        `;
        document.body.appendChild(indicator);
    }

    return indicator;
}

function bindPullToRefresh(signal) {
    const routeName = document.body.dataset.mobileRoute || '';
    if (!isMobileViewport() || !/dashboard/i.test(routeName)) {
        return;
    }

    const indicator = ensurePullIndicator();
    const label = indicator.querySelector('.mobile-pull-indicator__label');
    const scrollContainer = getScrollContainer();
    let startY = 0;
    let active = false;
    let distance = 0;

    document.addEventListener('touchstart', (event) => {
        if (getScrollTop(scrollContainer) > 0 || event.touches.length !== 1) {
            active = false;
            return;
        }

        startY = event.touches[0].clientY;
        active = true;
        distance = 0;
    }, { signal, passive: true });

    document.addEventListener('touchmove', (event) => {
        if (!active) {
            return;
        }

        distance = Math.max(0, event.touches[0].clientY - startY);
        if (distance < 8) {
            return;
        }

        indicator.classList.add('is-visible');
        label.textContent = distance > 84 ? 'Release to refresh' : 'Pull to refresh';
    }, { signal, passive: true });

    document.addEventListener('touchend', () => {
        if (!active) {
            return;
        }

        active = false;

        if (distance > 84) {
            label.textContent = 'Refreshing...';
            softNavigate(window.location.href, { historyMode: 'replace', preserveScroll: true });
        }

        distance = 0;
        window.setTimeout(() => {
            indicator.classList.remove('is-visible');
            label.textContent = 'Pull to refresh';
        }, 180);
    }, { signal });
}

function bindModalSwipeDismiss(signal) {
    let trackedPanel = null;
    let trackedOverlay = null;
    let startY = 0;
    let startX = 0;

    const findOverlay = (target) => target.closest('.fixed.inset-0, [role="dialog"]');
    const findPanel = (overlay) => overlay?.querySelector('.relative.mx-auto, .bg-white.rounded-lg, .bg-white.rounded-xl, .bg-white.rounded-2xl');

    document.addEventListener('touchstart', (event) => {
        trackedOverlay = findOverlay(event.target);
        trackedPanel = findPanel(trackedOverlay);

        if (!trackedOverlay || !trackedPanel || trackedOverlay.classList.contains('hidden')) {
            trackedOverlay = null;
            trackedPanel = null;
            return;
        }

        startY = event.touches[0].clientY;
        startX = event.touches[0].clientX;
    }, { signal, passive: true });

    document.addEventListener('touchend', (event) => {
        if (!trackedOverlay || !trackedPanel) {
            return;
        }

        const endY = event.changedTouches[0].clientY;
        const endX = event.changedTouches[0].clientX;
        const deltaY = endY - startY;
        const deltaX = Math.abs(endX - startX);

        if (deltaY > 96 && deltaY > deltaX) {
            const closeTrigger = trackedOverlay.querySelector('[data-modal-close], [aria-label=\"Close\"], [aria-label=\"close\"], .bi-x-lg, .bi-x');
            if (closeTrigger instanceof HTMLElement) {
                closeTrigger.click();
            } else {
                trackedOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        trackedOverlay = null;
        trackedPanel = null;
    }, { signal, passive: true });
}

function trimPrefetchCache() {
    while (prefetchedPages.size > 12) {
        const oldestKey = prefetchedPages.keys().next().value;
        prefetchedPages.delete(oldestKey);
    }
}

function prefetchPage(url) {
    const absoluteUrl = new URL(url, window.location.href).href;

    if (prefetchedPages.has(absoluteUrl)) {
        return prefetchedPages.get(absoluteUrl);
    }

    const request = fetch(absoluteUrl, {
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'text/html,application/xhtml+xml',
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Failed to prefetch ${absoluteUrl}`);
            }

            return response.text();
        })
        .catch((error) => {
            prefetchedPages.delete(absoluteUrl);
            throw error;
        });

    prefetchedPages.set(absoluteUrl, request);
    trimPrefetchCache();
    return request;
}

async function fetchPageDocument(url) {
    const html = await prefetchPage(url);
    return new DOMParser().parseFromString(html, 'text/html');
}

function buildGetFormUrl(form) {
    const url = new URL(form.getAttribute('action') || window.location.href, window.location.href);
    const formData = new FormData(form);

    url.search = '';

    formData.forEach((value, key) => {
        if (value === null || value === undefined) {
            return;
        }

        const normalized = String(value).trim();
        if (normalized === '') {
            return;
        }

        url.searchParams.append(key, normalized);
    });

    return url.href;
}

function shouldHandleForm(form) {
    if (!form || !isAuthenticatedShell()) {
        return false;
    }

    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    if (method !== 'GET') {
        return false;
    }

    if (form.target && form.target !== '_self') {
        return false;
    }

    if (form.closest('[data-mobile-no-soft-nav]')) {
        return false;
    }

    return isSameOriginUrl(form.getAttribute('action') || window.location.href);
}

async function executePageScripts(nextDocument) {
    const scripts = Array.from(nextDocument.querySelectorAll('script')).filter((script) => {
        if (script.hasAttribute('data-mobile-static-script')) {
            return false;
        }

        const src = script.getAttribute('src') || '';
        return !src.includes('/build/');
    });

    for (const script of scripts) {
        if (script.src) {
            const absoluteSrc = new URL(script.src, window.location.href).href;

            if (document.querySelector(`script[src="${absoluteSrc}"]`)) {
                continue;
            }

            await new Promise((resolve, reject) => {
                const runtimeScript = document.createElement('script');
                runtimeScript.src = absoluteSrc;
                runtimeScript.async = false;
                runtimeScript.onload = () => resolve();
                runtimeScript.onerror = reject;
                document.body.appendChild(runtimeScript);
            });

            continue;
        }

        runWithTrackedPageRuntime(() => {
            const runtimeScript = document.createElement('script');
            if (script.type) {
                runtimeScript.type = script.type;
            }

            runtimeScript.textContent = script.textContent;
            document.body.appendChild(runtimeScript);
            runtimeScript.remove();
        });
    }
}

function restoreScrollPosition(url, preserveScroll = false) {
    if (preserveScroll) {
        return;
    }

    const targetUrl = new URL(url, window.location.href);

    if (targetUrl.hash) {
        const targetId = decodeURIComponent(targetUrl.hash.replace(/^#/, ''));
        const target = document.getElementById(targetId);
        target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }

    scrollContainerToTop(getScrollContainer());
}

function swapShell(nextDocument) {
    const currentRoot = getShellRoot();
    const nextRoot = getShellRoot(nextDocument);

    if (!currentRoot || !nextRoot) {
        throw new Error('Mobile shell root not found.');
    }

    destroyChartsWithin(currentRoot);
    cleanupPageRuntimeState();
    syncManagedHead(nextDocument);
    syncBodyState(nextDocument);
    document.title = nextDocument.title;
    document.documentElement.lang = nextDocument.documentElement.lang || document.documentElement.lang;

    const importedRoot = document.importNode(nextRoot, true);
    currentRoot.replaceWith(importedRoot);

    if (window.Alpine?.initTree) {
        window.Alpine.initTree(importedRoot);
    }

    return importedRoot;
}

async function softNavigate(url, options = {}) {
    if (!isAuthenticatedShell()) {
        window.location.assign(url);
        return;
    }

    const historyMode = options.historyMode || 'push';
    const preserveScroll = options.preserveScroll === true;
    const requestId = ++navigationSequence;

    setNavigationProgress(true, false);

    try {
        const nextDocument = await fetchPageDocument(url);

        if (requestId !== navigationSequence) {
            return;
        }

        const nextShell = nextDocument.body.dataset.mobileShell || '';
        if (!isAuthenticatedShell(nextShell) || nextShell !== getCurrentShell() || !getShellRoot(nextDocument)) {
            window.location.assign(url);
            return;
        }

        const commitSwap = () => {
            swapShell(nextDocument);
        };

        if (typeof document.startViewTransition === 'function') {
            const transition = document.startViewTransition(commitSwap);
            await transition.finished;
        } else {
            commitSwap();
        }

        if (historyMode === 'push') {
            window.history.pushState({ mobile: true }, '', url);
        } else if (historyMode === 'replace') {
            window.history.replaceState({ mobile: true }, '', url);
        }

        initializeShellBindings();
        await executePageScripts(nextDocument);
        hydrateResponsiveTables(document);
        lazyLoadImages(document);
        await window.reinitializeBaseUi?.(document);
        restoreScrollPosition(url, preserveScroll);
        document.dispatchEvent(new CustomEvent('itdms:soft-navigated', { detail: { url } }));
    } catch (error) {
        console.warn('Soft navigation failed, falling back to full load:', error);
        window.location.assign(url);
        return;
    } finally {
        setNavigationProgress(true, true);
        window.setTimeout(() => setNavigationProgress(false, false), 220);
    }
}

function initializeShellBindings() {
    resetShellBindings();
    shellAbortController = new AbortController();
    const { signal } = shellAbortController;

    bindHeaderControls(signal);
    bindSidebarControls(signal);
    bindPullToRefresh(signal);
    bindModalSwipeDismiss(signal);
    bindNotificationBridge();
    hydrateResponsiveTables(document);
    lazyLoadImages(document);
    window.reinitializeThemeToggle?.(document);

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const anchor = event.target.closest('a[href]');
        if (!shouldHandleLink(anchor)) {
            return;
        }

        const targetUrl = new URL(anchor.href, window.location.href);
        const currentUrl = new URL(window.location.href);

        if (targetUrl.pathname === currentUrl.pathname && targetUrl.search === currentUrl.search && targetUrl.hash) {
            return;
        }

        event.preventDefault();
        softNavigate(targetUrl.href);
    }, { signal });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !shouldHandleForm(form)) {
            return;
        }

        event.preventDefault();
        softNavigate(buildGetFormUrl(form));
    }, { signal });

    const prefetchHandler = (event) => {
        const anchor = event.target.closest?.('a[href]');
        if (shouldHandleLink(anchor)) {
            prefetchPage(anchor.href).catch(() => {
                // Ignore prefetch failures and fall back to live navigation.
            });
        }
    };

    document.addEventListener('pointerdown', prefetchHandler, { signal, passive: true });
    document.addEventListener('touchstart', prefetchHandler, { signal, passive: true });
    document.addEventListener('mouseover', prefetchHandler, { signal, passive: true });

    window.addEventListener('resize', () => {
        hydrateResponsiveTables(document);
        if (deferredInstallPrompt) {
            showInstallPrompt();
        }
    }, { signal });

    window.addEventListener('popstate', () => {
        softNavigate(window.location.href, {
            historyMode: 'replace',
            preserveScroll: true,
        });
    }, { signal });
}

async function syncShellEnhancement(forceReinitialize = false) {
    const shouldEnhance = isAuthenticatedShell() && (isMobileViewport() || isStandaloneDisplayMode());

    if (!shouldEnhance) {
        if (shellEnhancementActive) {
            resetShellBindings();
            shellEnhancementActive = false;
        }

        return;
    }

    markCurrentHeadStylesAsManaged();

    if (!forceReinitialize && shellEnhancementActive) {
        return;
    }

    initializeShellBindings();
    await window.reinitializeBaseUi?.(document);
    shellEnhancementActive = true;
}

async function bootMobileApp() {
    if (!document.body?.hasAttribute('data-mobile-shell')) {
        return;
    }

    registerServiceWorker();
    await syncShellEnhancement(true);

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredInstallPrompt = event;
        if (isMobileViewport() || isStandaloneDisplayMode()) {
            showInstallPrompt();
        }
    });

    window.addEventListener('appinstalled', () => {
        deferredInstallPrompt = null;
        document.querySelector('.mobile-install-sheet')?.classList.remove('is-visible');
        sessionStorage.removeItem('it-dms-install-dismissed');
    });

    if (isStandaloneDisplayMode()) {
        document.documentElement.classList.add('mobile-standalone');
    }

    window.addEventListener('resize', () => {
        syncShellEnhancement();

        if (deferredInstallPrompt && (isMobileViewport() || isStandaloneDisplayMode())) {
            showInstallPrompt();
        }
    });
}

window.ITDMSMobileApp = {
    navigate: softNavigate,
};

bootMobileApp();
