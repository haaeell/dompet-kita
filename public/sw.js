const CACHE_NAME = 'dompetkita-pwa-v3';
const APP_SHELL = [
    '/favicon.ico',
    '/images/app-logo.png',
    '/images/pwa-icon-192.png',
    '/images/pwa-icon-512.png',
    '/images/pwa-icon-maskable-512.png',
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

    if (isDocument) {
        event.respondWith(fetch(request));
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
