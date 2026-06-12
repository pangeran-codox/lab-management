import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: window.location.port || 80,
    wssPort: window.location.port || 443,
    forceTLS: window.location.protocol === 'https:',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
    }
});

// Real-time Notification for Admin & Technician
window.Echo.channel('bookings')
    .listen('.booking.created', (e) => {
        // Filter: Admin/Operator see all, Technician only see their allowed labs
        const config = window.userConfig || { role: 'guest', allowedResources: [] };
        const isAdmin = ['admin', 'operator'].includes(config.role);
        const isAllowedTech = config.role === 'teknisi' && config.allowedResources.includes(parseInt(e.resource_id));

        if (isAdmin || isAllowedTech) {
            if (window.showGlobalNotification) {
                window.showGlobalNotification(`Booking Baru: ${e.title} oleh ${e.teacher_name} (${e.resource})`, 'ok');
            }
            
            // Update badge count
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                let count = parseInt(badge.textContent) || 0;
                badge.textContent = count + 1;
                badge.style.display = 'block';
            }
        }
    });