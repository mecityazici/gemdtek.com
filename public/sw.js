// GEMDTEK Service Worker — network-first with offline fallback
// v2: yalnızca başarılı (ok) cevaplar cache'lenir; sürüm artışı eski
// (htaccess /storage 403 döneminde zehirlenmiş) runtime cache'ini temizler.
const VERSION = 'v2';
const SHELL_CACHE = `gemdtek-shell-${VERSION}`;
const RUNTIME_CACHE = `gemdtek-runtime-${VERSION}`;
const OFFLINE_URL = '/offline';

const SHELL_ASSETS = [
  '/',
  '/offline',
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(SHELL_CACHE).then((cache) => cache.addAll(SHELL_ASSETS))
      .then(() => self.skipWaiting())
      .catch(() => {})
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== SHELL_CACHE && key !== RUNTIME_CACHE)
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Only handle same-origin GET requests
  if (request.method !== 'GET') return;
  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  // Skip admin panel and API entirely — they need fresh data and auth context
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/livewire') || url.pathname.startsWith('/api/')) {
    return;
  }

  // Navigation requests: network-first, fall back to cache, then offline page
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          if (response.ok) {
            const copy = response.clone();
            caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, copy)).catch(() => {});
          }
          return response;
        })
        .catch(() =>
          caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL))
        )
    );
    return;
  }

  // Static assets: cache-first
  if (/\.(?:js|css|woff2?|png|jpg|jpeg|webp|svg|ico)$/i.test(url.pathname)) {
    event.respondWith(
      caches.match(request).then((cached) =>
        cached ||
        fetch(request).then((response) => {
          if (response.ok) {
            const copy = response.clone();
            caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, copy)).catch(() => {});
          }
          return response;
        })
      )
    );
  }
});
