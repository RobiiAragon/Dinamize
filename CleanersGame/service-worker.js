const CACHE_NAME = 'cleaners-game-cache-v1';
const urlsToCache = [
    '/',
    '/index.html',
    '/styles.css',
    '/GameplayScene.js',
    '/assets/favicon.ico',
    '/assets/icon-192x192.png',
    '/assets/icon-512x512.png',
    '/assets/boss/Gud.png',
    '/assets/Background.png',
    '/assets/Basurero.png',
    '/assets/bench.png',
    '/assets/kiosk.png',
    '/assets/plant.png',
    '/assets/trash/trash.png',
    '/assets/player/idle/arriba1.png',
    '/assets/player/idle/abajo1.png',
    '/assets/player/idle/izquierda1.png',
    '/assets/player/idle/derecha1.png',
    '/assets/player/animation_loop_UP/arriba1.png',
    '/assets/player/animation_loop_UP/arriba2.png',
    '/assets/player/animation_loop_UP/arriba3.png',
    '/assets/player/animation_loop_down/abajo1.png',
    '/assets/player/animation_loop_down/abajo2.png',
    '/assets/player/animation_loop_down/abajo3.png',
    '/assets/player/animation_loop_left/izquierda1.png',
    '/assets/player/animation_loop_left/izquierda2.png',
    '/assets/player/animation_loop_left/izquierda3.png',
    '/assets/player/animation_loop_right/derecha1.png',
    '/assets/player/animation_loop_right/derecha2.png',
    '/assets/player/animation_loop_right/derecha3.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request);
            })
    );
});

self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});