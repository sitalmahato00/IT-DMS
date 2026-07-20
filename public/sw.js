const CACHE_VERSION = 'it-dms-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.webmanifest',
    '/icons/app-icon.svg',
    '/icons/maskable-icon.svg',
    '/images/default-logo.svg',
    '/favicon.ico',
    '/',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => ![STATIC_CACHE, RUNTIME_CACHE].includes(key))
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    const data = event.data || {};

    if (data.type === 'SKIP_WAITING') {
        self.skipWaiting();
        return;
    }

    if (data.type === 'SHOW_NOTIFICATION') {
        const title = data.title || 'IT-DMS';
        const options = {
            body: data.body || 'A new update is available.',
            icon: '/icons/app-icon.svg',
            badge: '/icons/app-icon.svg',
            data: {
                url: data.url || '/dashboard',
            },
            tag: data.tag || 'it-dms-local-alert',
            renotify: true,
        };

        event.waitUntil(self.registration.showNotification(title, options));
    }
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(handleNavigationRequest(request));
        return;
    }

    if (shouldCacheAsset(request, url)) {
        event.respondWith(handleAssetRequest(request));
    }
});

self.addEventListener('push', (event) => {
    let payload = {};

    try {
        payload = event.data ? event.data.json() : {};
    } catch (error) {
        payload = {
            title: 'IT-DMS',
            body: event.data ? event.data.text() : 'A new update is available.',
        };
    }

    const title = payload.title || 'IT-DMS';
    const options = {
        body: payload.body || 'A new update is available.',
        icon: '/icons/app-icon.svg',
        badge: '/icons/app-icon.svg',
        data: {
            url: payload.url || '/dashboard',
        },
        tag: payload.tag || 'it-dms-push',
        renotify: true,
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = new URL(event.notification.data?.url || '/dashboard', self.location.origin).href;

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
            for (const client of clients) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }

            return self.clients.openWindow(targetUrl);
        })
    );
});

async function handleNavigationRequest(request) {
    const cache = await caches.open(RUNTIME_CACHE);

    try {
        const freshResponse = await fetch(request);
        cache.put(request, freshResponse.clone());
        return freshResponse;
    } catch (error) {
        const cachedResponse = await cache.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }

        const offlineResponse = await caches.match(OFFLINE_URL);
        return offlineResponse || Response.error();
    }
}

async function handleAssetRequest(request) {
    const cache = await caches.open(RUNTIME_CACHE);
    const cachedResponse = await cache.match(request);

    const networkFetch = fetch(request)
        .then((response) => {
            if (response && response.status === 200) {
                cache.put(request, response.clone());
            }

            return response;
        })
        .catch(() => cachedResponse);

    return cachedResponse || networkFetch;
}

function shouldCacheAsset(request, url) {
    if (url.pathname.startsWith('/build/')) {
        return true;
    }

    if (url.pathname.startsWith('/icons/') || url.pathname.startsWith('/images/') || url.pathname.startsWith('/js/')) {
        return true;
    }

    return ['style', 'script', 'font', 'image'].includes(request.destination);
}
