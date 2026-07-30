// Service Worker KurbanQu
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function(event) {
    let payload = event.data ? event.data.text() : 'Notifikasi KurbanQu';
    try {
        const data = JSON.parse(payload);
        const title = data.title || 'KurbanQu';
        const options = {
            body: data.body || '',
            icon: '/assets/img/FIN.png',
            badge: '/assets/img/FIN.png',
            vibrate: [200, 100, 200]
        };
        event.waitUntil(self.registration.showNotification(title, options));
    } catch (e) {
        event.waitUntil(self.registration.showNotification('KurbanQu', {
            body: payload,
            icon: '/assets/img/FIN.png'
        }));
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/')
    );
});
