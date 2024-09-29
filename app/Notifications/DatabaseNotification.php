<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DatabaseNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Representasi notifikasi sebagai array data ini akan disimpan ke
     * dalam tabel notifications
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {

        return [
            'user_name' => $this->newUser->name, /** nama user yang baru melakukan registrasi */
            'user_id' => $this->newUser->id,
            'message' => $this->newUser->name . ' Baru saja mendaftar'
            /** Pesan yang berisi bahwa pengguna tersebut baru saja mendaftar */
        ];
    }
}
