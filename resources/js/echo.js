import Echo from 'laravel-echo';

/** Import Pusher dari 'pusher-js' dan mengikatnya (binding) ke window agar dapat diakses secara global */
import Pusher from 'pusher-js';
window.Pusher = Pusher;

/**  Inisialisasi Echo dengan konfigurasi Reverb (custom broadcaster) */
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

/** Mendengarkan channel privat bernama 'admin-notification' */
window.Echo.private('admin-notification')
    .listen('NewUser', (e) => {
        console.log(e);
        alert(e.user.name + " Baru saja mendaftar")
    })
