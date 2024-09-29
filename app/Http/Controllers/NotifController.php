<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifController extends Controller
{
    /**
     * Method berikut digunakan untuk menandai sebuah notifikasi dengan ID tertentu sebagai "telah dibaca"
     * Setelah menandai notifikasi, pengguna akan diarahkan kembali ke halaman sebelumnya.
     */
    public function markAsRead($id)
    {
        $notifications = auth()->user()->notifications()->find($id);

        if ($notifications) {
            $notifications->markAsRead();
        }

        return redirect()->back();
    }
}
