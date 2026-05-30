const CACHE_NAME = 'dompetkita-pwa-v10';
const APP_SHELL = [
    '/',
    '/transactions',
    '/transactions/create',
    '/favicon.ico',
    '/images/app-logo-dompetkita.png',
    '/images/pwa-icon-dompetkita-192.png',
    '/images/pwa-icon-dompetkita-512.png',
    '/images/pwa-icon-dompetkita-maskable-512.png',
    '/manifest.webmanifest'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);
    const isDocument = request.mode === 'navigate' || request.destination === 'document';
    const isRealtimeApi = url.pathname.startsWith('/chats/messages');

    if (isRealtimeApi) {
        event.respondWith(fetch(request));
        return;
    }

    if (isDocument) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    const copy = response.clone();

                    if (response.ok) {
                        caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    }

                    return response;
                })
                .catch(() => caches.match(request).then(cached => cached || caches.match('/')))
        );
        return;
    }

    if (url.origin === self.location.origin || ['style', 'script', 'image', 'font'].includes(request.destination)) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) {
                    return cached;
                }

                return fetch(request).then(response => {
                    const copy = response.clone();

                    if (response.ok) {
                        caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    }

                    return response;
                });
            })
        );
    }
});
