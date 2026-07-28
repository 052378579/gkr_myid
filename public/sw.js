const CACHE_NAME = 'gracia-cache-v3';
const ASSETS_TO_CACHE = [
  '/',
  '/site.webmanifest',
  '/favicon.ico',
  '/favicon.svg',
  '/favicon-96x96.png',
  '/apple-touch-icon.png',
  '/web-app-manifest-192x192.png',
  '/web-app-manifest-512x512.png',
  '/css/login.css'
];

// 1. Install Service Worker & Cache Aset Utama
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

// 2. Aktivasi & Hapus Cache Lama jika ada pembaruan versi
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// 3. Strategi Fetch: Hanya tangani GET Aset Statis, ABAIKAN POST/API/Crawler Streaming
self.addEventListener('fetch', (event) => {
  // PENTING: Wajib mengabaikan request POST, PUT, DELETE, streaming crawler, & API agar tidak diintersepsi PWA SW
  if (event.request.method !== 'GET') return;
  if (!event.request.url.startsWith(self.location.origin)) return;
  
  // Abaikan rute API, Crawler, Admin Streaming, dan rute dinamis backend
  const url = new URL(event.request.url);
  if (url.pathname.startsWith('/crawler/') || 
      url.pathname.startsWith('/api/') || 
      url.pathname.startsWith('/admin/')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Jika sukses GET 200, simpan cadangan offline
        if (response.status === 200) {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        // Jika offline, ambil dari cache
        return caches.match(event.request);
      })
  );
});