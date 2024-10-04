<?php

use Illuminate\Support\Facades\Broadcast;

/** Mendefinisikan channel broadcast bernama 'admin-notification' */
Broadcast::channel('admin-notification', function ($user) {

    /**
     *  Mengembalikan 'true' untuk mengizinkan semua pengguna terhubung ke channel ini
      * Dalam hal ini, tidak ada pembatasan autentikasi untuk mengakses channel 'admin-notification'
     */
    return true;
});
