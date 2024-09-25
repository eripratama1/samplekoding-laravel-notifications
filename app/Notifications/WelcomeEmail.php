<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Akun anda telah dibuat')
            ->greeting('Hai,' . $notifiable->name . '!')
            ->line('Selamat datang di platform kami. kami sangat senang anda bergabung.')
            ->line('Untuk memulai, silahkan kunjungi situs kami dengan menekan tombol di bawah')
            ->action('Dashboard', url('/dashboard'))
            ->from('samplekoding@gmail.com', 'Admin samplekoding')
            ->salutation('Salam hangat  ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
