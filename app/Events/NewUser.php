<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** Variabel publik untuk menyimpan data pengguna yang akan dikirim melalui event */
    public $user;


    /**
     * Create a new event instance.
     */

     /** Mengisi properti $user dengan data pengguna yang diterima di konstruktor */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        /** Mengembalikan channel privat bernama 'admin-notification' untuk menyiarkan event */
        return [
            new PrivateChannel('admin-notification'),
        ];
    }
}
