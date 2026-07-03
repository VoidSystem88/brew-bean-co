// BrewBeanCo Pro Service Worker
const CACHE_NAME = 'BrewBeanCo-v2';
const STATIC_CACHE = 'BrewBeanCo-static-v2';

// Files to cache for offline access
const urlsToCache = [
    '/',
    '/pos',
    '/mobile-pulse',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
];

// Install Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Caching static assets');
                return cache.addAll(urlsToCache);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate Service Worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== STATIC_CACHE && cache !== CACHE_NAME) {
                        console.log('Deleting old cache:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch - Network first, fallback to cache
self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Clone the response
                const responseClone = response.clone();
                // Cache the new response
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, responseClone);
                });
                return response;
            })
            .catch(() => {
                // If network fails, try cache
                return caches.match(event.request)
                    .then(cachedResponse => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        // If not in cache, return offline page
                        if (event.request.mode === 'navigate') {
                            return caches.match('/offline.html');
                        }
                        return new Response('Offline', {
                            status: 503,
                            statusText: 'Service Unavailable'
                        });
                    });
            })
    );
});

// Handle offline mode detection
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'OFFLINE_STATUS') {
        // Broadcast offline status to all clients
        self.clients.matchAll().then(clients => {
            clients.forEach(client => {
                client.postMessage({
                    type: 'OFFLINE_STATUS',
                    offline: event.data.offline
                });
            });
        });
    }
});
