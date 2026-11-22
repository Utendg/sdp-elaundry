// Service worker for the installable AUN E-Laundry app.
// Pages are always fetched from the network so orders, prices and notifications are never stale,
// and nobody's account data is stored on the device. Only the offline page, icons and the
// compiled CSS/JS are cached, so the app opens quickly and shows a clear message when offline.
const CACHE = 'elaundry-v1';
const PRECACHE = ['/offline.html', '/icons/icon-192.png', '/images/logo.svg'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(PRECACHE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const req = event.request;
    if (req.method !== 'GET') return; // forms (POST) always go straight to the server
    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;

    // Page navigations: network only, offline page when there is no connection.
    if (req.mode === 'navigate') {
        event.respondWith(fetch(req).catch(() => caches.match('/offline.html')));
        return;
    }

    // Versioned build assets and icons: cache first (file names change when the content changes).
    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/') || url.pathname.startsWith('/images/')) {
        event.respondWith(
            caches.match(req).then(hit => hit || fetch(req).then(res => {
                if (res.ok) { const copy = res.clone(); caches.open(CACHE).then(c => c.put(req, copy)); }
                return res;
            }))
        );
    }
});
