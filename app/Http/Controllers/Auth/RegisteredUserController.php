<?php

namespace App\Http\Controllers\Auth;

use App\Events\NewUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\DatabaseNotification;
use App\Notifications\WelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        //$user->notify(new WelcomeEmail());

        /**
         * Mengambil semua pengguna dengan role 'Admin' untuk mengirim notifikasi
         * dan menyimpannya ke tabel notifications
         */
        $admin = User::where('role', 'Admin')->get();
        Notification::send($admin, new DatabaseNotification($user));

        /**
         * Memicu event 'NewUser' untuk menyiarkan notifikasi tentang pendaftaran pengguna baru ke admin.
         *
         * Proses:
         * 1. Menginisialisasi array kosong `$unreadNotifications` untuk menyimpan notifikasi yang belum terbaca.
         * 2. Melakukan iterasi melalui setiap pengguna admin yang diberikan dalam variabel `$admin`.
         * 3. Menggabungkan notifikasi yang belum terbaca (`unreadNotifications`) dari setiap admin ke dalam array `$unreadNotifications` menggunakan `array_merge`.
         * 4. Setelah semua notifikasi belum terbaca dari setiap admin digabungkan, event `NewUser` dipicu, mengirimkan array `$unreadNotifications` sebagai data yang akan disiarkan.
         *
         * Tujuan:
         * - Mengumpulkan semua notifikasi belum terbaca dari setiap admin dan menyiarkannya sebagai bagian dari event `NewUser`.
         * - Event ini akan menangkap data notifikasi dan mengirimkannya ke sistem broadcast yang memungkinkan admin untuk menerima pemberitahuan tentang pengguna baru yang mendaftar.
         */
        $unreadNotifications = [];
        foreach ($admin as $adminUser) {
            // Menggabungkan notifikasi belum terbaca dari setiap admin ke array $unreadNotifications
            $unreadNotifications = array_merge($unreadNotifications, $adminUser->unreadNotifications->toArray());
        }

        // Memicu event 'NewUser' dan mengirimkan array notifikasi belum terbaca
        event(new NewUser($unreadNotifications));


        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
