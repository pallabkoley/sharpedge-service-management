const CACHE_NAME = 'sharpedge-cache-v2';

const urlsToCache = [
  '/',
  '/login'
];

// INSTALL
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
      .catch(error => {
        console.error('Cache failed:', error);
      })
  );
});

// FETCH
self.addEventListener('fetch', event => {

  // Never cache POST requests (important for Laravel)
  if (event.request.method !== 'GET') return;

  // Never cache logout or auth routes
  if (event.request.url.includes('/logout') ||
      event.request.url.includes('/login') ||
      event.request.url.includes('/register')) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});

// ACTIVATE
self.addEventListener('activate', event => {
  const whitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (!whitelist.includes(key)) {
            return caches.delete(key);
          }
        })
      );
    })
  );
});