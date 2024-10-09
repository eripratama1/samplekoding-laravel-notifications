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

/**
 * Mendengarkan channel privat bernama 'admin-notification' untuk event 'NewUser'
 * Ketika event diterima, data pengguna baru (user) akan diproses:
 *
 * 1. Mencetak data user di konsol.
 * 2. Mengecek apakah elemen dengan ID 'no-notifications' ada, jika ya, maka dihapus.
 * 3. Menghapus semua elemen yang ada di dalam 'notification-container' untuk membuat tempat baru bagi notifikasi yang baru diterima.
 * 4. Melakukan iterasi pada setiap notifikasi yang diterima dari `e.user`, membuat elemen baru untuk setiap notifikasi:
 *    - Menggunakan div untuk item notifikasi dengan class styling untuk tampilan responsif.
 *    - Isi dari notifikasi ditampilkan dengan `notificationData.data.message`.
 *    - Membuat form untuk setiap notifikasi dengan tombol "Mark As Read" untuk menandai notifikasi sebagai dibaca.
 *    - Menggunakan token CSRF yang diambil dari meta tag untuk keamanan dalam form.
 * 5. Menambahkan setiap notifikasi baru ke dalam 'notification-container'.
 * 6. Memperbarui elemen 'notification-count' untuk menampilkan jumlah notifikasi baru yang diterima:
 *    - Jika ada notifikasi baru, maka jumlah notifikasi diperbarui.
 *    - Jika sebelumnya elemen 'notification-count' disembunyikan (class 'hidden'), class tersebut akan dihapus untuk menampilkan jumlah notifikasi.
 */

window.Echo.private('admin-notification')
    .listen('NewUser', (e) => {
        console.log(e.user);

        const noNotificationElement = document.getElementById('no-notifications');
        if (noNotificationElement) {
            noNotificationElement.remove();
        }

        const notificationContainer = document.getElementById('notification-container');
        while (notificationContainer.firstChild) {
            notificationContainer.removeChild(notificationContainer.firstChild);
        }

        e.user.forEach(notificationData => {
            const newNotification = document.createElement('div');
            newNotification.classList.add('notification-item','py-2','px-4','text-sm', 'text-gray-700', 'dark:text-gray-300' ,'flex' ,'justify-between' ,'items-center');
            newNotification.innerHTML = `
                <span>${notificationData.data.message}</span>
                <form action="/notifications/${notificationData.id}/mark-as-read" method="POST">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="hidden" name="_method" value="PATCH">
                    <button
                        class="mx-2 items-center px-4 py-2 bg-gray-800 dark:bg-gray-200
                    border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800
                    uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700
                    dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                    dark:focus:ring-offset-gray-800
                    transition ease-in-out duration-150">
                        Mark As Read
                    </button>
                </form>
            `;
            notificationContainer.appendChild(newNotification);
        });

        const notificationCountElement = document.getElementById('notification-count');
        if (notificationCountElement) {
            notificationCountElement.innerText = e.user.length;

            if (e.user.length > 0 && notificationCountElement.classList.contains('hidden')) {
                notificationCountElement.classList.remove('hidden');
            }

        }
    })
